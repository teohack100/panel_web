import { TenantProductStatus, TenantStatus } from "@prisma/client";
import { revalidatePath } from "next/cache";
import Link from "next/link";

import { SectionHeader } from "@/components/control/section-header";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Table, TBody, TD, TH, THead, TR } from "@/components/ui/table";
import { requireControlAdmin } from "@/lib/auth";
import { writeAuditLog } from "@/lib/audit";
import { prisma } from "@/lib/db";
import { formatDateTime, formatUsd, toNumber } from "@/lib/format";
import { createTenantWithProvisioning, normalizeSlug } from "@/lib/tenant-onboarding";

type SearchParams = Record<string, string | string[] | undefined>;
function firstParam(value: string | string[] | undefined) {
  return Array.isArray(value) ? value[0] : value;
}

async function createTenantAction(formData: FormData) {
  "use server";

  const admin = await requireControlAdmin();

  const slug = normalizeSlug(String(formData.get("slug") ?? ""));
  const name = String(formData.get("name") ?? "").trim();
  const ownerIdRaw = String(formData.get("ownerId") ?? "").trim();
  const ownerEmailRaw = String(formData.get("ownerEmail") ?? "").trim().toLowerCase();
  const statusRaw = String(formData.get("status") ?? "").trim().toUpperCase();
  const initialCreditBalanceRaw = String(formData.get("initialCreditBalance") ?? "").trim();
  const planIdRaw = String(formData.get("planId") ?? "").trim();
  const productIdRaw = String(formData.get("productId") ?? "").trim();
  const primaryDomainRaw = String(formData.get("primaryDomain") ?? "").trim();
  const nodeKeyRaw = String(formData.get("nodeKey") ?? "").trim().toLowerCase();
  const phpVersionRaw = String(formData.get("phpVersion") ?? "").trim();
  const autoProvision = formData.get("autoProvision") === "on";
  const status = Object.values(TenantStatus).includes(statusRaw as TenantStatus)
    ? (statusRaw as TenantStatus)
    : TenantStatus.ACTIVE;

  if (!slug || !name || !ownerEmailRaw) return;

  const result = await createTenantWithProvisioning({
    slug,
    name,
    ownerId: ownerIdRaw || null,
    ownerEmail: ownerEmailRaw || null,
    planId: planIdRaw || null,
    productIds: productIdRaw ? [productIdRaw] : [],
    status,
    initialCreditBalance: initialCreditBalanceRaw || 0,
    primaryDomain: primaryDomainRaw || null,
    nodeKey: nodeKeyRaw || null,
    phpVersion: phpVersionRaw || null,
    autoProvision,
    createdByUserId: admin.id,
  });

  await writeAuditLog({
    action: "TENANT_CREATE",
    actorUserId: admin.id,
    tenantId: result.tenantId,
    targetType: "tenant",
    targetId: result.tenantId,
    metadata: {
      slug,
      productIdRaw: productIdRaw || null,
      primaryDomain: primaryDomainRaw || null,
      nodeKey: nodeKeyRaw || null,
      autoProvision,
      cloudTaskId: result.cloudTaskId,
      notes: result.notes,
    },
  });

  revalidatePath("/control/tenants");
}

async function setTenantStatusAction(formData: FormData) {
  "use server";

  const admin = await requireControlAdmin();
  const tenantId = String(formData.get("tenantId") ?? "").trim();
  const statusRaw = String(formData.get("status") ?? "").trim().toUpperCase();
  if (!tenantId) return;
  if (!Object.values(TenantStatus).includes(statusRaw as TenantStatus)) return;

  const nextStatus = statusRaw as TenantStatus;

  await prisma.tenant.update({
    where: { id: tenantId },
    data: { status: nextStatus },
  });

  await writeAuditLog({
    action: "TENANT_TOGGLE_STATUS",
    actorUserId: admin.id,
    tenantId,
    targetType: "tenant",
    targetId: tenantId,
    metadata: { nextStatus },
  });

  revalidatePath("/control/tenants");
}

async function setTenantPlanAction(formData: FormData) {
  "use server";

  const admin = await requireControlAdmin();
  const tenantId = String(formData.get("tenantId") ?? "").trim();
  const planIdRaw = String(formData.get("planId") ?? "").trim();
  if (!tenantId) return;

  await prisma.tenant.update({
    where: { id: tenantId },
    data: { planId: planIdRaw || null },
  });

  await writeAuditLog({
    action: "TENANT_SET_PLAN",
    actorUserId: admin.id,
    tenantId,
    targetType: "tenant",
    targetId: tenantId,
    metadata: { planId: planIdRaw || null },
  });

  revalidatePath("/control/tenants");
}

