import * as React from "react";
import { cva, type VariantProps } from "class-variance-authority";

import { cn } from "@/lib/utils";

const badgeVariants = cva("inline-flex items-center rounded-md border px-2 py-0.5 text-xs font-medium", {
  variants: {
    variant: {
      default: "border-slate-700 bg-slate-800 text-slate-100",
      success: "border-emerald-500/30 bg-emerald-500/15 text-emerald-300",
      warning: "border-amber-500/30 bg-amber-500/15 text-amber-300",
      danger: "border-rose-500/30 bg-rose-500/15 text-rose-300",
      info: "border-cyan-500/30 bg-cyan-500/15 text-cyan-300",
    },
  },
  defaultVariants: {
    variant: "default",
  },
});

export interface BadgeProps extends React.HTMLAttributes<HTMLDivElement>, VariantProps<typeof badgeVariants> {}

export function Badge({ className, variant, ...props }: BadgeProps) {
  return <div className={cn(badgeVariants({ variant }), className)} {...props} />;
}
