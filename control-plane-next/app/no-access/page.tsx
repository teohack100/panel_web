import Link from "next/link";
import { SignOutButton } from "@clerk/nextjs";

import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { syncCurrentUser } from "@/lib/auth";

export default async function NoAccessPage() {
  const user = await syncCurrentUser();

  return (
    <main className="mx-auto flex min-h-screen w-full max-w-3xl items-center justify-center px-4 py-16">
      <Card className="w-full bg-slate-900/85">
        <CardHeader>
          <CardTitle>Acceso restringido</CardTitle>
          <CardDescription>
            Tu cuenta existe, pero no tiene permisos de Control Plane central.
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="rounded-lg border border-slate-800 bg-slate-950/70 p-4 text-sm text-slate-300">
            <p>
              <span className="font-semibold text-white">Usuario:</span> {user.email}
            </p>
            <p>
              <span className="font-semibold text-white">Rol actual:</span> {user.role}
            </p>
          </div>

          <p className="text-sm text-slate-400">
            Si esta cuenta debe gestionar tenants, planes y pagos globales, agregala en
            <code className="ml-1 rounded bg-slate-800 px-1 py-0.5 text-xs">CONTROL_ADMIN_USER_IDS</code>
            o sube su rol a <code className="mx-1 rounded bg-slate-800 px-1 py-0.5 text-xs">SUPER_ADMIN</code>.
          </p>

          <div className="flex gap-3">
            <SignOutButton redirectUrl="/sign-in">
              <Button variant="secondary">Cambiar cuenta</Button>
            </SignOutButton>
            <Link href="/">
              <Button>Ir al inicio</Button>
            </Link>
          </div>
        </CardContent>
      </Card>
    </main>
  );
}
