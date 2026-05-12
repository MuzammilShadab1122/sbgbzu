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

import type { Participant } from "@/features/participants/types";
import { TEAM_META, TEAM_ORDER } from "@/features/participants/teams";
import Sticker from "@/components/Sticker"; 


function BigProfileCard({
  p,
  panel,
  isDark,
  onClick,
}: {
  p: Participant;
  panel: string;
  isDark: boolean;
  onClick: () => void;
}) {
  return (
    <button
      onClick={onClick}
      className={`group relative overflow-hidden rounded-3xl border ${panel} p-6 text-left transition hover:-translate-y-1 hover:shadow-2xl hover:shadow-violet-500/10`}
    >
      <div
        className="pointer-events-none absolute -inset-24 z-0 opacity-0 blur-3xl transition group-hover:opacity-100"
        style={{
          background:
            "radial-gradient(closest-side, rgba(168,85,247,0.25), rgba(217,70,239,0.14), transparent 70%)",
        }}
      />

      <div className="relative z-10 flex items-start gap-5">
        <img
          src={p.image || "/images/AWS-MembersPics/default.png"}
          alt={p.name}
          className="h-20 w-20 rounded-3xl object-cover ring-1 ring-white/10"
        />

        <div className="min-w-0 flex-1">
          <p className="text-2xl font-black tracking-[-0.04em]">{p.name}</p>
          <p className={`mt-1 text-sm ${isDark ? "text-zinc-300" : "text-zinc-600"}`}>
            {p.role}
          </p>
          <div className="mt-4 flex flex-wrap gap-2">
            <span className="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-violet-200">
              {p.team}
            </span>
            <span className="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-violet-200">
              {p.level}
            </span>
            {p.responsibilities && (
              <span className="line-clamp-1 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-white/70">
                {p.responsibilities}
              </span>
            )}
          </div>
        </div>

        {p.level !== "Lead" && (
          <div className="text-right">
            <p className="text-lg font-black tracking-[-0.05em] text-white">{fmt(p.points)}</p>
            <p className="mt-1 text-[10px] font-black uppercase tracking-[0.22em] text-violet-300/80">
              points
            </p>
          </div>
        )}
        {p.level === "Lead" && (
          <div className="text-right">
            <span className="text-xs font-black uppercase tracking-[0.18em] text-violet-300/60"></span>
          </div>
        )}
      </div>
    </button>
  );
}

function MemberTile({
  p,
  onClick,
  isDark,
}: {
  p: Participant;
  onClick: () => void;
  isDark: boolean;
}) {
  return (
    <button
      onClick={onClick}
      className="group flex w-full items-center gap-4 rounded-2xl border border-white/10 bg-white/[0.03] p-4 text-left transition hover:-translate-y-0.5 hover:bg-white/[0.06] hover:shadow-xl hover:shadow-violet-500/10"
    >
      <img
        src={p.image || "/images/AWS-MembersPics/default.png"}
        alt={p.name}
        className="h-12 w-12 rounded-2xl object-cover ring-1 ring-white/10"
      />

      <div className="min-w-0 flex-1">
        <p className="truncate text-sm font-black">{p.name}</p>
        <p className={`truncate text-xs ${isDark ? "text-zinc-400" : "text-zinc-500"}`}>{p.role}</p>
      </div>

      <div className="text-right">
        {p.level !== "Lead" ? (
          <>
            <p className="text-sm font-black text-violet-200">{fmt(p.points)}</p>
            <p className="text-[10px] font-black uppercase tracking-[0.18em] text-violet-300/70">pts</p>
          </>
        ) : (
          <span className="text-xs font-black uppercase tracking-[0.18em] text-violet-300/60"></span>
        )}
      </div>
        </button>
  );
}

