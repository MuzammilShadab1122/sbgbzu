import type { TeamKey } from "./types";

export const TEAM_ORDER: TeamKey[] = [
  "Core",
  "Technical",
  "Events",
  "Operations",
  "Marketing",
  "Media",
  "Design",
  "Media & Design",
  "Events & Operations",
  "General",
];

export const TEAM_META: Record<TeamKey, { title: string; blurb: string }> = {
  Core: { title: "Core Team", blurb: "Leads the chapter and supports all departments." },
  Technical: { title: "Technical Team", blurb: "Builds products, cloud projects, and deployments." },
  Events: { title: "Events Team", blurb: "Plans and executes workshops, onboarding, and sessions." },
  Operations: { title: "Operations Team", blurb: "Coordinates internal processes and logistics." },
  Marketing: { title: "Marketing Team", blurb: "Promotions, outreach, and community growth." },
  Media: { title: "Media Team", blurb: "Content, social posts, documentation, and coverage." },
  Design: { title: "Design Team", blurb: "Branding, posters, decks, and visual assets." },
  "Media & Design": { title: "Media & Design", blurb: "Combined media/design responsibility." },
  "Events & Operations": { title: "Events & Operations", blurb: "Combined events and operations responsibility." },
  General: { title: "General Members", blurb: "Builders and learners across different tracks." },
};