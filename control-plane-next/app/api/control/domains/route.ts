import { DomainStatus } from "@prisma/client";
import { NextResponse } from "next/server";
import { z } from "zod";

import { requireControlAdminApi } from "@/lib/control-auth";
import { prisma } from "@/lib/db";

const createSchema = z.object({
  tenantSlug: z.string().min(2).max(120),
  hostname: z.string().min(3).max(255),
  isPrimary: z.boolean().optional().default(false),
});

const patchSchema = z.object({
  id: z.string().min(2),
  status: z.nativeEnum(DomainStatus).optional(),
  sslEnabled: z.boolean().optional(),
});

function normalizeHost(raw: string) {
  return raw.toLowerCase().trim().replace(/^https?:\/\//, "").replace(/\/$/, "");
}

export async function GET() {
  const auth = await requireControlAdminApi();
  if (!auth.ok) return auth.response;

  const data = await prisma.tenantDomain.findMany({
    orderBy: [{ status: "asc" }, { createdAt: "desc" }],
    include: { tenant: true, cloudPanelSite: { include: { node: true } } },
  });

  return NextResponse.json({ data });
}

export async function POST(req: Request) {
  const auth = await requireControlAdminApi();
  if (!auth.ok) return auth.response;

  const json = await req.json().catch(() => null);
  const parsed = createSchema.safeParse(json);
  if (!parsed.success) {
    return NextResponse.json({ error: "invalid_payload", issues: parsed.error.flatten() }, { status: 400 });
  }

  const input = parsed.data;
  const tenant = await prisma.tenant.findUnique({ where: { slug: input.tenantSlug.toLowerCase() } });
  if (!tenant) {
    return NextResponse.json({ error: "tenant_not_found" }, { status: 404 });
  }

  const hostname = normalizeHost(input.hostname);

  const domain = await prisma.$transaction(async (tx) => {
    if (input.isPrimary) {
      await tx.tenantDomain.updateMany({
        where: { tenantId: tenant.id, isPrimary: true },
        data: { isPrimary: false },
      });
    }

    return tx.tenantDomain.upsert({
      where: { hostname },
      create: {
        tenantId: tenant.id,
        hostname,
        isPrimary: input.isPrimary,
      },
      update: {
        tenantId: tenant.id,
        isPrimary: input.isPrimary,
      },
    });
  });

  return NextResponse.json({ data: domain }, { status: 201 });
}

export async function PATCH(req: Request) {
  const auth = await requireControlAdminApi();
  if (!auth.ok) return auth.response;

  const json = await req.json().catch(() => null);
  const parsed = patchSchema.safeParse(json);
  if (!parsed.success) {
    return NextResponse.json({ error: "invalid_payload", issues: parsed.error.flatten() }, { status: 400 });
  }

  const input = parsed.data;

  const domain = await prisma.tenantDomain.update({
    where: { id: input.id },
    data: {
      ...(input.status ? { status: input.status } : {}),
      ...(typeof input.sslEnabled === "boolean" ? { sslEnabled: input.sslEnabled } : {}),
    },
  });

  return NextResponse.json({ data: domain });
}
