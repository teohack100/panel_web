import { ProductStatus } from "@prisma/client";
import { NextResponse } from "next/server";
import { z } from "zod";

import { requireControlAdminApi } from "@/lib/control-auth";
import { prisma } from "@/lib/db";

const payloadSchema = z.object({
  key: z.string().min(2).max(80),
  name: z.string().min(2).max(140),
  description: z.string().max(1200).optional(),
  status: z.nativeEnum(ProductStatus).optional(),
  defaultPlanId: z.string().optional().nullable(),
});

export async function GET() {
  const auth = await requireControlAdminApi();
  if (!auth.ok) return auth.response;

  const data = await prisma.systemProduct.findMany({
    include: { defaultPlan: true, _count: { select: { tenants: true } } },
    orderBy: [{ status: "asc" }, { createdAt: "desc" }],
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

  const item = await prisma.systemProduct.upsert({
    where: { key: input.key.toLowerCase() },
    create: {
      key: input.key.toLowerCase(),
      name: input.name,
      description: input.description,
      status: input.status ?? ProductStatus.ACTIVE,
      defaultPlanId: input.defaultPlanId || null,
    },
    update: {
      name: input.name,
      description: input.description,
      status: input.status,
      defaultPlanId: input.defaultPlanId || null,
    },
  });

  return NextResponse.json({ data: item }, { status: 201 });
}
