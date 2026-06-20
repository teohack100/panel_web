import { CloudPanelTaskStatus, CloudPanelTaskType, Prisma } from "@prisma/client";
import { NextResponse } from "next/server";
import { z } from "zod";

import { requireControlAdminApi } from "@/lib/control-auth";
import { prisma } from "@/lib/db";

const createSchema = z.object({
  nodeId: z.string().optional(),
  nodeKey: z.string().optional(),
  tenantId: z.string().optional().nullable(),
  tenantSlug: z.string().optional().nullable(),
  taskType: z.nativeEnum(CloudPanelTaskType),
  payload: z.record(z.string(), z.unknown()).default({}),
  runAfter: z.string().optional(),
  idempotencyKey: z.string().optional().nullable(),
  maxAttempts: z.coerce.number().int().min(1).max(30).optional().default(5),
});

const patchSchema = z.object({
  id: z.string().min(2),
  status: z.nativeEnum(CloudPanelTaskStatus).optional(),
  errorMessage: z.string().optional().nullable(),
  result: z.record(z.string(), z.unknown()).optional(),
});

export async function GET(req: Request) {
  const auth = await requireControlAdminApi();
  if (!auth.ok) return auth.response;

  const { searchParams } = new URL(req.url);
  const nodeKey = searchParams.get("nodeKey");
  const status = searchParams.get("status");

  const where: {
    node?: { key: string };
    status?: CloudPanelTaskStatus;
  } = {};

  if (nodeKey) where.node = { key: nodeKey };
  if (status && Object.values(CloudPanelTaskStatus).includes(status as CloudPanelTaskStatus)) {
    where.status = status as CloudPanelTaskStatus;
  }

  const data = await prisma.cloudPanelTask.findMany({
    where,
    orderBy: { createdAt: "desc" },
    take: 300,
    include: { node: true, tenant: true, createdBy: true },
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

  const node = input.nodeId
    ? await prisma.cloudPanelNode.findUnique({ where: { id: input.nodeId } })
    : await prisma.cloudPanelNode.findUnique({ where: { key: (input.nodeKey || "").toLowerCase() } });

  if (!node) {
    return NextResponse.json({ error: "node_not_found" }, { status: 404 });
  }

  let tenantId = input.tenantId ?? null;
  if (!tenantId && input.tenantSlug) {
    const tenant = await prisma.tenant.findUnique({ where: { slug: input.tenantSlug.toLowerCase() } });
    tenantId = tenant?.id ?? null;
  }

  const data = await prisma.cloudPanelTask.create({
    data: {
      nodeId: node.id,
      tenantId,
      taskType: input.taskType,
      payload: input.payload as Prisma.InputJsonValue,
      runAfter: input.runAfter ? new Date(input.runAfter) : new Date(),
      createdByUserId: auth.userId,
      idempotencyKey: input.idempotencyKey,
      maxAttempts: input.maxAttempts,
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

  const data = await prisma.cloudPanelTask.update({
    where: { id: input.id },
    data: {
      ...(input.status ? { status: input.status } : {}),
      ...(input.errorMessage !== undefined ? { errorMessage: input.errorMessage } : {}),
      ...(input.result ? { result: input.result as Prisma.InputJsonValue } : {}),
      ...(input.status === CloudPanelTaskStatus.SUCCESS || input.status === CloudPanelTaskStatus.FAILED
        ? { completedAt: new Date() }
        : {}),
    },
  });

  return NextResponse.json({ data });
}
