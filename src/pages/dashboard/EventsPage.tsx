import { useMemo } from "react";
import { motion } from "framer-motion";
import GallerySlider from "@/components/GallerySlider";
import { useTheme } from "@/app/providers/ThemeProvider";
import { getTokens } from "@/app/theme/tokens";

import Breadcrumb from "@/components/Breadcrumb";
import SectionLabel from "@/components/SectionLabel";

import { events } from "@/features/events/seed";

function toTime(s: string) {
  const t = Date.parse(s);
  return Number.isFinite(t) ? t : 0;
}

export default function EventsPage() {
  const { isDark } = useTheme();
  const { panel, softText, mutedText } = getTokens(isDark);

  const { upcoming, past } = useMemo(() => {
    const upcoming = events
      .filter((e) => e.type === "upcoming")
      .slice()
      .sort((a, b) => toTime(a.date) - toTime(b.date));

    const past = events
      .filter((e) => e.type === "past")
      .slice()
      .sort((a, b) => toTime(b.date) - toTime(a.date));

    return { upcoming, past };
  }, []);

  return (
    <motion.div
      initial={{ opacity: 0, y: 18 }}
      animate={{ opacity: 1, y: 0 }}
      exit={{ opacity: 0, y: -18 }}
      transition={{ duration: 0.35 }}
      className="mx-auto max-w-7xl px-6 pb-24 pt-12 lg:px-8"
    >
      <Breadcrumb crumbs={["AWS Student Builders", "Events"]} softText={softText} />

      <SectionLabel
        eyebrow="Events"
        title="Upcoming sessions and past event gallery"
        copy="Track what’s coming next and explore highlights from our completed events."
        mutedText={mutedText}
      />

      {/* UPCOMING */}
      <section className="mt-14">
        <div className="flex items-end justify-between gap-6">
          <div>
            <h3 className="text-2xl font-black tracking-[-0.04em]">Upcoming events</h3>
            <p className={`mt-2 text-sm ${softText}`}>Registration links and details.</p>
          </div>
          <span className="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-[10px] font-black uppercase tracking-[0.18em]">
            {upcoming.length} upcoming
          </span>
        </div>

        {upcoming.length === 0 ? (
          <div className={`mt-6 rounded-3xl border ${panel} p-6`}>
            <p className="font-black">No upcoming events listed yet</p>
            <p className={`mt-2 text-sm ${softText}`}>Add upcoming events in <code>src/features/events/seed.ts</code>.</p>
          </div>
        ) : (
          <div className="mt-6 grid gap-4 lg:grid-cols-2">
            {upcoming.map((e) => (
              <article key={e.id} className={`overflow-hidden rounded-3xl border ${panel}`}>
  {/* image */}
  {e.image ? (
    <div className="relative h-44 w-full">
      <img
        src={e.image}
        alt={e.title}
        className="h-full w-full object-cover"
      />
      <div className="absolute inset-0 bg-gradient-to-t from-black/55 via-black/10 to-transparent" />
    </div>
  ) : (
    <div className="h-44 w-full bg-gradient-to-br from-violet-800/40 via-fuchsia-700/20 to-black" />
  )}

  <div className="p-6">
    <div className="flex items-start justify-between gap-4">
      <div>
        <p className="text-[10px] font-black uppercase tracking-[0.26em] text-violet-400">
          {e.date}
        </p>
        <h4 className="mt-2 text-xl font-black tracking-[-0.04em]">{e.title}</h4>
        <p className={`mt-2 text-sm ${softText}`}>{e.location}</p>
      </div>

      <span className="rounded-full border border-emerald-300/30 bg-emerald-500/10 px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-emerald-200">
        Upcoming
      </span>
    </div>

    <p className={`mt-4 leading-7 ${mutedText}`}>{e.description}</p>

    {e.link && (
      <a
        href={e.link}
        target="_blank"
        rel="noreferrer"
        className="mt-5 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-5 py-2.5 text-xs font-black uppercase tracking-[0.16em] hover:bg-white/10"
      >
        Open link →
      </a>
    )}
  </div>
</article>
            ))}
          </div>
        )}
      </section>

      {/* PAST */}
      <section className="mt-16">
        <div className="flex items-end justify-between gap-6">
          <div>
            <h3 className="text-2xl font-black tracking-[-0.04em]">Past event gallery</h3>
            <p className={`mt-2 text-sm ${softText}`}>Recaps, photos, and YouTube links.</p>
          </div>
          <span className="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-[10px] font-black uppercase tracking-[0.18em]">
            {past.length} past
          </span>
        </div>

        {past.length === 0 ? (
          <div className={`mt-6 rounded-3xl border ${panel} p-6`}>
            <p className="font-black">No past events yet</p>
        </div>
        ) : (
          <div className="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {past.map((e) => {
               const galleryImages: string[] =
                Array.isArray(e.gallery) && e.gallery.length > 0
                  ? e.gallery
                  : e.image
                  ? [e.image]
                  : [];

              
             return (
              
              <a
                key={e.id}
                
                target="_blank"
                rel="noreferrer"
                className="group relative overflow-hidden rounded-3xl border border-white/10 bg-black"
              >
 {galleryImages.length > 0 ? (
                    <GallerySlider images={galleryImages} title={e.title} />
                  ) : (
                    <>
                      <div className="h-52 w-full bg-gradient-to-br from-violet-800/40 via-fuchsia-700/20 to-black" />
                      <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent" />
                    </>
                  )}

                  {/* Text overlay — sits on top of the slider's gradient */}
                  <div className="absolute bottom-0 left-0 right-0 p-5">
                    <p className="text-sm font-black text-white">{e.title}</p>
                    <p className="mt-1 text-[10px] font-black uppercase tracking-[0.22em] text-violet-200">
                      {e.date} • {e.location}
                    </p>
                  </div>

                  {/* Watch badge */}
                  {e.link && (
                    <span className="absolute right-3 top-3 z-10 rounded-full bg-white px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-black">
                      Watch
                    </span>
                  )}
                </a>
              );
            })}
          </div>
        )}
      </section>
    </motion.div>
  );
}