import { Prisma, RechargeStatus } from "@prisma/client";
import { NextResponse } from "next/server";
import Stripe from "stripe";

import { prisma } from "@/lib/db";
import { stripe } from "@/lib/stripe";

export const runtime = "nodejs";

async function markRechargePaid(rechargeId: string, providerRef: string | null, payload: unknown) {
  const recharge = await prisma.recharge.findUnique({ where: { id: rechargeId } });
  if (!recharge) return;

  if (recharge.status === RechargeStatus.PAID) return;

  await prisma.$transaction(async (tx) => {
    await tx.recharge.update({
      where: { id: rechargeId },
      data: {
        status: RechargeStatus.PAID,
        providerRef: providerRef || recharge.providerRef,
        paidAt: new Date(),
        metadata: payload as Prisma.InputJsonValue,
      },
    });

    await tx.tenant.update({
      where: { id: recharge.tenantId },
      data: {
        creditBalance: {
          increment: recharge.creditsGranted,
        },
      },
    });

    await tx.webhookEvent.create({
      data: {
        tenantId: recharge.tenantId,
        provider: "stripe",
        eventType: "checkout.session.completed",
        eventId: providerRef || undefined,
        payload: payload as Prisma.InputJsonValue,
        status: "processed",
        processedAt: new Date(),
      },
    }).catch(() => null);
  });
}

export async function POST(req: Request) {
  if (!stripe || !process.env.STRIPE_WEBHOOK_SECRET) {
    return NextResponse.json({ error: "stripe_not_configured" }, { status: 503 });
  }

  const signature = req.headers.get("stripe-signature");
  if (!signature) {
    return NextResponse.json({ error: "missing_signature" }, { status: 400 });
  }

  const body = await req.text();

  let event: Stripe.Event;
  try {
    event = stripe.webhooks.constructEvent(body, signature, process.env.STRIPE_WEBHOOK_SECRET);
  } catch (error) {
    const message = error instanceof Error ? error.message : "invalid_signature";
    return NextResponse.json({ error: message }, { status: 400 });
  }

  try {
    if (event.type === "checkout.session.completed") {
      const session = event.data.object as Stripe.Checkout.Session;
      const rechargeId = session.metadata?.rechargeId;
      if (rechargeId) {
        await markRechargePaid(rechargeId, session.id, session);
      }
    }

    if (event.type === "payment_intent.payment_failed") {
      const paymentIntent = event.data.object as Stripe.PaymentIntent;
      const rechargeId = paymentIntent.metadata?.rechargeId;
      if (rechargeId) {
        await prisma.recharge.update({
          where: { id: rechargeId },
          data: {
            status: RechargeStatus.FAILED,
            providerRef: paymentIntent.id,
            metadata: paymentIntent as unknown as Prisma.InputJsonValue,
          },
        }).catch(() => null);
      }
    }
  } catch {
    return NextResponse.json({ error: "processing_failed" }, { status: 500 });
  }

  return NextResponse.json({ received: true });
}
