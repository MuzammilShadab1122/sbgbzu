import { useEffect, useMemo, useState } from "react";
import { useNavigate } from "react-router-dom";

import { useTheme } from "@/app/providers/ThemeProvider";
import { useAuth } from "@/app/providers/AuthProvider";
import { useAppData } from "@/app/providers/DataProvider";
import { getTokens } from "@/app/theme/tokens";
import Sticker from "@/components/Sticker";
import SectionLabel from "@/components/SectionLabel";
import StatBar from "@/components/StatBar";
import MemberModal from "@/features/participants/components/MemberModal";
import { fmt } from "@/lib/format";

import { partners, universities } from "@/features/collaborations/seed";
import { HIGHLIGHTS } from "@/content/highlights";

import { useReducedMotion, motion } from "framer-motion";

type Collab = {
  name: string;
  logo?: string;  
  href?: string;
  kind?: "Industry partner" | "Academic partner";
};

function CollabCard({ item }: { item: Collab }) {
  return (
    <a
      href={item.href || "#"}
      target={item.href ? "_blank" : undefined}
      rel={item.href ? "noreferrer" : undefined}
      className="group flex items-center gap-3 rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-3 backdrop-blur transition hover:bg-white/[0.07]"
      onClick={(e) => {
        if (!item.href) e.preventDefault();
      }}
    >
      {/* logo */}
      {item.logo ? (
        <img
          src={item.logo}
          alt={item.name}
          className="h-10 w-10 rounded-xl object-contain bg-white/5 ring-1 ring-white/10 p-1"
          draggable={false}
        />
      ) : (
        <div className="grid h-10 w-10 place-items-center rounded-xl bg-white/5 ring-1 ring-white/10">
          <span className="text-xs font-black tracking-[-0.03em] text-white/80">
            {item.name
              .split(" ")
              .slice(0, 2)
              .map((s) => s[0])
              .join("")
              .toUpperCase()}
          </span>
        </div>
      )}

      {/* text */}
      <div className="min-w-0">
        <p className="truncate text-sm font-black text-white">{item.name}</p>
        <p className="truncate text-[10px] font-black uppercase tracking-[0.22em] text-violet-300/80">
          {item.kind ?? "Partner"}
        </p>
      </div>
    </a>
  );
}

function CollaborationMarquee({
  items,
  panel,
  mutedText,
}: {
  items: Collab[];
  panel: string;
  mutedText: string;
}) {
  const reduceMotion = useReducedMotion();
  const loop = [...items, ...items]; // duplicate for seamless loop

  return (
    <div className={`relative mt-12 overflow-hidden rounded-3xl border ${panel} bg-white/[0.02]`}>
      {/* subtle gradient wash */}
      <div
        className="pointer-events-none absolute inset-0 opacity-70"
        style={{
          background:
            "radial-gradient(900px 260px at 15% 20%, rgba(168,85,247,0.14), transparent 60%), radial-gradient(900px 260px at 85% 60%, rgba(59,130,246,0.10), transparent 55%)",
        }}
      />

      {/* edge fade */}
      <div className="pointer-events-none absolute inset-y-0 left-0 w-24 bg-gradient-to-r from-black/35 to-transparent" />
      <div className="pointer-events-none absolute inset-y-0 right-0 w-24 bg-gradient-to-l from-black/35 to-transparent" />

      <div className="relative px-6 py-6">
        <div className="flex items-end justify-between gap-6">
          <div>
            <p className="text-[10px] font-black uppercase tracking-[0.28em] text-violet-300">
              Collaborations
            </p>
            <h3 className="mt-2 text-2xl font-black tracking-[-0.04em] text-white">
              Partners & campus collaborations
            </h3>
            <p className={`mt-2 text-sm ${mutedText}`}>
              Our Industry and Academic partners.
            </p>
          </div>

          <div className="hidden sm:block rounded-full border border-white/10 bg-white/5 px-4 py-2 text-[10px] font-black uppercase tracking-[0.18em] text-white">
            {items.length} collaborations
          </div>
        </div>

        <div className="mt-8 overflow-hidden">
          <motion.div
            className="flex w-max gap-4 py-2"
            animate={
              reduceMotion
                ? undefined
                : {
                    x: ["0%", "-50%"],
                  }
            }
            transition={
              reduceMotion
                ? undefined
                : {
                    duration: 22,
                    repeat: Infinity,
                    ease: "linear",
                  }
            }
          >
            {loop.map((item, idx) => (
              <CollabCard key={`${item.name}-${idx}`} item={item} />
            ))}
          </motion.div>
        </div>
      </div>
    </div>
  );
}

