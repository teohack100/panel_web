import Link from "next/link";
import { SignOutButton, UserButton } from "@clerk/nextjs";
import {
  AppWindow,
  Building2,
  CloudCog,
  CreditCard,
  Globe,
  LayoutDashboard,
  LogOut,
  Paintbrush,
  ShieldX,
  Sparkles,
} from "lucide-react";

import { requireControlAdmin } from "@/lib/auth";
import { isAuthBypassed } from "@/lib/env";

const navItems = [
  { href: "/control", label: "Dashboard", icon: LayoutDashboard },
  { href: "/control/systems", label: "Sistemas", icon: AppWindow },
  { href: "/control/tenants", label: "Clientes / Tiendas", icon: Building2 },
  { href: "/control/plans", label: "Planes", icon: Sparkles },
  { href: "/control/payments", label: "Pagos", icon: CreditCard },
  { href: "/control/domains", label: "Dominios", icon: Globe },
  { href: "/control/suspensions", label: "Suspension", icon: ShieldX },
  { href: "/control/white-label", label: "Marca Blanca", icon: Paintbrush },
  { href: "/control/cloudpanel", label: "CloudPanel", icon: CloudCog },
];

export default async function ControlLayout({ children }: { children: React.ReactNode }) {
  const dbUser = await requireControlAdmin();
  const bypassAuth = isAuthBypassed();

  return (
    <div className="min-h-screen">
      <div className="mx-auto grid min-h-screen w-full max-w-[1800px] grid-cols-1 md:grid-cols-[280px_1fr]">
        <aside className="border-r border-slate-800 bg-slate-950/80 p-4 backdrop-blur md:sticky md:top-0 md:h-screen lg:p-5">
          <div className="flex h-full flex-col">
            <div className="mb-6 rounded-xl border border-cyan-400/20 bg-slate-900/90 p-4">
              <p className="text-xs uppercase tracking-[0.2em] text-cyan-300">Control Plane</p>
              <h2 className="mt-1 text-xl font-bold text-white">PROGRAMMIT</h2>
              <p className="mt-2 text-xs text-slate-400">Cerebro central SaaS White-Label</p>
            </div>

            <nav className="flex-1 space-y-1 overflow-y-auto">
              {navItems.map((item) => {
                const Icon = item.icon;
                return (
                  <Link
                    key={item.href}
                    href={item.href}
                    className="flex items-center gap-3 rounded-lg border border-transparent px-3 py-2 text-sm text-slate-300 transition hover:border-slate-700 hover:bg-slate-900 hover:text-white"
                  >
                    <Icon className="h-4 w-4 text-cyan-300" />
                    {item.label}
                  </Link>
                );
              })}
            </nav>

            <div className="mt-4 rounded-xl border border-slate-800 bg-slate-900/80 p-3">
              <p className="text-xs uppercase tracking-[0.2em] text-slate-400">Panel Central / Superadmin</p>
              <p className="mt-1 truncate text-sm text-slate-200">{dbUser.email}</p>
              {bypassAuth ? (
                <span className="mt-3 inline-flex rounded-md border border-emerald-400/40 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300">
                  BOOTSTRAP MODE
                </span>
              ) : (
                <div className="mt-3 flex items-center gap-2">
                  <SignOutButton redirectUrl="/sign-in">
                    <button className="inline-flex flex-1 items-center justify-center gap-2 rounded-md border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-200 transition hover:bg-slate-800 hover:text-white">
                      <LogOut className="h-4 w-4" />
                      Cerrar sesión
                    </button>
                  </SignOutButton>
                  <UserButton afterSignOutUrl="/sign-in" />
                </div>
              )}
            </div>
          </div>
        </aside>

        <section className="min-w-0">
          <main className="p-5 lg:p-8">{children}</main>
        </section>
      </div>
    </div>
  );
}
