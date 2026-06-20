import { DomainStatus } from "@prisma/client";
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

function normalizeHost(raw: string) {
  return raw.toLowerCase().trim().replace(/^https?:\/\//, "").replace(/\/$/, "");
}

async function addDomainAction(formData: FormData) {
  "use server";

  const admin = await requireControlAdmin();
  const tenantSlug = String(formData.get("tenantSlug") ?? "").trim().toLowerCase();
  const hostname = normalizeHost(String(formData.get("hostname") ?? ""));
  const isPrimary = formData.get("isPrimary") === "on";

  if (!tenantSlug || !hostname) return;

  const tenant = await prisma.tenant.findUnique({ where: { slug: tenantSlug } });
  if (!tenant) return;

  await prisma.$transaction(async (tx) => {
    if (isPrimary) {
      await tx.tenantDomain.updateMany({
        where: { tenantId: tenant.id, isPrimary: true },
        data: { isPrimary: false },
      });
    }

    await tx.tenantDomain.upsert({
      where: { hostname },
      create: {
        tenantId: tenant.id,
        hostname,
        isPrimary,
      },
      update: {
        tenantId: tenant.id,
        isPrimary,
      },
    });
  });

  await writeAuditLog({
    action: "DOMAIN_UPSERT",
    actorUserId: admin.id,
    tenantId: tenant.id,
    targetType: "domain",
    targetId: hostname,
    metadata: { isPrimary },
  });

  revalidatePath("/control/domains");
}

async function updateDomainAction(formData: FormData) {
  "use server";

  const admin = await requireControlAdmin();
  const id = String(formData.get("id") ?? "").trim();
  const status = String(formData.get("status") ?? "").trim() as DomainStatus;
  const sslEnabled = formData.get("sslEnabled") === "on";

  if (!id || !Object.values(DomainStatus).includes(status)) return;

  const updated = await prisma.tenantDomain.update({
    where: { id },
    data: {
      status,
      sslEnabled,
    },
  });

  await writeAuditLog({
    action: "DOMAIN_UPDATE",
    actorUserId: admin.id,
    tenantId: updated.tenantId,
    targetType: "domain",
    targetId: updated.hostname,
    metadata: { status, sslEnabled },
  });

  revalidatePath("/control/domains");
}

export default async function DomainsPage() {
  await requireControlAdmin();

  const domains = await prisma.tenantDomain.findMany({
    orderBy: [{ status: "asc" }, { createdAt: "desc" }],
    include: { tenant: true, cloudPanelSite: { include: { node: true } } },
    take: 200,
  });

  return (
    <div className="space-y-6">
      <SectionHeader
        eyebrow="Dominios"
        title="Dominios, DNS y SSL"
        description="Gestion central de hostnames por tenant y estado operativo."
      />

      <Card>
        <CardHeader>
          <CardTitle>Registrar dominio</CardTitle>
          <CardDescription>Asocia o mueve dominios entre tenants.</CardDescription>
        </CardHeader>
        <CardContent>
          <form action={addDomainAction} className="grid gap-3 md:grid-cols-3">
            <Input name="tenantSlug" placeholder="cliente-demo" required />
            <Input name="hostname" placeholder="panel.cliente.com" required />
            <label className="inline-flex items-center gap-2 text-sm text-slate-200">
              <input name="isPrimary" type="checkbox" className="h-4 w-4" />
              Dominio primario
            </label>
            <div className="md:col-span-3">
              <Button type="submit">Guardar dominio</Button>
            </div>
          </form>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Inventario de dominios</CardTitle>
          <CardDescription>{domains.length} registros</CardDescription>
        </CardHeader>
        <CardContent className="overflow-x-auto">
          <Table>
            <THead>
              <TR>
                <TH>Hostname</TH>
                <TH>Tenant</TH>
                <TH>Estado</TH>
                <TH>SSL</TH>
                <TH>CloudPanel</TH>
                <TH>Actualizado</TH>
                <TH>Accion</TH>
              </TR>
            </THead>
            <TBody>
              {domains.map((domain) => (
                <TR key={domain.id}>
                  <TD>
                    <p className="font-medium text-white">{domain.hostname}</p>
                    <p className="text-xs text-slate-400">{domain.isPrimary ? "Primario" : "Secundario"}</p>
                  </TD>
                  <TD>{domain.tenant.slug}</TD>
                  <TD>
                    <Badge>{domain.status}</Badge>
                  </TD>
                  <TD>{domain.sslEnabled ? "ON" : "OFF"}</TD>
                  <TD>{domain.cloudPanelSite?.node?.name ?? "-"}</TD>
                  <TD>{formatDateTime(domain.updatedAt)}</TD>
                  <TD>
                    <form action={updateDomainAction} className="flex flex-wrap items-center gap-2">
                      <input type="hidden" name="id" value={domain.id} />
                      <select
                        name="status"
                        defaultValue={domain.status}
                        className="h-8 rounded-md border border-slate-700 bg-slate-900 px-2 text-xs text-slate-100"
                      >
                        {Object.values(DomainStatus).map((status) => (
                          <option key={status} value={status}>
                            {status}
                          </option>
                        ))}
                      </select>
                      <label className="inline-flex items-center gap-1 text-xs text-slate-300">
                        <input type="checkbox" name="sslEnabled" defaultChecked={domain.sslEnabled} className="h-3 w-3" />
                        SSL
                      </label>
                      <Button size="sm" type="submit" variant="outline">
                        Guardar
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
