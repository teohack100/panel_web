import { RechargeStatus } from "@prisma/client";
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
import { formatDateTime, formatUsd } from "@/lib/format";

function toNumber(raw: FormDataEntryValue | null, fallback = 0) {
  const value = Number(String(raw ?? ""));
  return Number.isFinite(value) ? value : fallback;
}

async function saveFinanceGlobalsAction(formData: FormData) {
  "use server";

  const admin = await requireControlAdmin();

  const creditUsd = toNumber(formData.get("globalCreditUsd"), 1);
  const usdToBob = toNumber(formData.get("globalUsdToBob"), 6.96);

  await prisma.controlSetting.upsert({
    where: { key: "finance.defaults" },
    create: {
      key: "finance.defaults",
      value: {
        globalCreditUsd: creditUsd,
        globalUsdToBob: usdToBob,
      },
      description: "Valores globales por defecto para creditos y tipo de cambio.",
    },
    update: {
      value: {
        globalCreditUsd: creditUsd,
        globalUsdToBob: usdToBob,
      },
    },
  });

  await writeAuditLog({
    action: "FINANCE_GLOBALS_SAVE",
    actorUserId: admin.id,
    targetType: "control_setting",
    targetId: "finance.defaults",
    metadata: { creditUsd, usdToBob },
  });

  revalidatePath("/control/payments");
}

async function savePaymentMethodAction(formData: FormData) {
  "use server";

  const admin = await requireControlAdmin();
  const tenantSlug = String(formData.get("tenantSlug") ?? "").trim().toLowerCase();
  const key = String(formData.get("key") ?? "").trim().toLowerCase();
  const provider = String(formData.get("provider") ?? "").trim() || "custom";
  const displayName = String(formData.get("displayName") ?? "").trim();

  if (!tenantSlug || !key || !displayName) return;

  const tenant = await prisma.tenant.findUnique({ where: { slug: tenantSlug } });
  if (!tenant) return;

  await prisma.paymentMethod.upsert({
    where: { tenantId_key: { tenantId: tenant.id, key } },
    create: {
      tenantId: tenant.id,
      key,
      provider,
      displayName,
      minUsd: toNumber(formData.get("minUsd"), 1),
      maxUsd: toNumber(formData.get("maxUsd"), 1000),
      usdToBobRate: toNumber(formData.get("usdToBobRate"), 6.96),
      feeFixedUsd: toNumber(formData.get("feeFixedUsd"), 0),
      feePercent: toNumber(formData.get("feePercent"), 0),
      creditPriceUsd: toNumber(formData.get("creditPriceUsd"), 1),
      isEnabled: formData.get("isEnabled") === "on",
      config: {
        apiKey: String(formData.get("apiKey") ?? ""),
        secretKey: String(formData.get("secretKey") ?? ""),
        instructions: String(formData.get("instructions") ?? ""),
      },
    },
    update: {
      provider,
      displayName,
      minUsd: toNumber(formData.get("minUsd"), 1),
      maxUsd: toNumber(formData.get("maxUsd"), 1000),
      usdToBobRate: toNumber(formData.get("usdToBobRate"), 6.96),
      feeFixedUsd: toNumber(formData.get("feeFixedUsd"), 0),
      feePercent: toNumber(formData.get("feePercent"), 0),
      creditPriceUsd: toNumber(formData.get("creditPriceUsd"), 1),
      isEnabled: formData.get("isEnabled") === "on",
      config: {
        apiKey: String(formData.get("apiKey") ?? ""),
        secretKey: String(formData.get("secretKey") ?? ""),
        instructions: String(formData.get("instructions") ?? ""),
      },
    },
  });

  await writeAuditLog({
    action: "PAYMENT_METHOD_UPSERT",
    actorUserId: admin.id,
    tenantId: tenant.id,
    targetType: "payment_method",
    targetId: `${tenant.id}:${key}`,
  });

  revalidatePath("/control/payments");
}

