import { Prisma } from "@prisma/client";
import { NextResponse } from "next/server";
import { z } from "zod";

import { requireControlAdminApi } from "@/lib/control-auth";
import { prisma } from "@/lib/db";

const upsertSchema = z.object({
  tenantSlug: z.string().min(2).max(120),
  key: z.string().min(2).max(80),
  provider: z.string().min(2).max(80),
  displayName: z.string().min(2).max(160),
  minUsd: z.coerce.number().min(0).default(1),
  maxUsd: z.coerce.number().min(0).default(1000),
  usdToBobRate: z.coerce.number().min(0).default(6.96),
  feeFixedUsd: z.coerce.number().min(0).default(0),
  feePercent: z.coerce.number().min(0).default(0),
  creditPriceUsd: z.coerce.number().min(0).optional(),
  isEnabled: z.boolean().optional().default(true),
  config: z.record(z.string(), z.unknown()).optional(),
});

const patchSchema = z.object({
  id: z.string().min(2),
  isEnabled: z.boolean().optional(),
  usdToBobRate: z.coerce.number().min(0).optional(),
  creditPriceUsd: z.coerce.number().min(0).optional(),
});

export async function GET() {
  const auth = await requireControlAdminApi();
  if (!auth.ok) return auth.response;

  const data = await prisma.paymentMethod.findMany({
    include: { tenant: true },
    orderBy: [{ isEnabled: "desc" }, { updatedAt: "desc" }],
  });

  return NextResponse.json({ data });
}

export async function POST(req: Request) {
  const auth = await requireControlAdminApi();
  if (!auth.ok) return auth.response;

  const json = await req.json().catch(() => null);
  const parsed = upsertSchema.safeParse(json);
  if (!parsed.success) {
    return NextResponse.json({ error: "invalid_payload", issues: parsed.error.flatten() }, { status: 400 });
  }

  const input = parsed.data;
  const tenant = await prisma.tenant.findUnique({ where: { slug: input.tenantSlug.toLowerCase() } });
  if (!tenant) {
    return NextResponse.json({ error: "tenant_not_found" }, { status: 404 });
  }

  const data = await prisma.paymentMethod.upsert({
    where: { tenantId_key: { tenantId: tenant.id, key: input.key.toLowerCase() } },
    create: {
      tenantId: tenant.id,
      key: input.key.toLowerCase(),
      provider: input.provider,
      displayName: input.displayName,
      minUsd: input.minUsd,
      maxUsd: input.maxUsd,
      usdToBobRate: input.usdToBobRate,
      feeFixedUsd: input.feeFixedUsd,
      feePercent: input.feePercent,
      creditPriceUsd: input.creditPriceUsd,
      isEnabled: input.isEnabled,
      config: input.config as Prisma.InputJsonValue | undefined,
    },
    update: {
      provider: input.provider,
      displayName: input.displayName,
      minUsd: input.minUsd,
      maxUsd: input.maxUsd,
      usdToBobRate: input.usdToBobRate,
      feeFixedUsd: input.feeFixedUsd,
      feePercent: input.feePercent,
      creditPriceUsd: input.creditPriceUsd,
      isEnabled: input.isEnabled,
      config: input.config as Prisma.InputJsonValue | undefined,
    },
  });

  return NextResponse.json({ data }, { status: 201 });
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
  const data = await prisma.paymentMethod.update({
    where: { id: input.id },
    data: {
      ...(typeof input.isEnabled === "boolean" ? { isEnabled: input.isEnabled } : {}),
      ...(typeof input.usdToBobRate === "number" ? { usdToBobRate: input.usdToBobRate } : {}),
      ...(typeof input.creditPriceUsd === "number" ? { creditPriceUsd: input.creditPriceUsd } : {}),
    },
  });

  return NextResponse.json({ data });
}
