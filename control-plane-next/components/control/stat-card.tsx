import { Card, CardContent } from "@/components/ui/card";

type StatCardProps = {
  title: string;
  value: string | number;
  hint?: string;
};

export function StatCard({ title, value, hint }: StatCardProps) {
  return (
    <Card className="bg-slate-900/80">
      <CardContent className="space-y-2 p-4">
        <p className="text-xs uppercase tracking-wide text-slate-400">{title}</p>
        <p className="text-3xl font-bold text-white">{value}</p>
        {hint ? <p className="text-xs text-slate-400">{hint}</p> : null}
      </CardContent>
    </Card>
  );
}
