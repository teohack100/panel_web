import { NextResponse } from "next/server";
import { z } from "zod";

import { requireControlAdminApi } from "@/lib/control-auth";
import { encryptText, hashToken } from "@/lib/crypto";
import { prisma } from "@/lib/db";

const upsertSchema = z.object({
  key: z.string().min(2).max(120),
  name: z.string().min(2).max(160),
  baseUrl: z.string().url(),
  username: z.string().optional(),
  secret: z.string().optional(),
  agentToken: z.string().min(16),
  isEnabled: z.boolean().optional().default(true),
});

const patchSchema = z.object({
  id: z.string().min(2),
  isEnabled: z.boolean().optional(),
  name: z.string().min(2).max(160).optional(),
});

export async function GET() {
  const auth = await requireControlAdminApi();
  if (!auth.ok) return auth.response;

  const data = await prisma.cloudPanelNode.findMany({
    include: { _count: { select: { sites: true, tasks: true } } },
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

  const data = await prisma.cloudPanelNode.upsert({
    where: { key: input.key.toLowerCase() },
    create: {
      key: input.key.toLowerCase(),
      name: input.name,
      baseUrl: input.baseUrl,
      username: input.username,
      secretEncrypted: input.secret ? encryptText(input.secret) : null,
      agentTokenHash: hashToken(input.agentToken),
      isEnabled: input.isEnabled,
    },
    update: {
      name: input.name,
      baseUrl: input.baseUrl,
      username: input.username,
      ...(input.secret ? { secretEncrypted: encryptText(input.secret) } : {}),
      agentTokenHash: hashToken(input.agentToken),
      isEnabled: input.isEnabled,
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

  const data = await prisma.cloudPanelNode.update({
    where: { id: input.id },
    data: {
      ...(typeof input.isEnabled === "boolean" ? { isEnabled: input.isEnabled } : {}),
      ...(input.name ? { name: input.name } : {}),
    },
  });

  return NextResponse.json({ data });
}
