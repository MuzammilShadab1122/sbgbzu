export default function SectionLabel({
  eyebrow,
  title,
  copy,
  mutedText,
}: {
  eyebrow: string;
  title: string;
  copy: string;
  mutedText: string;
}) {
  return (
    <div className="max-w-4xl">
      <p className="text-sm font-black uppercase tracking-[0.36em] text-violet-400">{eyebrow}</p>
      <h2 className="mt-5 text-4xl font-black leading-none tracking-[-0.07em] sm:text-5xl">{title}</h2>
      <p className={`mt-6 max-w-3xl text-lg leading-8 ${mutedText}`}>{copy}</p>
    </div>
  );
}