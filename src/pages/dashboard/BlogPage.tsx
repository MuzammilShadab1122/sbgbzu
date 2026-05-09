import { useMemo, useState } from "react";
import { motion } from "framer-motion";

import { useTheme } from "@/app/providers/ThemeProvider";
import { useAppData } from "@/app/providers/DataProvider";
import { getTokens } from "@/app/theme/tokens";

import Breadcrumb from "@/components/Breadcrumb";
import SectionLabel from "@/components/SectionLabel";

export default function BlogPage() {
  const { isDark } = useTheme();
  const { posts } = useAppData();
  const { panel, softText, mutedText } = getTokens(isDark);

  const [query, setQuery] = useState("");
  const [category, setCategory] = useState<string>("All");

  const categories = useMemo(() => {
    const set = new Set(posts.map((p) => p.category));
    return ["All", ...Array.from(set)];
  }, [posts]);

  const filtered = useMemo(() => {
    const q = query.trim().toLowerCase();
    return posts.filter((p) => {
      const matchQ =
        !q ||
        [p.title, p.excerpt, p.category, p.date]
          .join(" ")
          .toLowerCase()
          .includes(q);

      const matchCat = category === "All" || p.category === category;
      return matchQ && matchCat;
    });
  }, [posts, query, category]);

  return (
    <motion.div
      initial={{ opacity: 0, y: 18 }}
      animate={{ opacity: 1, y: 0 }}
      exit={{ opacity: 0, y: -18 }}
      transition={{ duration: 0.35 }}
      className="mx-auto max-w-7xl px-6 pb-24 pt-12 lg:px-8"
    >
      <Breadcrumb crumbs={["AWS Student Builders", "Blog"]} softText={softText} />

      <SectionLabel
        eyebrow="Updates"
        title="Announcements, event recaps, and program notes"
        copy="Static blog posts. Update them by editing src/features/blog/seed.ts and redeploying."
        mutedText={mutedText}
      />

      <div className="mt-10 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <input
          value={query}
          onChange={(e) => setQuery(e.target.value)}
          placeholder="Search posts…"
          className={`w-full rounded-full border px-5 py-3 text-sm outline-none transition sm:max-w-sm ${
            isDark
              ? "border-white/10 bg-white/5 text-white placeholder:text-zinc-500 focus:border-violet-300"
              : "border-violet-950/10 bg-white text-[#160f24] placeholder:text-zinc-400 focus:border-violet-500"
          }`}
        />

        <select
          value={category}
          onChange={(e) => setCategory(e.target.value)}
          className={`rounded-full border px-4 py-3 text-sm outline-none transition ${
            isDark
              ? "border-white/10 bg-black/25 text-white focus:border-violet-300"
              : "border-violet-950/10 bg-white text-[#160f24] focus:border-violet-500"
          }`}
        >
          {categories.map((c) => (
            <option key={c} value={c}>{c}</option>
          ))}
        </select>
      </div>

      {filtered.length === 0 ? (
        <div className={`mt-10 rounded-3xl border ${panel} p-7`}>
          <p className="font-black">No posts found</p>
          <p className={`mt-2 text-sm ${softText}`}>
            Add posts in <code>src/features/blog/seed.ts</code>.
          </p>
        </div>
      ) : (
        <div className="mt-10 grid gap-6 md:grid-cols-2">
          {filtered.map((post) => (
            <article key={post.id} className={`rounded-3xl border ${panel} p-7`}>
              <p className="text-[10px] font-black uppercase tracking-[0.28em] text-violet-400">
                {post.category} / {post.date}
              </p>
              <h3 className="mt-4 text-2xl font-black leading-tight tracking-[-0.05em]">
                {post.title}
              </h3>
              <p className={`mt-3 leading-7 ${mutedText}`}>{post.excerpt}</p>
            </article>
          ))}
        </div>
      )}
    </motion.div>
  );
}