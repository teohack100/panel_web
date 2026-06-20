import { NextResponse } from "next/server";
import { auth } from "@clerk/nextjs/server";

import { isControlAdminApi } from "@/lib/auth";
import { isAuthBypassed } from "@/lib/env";

export async function requireControlAdminApi() {
  const allowed = await isControlAdminApi();
  if (!allowed) {
    return {
      ok: false as const,
      response: NextResponse.json({ error: "forbidden" }, { status: 403 }),
      userId: null,
    };
  }

  if (isAuthBypassed()) {
    return {
      ok: true as const,
      response: null,
      userId: "local-admin",
    };
  }

  const session = await auth();
  return {
    ok: true as const,
    response: null,
    userId: session.userId,
  };
}