export default function TeamPage() {
  const { isDark } = useTheme();
  const { canAdmin } = useAuth();
  const { participants, starOfMonth, setStarOfMonth } = useAppData();
  const { panel, softText, mutedText } = getTokens(isDark);

  const [selectedMember, setSelectedMember] = useState<Participant | null>(null);

  const byTeam = useMemo(() => {
    const map = new Map<string, Participant[]>();
    for (const p of participants) {
      const key = p.team || "General";
      map.set(key, [...(map.get(key) || []), p]);
    }
    for (const [, arr] of map) arr.sort((a, b) => b.points - a.points);
    return map;
  }, [participants]);

  const leads = useMemo(() => {
    const core = byTeam.get("Core") || [];
    return [...core].sort((a, b) => {
      const w = (x: Participant) => (x.level === "Lead" ? 2 : x.level === "Core" ? 1 : 0);
      return w(b) - w(a) || b.points - a.points;
    });
  }, [byTeam]);

  const leaderSpotlight = leads[0] || null;
  const otherLeads = leads.slice(1);

  const teamSections = useMemo(() => {
    const keys = TEAM_ORDER.filter((k) => k !== "Core");
    return keys
      .map((k) => ({ key: k, members: byTeam.get(k) || [] }))
      .filter((x) => x.members.length > 0);
  }, [byTeam]);

  function makeStarOfMonth(id: number) {
    if (!canAdmin) return;
    setStarOfMonth(id);
  }

  return (
    <motion.div
      initial={{ opacity: 0, y: 18 }}
      animate={{ opacity: 1, y: 0 }}
      exit={{ opacity: 0, y: -18 }}
      transition={{ duration: 0.35 }}
      className="mx-auto max-w-7xl px-6 pb-24 pt-12 lg:px-8"
    >
      <Breadcrumb crumbs={["AWS Student Builders", "Our Teams"]} softText={softText} />

      <SectionLabel
        eyebrow="Our ambassadors"
        title="Meet the chapter leadership & teams"
        copy="A static directory of our student builder chapter organized by teams and responsibilities."
        mutedText={mutedText}
      />

      {/* LEADS (professional sticker layout: corners + subtle watermark) */}
      <section className="mt-12">
        <div className="relative overflow-hidden rounded-3xl border border-white/10 bg-white/[0.02] p-6">
          {/* a subtle tint behind stickers so they feel cohesive */}
          <div
            className="pointer-events-none absolute inset-0 z-0 opacity-70"
            style={{
              background:
                "radial-gradient(1200px 220px at 20% 10%, rgba(168,85,247,0.16), transparent 60%), radial-gradient(900px 260px at 85% 30%, rgba(59,130,246,0.10), transparent 55%)",
            }}
          />

          {/* Stickers layer (behind, subtle, and mostly outside the content area) */}
          <div className="pointer-events-none absolute inset-0 z-0 hidden lg:block">
            {/* top-left decal */}
            <Sticker
              isDark={isDark}
              src="/images/stickers/aws.png"
              className="-left-6 -top-12 h-24 w-24 opacity-70"
              floatDelay={0.0}
              tilt={-10}
            />

            {/* top-right small accent */}
            <Sticker
              isDark={isDark}
              src="/images/sbg-logo.png"
              className="-right-10 top-10 h-20 w-20 opacity-45"
              floatDelay={0.18}
              tilt={12}
            />

            {/* center/bottom watermark (very subtle) */}
            <Sticker
              isDark={isDark}
              src="/images/stickers/cloud.png"
              className="left-1/2 top-10 h-44 w-44 -translate-x-1/2 opacity-20"
              floatDelay={0.35}
              tilt={6}
            />
          </div>

          {/* Real header content ABOVE stickers */}
          <div className="relative z-10 flex items-end justify-between gap-6">
            <div>
              <h3 className="text-2xl font-black tracking-[-0.04em]">Core Team</h3>
              <p className={`mt-2 text-sm ${softText}`}>Chapter leadership and core coordinators.</p>
            </div>
            <span className="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-[10px] font-black uppercase tracking-[0.18em]">
              {leads.length} members
            </span>
          </div>
        </div>

        {leaderSpotlight ? (
          <div className="mt-6 grid gap-6 lg:grid-cols-2">
            <BigProfileCard
              p={leaderSpotlight}
              panel={panel}
              isDark={isDark}
              onClick={() => setSelectedMember(leaderSpotlight)}
            />

            <div className="grid gap-4">
              {otherLeads.slice(0, 7).map((p) => (
                <MemberTile key={p.id} p={p} isDark={isDark} onClick={() => setSelectedMember(p)} />
              ))}

              {otherLeads.length > 7 && (
                <div className={`rounded-2xl border ${panel} p-4`}>
                  <p className="text-sm font-bold">+{otherLeads.length - 7} more leads</p>
                  <p className={`mt-1 text-xs ${softText}`}>Scroll down to see all teams and members.</p>
                </div>
              )}
            </div>
          </div>
        ) : (
          <div className={`mt-6 rounded-3xl border ${panel} p-6`}>
            <p className="font-black">No leads found</p>
          </div>
        )}
      </section>

      {/* TEAM SECTIONS */}
      <section className="mt-16 space-y-14">
        {teamSections.map(({ key, members }) => {
          const meta = TEAM_META[key] || { title: key, blurb: "" };
          
          // Technical team: show Developer-level members as spotlights
          // Other teams: show 1 spotlight (top member)
          let spotlights: Participant[] = [];
          let rest: Participant[] = [];
          
          if (key === "Technical") {
            spotlights = members.filter(p => p.level === "Developer");
            rest = members.filter(p => p.level !== "Developer");
          } else {
            spotlights = members.slice(0, 1);
            rest = members.slice(1);
          }

          return (
            <div key={key}>
              {/* header */}
              <div className="relative">
                {/* subtle corner decals only (cleaner) */}
                <div className="pointer-events-none absolute inset-0 z-0 hidden lg:block">
                  <Sticker
                    isDark={isDark}
                    src="/images/stickers/aws.png"
                    className="-left-1 -top-8 h-16 w-16 opacity-18"
                    floatDelay={0.08}
                    tilt={-8}
                  />
                  <Sticker
                    isDark={isDark}
                    src="/images/stickers/cloud.png"
                    className="-right-10 -top-10 h-16 w-16 opacity-14"
                    floatDelay={0.22}
                    tilt={10}
                  />
                </div>

                <div className="relative z-10 flex items-end justify-between gap-6">
                  <div>
                    <h3 className="text-2xl font-black tracking-[-0.04em]">{meta.title}</h3>
                    <p className={`mt-2 text-sm ${softText}`}>{meta.blurb}</p>
                  </div>
                  <span className="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-[10px] font-black uppercase tracking-[0.18em]">
                    {members.length} members
                  </span>
                </div>
              </div>

              {/* content */}
              <div className={`mt-6 grid gap-6 ${key === "Technical" && spotlights.length > 0 ? `lg:grid-cols-${spotlights.length === 1 ? 2 : 3}` : "lg:grid-cols-2"}`}>
                {/* Spotlights */}
                {spotlights.length > 0 && (
                  <div className={`${key === "Technical" && spotlights.length > 1 ? "lg:col-span-2 grid gap-6 lg:grid-cols-2" : ""}`}>
                    {spotlights.map((p) => (
                      <BigProfileCard
                        key={p.id}
                        p={p}
                        panel={panel}
                        isDark={isDark}
                        onClick={() => setSelectedMember(p)}
                      />
                    ))}
                  </div>
                )}

                {/* Rest of members */}
                <div className="grid gap-3">
                  {rest.slice(0, 6).map((p) => (
                    <MemberTile key={p.id} p={p} isDark={isDark} onClick={() => setSelectedMember(p)} />
                  ))}

                  {rest.length > 6 && (
                    <div className={`rounded-2xl border ${panel} p-4`}>
                      <p className="text-sm font-bold">+{rest.length - 6} more members in {meta.title}</p>
                    </div>
                  )}
                </div>
              </div>
            </div>
          );
        })}
      </section>

      <MemberModal
        member={selectedMember}
        canAdmin={canAdmin}
        isDark={isDark}
        starOfMonth={starOfMonth ?? -1}
        mutedText={mutedText}
        onClose={() => setSelectedMember(null)}
        onMakeStar={(id) => {
          makeStarOfMonth(id);
          setSelectedMember(null);
        }}
      />
    </motion.div>
  );
}