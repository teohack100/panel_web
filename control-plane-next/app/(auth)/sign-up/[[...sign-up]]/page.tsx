import { SignUp } from "@clerk/nextjs";
import { redirect } from "next/navigation";

import { isAuthBypassed } from "@/lib/env";

export default function SignUpPage() {
  if (isAuthBypassed()) {
    redirect("/control");
  }

  return (
    <main className="flex min-h-screen items-center justify-center p-6">
      <SignUp appearance={{ elements: { card: "shadow-2xl" } }} />
    </main>
  );
}
