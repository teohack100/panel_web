import { Prisma, TenantStatus } from "@prisma/client";
import Link from "next/link";
import { redirect } from "next/navigation";

import { SectionHeader } from "@/components/control/section-header";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { requireControlAdmin } from "@/lib/auth";
import { writeAuditLog } from "@/lib/audit";
import { prisma } from "@/lib/db";
import {
  createTenantWithProvisioning,
  isValidHostname,
  normalizeHostname,
  normalizeSlug,
} from "@/lib/tenant-onboarding";

type SearchParams = Record<string, string | string[] | undefined>;

function firstParam(value: string | string[] | undefined) {
  return Array.isArray(value) ? value[0] : value;
}

function buildErrorUrl(reason: string) {
  return `/control/tenants/new?error=${encodeURIComponent(reason)}`;
}

async function createTenantWizardAction(formData: FormData) {
  "use server";

  const admin = await requireControlAdmin();

  const slug = normalizeSlug(String(formData.get("slug") ?? ""));
  const name = String(formData.get("name") ?? "").trim();
  const ownerEmail = String(formData.get("ownerEmail") ?? "").trim().toLowerCase();
  const ownerIdRaw = String(formData.get("ownerId") ?? "").trim();
  const statusRaw = String(formData.get("status") ?? "").trim().toUpperCase();
  const initialCreditBalanceRaw = String(formData.get("initialCreditBalance") ?? "").trim();
  const planIdRaw = String(formData.get("planId") ?? "").trim();
  const productIdRaw = String(formData.get("productId") ?? "").trim();
  const primaryDomain = normalizeHostname(String(formData.get("primaryDomain") ?? ""));
  const nodeKey = String(formData.get("nodeKey") ?? "").trim().toLowerCase();
  const phpVersion = String(formData.get("phpVersion") ?? "").trim();
  const autoProvision = formData.get("autoProvision") === "on";

  if (!slug || !name || !ownerEmail) {
    redirect(buildErrorUrl("campos_requeridos"));
  }

  if (!isValidHostname(primaryDomain)) {
    redirect(buildErrorUrl("dominio_invalido"));
  }

  if (autoProvision && !nodeKey) {
    redirect(buildErrorUrl("nodo_requerido_auto_provision"));
  }

  const status = Object.values(TenantStatus).includes(statusRaw as TenantStatus)
    ? (statusRaw as TenantStatus)
    : TenantStatus.ACTIVE;

  const [existingSlug, existingDomain] = await Promise.all([
    prisma.tenant.findUnique({ where: { slug }, select: { id: true } }),
    prisma.tenantDomain.findUnique({ where: { hostname: primaryDomain }, select: { id: true } }),
  ]);

  if (existingSlug) {
    redirect(buildErrorUrl("slug_ya_existe"));
  }
  if (existingDomain) {
    redirect(buildErrorUrl("dominio_ya_existe"));
  }

  try {
    const result = await createTenantWithProvisioning({
      slug,
      name,
      ownerId: ownerIdRaw || null,
      ownerEmail,
      planId: planIdRaw || null,
      productIds: productIdRaw ? [productIdRaw] : [],
      status,
      initialCreditBalance: initialCreditBalanceRaw || 0,
      primaryDomain,
      nodeKey: nodeKey || null,
      phpVersion: phpVersion || "8.2",
      autoProvision,
      failOnDomainExists: true,
      createdByUserId: admin.id,
    });

    await writeAuditLog({
      action: "TENANT_WIZARD_CREATE",
      actorUserId: admin.id,
      tenantId: result.tenantId,
      targetType: "tenant",
      targetId: result.tenantId,
      metadata: {
        slug,
        primaryDomain,
        nodeKey: nodeKey || null,
        autoProvision,
        cloudTaskId: result.cloudTaskId,
      },
    });

    const created = encodeURIComponent(result.tenantSlug);
    const queued = result.cloudTaskId ? "&queued=1" : "";
    redirect(`/control/tenants?created=${created}${queued}`);
  } catch (error) {
    if (error instanceof Prisma.PrismaClientKnownRequestError && error.code === "P2002") {
      redirect(buildErrorUrl("duplicado"));
    }
    if (error instanceof Error && error.message === "domain_already_exists") {
      redirect(buildErrorUrl("dominio_ya_existe"));
    }
    redirect(buildErrorUrl("error_creando_tenant"));
  }
}