export default function HomePage() {
  const nav = useNavigate();
  const { isDark } = useTheme();
  const { canAdmin } = useAuth();
  const { participants, posts } = useAppData();

  const { panel, mutedText, softText, lineColor } = getTokens(isDark);

  const [selectedMember, setSelectedMember] = useState<any>(null);
  const [statsAnimated, setStatsAnimated] = useState(false);

    const collaborations: Collab[] = [
    {
      name: "GoClouds",
      logo: "/images/partners/gocloud.png",
      kind: "Industry partner",
    },
    {
      name: "BitOps Technologies",
      logo: "/images/partners/bitsops.jpeg",
      kind: "Industry partner",
    },
    {
      name: "Pie & Ai",
      logo: "/images/partners/pie-ai.png",
      kind: "Industry partner",
    },
    {
      name: "Bahauddin Zakariya University",
      logo: "/images/partners/bzu.png",
      kind: "Academic partner",
    },
  ];
  const sorted = useMemo(
    () => [...participants].sort((a, b) => b.points - a.points),
    [participants]
  );

  const starMember = useMemo(() => {
    if (HIGHLIGHTS.starOfMonthId == null) return null;
    return participants.find((p) => p.id === HIGHLIGHTS.starOfMonthId) || null;
  }, [participants]);

  const grinderMembers = useMemo(() => {
    return HIGHLIGHTS.monthlyGrinders
      .map((id) => participants.find((p) => p.id === id))
      .filter(Boolean) as typeof participants;
  }, [participants]);

  const totalPoints = participants.reduce((s, p) => s + p.points, 0);
  const activeTracks = new Set(participants.map((p) => p.track)).size;

  const statRows = [
    {
      label: "Student builders",
      value: participants.length,
      display: `${participants.length}+`,
      max:40,
      color: "from-violet-500 to-fuchsia-500",
    },
    {
      label: "Monthly grinders",
      value: grinderMembers.length,
      max:5,
      display: `${grinderMembers.length}`,
      color: "from-purple-500 to-indigo-500",
    },
    {
      label: "Learning points",
      value: Math.round(totalPoints / 1000),
      max: 500,
      display: `${Math.round(totalPoints / 1000)}K`,
      color: "from-fuchsia-500 to-violet-500",
    },
    {
      label: "Collaboration partners",
      value: partners.length,
      max:100,
      display: `${partners.length}`,
      color: "from-white/80 to-violet-300",
    },
    {
      label: "Campus chapters",
      value: universities.length,
      max: 50,
      display: `${universities.length}`,
      color: "from-violet-300 to-purple-600",
    },
    {
      label: "Active tracks",
      value: activeTracks,
      max: 200,
      display: `${activeTracks}`,
      color: "from-indigo-400 to-fuchsia-500",
    },
  ];

  useEffect(() => {
    const t = window.setTimeout(() => setStatsAnimated(true), 300);
    return () => window.clearTimeout(t);
  }, []);

  return (
    <>
      {/* HERO (compact) */}
      <section className="relative overflow-hidden">
        <div
          className="absolute inset-0 bg-cover bg-center"
          style={{
            backgroundImage:
              "linear-gradient(105deg,rgba(5,3,10,0.96) 0%,rgba(21,8,38,0.88) 52%,rgba(5,3,10,0.35) 100%),url('/images/aws-student-builders-hero.png')",
          }}
        />

        <motion.div
          aria-hidden
          animate={{ opacity: [0.16, 0.34, 0.16], scale: [1, 1.05, 1] }}
          transition={{ duration: 8, repeat: Infinity }}
          className="absolute -left-40 top-10 h-[26rem] w-[26rem] rounded-full bg-violet-700/30 blur-3xl pointer-events-none"
        />
        <motion.div
          aria-hidden
          animate={{ y: [0, -14, 0], opacity: [0.12, 0.28, 0.12] }}
          transition={{ duration: 7, repeat: Infinity }}
          className="absolute bottom-10 right-0 h-80 w-80 rounded-full bg-fuchsia-500/20 blur-3xl pointer-events-none"
        />

        <div className="relative z-10 mx-auto w-full max-w-7xl px-6 py-14 lg:px-8 lg:py-16">
          <div className="grid items-start gap-8 lg:grid-cols-[1.05fr_0.95fr] lg:gap-10">
            {/* LEFT */}
            <div className="max-w-2xl">
              <div className="mb-4 flex flex-wrap items-center gap-2">
                <span className="rounded-full border border-white/10 bg-white/[0.05] px-3 py-1.5 text-[10px] font-black uppercase tracking-[0.22em] text-violet-200">
                  AWS Student Builder Group • BZU
                </span>
                <span className="rounded-full border border-violet-400/20 bg-violet-500/10 px-3 py-1.5 text-[10px] font-black uppercase tracking-[0.22em] text-violet-300">
                  Cloud • Community • Portfolio
                </span>
              </div>

              <h1
                className="font-black leading-[1.02] tracking-[-0.06em] text-white"
                style={{ fontSize: "clamp(2.1rem, 3.6vw, 3.6rem)" }}
              >
                Empowering innovation through the power of AWS.
              </h1>

              <p className="mt-4 max-w-xl text-sm leading-7 text-zinc-200 sm:text-base">
                A student-led platform for team profiles, points leaderboard, spotlights,
                events, notices, and collaboration portfolios.
              </p>

              <div className="mt-7 flex flex-col gap-3 sm:flex-row">
                <button
                  onClick={() => nav("/team")}
                  className="rounded-full bg-white px-6 py-3.5 text-[11px] font-black uppercase tracking-[0.18em] text-[#13091f] transition hover:bg-violet-100"
                >
                  View teams →
                </button>

                <button
                  onClick={() => nav("/leaderboard")}
                  className="rounded-full border border-white/20 bg-white/5 px-6 py-3.5 text-[11px] font-black uppercase tracking-[0.18em] text-white backdrop-blur transition hover:border-violet-300 hover:bg-violet-500/20"
                >
                  Leaderboard →
                </button>

                {canAdmin && (
                  <button
                    onClick={() => nav("/admin")}
                    className="rounded-full border border-violet-400/30 bg-violet-500/10 px-6 py-3.5 text-[11px] font-black uppercase tracking-[0.18em] text-violet-200 transition hover:bg-violet-500/20"
                  >
                    Admin →
                  </button>
                )}
              </div>
            </div>

            {/*Highlights */}
            <div className={`rounded-3xl border ${panel} p-5 backdrop-blur-2xl lg:p-6`}>
              <p className="text-[10px] font-black uppercase tracking-[0.28em] text-violet-300">
                Highlights ({HIGHLIGHTS.monthLabel})
              </p>
                    <Sticker
      isDark={isDark}
      src="/images/stickers/aws.png"
      className="-right-8 top-2 h-24 w-24 opacity-20"
      floatDelay={0.05}
      tilt={8}
    />
    <Sticker
      isDark={isDark}
      src="/images/stickers/cloud.png"
      className="-left-1 top-1 h-24 w-24 opacity-55"
      floatDelay={0.18}
      tilt={-14}
    />
              {/* STAR */}
              <div className="mt-4 rounded-2xl border border-white/10 bg-white/[0.03] p-4" >
                <div className="flex items-center gap-2">
                  <span className="text-[10px] font-black uppercase tracking-[0.26em] text-amber-200">
                    ★ Star of the month
                  </span>
                </div>

                {starMember ? (
                  <button
                    onClick={() => setSelectedMember(starMember)}
                    className="mt-3 flex w-full items-center gap-3 text-left"
                  >
                    <img
                      src={starMember.image || "/images/AWS-MembersPics/default.png"}
                      alt={starMember.name}
                      className="h-12 w-12 rounded-2xl object-cover"
                    />
                    <div className="min-w-0 flex-1">
                      <p className="truncate text-sm font-black text-white">{starMember.name}</p>
                      <p className="truncate text-xs text-zinc-300">{starMember.role}</p>
                    </div>
                    <div className="text-right">
                      <p className="text-lg font-black text-white">{fmt(starMember.points)}</p>
                      <p className="text-[10px] font-black uppercase tracking-[0.2em] text-violet-300/80">pts</p>
                    </div>
                  </button>
                ) : (
                  <div className="mt-3 rounded-xl border border-white/10 bg-black/20 p-3">
                    <p className="text-sm font-bold text-white">Not announced yet</p>
                    <p className={`mt-1 text-xs ${softText}`}>
                      Set <code>starOfMonthId</code> in highlights.ts
                    </p>
                  </div>
                )}
              </div>

              {/* MONTHLY GRINDERS */}
              <div className="mt-4 rounded-2xl border border-white/10 bg-white/[0.03] p-4">
                <div className="flex items-center justify-between gap-4">
                  <p className="text-[10px] font-black uppercase tracking-[0.26em] text-violet-200">
                    🏆 Monthly grinders
                  </p>
                  <button
                    onClick={() => nav("/leaderboard")}
                    className="rounded-full border border-white/15 bg-white/5 px-4 py-2 text-[10px] font-black uppercase tracking-[0.18em] text-white hover:bg-white/10"
                  >
                    View →
                  </button>
                </div>

                {grinderMembers.length > 0 ? (
                  <div className="mt-3 grid gap-2">
                    {grinderMembers.slice(0, 3).map((p, i) => (
                      <button
                        key={p.id}
                        onClick={() => setSelectedMember(p)}
                        className="flex items-center gap-3 rounded-xl border border-white/10 bg-white/[0.04] px-3 py-2 text-left hover:bg-white/[0.07]"
                      >
                        <span className="grid h-7 w-7 place-items-center rounded-full border border-white/10 bg-black/30 text-[11px] font-black text-violet-200">
                          {i + 1}
                        </span>
                        <img
                          src={p.image || "/images/AWS-MembersPics/default.png"}
                          alt={p.name}
                          className="h-9 w-9 rounded-full object-cover"
                        />
                        <div className="min-w-0 flex-1">
                          <p className="truncate text-sm font-black text-white">{p.name}</p>
                          <p className="truncate text-[11px] text-zinc-400">{p.role || "—"}</p>
                        </div>
                        <div className="text-right">
                          <p className="text-sm font-black text-white">{fmt(p.points)}</p>
                          <p className="text-[10px] font-black uppercase tracking-[0.18em] text-violet-300/80">pts</p>
                        </div>
                      </button>
                    ))}
                    {grinderMembers.length > 3 && (
                      <p className={`text-xs ${softText}`}>+{grinderMembers.length - 3} more assigned</p>
                    )}
                  </div>
                ) : (
                  <div className="mt-3 rounded-xl border border-white/10 bg-black/20 p-3">
                    <p className="text-sm font-bold text-white">Not announced yet</p>
                  </div>
                )}
              </div>
            </div>
          </div>
        </div>
      </section>

            {/* Collaborations  */}
      <section className={`border-t ${lineColor} px-6 py-20 lg:px-8`}>
        <div className="mx-auto max-w-7xl">
          <CollaborationMarquee items={collaborations} panel={panel} mutedText={mutedText} />
        </div>
      </section>

      {/* Vision / Mission */}
      <section className={`border-t ${lineColor} px-6 py-24 lg:px-8`}>
        <div className="mx-auto grid max-w-7xl gap-14 lg:grid-cols-2">
          <SectionLabel
            eyebrow="Our vision and mission"
            title="Build cloud skills that turn student ideas into deployed products"
            copy="Bringing cloud and tech opportunites to Multan. Our group helps students learn AWS, collaborate across campuses, ship practical portfolios, and grow into cloud-ready engineers."
            mutedText={mutedText}
          />
          <div className="grid gap-6 sm:grid-cols-2">
            {[
              ["01", "Hands-on AWS labs", "Guided labs around compute, storage, serverless APIs, identity, and deployment workflows."],
              ["02", "Portfolio-first learning", "Every workshop should lead to a project, GitHub commit, demo, or published case study."],
              ["03", "Community leadership", "Members grow by mentoring juniors, hosting sessions, and supporting campus events."],
              ["04", "Career readiness", "The platform highlights skills, points, projects, and contributions for internships and roles."],
            ].map(([n, t, c]) => (
              <div key={n} className="border-t border-violet-400/60 pt-5">
                <p className="text-sm font-black text-violet-400">{n}</p>
                <h3 className="mt-3 text-xl font-black tracking-[-0.04em]">{t}</h3>
                <p className={`mt-3 text-sm leading-6 ${mutedText}`}>{c}</p>
              </div>
            ))}
          </div>
               <Sticker
      isDark={isDark}
      src="/images/stickers/hero.jpeg"
      className="left-90 bottom-4 h-70 w-70 opacity-0"
      floatDelay={0.05}
      tilt={-4}
    />
        </div>
      </section>

      {/* Latest notices */}
      <section className={`border-t ${lineColor} px-6 py-24 lg:px-8`}>
        <div className="mx-auto max-w-7xl">
          <div className="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
            <SectionLabel
              eyebrow="Notices"
              title="Latest student builder updates"
              copy="Announcements, event recaps, and program updates."
              mutedText={mutedText}
            />
            <button
              onClick={() => nav("/blog")}
              className="rounded-full border border-white/15 bg-white/5 px-6 py-3 text-xs font-black uppercase tracking-[0.18em] hover:bg-white/10"
            >
              View all posts →
            </button>
          </div>

          <div className="mt-14 grid gap-8 md:grid-cols-3">
            {posts.slice(0, 3).map((post) => (
              <article key={post.id} className="border-t border-violet-400/70 pt-7">
                <p className="text-[10px] font-black uppercase tracking-[0.28em] text-violet-400">
                  {post.category} / {post.date}
                </p>
                <h3 className="mt-5 text-2xl font-black leading-tight tracking-[-0.05em]">{post.title}</h3>
                <p className={`mt-4 leading-7 ${mutedText}`}>{post.excerpt}</p>
              </article>
            ))}
          </div>
        </div>
      </section>

  {/* Stats */}
      <section className={`border-t ${lineColor} px-6 py-24 lg:px-8`}>
        <div className="mx-auto max-w-7xl">
          <SectionLabel
            eyebrow="Live program insights"
            title="Student builder progress at a glance"
            copy="Track member growth, grinder activity, learning points, campus chapters, and collaboration partners."
            mutedText={mutedText}
          />
          <div className="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            {statRows.map((s, i) => (
              <StatBar key={s.label} {...s} isAnimated={statsAnimated} index={i} panel={panel} />
            ))}
          </div>
        </div>
      </section>

      <MemberModal
        member={selectedMember}
        rank={selectedMember ? sorted.findIndex((p) => p.id === selectedMember.id) + 1 : 0}
        canAdmin={false}                
        isDark={isDark}
        starOfMonth={HIGHLIGHTS.starOfMonthId ?? -1}
        mutedText={mutedText}
        onClose={() => setSelectedMember(null)}
        onMakeStar={() => {}}
      />
    </>
  );
}