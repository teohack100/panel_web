import { SignIn } from "@clerk/nextjs";
import { redirect } from "next/navigation";

import { isAuthBypassed } from "@/lib/env";

export default function SignInPage() {
  if (isAuthBypassed()) {
    redirect("/control");
  }

  return (
    <main className="flex min-h-screen items-center justify-center p-6">
      <SignIn appearance={{ elements: { card: "shadow-2xl" } }} />
    </main>
  );
}
