import { TenantStatus } from "@prisma/client";
import { NextResponse } from "next/server";
import { z } from "zod";

import { requireControlAdminApi } from "@/lib/control-auth";
import { createTenantWithProvisioning } from "@/lib/tenant-onboarding";

const payloadSchema = z.object({
  slug: z.string().min(2).max(80),
  name: z.string().min(2).max(140),
  ownerId: z.string().min(2).max(120).optional(),
  ownerEmail: z.string().email().optional(),
  planId: z.string().optional().nullable(),
  productIds: z.array(z.string()).optional().default([]),
  primaryDomain: z.string().min(3),
  nodeKey: z.string().min(2),
  phpVersion: z.string().optional().nullable(),
  initialCreditBalance: z.coerce.number().min(0).optional(),
  status: z.nativeEnum(TenantStatus).optional(),
}).refine((data) => Boolean(data.ownerId?.trim() || data.ownerEmail?.trim()), {
  message: "ownerId u ownerEmail es requerido",
  path: ["ownerEmail"],
});

export async function POST(req: Request) {
  const auth = await requireControlAdminApi();
  if (!auth.ok) return auth.response;

  const json = await req.json().catch(() => null);
  const parsed = payloadSchema.safeParse(json);
  if (!parsed.success) {
    return NextResponse.json({ error: "invalid_payload", issues: parsed.error.flatten() }, { status: 400 });
  }

  const input = parsed.data;

  const result = await createTenantWithProvisioning({
    ...input,
    status: input.status ?? TenantStatus.ACTIVE,
    autoProvision: true,
    createdByUserId: auth.userId,
  });

  return NextResponse.json({ data: result }, { status: 201 });
}
