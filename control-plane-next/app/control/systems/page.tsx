import { ProductStatus } from "@prisma/client";
import { revalidatePath } from "next/cache";

import { SectionHeader } from "@/components/control/section-header";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Table, TBody, TD, TH, THead, TR } from "@/components/ui/table";
import { Textarea } from "@/components/ui/textarea";
import { requireControlAdmin } from "@/lib/auth";
import { writeAuditLog } from "@/lib/audit";
import { prisma } from "@/lib/db";

async function createSystemAction(formData: FormData) {
  "use server";

  const admin = await requireControlAdmin();

  const key = String(formData.get("key") ?? "").trim().toLowerCase();
  const name = String(formData.get("name") ?? "").trim();
  const description = String(formData.get("description") ?? "").trim() || null;
  const defaultPlanIdRaw = String(formData.get("defaultPlanId") ?? "").trim();

  if (!key || !name) return;

  await prisma.systemProduct.upsert({
    where: { key },
    create: {
      key,
      name,
      description,
      defaultPlanId: defaultPlanIdRaw || null,
      status: ProductStatus.ACTIVE,
    },
    update: {
      name,
      description,
      defaultPlanId: defaultPlanIdRaw || null,
    },
  });

  await writeAuditLog({
    action: "SYSTEM_UPSERT",
    actorUserId: admin.id,
    targetType: "system_product",
    targetId: key,
  });

  revalidatePath("/control/systems");
}

async function toggleSystemStatusAction(formData: FormData) {
  "use server";

  const admin = await requireControlAdmin();
  const id = String(formData.get("id") ?? "").trim();
  if (!id) return;

  const current = await prisma.systemProduct.findUnique({ where: { id } });
  if (!current) return;

  const status = current.status === ProductStatus.ACTIVE ? ProductStatus.INACTIVE : ProductStatus.ACTIVE;

  await prisma.systemProduct.update({
    where: { id },
    data: { status },
  });

  await writeAuditLog({
    action: "SYSTEM_TOGGLE_STATUS",
    actorUserId: admin.id,
    targetType: "system_product",
    targetId: id,
    metadata: { status },
  });

  revalidatePath("/control/systems");
}

export default async function SystemsPage() {
  await requireControlAdmin();

  const [systems, plans] = await Promise.all([
    prisma.systemProduct.findMany({
      orderBy: [{ status: "asc" }, { createdAt: "desc" }],
      include: { defaultPlan: true, _count: { select: { tenants: true } } },
    }),
    prisma.plan.findMany({ where: { isActive: true }, orderBy: { name: "asc" } }),
  ]);

  return (
    <div className="space-y-6">
      <SectionHeader
        eyebrow="Productos"
        title="Sistemas / Productos"
        description="Catalogo maestro de sistemas que despues se asignan a cada tienda/tenant."
      />

      <Card>
        <CardHeader>
          <CardTitle>Nuevo sistema</CardTitle>
          <CardDescription>Alta rapida o actualizacion por key.</CardDescription>
        </CardHeader>
        <CardContent>
          <form action={createSystemAction} className="grid gap-3 md:grid-cols-2">
            <Input name="key" placeholder="vpn-panel" required />
            <Input name="name" placeholder="VPN Panel" required />
            <div className="md:col-span-2">
              <Textarea name="description" placeholder="Descripcion del sistema..." />
            </div>
            <select
              name="defaultPlanId"
              className="h-10 rounded-md border border-slate-700 bg-slate-900 px-3 text-sm text-slate-100"
              defaultValue=""
            >
              <option value="">Sin plan por defecto</option>
              {plans.map((plan) => (
                <option key={plan.id} value={plan.id}>
                  {plan.name}
                </option>
              ))}
            </select>
            <div>
              <Button type="submit">Guardar sistema</Button>
            </div>
          </form>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Catalogo de sistemas</CardTitle>
          <CardDescription>{systems.length} registrados</CardDescription>
        </CardHeader>
        <CardContent className="overflow-x-auto">
          <Table>
            <THead>
              <TR>
                <TH>Key</TH>
                <TH>Nombre</TH>
                <TH>Plan default</TH>
                <TH>Tenants</TH>
                <TH>Estado</TH>
                <TH>Accion</TH>
              </TR>
            </THead>
            <TBody>
              {systems.map((system) => (
                <TR key={system.id}>
                  <TD className="font-mono text-xs">{system.key}</TD>
                  <TD>
                    <p className="font-medium text-white">{system.name}</p>
                    <p className="text-xs text-slate-400">{system.description ?? "-"}</p>
                  </TD>
                  <TD>{system.defaultPlan?.name ?? "-"}</TD>
                  <TD>{system._count.tenants}</TD>
                  <TD>
                    <Badge>{system.status}</Badge>
                  </TD>
                  <TD>
                    <form action={toggleSystemStatusAction}>
                      <input type="hidden" name="id" value={system.id} />
                      <Button type="submit" size="sm" variant={system.status === ProductStatus.ACTIVE ? "secondary" : "default"}>
                        {system.status === ProductStatus.ACTIVE ? "Desactivar" : "Activar"}
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