export default async function PaymentsPage() {
  await requireControlAdmin();

  const [methods, recharges, paidAgg, defaults] = await Promise.all([
    prisma.paymentMethod.findMany({
      orderBy: [{ isEnabled: "desc" }, { updatedAt: "desc" }],
      include: { tenant: true },
      take: 50,
    }),
    prisma.recharge.findMany({
      orderBy: { createdAt: "desc" },
      take: 30,
      include: { tenant: true, user: true, paymentMethod: true },
    }),
    prisma.recharge.aggregate({ where: { status: RechargeStatus.PAID }, _sum: { amountUsd: true, creditsGranted: true } }),
    prisma.controlSetting.findUnique({ where: { key: "finance.defaults" } }),
  ]);

  const financeDefaults = (defaults?.value ?? {}) as { globalCreditUsd?: number; globalUsdToBob?: number };

  return (
    <div className="space-y-6">
      <SectionHeader
        eyebrow="Finanzas"
        title="Pagos y recargas"
        description="Define precio por credito, tipo de cambio, metodos de pago por tenant y monitoreo de recargas."
      />

      <Card>
        <CardHeader>
          <CardTitle>Configuracion global</CardTitle>
          <CardDescription>Valores por defecto usados al crear nuevos metodos.</CardDescription>
        </CardHeader>
        <CardContent>
          <form action={saveFinanceGlobalsAction} className="grid gap-3 md:grid-cols-3">
            <Input name="globalCreditUsd" type="number" step="0.0001" defaultValue={financeDefaults.globalCreditUsd ?? 1} />
            <Input name="globalUsdToBob" type="number" step="0.0001" defaultValue={financeDefaults.globalUsdToBob ?? 6.96} />
            <div>
              <Button type="submit">Guardar defaults</Button>
            </div>
          </form>
          <div className="mt-3 text-sm text-slate-300">
            Cobrado total: <span className="font-semibold text-white">{formatUsd(paidAgg._sum.amountUsd)}</span> | Creditos entregados: {String(paidAgg._sum.creditsGranted ?? 0)}
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Metodo de pago por tenant</CardTitle>
          <CardDescription>Configura API key/secret, limites, fee y tipo de cambio.</CardDescription>
        </CardHeader>
        <CardContent>
          <form action={savePaymentMethodAction} className="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
            <Input name="tenantSlug" placeholder="cliente-demo" required />
            <Input name="key" placeholder="veripagos" required />
            <Input name="provider" placeholder="veripagos" />
            <Input name="displayName" placeholder="QR Bolivia Automatico" required />
            <Input name="minUsd" type="number" step="0.01" placeholder="1" />
            <Input name="maxUsd" type="number" step="0.01" placeholder="1000" />
            <Input name="usdToBobRate" type="number" step="0.0001" placeholder="6.96" />
            <Input name="creditPriceUsd" type="number" step="0.0001" placeholder="1" />
            <Input name="feeFixedUsd" type="number" step="0.0001" placeholder="0" />
            <Input name="feePercent" type="number" step="0.001" placeholder="0" />
            <Input name="apiKey" placeholder="API_KEY" />
            <Input name="secretKey" placeholder="SECRET_KEY" />
            <div className="xl:col-span-4">
              <Textarea name="instructions" placeholder="Instrucciones del metodo para el cliente final" />
            </div>
            <label className="inline-flex items-center gap-2 text-sm text-slate-200 xl:col-span-4">
              <input type="checkbox" name="isEnabled" defaultChecked className="h-4 w-4" />
              Activo
            </label>
            <div className="xl:col-span-4">
              <Button type="submit">Guardar metodo</Button>
            </div>
          </form>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Metodos activos</CardTitle>
          <CardDescription>{methods.length} configurados</CardDescription>
        </CardHeader>
        <CardContent className="overflow-x-auto">
          <Table>
            <THead>
              <TR>
                <TH>Tenant</TH>
                <TH>Metodo</TH>
                <TH>Limites</TH>
                <TH>Tasa</TH>
                <TH>Fee</TH>
                <TH>Estado</TH>
              </TR>
            </THead>
            <TBody>
              {methods.map((method) => (
                <TR key={method.id}>
                  <TD>{method.tenant.slug}</TD>
                  <TD>
                    <p className="font-medium text-white">{method.displayName}</p>
                    <p className="text-xs text-slate-400">{method.provider} / {method.key}</p>
                  </TD>
                  <TD>
                    {formatUsd(method.minUsd)} - {formatUsd(method.maxUsd)}
                  </TD>
                  <TD>1 USD = {Number(method.usdToBobRate).toFixed(2)} BOB</TD>
                  <TD>
                    {formatUsd(method.feeFixedUsd)} + {Number(method.feePercent).toFixed(2)}%
                  </TD>
                  <TD>
                    <Badge>{method.isEnabled ? "ENABLED" : "DISABLED"}</Badge>
                  </TD>
                </TR>
              ))}
            </TBody>
          </Table>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Historial de recargas</CardTitle>
        </CardHeader>
        <CardContent className="overflow-x-auto">
          <Table>
            <THead>
              <TR>
                <TH>Fecha</TH>
                <TH>Tenant</TH>
                <TH>Usuario</TH>
                <TH>Metodo</TH>
                <TH>Monto</TH>
                <TH>Creditos</TH>
                <TH>Estado</TH>
              </TR>
            </THead>
            <TBody>
              {recharges.map((recharge) => (
                <TR key={recharge.id}>
                  <TD>{formatDateTime(recharge.createdAt)}</TD>
                  <TD>{recharge.tenant.slug}</TD>
                  <TD>{recharge.user.email}</TD>
                  <TD>{recharge.paymentMethod.displayName}</TD>
                  <TD>{formatUsd(recharge.amountUsd)}</TD>
                  <TD>{String(recharge.creditsGranted)}</TD>
                  <TD>
                    <Badge>{recharge.status}</Badge>
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
