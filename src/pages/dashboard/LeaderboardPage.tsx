import { useMemo, useState } from "react";
import { motion } from "framer-motion";
import { useTheme } from "@/app/providers/ThemeProvider";
import { useAuth } from "@/app/providers/AuthProvider";
import { useAppData } from "@/app/providers/DataProvider";
import { getTokens } from "@/app/theme/tokens";

import Breadcrumb from "@/components/Breadcrumb";
import SectionLabel from "@/components/SectionLabel";
import MemberModal from "@/features/participants/components/MemberModal";
import { fmt } from "@/lib/format";
import { getTeam } from "@/features/participants/utils";

export default function LeaderboardPage() {
  const { isDark } = useTheme();
  const { canAdmin } = useAuth();
  const { participants, starOfMonth, setStarOfMonth } = useAppData();
  const { panel, softText, mutedText } = getTokens(isDark);

  const [query, setQuery] = useState("");
  const [selectedMember, setSelectedMember] = useState<any>(null);

  // Filter out leaders (they keep records, don't participate in rankings)
  const activeParticipants = useMemo(() => participants.filter(p => p.level !== "Lead"), [participants]);
  
  const sorted = useMemo(() => [...activeParticipants].sort((a, b) => b.points - a.points), [activeParticipants]);

  const searched = useMemo(() => {
    const q = query.trim().toLowerCase();
    if (!q) return sorted;
    return sorted.filter(p => [p.name, p.role, p.track, p.campus, getTeam(p), p.level].filter(Boolean).join(" ").toLowerCase().includes(q));
  }, [query, sorted]);

  function makeStarOfMonth(id: number) {
    if (!canAdmin) return;
    setStarOfMonth(id);
  }

  return (
    <motion.div
      initial={{ opacity: 0, y: 18 }} animate={{ opacity: 1, y: 0 }} exit={{ opacity: 0, y: -18 }}
      transition={{ duration: 0.35 }}
      className="mx-auto max-w-7xl px-6 pb-24 pt-12 lg:px-8"
    >
      <Breadcrumb crumbs={["AWS Student Builders", "Leaderboard"]} softText={softText} />

      <SectionLabel
        eyebrow="Points scoreboard"
        title="Leaderboard rankings"
        copy="Search members and view rankings based on points."
        mutedText={mutedText}
      />

      <div className="mt-12 flex flex-col gap-4 border-b border-violet-400/30 pb-6 sm:flex-row sm:items-center sm:justify-between">
        <p className="text-xl font-black tracking-[-0.03em]">Rankings ({activeParticipants.length})</p>
        <input
          value={query}
          onChange={e => setQuery(e.target.value)}
          placeholder="Search name, role, track, team…"
          className={`w-full rounded-full border px-5 py-3 text-sm outline-none transition sm:max-w-sm ${
            isDark
              ? "border-white/10 bg-white/5 text-white placeholder:text-zinc-500 focus:border-violet-300"
              : "border-violet-950/10 bg-white text-[#160f24] placeholder:text-zinc-400 focus:border-violet-500"
          }`}
        />
      </div>

      <div className={`mt-8 overflow-hidden rounded-3xl border ${panel}`}>
        <div className="divide-y divide-white/10">
          {searched.map(p => {
            const rank = sorted.findIndex(s => s.id === p.id) + 1;
            return (
              <button
                key={p.id}
                onClick={() => setSelectedMember(p)}
                className={`grid w-full gap-4 px-6 py-5 text-left transition md:grid-cols-[72px_1fr_1fr_auto] md:items-center ${
                  isDark ? "hover:bg-white/[0.035]" : "hover:bg-violet-50"
                }`}
              >
                <span className="text-3xl font-black tracking-[-0.06em] text-violet-400">
                  {String(rank).padStart(2, "0")}
                </span>

                <div className="flex items-center gap-4">
                  <img src={p.image || "/images/AWS-MembersPics/default.png"} alt={p.name} className="h-16 w-16 rounded-full object-cover" />
                  <div>
                    <p className="text-lg font-black tracking-[-0.03em]">{p.name}</p>
                    <p className={`text-sm ${softText}`}>{p.role || "Role available"}</p>
                    <p className="mt-1 text-[10px] font-black uppercase tracking-[0.22em] text-violet-400">
                      {getTeam(p)} 
                    </p>
                  </div>
                </div>

                <div>
                  <p className={`mt-1 text-sm ${softText}`}>{p.campus || "—"}</p>
                </div>

                <div className="text-left md:text-right">
                  <p className="text-2xl font-black tracking-[-0.05em]">{fmt(p.points)}</p>
                </div>
              </button>
            );
          })}
        </div>
      </div>

      <MemberModal
        member={selectedMember}
        rank={selectedMember ? sorted.findIndex(p => p.id === selectedMember.id) + 1 : 0}
        canAdmin={canAdmin}
        isDark={isDark}
        starOfMonth={starOfMonth}
        mutedText={mutedText}
        onClose={() => setSelectedMember(null)}
        onMakeStar={(id) => { makeStarOfMonth(id); setSelectedMember(null); }}
      />
    </motion.div>
  );
}