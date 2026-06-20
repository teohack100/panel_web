import { NextRequest, NextResponse } from "next/server";
import { clerkMiddleware } from "@clerk/nextjs/server";

const clerkProxy = clerkMiddleware((_, req: NextRequest) => {
  if (req.nextUrl.pathname.startsWith("/sign-up")) {
    return NextResponse.redirect(new URL("/sign-in", req.url));
  }
  return NextResponse.next();
});

export default async function proxy(req: NextRequest, evt: unknown) {
  // Avoid middleware re-entry loops on internal rewrites.
  if (req.headers.get("x-middleware-subrequest")) {
    return NextResponse.next();
  }
  return clerkProxy(req, evt as never);
}

export const config = {
  matcher: ["/((?!.+\\.[\\w]+$|_next).*)", "/", "/(api|trpc)(.*)"],
};
