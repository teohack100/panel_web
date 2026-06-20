import { NextResponse } from "next/server";

export async function GET() {
  return NextResponse.json({ ok: true, service: "control-plane", timestamp: new Date().toISOString() });
}
