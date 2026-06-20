import { Prisma } from "@prisma/client";
import { prisma } from "@/lib/db";

type AuditPayload = {
  action: string;
  actorUserId?: string | null;
  tenantId?: string | null;
  targetType?: string | null;
  targetId?: string | null;
  metadata?: unknown;
  ipAddress?: string | null;
  userAgent?: string | null;
};

export async function writeAuditLog(payload: AuditPayload) {
  try {
    await prisma.auditLog.create({
      data: {
        action: payload.action,
        actorUserId: payload.actorUserId ?? null,
        tenantId: payload.tenantId ?? null,
        targetType: payload.targetType ?? null,
        targetId: payload.targetId ?? null,
        metadata: payload.metadata as Prisma.InputJsonValue | undefined,
        ipAddress: payload.ipAddress ?? null,
        userAgent: payload.userAgent ?? null,
      },
    });
  } catch {
    // Best-effort logging to avoid blocking business flow.
  }
}
