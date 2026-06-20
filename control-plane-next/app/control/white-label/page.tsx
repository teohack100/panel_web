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
import { formatDateTime } from "@/lib/format";

async function saveWhiteLabelAction(formData: FormData) {
  "use server";

  const admin = await requireControlAdmin();
  const tenantSlug = String(formData.get("tenantSlug") ?? "").trim().toLowerCase();
  if (!tenantSlug) return;

  const tenant = await prisma.tenant.findUnique({ where: { slug: tenantSlug } });
  if (!tenant) return;

  const data = {
    companyLegalName: String(formData.get("companyLegalName") ?? "").trim() || null,
    appName: String(formData.get("appName") ?? "").trim() || null,
    supportEmail: String(formData.get("supportEmail") ?? "").trim() || null,
    supportWhatsapp: String(formData.get("supportWhatsapp") ?? "").trim() || null,
    primaryColor: String(formData.get("primaryColor") ?? "").trim() || null,
    secondaryColor: String(formData.get("secondaryColor") ?? "").trim() || null,
    loginHeadline: String(formData.get("loginHeadline") ?? "").trim() || null,
    loginSubheadline: String(formData.get("loginSubheadline") ?? "").trim() || null,
    customCss: String(formData.get("customCss") ?? "").trim() || null,
    showPoweredBy: formData.get("showPoweredBy") === "on",
  };

  await prisma.whiteLabelProfile.upsert({
    where: { tenantId: tenant.id },
    create: {
      tenantId: tenant.id,
      ...data,
    },
    update: data,
  });

  await writeAuditLog({
    action: "WHITE_LABEL_UPSERT",
    actorUserId: admin.id,
    tenantId: tenant.id,
    targetType: "white_label",
    targetId: tenant.id,
    metadata: { tenantSlug },
  });

  revalidatePath("/control/white-label");
}

export default async function WhiteLabelPage() {
  await requireControlAdmin();

  const profiles = await prisma.whiteLabelProfile.findMany({
    orderBy: { updatedAt: "desc" },
    include: { tenant: true },
    take: 100,
  });

  return (
    <div className="space-y-6">
      <SectionHeader
        eyebrow="Marca Blanca"
        title="White-label branding"
        description="Controla apariencia, textos y contacto por tenant desde el core central."
      />

      <Card>
        <CardHeader>
          <CardTitle>Editar marca blanca</CardTitle>
          <CardDescription>Alta o actualizacion por slug del tenant.</CardDescription>
        </CardHeader>
        <CardContent>
          <form action={saveWhiteLabelAction} className="grid gap-3 md:grid-cols-2 lg:grid-cols-3">
            <Input name="tenantSlug" placeholder="cliente-demo" required />
            <Input name="companyLegalName" placeholder="Empresa SRL" />
            <Input name="appName" placeholder="Nombre app" />
            <Input name="supportEmail" placeholder="soporte@cliente.com" />
            <Input name="supportWhatsapp" placeholder="+5917xxxxxxx" />
            <Input name="primaryColor" placeholder="#0ea5e9" />
            <Input name="secondaryColor" placeholder="#84cc16" />
            <Input name="loginHeadline" placeholder="Bienvenido al panel" className="lg:col-span-2" />
            <Input name="loginSubheadline" placeholder="Gestiona todo en un solo lugar" className="lg:col-span-3" />
            <div className="lg:col-span-3">
              <Textarea name="customCss" placeholder="CSS custom opcional" />
            </div>
            <label className="inline-flex items-center gap-2 text-sm text-slate-200 lg:col-span-3">
              <input type="checkbox" name="showPoweredBy" defaultChecked className="h-4 w-4" />
              Mostrar &quot;Powered by&quot;
            </label>
            <div className="lg:col-span-3">
              <Button type="submit">Guardar branding</Button>
            </div>
          </form>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Perfiles configurados</CardTitle>
          <CardDescription>{profiles.length} tenants con marca personalizada</CardDescription>
        </CardHeader>
        <CardContent className="overflow-x-auto">
          <Table>
            <THead>
              <TR>
                <TH>Tenant</TH>
                <TH>App</TH>
                <TH>Contacto</TH>
                <TH>Colores</TH>
                <TH>Powered by</TH>
                <TH>Actualizado</TH>
              </TR>
            </THead>
            <TBody>
              {profiles.map((profile) => (
                <TR key={profile.id}>
                  <TD>{profile.tenant.slug}</TD>
                  <TD>{profile.appName ?? profile.tenant.name}</TD>
                  <TD>{profile.supportEmail ?? "-"}</TD>
                  <TD>
                    <p className="text-xs">P: {profile.primaryColor ?? "-"}</p>
                    <p className="text-xs">S: {profile.secondaryColor ?? "-"}</p>
                  </TD>
                  <TD>
                    <Badge>{profile.showPoweredBy ? "ON" : "OFF"}</Badge>
                  </TD>
                  <TD>{formatDateTime(profile.updatedAt)}</TD>
                </TR>
              ))}
            </TBody>
          </Table>
        </CardContent>
      </Card>
    </div>
  );
}
