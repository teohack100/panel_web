import Link from "next/link";
import { Button } from "@/components/ui/button";

export default function HomePage() {
  return (
    <main className="mx-auto flex min-h-screen w-full max-w-6xl flex-col items-center justify-center px-6 py-20">
      <div className="w-full rounded-2xl border border-cyan-500/25 bg-slate-900/75 p-10 shadow-2xl shadow-cyan-950/40">
        <p className="mb-3 text-sm font-semibold uppercase tracking-[0.3em] text-cyan-300">Programmit SaaS White-Label</p>
        <h1 className="text-balance text-4xl font-bold leading-tight text-white md:text-6xl">
          Control Plane central para administrar todo el ecosistema.
        </h1>
        <p className="mt-5 max-w-3xl text-lg text-slate-300">
          Sistemas, clientes, planes, pagos, dominios, suspension y marca blanca desde un solo cerebro.
        </p>

        <div className="mt-8 flex flex-wrap gap-3">
          <Link href="/sign-in">
            <Button size="lg">Entrar al Control Plane</Button>
          </Link>
        </div>
      </div>
    </main>
  );
}
