import { CloudPanelTaskStatus } from "@prisma/client";
import { NextResponse } from "next/server";

import { authenticateCloudPanelNode } from "@/lib/cloudpanel-auth";
import { prisma } from "@/lib/db";
import { checkRateLimit } from "@/lib/rate-limit";

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

  const limit = await checkRateLimit(`agent:pull:${node.key}`);
  if (!limit.success) {
    return NextResponse.json({ error: "rate_limited" }, { status: 429 });
  }

  const now = new Date();

  const task = await prisma.$transaction(async (tx) => {
    await tx.cloudPanelNode.update({
      where: { id: node.id },
      data: { lastSeenAt: now },
    });

    const candidate = await tx.cloudPanelTask.findFirst({
      where: {
        nodeId: node.id,
        status: CloudPanelTaskStatus.PENDING,
        runAfter: { lte: now },
      },
      orderBy: [{ runAfter: "asc" }, { createdAt: "asc" }],
    });

    if (!candidate) {
      return null;
    }

    const lock = await tx.cloudPanelTask.updateMany({
      where: {
        id: candidate.id,
        status: CloudPanelTaskStatus.PENDING,
      },
      data: {
        status: CloudPanelTaskStatus.RUNNING,
        lockedAt: now,
        attempts: candidate.attempts + 1,
      },
    });

    if (lock.count === 0) {
      return null;
    }

    return tx.cloudPanelTask.findUnique({ where: { id: candidate.id } });
  });

  return NextResponse.json({
    ok: true,
    node: { id: node.id, key: node.key, name: node.name },
    task,
  });
}
