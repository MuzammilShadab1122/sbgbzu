export default function Breadcrumb({
  crumbs,
  onBack,
  softText,
}: {
  crumbs: string[];
  onBack?: () => void;
  softText: string;
}) {
  return (
    <div className="mb-8 flex items-center gap-2">
      {onBack && (
        <button
          onClick={onBack}
          className="flex items-center gap-1.5 text-xs font-black uppercase tracking-[0.18em] text-violet-400 hover:text-violet-300 transition"
        >
          <svg className="h-3 w-3" fill="none" stroke="currentColor" strokeWidth={2.5} viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" d="M15 19l-7-7 7-7" />
          </svg>
          Back
        </button>
      )}

      <div className={`flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.22em] ${softText}`}>
        {crumbs.map((crumb, i) => (
          <span key={crumb} className="flex items-center gap-1.5">
            {i > 0 && <span className="text-zinc-600">/</span>}
            <span className={i === crumbs.length - 1 ? "text-violet-400" : ""}>{crumb}</span>
          </span>
        ))}
      </div>
    </div>
  );
}