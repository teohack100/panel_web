import { RechargeStatus } from "@prisma/client";
import { NextResponse } from "next/server";

import { requireControlAdminApi } from "@/lib/control-auth";
import { prisma } from "@/lib/db";

export async function GET(req: Request) {
  const auth = await requireControlAdminApi();
  if (!auth.ok) return auth.response;

  const { searchParams } = new URL(req.url);
  const statusRaw = searchParams.get("status");
  const tenantSlug = searchParams.get("tenant");

  const where: {
    status?: RechargeStatus;
    tenant?: { slug: string };
  } = {};

  if (statusRaw && Object.values(RechargeStatus).includes(statusRaw as RechargeStatus)) {
    where.status = statusRaw as RechargeStatus;
  }

  if (tenantSlug) {
    where.tenant = { slug: tenantSlug };
  }

  const data = await prisma.recharge.findMany({
    where,
    orderBy: { createdAt: "desc" },
    include: { tenant: true, user: true, paymentMethod: true },
    take: 200,
  });

  return NextResponse.json({ data });
}