async function setTenantCreditBalanceAction(formData: FormData) {
  "use server";

  const admin = await requireControlAdmin();
  const tenantId = String(formData.get("tenantId") ?? "").trim();
  const creditBalanceRaw = String(formData.get("creditBalance") ?? "").trim();
  if (!tenantId) return;

  const creditBalance = Number(creditBalanceRaw);
  if (!Number.isFinite(creditBalance) || creditBalance < 0) return;

  await prisma.tenant.update({
    where: { id: tenantId },
    data: { creditBalance },
  });

  await writeAuditLog({
    action: "TENANT_SET_CREDIT_BALANCE",
    actorUserId: admin.id,
    tenantId,
    targetType: "tenant",
    targetId: tenantId,
    metadata: { creditBalance },
  });

  revalidatePath("/control/tenants");
}

async function assignProductAction(formData: FormData) {
  "use server";

  const admin = await requireControlAdmin();
  const tenantId = String(formData.get("tenantId") ?? "").trim();
  const productId = String(formData.get("productId") ?? "").trim();
  if (!tenantId || !productId) return;

  await prisma.tenantProduct.upsert({
    where: { tenantId_productId: { tenantId, productId } },
    create: {
      tenantId,
      productId,
      status: TenantProductStatus.PROVISIONING,
    },
    update: {
      status: TenantProductStatus.ACTIVE,
    },
  });

  await writeAuditLog({
    action: "TENANT_ASSIGN_PRODUCT",
    actorUserId: admin.id,
    tenantId,
    targetType: "tenant_product",
    targetId: `${tenantId}:${productId}`,
  });

  revalidatePath("/control/tenants");
}

