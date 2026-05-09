import type { BlogPost } from "./types";

export const initialPosts: BlogPost[] = [
  {
    id: 1,
    title: "Welcome to AWS Student Builder Group — BZU",
    category: "Announcement",
    date: "Apr 18, 2026",
    excerpt:
      "We’re building a student-driven cloud community focused on learning, collaboration, and shipping real AWS projects.",
  },
  {
    id: 2,
    title: "How points work in our leaderboard",
    category: "Program",
    date: "Apr 21, 2026",
    excerpt:
      "Points reward learning, participation, projects, mentorship, and consistency. This post explains the rules clearly.",
  },
  {
    id: 3,
    title: "Online orientation recap + next steps",
    category: "Event",
    date: "Apr 20, 2026",
    excerpt:
      "Quick recap of the online orientation and the next steps for each team.",
  },
  {
    id: 4,
    title: "Onboarding session highlights (Physical)",
    category: "Event",
    date: "Apr 27, 2026",
    excerpt:
      "Team induction, roadmap, and how we’ll execute events, technical tasks, and content.",
  },
  {
    id: 5,
    title: "Upcoming: May 12 AWS workshop — what to prepare",
    category: "Announcement",
    date: "May 09, 2026",
    excerpt:
      "Bring your laptop, AWS account (if possible), and be ready for hands-on practice. Details inside.",
  },
];