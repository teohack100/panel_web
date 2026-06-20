import { TenantProductStatus, TenantStatus } from "@prisma/client";
import { revalidatePath } from "next/cache";

import { SectionHeader } from "@/components/control/section-header";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Table, TBody, TD, TH, THead, TR } from "@/components/ui/table";
import { requireControlAdmin } from "@/lib/auth";
import { writeAuditLog } from "@/lib/audit";
import { prisma } from "@/lib/db";
import { formatDateTime } from "@/lib/format";

async function suspendTenantAction(formData: FormData) {
  "use server";

  const admin = await requireControlAdmin();
  const slug = String(formData.get("slug") ?? "").trim().toLowerCase();
  const reason = String(formData.get("reason") ?? "").trim();

  if (!slug) return;

  const tenant = await prisma.tenant.findUnique({ where: { slug } });
  if (!tenant) return;

  await prisma.tenant.update({ where: { id: tenant.id }, data: { status: TenantStatus.SUSPENDED } });

  await writeAuditLog({
    action: "TENANT_SUSPEND",
    actorUserId: admin.id,
    tenantId: tenant.id,
    targetType: "tenant",
    targetId: tenant.id,
    metadata: { reason: reason || null },
  });

  revalidatePath("/control/suspensions");
}

async function reactivateTenantAction(formData: FormData) {
  "use server";

  const admin = await requireControlAdmin();
  const tenantId = String(formData.get("tenantId") ?? "").trim();
  if (!tenantId) return;

  await prisma.tenant.update({ where: { id: tenantId }, data: { status: TenantStatus.ACTIVE } });

  await writeAuditLog({
    action: "TENANT_REACTIVATE",
    actorUserId: admin.id,
    tenantId,
    targetType: "tenant",
    targetId: tenantId,
  });

  revalidatePath("/control/suspensions");
}

async function toggleProductSuspendAction(formData: FormData) {
  "use server";

  const admin = await requireControlAdmin();
  const id = String(formData.get("id") ?? "").trim();
  if (!id) return;

  const row = await prisma.tenantProduct.findUnique({ where: { id } });
  if (!row) return;

  const nextStatus = row.status === TenantProductStatus.SUSPENDED ? TenantProductStatus.ACTIVE : TenantProductStatus.SUSPENDED;

  await prisma.tenantProduct.update({ where: { id }, data: { status: nextStatus } });

  await writeAuditLog({
    action: "TENANT_PRODUCT_TOGGLE_SUSPEND",
    actorUserId: admin.id,
    tenantId: row.tenantId,
    targetType: "tenant_product",
    targetId: id,
    metadata: { nextStatus },
  });

  revalidatePath("/control/suspensions");
}

export default async function SuspensionsPage() {
  await requireControlAdmin();

  const [suspendedTenants, tenantProducts] = await Promise.all([
    prisma.tenant.findMany({
      where: { status: TenantStatus.SUSPENDED },
      orderBy: { updatedAt: "desc" },
      include: { owner: true, plan: true },
    }),
    prisma.tenantProduct.findMany({
      where: { status: TenantProductStatus.SUSPENDED },
      orderBy: { updatedAt: "desc" },
      include: { tenant: true, product: true },
    }),
  ]);

  return (
    <div className="space-y-6">
      <SectionHeader
        eyebrow="Suspension"
        title="Control de bloqueo y reactivacion"
        description="Suspende o reactiva tenants completos y productos individuales."
      />

      <Card>
        <CardHeader>
          <CardTitle>Suspender tenant</CardTitle>
          <CardDescription>Aplica bloqueo operativo inmediato a una tienda.</CardDescription>
        </CardHeader>
        <CardContent>
          <form action={suspendTenantAction} className="grid gap-3 md:grid-cols-3">
            <Input name="slug" placeholder="cliente-demo" required />
            <Input name="reason" placeholder="Motivo (opcional)" />
            <div>
              <Button type="submit" variant="destructive">
                Suspender ahora
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Tenants suspendidos</CardTitle>
          <CardDescription>{suspendedTenants.length} bloqueados</CardDescription>
        </CardHeader>
        <CardContent className="overflow-x-auto">
          <Table>
            <THead>
              <TR>
                <TH>Tenant</TH>
                <TH>Owner</TH>
                <TH>Plan</TH>
                <TH>Estado</TH>
                <TH>Fecha</TH>
                <TH>Accion</TH>
              </TR>
            </THead>
            <TBody>
              {suspendedTenants.map((tenant) => (
                <TR key={tenant.id}>
                  <TD>
                    <p className="font-medium text-white">{tenant.name}</p>
                    <p className="text-xs text-slate-400">{tenant.slug}</p>
                  </TD>
                  <TD>{tenant.owner.email}</TD>
                  <TD>{tenant.plan?.name ?? "-"}</TD>
                  <TD>
                    <Badge>{tenant.status}</Badge>
                  </TD>
                  <TD>{formatDateTime(tenant.updatedAt)}</TD>
                  <TD>
                    <form action={reactivateTenantAction}>
                      <input type="hidden" name="tenantId" value={tenant.id} />
                      <Button size="sm" type="submit">
                        Reactivar
                      </Button>
                    </form>
                  </TD>
                </TR>
              ))}
            </TBody>
          </Table>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Productos suspendidos</CardTitle>
          <CardDescription>{tenantProducts.length} registros</CardDescription>
        </CardHeader>
        <CardContent className="overflow-x-auto">
          <Table>
            <THead>
              <TR>
                <TH>Tenant</TH>
                <TH>Producto</TH>
                <TH>Estado</TH>
                <TH>Actualizado</TH>
                <TH>Accion</TH>
              </TR>
            </THead>
            <TBody>
              {tenantProducts.map((row) => (
                <TR key={row.id}>
                  <TD>{row.tenant.slug}</TD>
                  <TD>{row.product.name}</TD>
                  <TD>
                    <Badge>{row.status}</Badge>
                  </TD>
                  <TD>{formatDateTime(row.updatedAt)}</TD>
                  <TD>
                    <form action={toggleProductSuspendAction}>
                      <input type="hidden" name="id" value={row.id} />
                      <Button type="submit" size="sm" variant="secondary">
                        Reactivar
                      </Button>
                    </form>
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
