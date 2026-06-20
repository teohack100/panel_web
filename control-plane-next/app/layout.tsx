import type { Metadata } from "next";
import { ClerkProvider } from "@clerk/nextjs";

import { isAuthBypassed } from "@/lib/env";
import "./globals.css";

export const metadata: Metadata = {
  title: "PROGRAMMIT Control Plane",
  description: "Control central multi-tenant para paneles white-label y CloudPanel.",
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  if (isAuthBypassed()) {
    return (
      <html lang="es">
        <body className="min-h-screen bg-slate-950 text-slate-100 antialiased">{children}</body>
      </html>
    );
  }

  return (
    <ClerkProvider>
      <html lang="es">
        <body className="min-h-screen bg-slate-950 text-slate-100 antialiased">{children}</body>
      </html>
    </ClerkProvider>
  );
}
