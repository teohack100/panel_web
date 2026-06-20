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
import { formatUsd } from "@/lib/format";

function toNumber(raw: FormDataEntryValue | null, fallback = 0) {
  const value = Number(String(raw ?? ""));
  return Number.isFinite(value) ? value : fallback;
}

async function savePlanAction(formData: FormData) {
  "use server";

  const admin = await requireControlAdmin();
  const code = String(formData.get("code") ?? "").trim().toLowerCase();
  const name = String(formData.get("name") ?? "").trim();
  const description = String(formData.get("description") ?? "").trim() || null;

  if (!code || !name) return;

  await prisma.plan.upsert({
    where: { code },
    create: {
      code,
      name,
      description,
      monthlyPriceUsd: toNumber(formData.get("monthlyPriceUsd"), 0),
      creditPriceUsd: toNumber(formData.get("creditPriceUsd"), 1),
      includedCredits: Math.max(0, Math.floor(toNumber(formData.get("includedCredits"), 0))),
      maxUsers: Math.max(1, Math.floor(toNumber(formData.get("maxUsers"), 100))),
      maxDomains: Math.max(1, Math.floor(toNumber(formData.get("maxDomains"), 5))),
      maxCloudPanelNodes: Math.max(1, Math.floor(toNumber(formData.get("maxCloudPanelNodes"), 1))),
      isActive: true,
    },
    update: {
      name,
      description,
      monthlyPriceUsd: toNumber(formData.get("monthlyPriceUsd"), 0),
      creditPriceUsd: toNumber(formData.get("creditPriceUsd"), 1),
      includedCredits: Math.max(0, Math.floor(toNumber(formData.get("includedCredits"), 0))),
      maxUsers: Math.max(1, Math.floor(toNumber(formData.get("maxUsers"), 100))),
      maxDomains: Math.max(1, Math.floor(toNumber(formData.get("maxDomains"), 5))),
      maxCloudPanelNodes: Math.max(1, Math.floor(toNumber(formData.get("maxCloudPanelNodes"), 1))),
    },
  });

  await writeAuditLog({
    action: "PLAN_UPSERT",
    actorUserId: admin.id,
    targetType: "plan",
    targetId: code,
  });

  revalidatePath("/control/plans");
}

async function togglePlanAction(formData: FormData) {
  "use server";

  const admin = await requireControlAdmin();
  const id = String(formData.get("id") ?? "").trim();
  if (!id) return;

  const plan = await prisma.plan.findUnique({ where: { id } });
  if (!plan) return;

  await prisma.plan.update({
    where: { id },
    data: { isActive: !plan.isActive },
  });

  await writeAuditLog({
    action: "PLAN_TOGGLE_ACTIVE",
    actorUserId: admin.id,
    targetType: "plan",
    targetId: id,
    metadata: { nextState: !plan.isActive },
  });

  revalidatePath("/control/plans");
}

export default async function PlansPage() {
  await requireControlAdmin();

  const plans = await prisma.plan.findMany({
    orderBy: [{ isActive: "desc" }, { monthlyPriceUsd: "asc" }],
    include: { _count: { select: { tenants: true, systemProducts: true } } },
  });

  return (
    <div className="space-y-6">
      <SectionHeader
        eyebrow="Planes"
        title="Planes y precios"
        description="Controla limites, precio por credito y capacidad por tenant."
      />

      <Card>
        <CardHeader>
          <CardTitle>Nuevo plan</CardTitle>
          <CardDescription>Se crea o actualiza por codigo unico.</CardDescription>
        </CardHeader>
        <CardContent>
          <form action={savePlanAction} className="grid gap-3 md:grid-cols-2 lg:grid-cols-4">
            <Input name="code" placeholder="starter" required />
            <Input name="name" placeholder="Starter" required />
            <Input name="monthlyPriceUsd" type="number" step="0.01" placeholder="29" />
            <Input name="creditPriceUsd" type="number" step="0.0001" placeholder="1.00" />
            <Input name="includedCredits" type="number" step="1" placeholder="100" />
            <Input name="maxUsers" type="number" step="1" placeholder="100" />
            <Input name="maxDomains" type="number" step="1" placeholder="5" />
            <Input name="maxCloudPanelNodes" type="number" step="1" placeholder="1" />
            <div className="lg:col-span-4">
              <Textarea name="description" placeholder="Descripcion del plan" />
            </div>
            <div className="lg:col-span-4">
              <Button type="submit">Guardar plan</Button>
            </div>
          </form>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Catalogo</CardTitle>
        </CardHeader>
        <CardContent className="overflow-x-auto">
          <Table>
            <THead>
              <TR>
                <TH>Plan</TH>
                <TH>Mensual</TH>
                <TH>Credito</TH>
                <TH>Limites</TH>
                <TH>Uso</TH>
                <TH>Estado</TH>
                <TH>Accion</TH>
              </TR>
            </THead>
            <TBody>
              {plans.map((plan) => (
                <TR key={plan.id}>
                  <TD>
                    <p className="font-medium text-white">{plan.name}</p>
                    <p className="text-xs text-slate-400">{plan.code}</p>
                  </TD>
                  <TD>{formatUsd(plan.monthlyPriceUsd)}</TD>
                  <TD>{formatUsd(plan.creditPriceUsd)}</TD>
                  <TD>
                    <p className="text-xs">Users: {plan.maxUsers}</p>
                    <p className="text-xs">Dominios: {plan.maxDomains}</p>
                    <p className="text-xs">Nodos: {plan.maxCloudPanelNodes}</p>
                  </TD>
                  <TD>
                    <p className="text-xs">Tenants: {plan._count.tenants}</p>
                    <p className="text-xs">Systems default: {plan._count.systemProducts}</p>
                  </TD>
                  <TD>
                    <Badge>{plan.isActive ? "ACTIVE" : "INACTIVE"}</Badge>
                  </TD>
                  <TD>
                    <form action={togglePlanAction}>
                      <input type="hidden" name="id" value={plan.id} />
                      <Button type="submit" size="sm" variant={plan.isActive ? "secondary" : "default"}>
                        {plan.isActive ? "Desactivar" : "Activar"}
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
