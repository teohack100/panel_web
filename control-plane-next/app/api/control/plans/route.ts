import { NextResponse } from "next/server";
import { z } from "zod";

import { requireControlAdminApi } from "@/lib/control-auth";
import { prisma } from "@/lib/db";

const upsertSchema = z.object({
  code: z.string().min(2).max(80),
  name: z.string().min(2).max(140),
  description: z.string().max(1200).optional(),
  monthlyPriceUsd: z.coerce.number().min(0).default(0),
  creditPriceUsd: z.coerce.number().min(0).default(1),
  includedCredits: z.coerce.number().int().min(0).default(0),
  maxUsers: z.coerce.number().int().min(1).default(100),
  maxDomains: z.coerce.number().int().min(1).default(5),
  maxCloudPanelNodes: z.coerce.number().int().min(1).default(1),
  isActive: z.boolean().optional().default(true),
});

const patchSchema = z.object({
  id: z.string().min(2),
  isActive: z.boolean().optional(),
  monthlyPriceUsd: z.coerce.number().min(0).optional(),
  creditPriceUsd: z.coerce.number().min(0).optional(),
});

export async function GET() {
  const auth = await requireControlAdminApi();
  if (!auth.ok) return auth.response;

  const data = await prisma.plan.findMany({
    include: { _count: { select: { tenants: true, systemProducts: true } } },
    orderBy: [{ isActive: "desc" }, { monthlyPriceUsd: "asc" }],
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

  const plan = await prisma.plan.upsert({
    where: { code: input.code.toLowerCase() },
    create: {
      code: input.code.toLowerCase(),
      name: input.name,
      description: input.description,
      monthlyPriceUsd: input.monthlyPriceUsd,
      creditPriceUsd: input.creditPriceUsd,
      includedCredits: input.includedCredits,
      maxUsers: input.maxUsers,
      maxDomains: input.maxDomains,
      maxCloudPanelNodes: input.maxCloudPanelNodes,
      isActive: input.isActive,
    },
    update: {
      name: input.name,
      description: input.description,
      monthlyPriceUsd: input.monthlyPriceUsd,
      creditPriceUsd: input.creditPriceUsd,
      includedCredits: input.includedCredits,
      maxUsers: input.maxUsers,
      maxDomains: input.maxDomains,
      maxCloudPanelNodes: input.maxCloudPanelNodes,
      isActive: input.isActive,
    },
  });

  return NextResponse.json({ data: plan }, { status: 201 });
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

  const plan = await prisma.plan.update({
    where: { id: input.id },
    data: {
      ...(typeof input.isActive === "boolean" ? { isActive: input.isActive } : {}),
      ...(typeof input.monthlyPriceUsd === "number" ? { monthlyPriceUsd: input.monthlyPriceUsd } : {}),
      ...(typeof input.creditPriceUsd === "number" ? { creditPriceUsd: input.creditPriceUsd } : {}),
    },
  });

  return NextResponse.json({ data: plan });
}
