import { motion } from "framer-motion";
import { useTheme } from "@/app/providers/ThemeProvider";
import { getTokens } from "@/app/theme/tokens";
import Breadcrumb from "@/components/Breadcrumb";
import SectionLabel from "@/components/SectionLabel";
import { useAppData } from "@/app/providers/DataProvider";
import { HIGHLIGHTS } from "@/content/highlights";

export default function AdminPage() {
  const { isDark } = useTheme();
  const { panel, softText, mutedText } = getTokens(isDark);
  const { participants } = useAppData();

  const starMember =
    HIGHLIGHTS.starOfMonthId == null
      ? null
      : participants.find((p) => p.id === HIGHLIGHTS.starOfMonthId) || null;

  const grinderMembers = HIGHLIGHTS.monthlyGrinders
    .map((id) => participants.find((p) => p.id === id))
    .filter(Boolean);

  return (
    <motion.div
      initial={{ opacity: 0, y: 18 }}
      animate={{ opacity: 1, y: 0 }}
      exit={{ opacity: 0, y: -18 }}
      transition={{ duration: 0.35 }}
      className="mx-auto max-w-7xl px-6 pb-24 pt-12 lg:px-8"
    >
      <Breadcrumb crumbs={["AWS Student Builders", "Admin (Static Mode)"]} softText={softText} />

      <SectionLabel
        eyebrow="Static deployment"
        title="Admin edits are disabled (seed-only website)"
        copy="This website is currently static. To update members, events, blog posts, or highlights, edit the seed/config files and redeploy."
        mutedText={mutedText}
      />

      <div className={`mt-10 rounded-3xl border ${panel} p-6 lg:p-8`}>
        <div className="rounded-3xl border border-amber-300/30 bg-amber-500/10 p-5">
          <p className="text-[10px] font-black uppercase tracking-[0.28em] text-amber-200">
            Static Mode Enabled
          </p>
          <p className={`mt-2 text-sm ${mutedText}`}>
            No backend is connected yet, so the admin panel is read-only to keep the website consistent for all users.
          </p>
        </div>

        <div className="mt-8 grid gap-6 lg:grid-cols-2">
          <div className={`rounded-3xl border ${panel} p-6`}>
            <h3 className="text-lg font-black tracking-[-0.04em]">Where to update data</h3>
            <ul className={`mt-4 space-y-2 text-sm ${softText}`}>
              <li>
                Members/Teams/Points: <code className="text-violet-300">src/features/participants/seed.ts</code>
              </li>
              <li>
                Events: <code className="text-violet-300">src/features/events/seed.ts</code>
              </li>
              <li>
                Blog posts: <code className="text-violet-300">src/features/blog/seed.ts</code>
              </li>
              <li>
                Star/Monthly grinders: <code className="text-violet-300">src/content/highlights.ts</code>
              </li>
            </ul>
          </div>

          <div className={`rounded-3xl border ${panel} p-6`}>
            <h3 className="text-lg font-black tracking-[-0.04em]">Current highlights</h3>

            <div className="mt-4 rounded-2xl border border-white/10 bg-white/[0.03] p-4">
              <p className="text-[10px] font-black uppercase tracking-[0.24em] text-violet-300">
                Star of the month ({HIGHLIGHTS.monthLabel})
              </p>
              <p className={`mt-2 text-sm ${softText}`}>
                {starMember ? starMember.name : "Not announced yet"}
              </p>
            </div>

            <div className="mt-4 rounded-2xl border border-white/10 bg-white/[0.03] p-4">
              <p className="text-[10px] font-black uppercase tracking-[0.24em] text-violet-300">
                Monthly grinders
              </p>
              {grinderMembers.length ? (
                <ul className={`mt-2 list-disc pl-5 text-sm ${softText}`}>
                  {grinderMembers.map((m: any) => (
                    <li key={m.id}>{m.name}</li>
                  ))}
                </ul>
              ) : (
                <p className={`mt-2 text-sm ${softText}`}>Not announced yet</p>
              )}
            </div>
          </div>
        </div>

        <p className={`mt-8 text-sm ${softText}`}>
          When backend is ready, we will re-enable real admin editing with Cognito authentication.
        </p>
      </div>
    </motion.div>
  );
}