function getErrorMessage(code?: string) {
  switch (code) {
    case "campos_requeridos":
      return "Completa slug, nombre y email del owner.";
    case "dominio_invalido":
      return "El dominio primario no es valido. Ejemplo: panel.cliente.com";
    case "nodo_requerido_auto_provision":
      return "Si activas auto-provision, debes elegir un nodo CloudPanel.";
    case "slug_ya_existe":
      return "Ese slug ya existe. Usa otro identificador de tienda.";
    case "dominio_ya_existe":
      return "Ese dominio ya esta asignado a otra tienda.";
    case "duplicado":
      return "Ya existe un registro con esos datos (slug/dominio).";
    case "error_creando_tenant":
      return "No se pudo crear el tenant. Revisa datos e intenta de nuevo.";
    default:
      return "";
  }
}

export default async function NewTenantWizardPage({
  searchParams,
}: {
  searchParams: Promise<SearchParams>;
}) {
  await requireControlAdmin();
  const params = await searchParams;
  const errorCode = firstParam(params.error);
  const errorMessage = getErrorMessage(errorCode);

  const [plans, products, nodes] = await Promise.all([
    prisma.plan.findMany({ where: { isActive: true }, orderBy: { name: "asc" } }),
    prisma.systemProduct.findMany({ where: { status: "ACTIVE" }, orderBy: { name: "asc" } }),
    prisma.cloudPanelNode.findMany({ where: { isEnabled: true }, orderBy: { name: "asc" } }),
  ]);

  return (
    <div className="space-y-6">
      <SectionHeader
        eyebrow="Wizard"
        title="Alta de cliente + dominio"
        description="Crea tenant, owner comercial y dominio primario en un solo flujo."
      />

      {errorMessage ? (
        <div className="rounded-lg border border-rose-500/40 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
          {errorMessage}
        </div>
      ) : null}

      <Card>
        <CardHeader>
          <CardTitle>Onboarding de tienda</CardTitle>
          <CardDescription>
            En un solo paso: tenant, plan inicial, producto, dominio y cola de provisionamiento.
          </CardDescription>
        </CardHeader>
        <CardContent>
          <form action={createTenantWizardAction} className="grid gap-3 md:grid-cols-2 lg:grid-cols-3">
            <Input name="slug" placeholder="cliente-demo" required />
            <Input name="name" placeholder="Cliente Demo" required />
            <Input name="ownerEmail" placeholder="owner@cliente.com" type="email" required />

            <Input name="ownerId" placeholder="owner_id opcional (si ya existe)" />
            <select
              name="status"
              className="h-10 rounded-md border border-slate-700 bg-slate-900 px-3 text-sm text-slate-100"
              defaultValue={TenantStatus.ACTIVE}
            >
              <option value={TenantStatus.ACTIVE}>ACTIVE</option>
              <option value={TenantStatus.TRIAL}>TRIAL</option>
              <option value={TenantStatus.SUSPENDED}>SUSPENDED</option>
              <option value={TenantStatus.CANCELED}>CANCELED</option>
            </select>
            <Input name="initialCreditBalance" type="number" min="0" step="0.0001" defaultValue="0" />

            <select
              name="planId"
              className="h-10 rounded-md border border-slate-700 bg-slate-900 px-3 text-sm text-slate-100"
              defaultValue=""
            >
              <option value="">Sin plan inicial</option>
              {plans.map((plan) => (
                <option key={plan.id} value={plan.id}>
                  {plan.name}
                </option>
              ))}
            </select>
            <select
              name="productId"
              className="h-10 rounded-md border border-slate-700 bg-slate-900 px-3 text-sm text-slate-100"
              defaultValue=""
            >
              <option value="">Sin producto inicial</option>
              {products.map((product) => (
                <option key={product.id} value={product.id}>
                  {product.name}
                </option>
              ))}
            </select>
            <Input name="primaryDomain" placeholder="panel.cliente.com" required />

            <select
              name="nodeKey"
              className="h-10 rounded-md border border-slate-700 bg-slate-900 px-3 text-sm text-slate-100"
              defaultValue=""
            >
              <option value="">Sin nodo (si no auto-provisionas)</option>
              {nodes.map((node) => (
                <option key={node.id} value={node.key}>
                  {node.name} ({node.key})
                </option>
              ))}
            </select>
            <Input name="phpVersion" placeholder="8.2" defaultValue="8.2" />
            <label className="inline-flex h-10 items-center gap-2 rounded-md border border-slate-700 px-3 text-sm text-slate-200">
              <input type="checkbox" name="autoProvision" defaultChecked className="h-4 w-4" />
              Auto-provision CloudPanel
            </label>

            <div className="flex items-center gap-3 md:col-span-2 lg:col-span-3">
              <Button type="submit">Crear tenant ahora</Button>
              <Link href="/control/tenants">
                <Button type="button" variant="outline">
                  Volver a clientes
                </Button>
              </Link>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  );
}

