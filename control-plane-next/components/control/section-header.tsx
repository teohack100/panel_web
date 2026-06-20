type SectionHeaderProps = {
  eyebrow?: string;
  title: string;
  description?: string;
};

export function SectionHeader({ eyebrow, title, description }: SectionHeaderProps) {
  return (
    <header className="mb-6 space-y-2">
      {eyebrow ? <p className="text-xs uppercase tracking-[0.25em] text-cyan-300">{eyebrow}</p> : null}
      <h1 className="text-2xl font-bold text-white md:text-3xl">{title}</h1>
      {description ? <p className="max-w-3xl text-sm text-slate-300">{description}</p> : null}
    </header>
  );
}
