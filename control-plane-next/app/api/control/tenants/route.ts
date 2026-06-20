import { TenantStatus } from "@prisma/client";
import { NextResponse } from "next/server";
import { z } from "zod";

import { requireControlAdminApi } from "@/lib/control-auth";
import { prisma } from "@/lib/db";
import { createTenantWithProvisioning, normalizeSlug } from "@/lib/tenant-onboarding";

const payloadSchema = z.object({
  slug: z.string().min(2).max(80),
  name: z.string().min(2).max(140),
  ownerId: z.string().min(2).max(120).optional(),
  ownerEmail: z.string().email().optional(),
  planId: z.string().optional().nullable(),
  productIds: z.array(z.string()).optional(),
  primaryDomain: z.string().optional().nullable(),
  nodeKey: z.string().optional().nullable(),
  phpVersion: z.string().optional().nullable(),
  initialCreditBalance: z.coerce.number().min(0).optional(),
  autoProvision: z.boolean().optional(),
  status: z.nativeEnum(TenantStatus).optional(),
}).refine((data) => Boolean(data.ownerId?.trim() || data.ownerEmail?.trim()), {
  message: "ownerId u ownerEmail es requerido",
  path: ["ownerEmail"],
});

const patchSchema = z.object({
  id: z.string().min(2),
  name: z.string().min(2).max(140).optional(),
  brandName: z.string().max(140).nullable().optional(),
  status: z.nativeEnum(TenantStatus).optional(),
  planId: z.string().nullable().optional(),
  creditBalance: z.coerce.number().min(0).optional(),
  timezone: z.string().min(2).max(80).optional(),
  ownerId: z.string().min(2).max(120).optional(),
});

const deleteSchema = z.object({
  id: z.string().min(2),
  force: z.boolean().optional().default(false),
});

export async function GET() {
  const auth = await requireControlAdminApi();
  if (!auth.ok) return auth.response;

  const data = await prisma.tenant.findMany({
    orderBy: { createdAt: "desc" },
    include: {
      owner: true,
      plan: true,
      _count: { select: { domains: true, products: true } },
    },
  });

  return NextResponse.json({ data });
}

export async function POST(req: Request) {
  const auth = await requireControlAdminApi();
  if (!auth.ok) return auth.response;

  const json = await req.json().catch(() => null);
  const parsed = payloadSchema.safeParse(json);
  if (!parsed.success) {
    return NextResponse.json({ error: "invalid_payload", issues: parsed.error.flatten() }, { status: 400 });
  }

  const input = parsed.data;
  const slug = normalizeSlug(input.slug);
  if (!slug) {
    return NextResponse.json({ error: "invalid_slug" }, { status: 400 });
  }

  const result = await createTenantWithProvisioning({
    slug,
    name: input.name,
    ownerId: input.ownerId || null,
    ownerEmail: input.ownerEmail,
    planId: input.planId || null,
    productIds: input.productIds ?? [],
    primaryDomain: input.primaryDomain || null,
    nodeKey: input.nodeKey || null,
    phpVersion: input.phpVersion || null,
    initialCreditBalance: input.initialCreditBalance ?? 0,
    autoProvision: input.autoProvision ?? true,
    status: input.status ?? TenantStatus.ACTIVE,
    createdByUserId: auth.userId,
  });

  const tenant = await prisma.tenant.findUnique({
    where: { id: result.tenantId },
    include: {
      owner: true,
      plan: true,
      _count: { select: { domains: true, products: true } },
    },
  });

  return NextResponse.json({ data: tenant, provisioning: result }, { status: 201 });
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

  if (input.ownerId) {
    const owner = await prisma.user.findUnique({ where: { id: input.ownerId } });
    if (!owner) {
      return NextResponse.json({ error: "owner_not_found" }, { status: 404 });
    }
  }

  const updated = await prisma.$transaction(async (tx) => {
    const tenant = await tx.tenant.update({
      where: { id: input.id },
      data: {
        ...(input.name ? { name: input.name } : {}),
        ...(input.brandName !== undefined ? { brandName: input.brandName || null } : {}),
        ...(input.status ? { status: input.status } : {}),
        ...(input.planId !== undefined ? { planId: input.planId || null } : {}),
        ...(typeof input.creditBalance === "number" ? { creditBalance: input.creditBalance } : {}),
        ...(input.timezone ? { timezone: input.timezone } : {}),
        ...(input.ownerId ? { ownerId: input.ownerId } : {}),
      },
      include: {
        owner: true,
        plan: true,
        _count: { select: { domains: true, products: true } },
      },
    });

    if (input.ownerId) {
      await tx.tenantMembership.upsert({
        where: {
          tenantId_userId: {
            tenantId: input.id,
            userId: input.ownerId,
          },
        },
        create: {
          tenantId: input.id,
          userId: input.ownerId,
          role: "OWNER",
        },
        update: {
          role: "OWNER",
        },
      });
    }

    return tenant;
  });

  return NextResponse.json({ data: updated });
}

export async function DELETE(req: Request) {
  const auth = await requireControlAdminApi();
  if (!auth.ok) return auth.response;

  const json = await req.json().catch(() => null);
  const parsed = deleteSchema.safeParse(json);
  if (!parsed.success) {
    return NextResponse.json({ error: "invalid_payload", issues: parsed.error.flatten() }, { status: 400 });
  }

  const { id, force } = parsed.data;
  if (force) {
    await prisma.tenant.delete({ where: { id } });
    return NextResponse.json({ ok: true, mode: "hard_delete" });
  }

  const tenant = await prisma.tenant.update({
    where: { id },
    data: { status: TenantStatus.CANCELED },
  });

  return NextResponse.json({ data: tenant, mode: "soft_cancel" });
}