export default async function TenantsPage({
  searchParams,
}: {
  searchParams: Promise<SearchParams>;
}) {
  await requireControlAdmin();
  const params = await searchParams;
  const createdSlug = firstParam(params.created);
  const queued = firstParam(params.queued) === "1";

  const [tenants, plans, products, nodes] = await Promise.all([
    prisma.tenant.findMany({
      orderBy: { createdAt: "desc" },
      include: {
        owner: true,
        plan: true,
        _count: { select: { domains: true, products: true } },
      },
    }),
    prisma.plan.findMany({ where: { isActive: true }, orderBy: { name: "asc" } }),
    prisma.systemProduct.findMany({ where: { status: "ACTIVE" }, orderBy: { name: "asc" } }),
    prisma.cloudPanelNode.findMany({ where: { isEnabled: true }, orderBy: { name: "asc" } }),
  ]);
  const activeTenants = tenants.filter((tenant) => tenant.status === TenantStatus.ACTIVE).length;
  const suspendedTenants = tenants.filter((tenant) => tenant.status === TenantStatus.SUSPENDED).length;

  return (
    <div className="space-y-6">
      <SectionHeader
        eyebrow="Clientes"
        title="Tenants / Tiendas"
        description="Alta, suspension, asignacion de planes y productos para cada cliente white-label."
      />

      {createdSlug ? (
        <div className="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
          Tenant creado: <span className="font-semibold">{createdSlug}</span>
          {queued ? " | Provision CloudPanel en cola." : ""}
        </div>
      ) : null}

      <Card>
        <CardHeader>
          <div className="flex flex-wrap items-center justify-between gap-3">
            <div>
              <CardTitle>Crear tenant</CardTitle>
              <CardDescription>Provisiona una nueva tienda dentro del ecosistema (alta completa).</CardDescription>
            </div>
            <Link href="/control/tenants/new">
              <Button type="button" variant="outline">
                Wizard alta + dominio
              </Button>
            </Link>
          </div>
        </CardHeader>
        <CardContent>
          <form action={createTenantAction} className="grid gap-3 md:grid-cols-2 lg:grid-cols-3">
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
            <Input name="initialCreditBalance" type="number" min="0" step="0.0001" placeholder="Saldo inicial" defaultValue="0" />
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
            <Input name="primaryDomain" placeholder="panel.cliente.com" />
            <select
              name="nodeKey"
              className="h-10 rounded-md border border-slate-700 bg-slate-900 px-3 text-sm text-slate-100"
              defaultValue=""
            >
              <option value="">Sin nodo (sin provision cloud)</option>
              {nodes.map((node) => (
                <option key={node.id} value={node.key}>
                  {node.name} ({node.key})
                </option>
              ))}
            </select>
            <Input name="phpVersion" placeholder="8.2" defaultValue="8.2" />
            <label className="inline-flex items-center gap-2 text-sm text-slate-200">
              <input type="checkbox" name="autoProvision" defaultChecked className="h-4 w-4" />
              Auto-provisionar en CloudPanel
            </label>
            <div className="md:col-span-2 lg:col-span-3">
              <Button type="submit">Crear tenant</Button>
            </div>
          </form>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Listado de tenants</CardTitle>
          <CardDescription>
            {tenants.length} tiendas en total | {activeTenants} activas | {suspendedTenants} suspendidas
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4 overflow-x-auto">
          <Table>
            <THead>
              <TR>
                <TH>Tienda</TH>
                <TH>Owner</TH>
                <TH>Plan</TH>
                <TH>Creditos</TH>
                <TH>Dominios</TH>
                <TH>Productos</TH>
                <TH>Estado</TH>
                <TH>Acciones</TH>
              </TR>
            </THead>
            <TBody>
              {tenants.map((tenant) => (
                <TR key={tenant.id}>
                  <TD>
                    <p className="font-medium text-white">{tenant.name}</p>
                    <p className="text-xs text-slate-400">{tenant.slug}</p>
                    <p className="text-xs text-slate-500">{formatDateTime(tenant.createdAt)}</p>
                  </TD>
                  <TD>{tenant.owner.email}</TD>
                  <TD>
                    <form action={setTenantPlanAction} className="flex items-center gap-2">
                      <input type="hidden" name="tenantId" value={tenant.id} />
                      <select
                        name="planId"
                        className="h-8 rounded-md border border-slate-700 bg-slate-900 px-2 text-xs text-slate-100"
                        defaultValue={tenant.planId ?? ""}
                      >
                        <option value="">Sin plan</option>
                        {plans.map((plan) => (
                          <option key={plan.id} value={plan.id}>
                            {plan.name}
                          </option>
                        ))}
                      </select>
                      <Button size="sm" type="submit" variant="outline">
                        Plan
                      </Button>
                    </form>
                  </TD>
                  <TD>
                    <p className="mb-2 text-xs text-slate-200">{formatUsd(tenant.creditBalance)}</p>
                    <form action={setTenantCreditBalanceAction} className="flex items-center gap-2">
                      <input type="hidden" name="tenantId" value={tenant.id} />
                      <Input
                        name="creditBalance"
                        type="number"
                        min="0"
                        step="0.0001"
                        defaultValue={toNumber(tenant.creditBalance).toFixed(4)}
                        className="h-8 w-28 text-xs"
                      />
                      <Button size="sm" type="submit" variant="outline">
                        Saldo
                      </Button>
                    </form>
                  </TD>
                  <TD>{tenant._count.domains}</TD>
                  <TD>{tenant._count.products}</TD>
                  <TD>
                    <div className="space-y-2">
                      <Badge>{tenant.status}</Badge>
                      <form action={setTenantStatusAction} className="flex items-center gap-2">
                        <input type="hidden" name="tenantId" value={tenant.id} />
                        <select
                          name="status"
                          className="h-8 rounded-md border border-slate-700 bg-slate-900 px-2 text-xs text-slate-100"
                          defaultValue={tenant.status}
                        >
                          <option value={TenantStatus.ACTIVE}>ACTIVE</option>
                          <option value={TenantStatus.TRIAL}>TRIAL</option>
                          <option value={TenantStatus.SUSPENDED}>SUSPENDED</option>
                          <option value={TenantStatus.CANCELED}>CANCELED</option>
                        </select>
                        <Button type="submit" size="sm" variant="outline">
                          Estado
                        </Button>
                      </form>
                    </div>
                  </TD>
                  <TD>
                    <div className="flex flex-wrap gap-2">
                      <form action={assignProductAction} className="flex items-center gap-2">
                        <input type="hidden" name="tenantId" value={tenant.id} />
                        <select
                          name="productId"
                          className="h-8 rounded-md border border-slate-700 bg-slate-900 px-2 text-xs text-slate-100"
                          defaultValue=""
                        >
                          <option value="" disabled>
                            Agregar producto...
                          </option>
                          {products.map((product) => (
                            <option key={product.id} value={product.id}>
                              {product.name}
                            </option>
                          ))}
                        </select>
                        <Button size="sm" type="submit" variant="outline">
                          Asignar
                        </Button>
                      </form>
                    </div>
                  </TD>
                </TR>
              ))}
            </TBody>
          </Table>
        </CardContent>
      </Card>
    </div>
  );
}
