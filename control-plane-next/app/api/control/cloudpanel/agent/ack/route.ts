import { CloudPanelTaskStatus, Prisma } from "@prisma/client";
import { NextResponse } from "next/server";
import { z } from "zod";

import { authenticateCloudPanelNode } from "@/lib/cloudpanel-auth";
import { prisma } from "@/lib/db";
import { checkRateLimit } from "@/lib/rate-limit";

const ackSchema = z.object({
  taskId: z.string().min(2),
  status: z.enum(["SUCCESS", "FAILED", "CANCELED"]),
  result: z.record(z.string(), z.unknown()).optional(),
  errorMessage: z.string().optional(),
  retryAfterSeconds: z.coerce.number().int().min(10).max(3600).optional().default(60),
  retry: z.boolean().optional().default(true),
});

function bearerTokenFromHeader(value: string | null) {
  if (!value) return "";
  const [scheme, token] = value.split(" ");
  if (scheme?.toLowerCase() !== "bearer" || !token) return "";
  return token.trim();
}

export async function POST(req: Request) {
  const nodeKey = req.headers.get("x-node-key")?.trim().toLowerCase() ?? "";
  const bearer = bearerTokenFromHeader(req.headers.get("authorization"));

  const node = await authenticateCloudPanelNode(nodeKey, bearer);
  if (!node) {
    return NextResponse.json({ error: "unauthorized" }, { status: 401 });
  }

  const limit = await checkRateLimit(`agent:ack:${node.key}`);
  if (!limit.success) {
    return NextResponse.json({ error: "rate_limited" }, { status: 429 });
  }

  const json = await req.json().catch(() => null);
  const parsed = ackSchema.safeParse(json);
  if (!parsed.success) {
    return NextResponse.json({ error: "invalid_payload", issues: parsed.error.flatten() }, { status: 400 });
  }

  const input = parsed.data;

  const task = await prisma.cloudPanelTask.findFirst({
    where: { id: input.taskId, nodeId: node.id },
  });

  if (!task) {
    return NextResponse.json({ error: "task_not_found" }, { status: 404 });
  }

  const now = new Date();

  let nextStatus: CloudPanelTaskStatus;
  let completedAt: Date | null = null;
  let runAfter: Date | undefined;
  let lockedAt: Date | null = null;

  if (
    input.status === "FAILED" &&
    input.retry &&
    task.attempts < task.maxAttempts
  ) {
    nextStatus = CloudPanelTaskStatus.PENDING;
    runAfter = new Date(now.getTime() + input.retryAfterSeconds * 1000);
    lockedAt = null;
  } else {
    nextStatus = input.status;
    completedAt = now;
    lockedAt = task.lockedAt;
  }

  const updated = await prisma.$transaction(async (tx) => {
    await tx.cloudPanelNode.update({ where: { id: node.id }, data: { lastSeenAt: now } });

    return tx.cloudPanelTask.update({
      where: { id: task.id },
      data: {
        status: nextStatus,
        result: input.result as Prisma.InputJsonValue | undefined,
        errorMessage: input.errorMessage,
        completedAt,
        runAfter,
        lockedAt,
      },
    });
  });

  return NextResponse.json({ ok: true, data: updated });
}
