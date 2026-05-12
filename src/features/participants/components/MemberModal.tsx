import { AnimatePresence, motion } from "framer-motion";
import type { Participant } from "@/features/participants/types";
import { fmt } from "@/lib/format";

export default function MemberModal({
  member,
  canAdmin,
  isDark,
  starOfMonth,
  mutedText,
  onClose,
  onMakeStar,
}: {
  member: Participant | null;
  canAdmin: boolean;
  isDark: boolean;
  starOfMonth: number;
  mutedText: string;
  onClose: () => void;
  onMakeStar: (id: number) => void;
}) {
  return (
    <AnimatePresence>
      {member && (
        <div
          className="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 p-6 backdrop-blur-sm"
          onClick={onClose}
        >
          <motion.div
            initial={{ opacity: 0, scale: 0.96 }} animate={{ opacity: 1, scale: 1 }}
            exit={{ opacity: 0, scale: 0.96 }}
            onClick={e => e.stopPropagation()}
            className={`w-full max-w-lg rounded-3xl border p-8 ${
              isDark ? "border-white/10 bg-[#0a0612] text-white" : "border-violet-950/10 bg-white text-[#160f24]"
            }`}
          >
            <div className="mb-6 flex items-start justify-between gap-4">
              <div className="flex items-start gap-4">
                <img
                  src={member.image || "/images/AWS-MembersPics/default.png"}
                  alt={member.name}
                  className="h-16 w-16 rounded-full object-cover"
                />
                <div>
                  <div className="mb-3 flex flex-wrap gap-2">
                    <span className="rounded-full border border-violet-400/30 bg-violet-500/10 px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-violet-300">
                      {member.level}
                    </span>
                    {starOfMonth === member.id && (
                      <span className="rounded-full border border-amber-400/30 bg-amber-500/10 px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-amber-300">
                        ★ Star of the month
                      </span>
                    )}
                  </div>

                  <h3 className="text-3xl font-black tracking-[-0.05em]">{member.name}</h3>
                  <p className={`mt-1 ${mutedText}`}>{member.role || "Role available"}</p>
                </div>
              </div>

              <button
                onClick={onClose}
                className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-white/15 text-zinc-400 hover:text-white transition"
              >
                <svg className="h-4 w-4" fill="none" stroke="currentColor" strokeWidth={2.5} viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <div className="space-y-3">
              {[
                ...(member.level !== "Lead" ? [["Points", fmt(member.points)]] : []),
                ["Responsibility", member.responsibilities || "—"],
              ].map(([label, value]) => (
                <div key={label} className="flex flex-col justify-between gap-6 border-b border-violet-400/15 pb-3">
                  <span className="text-xs font-black uppercase tracking-[0.2em] text-violet-400">{label}</span>
                  <span className="text-right text-sm font-medium">{value}</span>
                </div>
              ))}
            </div>

            <div className="mt-7 flex flex-col gap-3 sm:flex-row">
              {canAdmin && starOfMonth !== member.id && (
                <button
                  onClick={() => onMakeStar(member.id)}
                  className="flex-1 rounded-full border border-violet-400/30 bg-violet-500/10 px-5 py-3 text-xs font-black uppercase tracking-[0.18em] text-violet-200 hover:bg-violet-500/20"
                >
                  Make star of month
                </button>
              )}
              <button
                onClick={onClose}
                className="flex-1 rounded-full bg-white px-5 py-3 text-xs font-black uppercase tracking-[0.18em] text-black hover:bg-violet-100"
              >
                ← Close profile
              </button>
            </div>
          </motion.div>
        </div>
      )}
    </AnimatePresence>
  );
}