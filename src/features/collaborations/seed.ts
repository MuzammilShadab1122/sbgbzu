import type { Partner, Portfolio, University } from "./types";

export const portfolios: Portfolio[] = [
  {
    id: 1,
    title: "Campus Cloud Portal",
    description:
      "A multi-tenant portal for student chapters, events, and learning resources hosted on AWS.",
    members: "Aleena, Aqib, Rizwan, Muzammil",
    impact: "1.2k active learners",
    image: "/images/collaboration-portfolio.jpg",
  },
  {
    id: 2,
    title: "Event Registration Engine",
    description:
      "A practical registration and attendance flow for workshops, labs, and campus meetups.",
    members: "Fatima Noor, Hassan Ali, Ayesha Khan",
    impact: "35+ events supported",
    image: "/images/event-workshop.jpg",
  },
  {
    id: 3,
    title: "Cloud Learning Tracker",
    description:
      "A student progress system for AWS labs, badges, learning paths, and certification readiness.",
    members: "Muzammil, Omer Shah, Hira Batool",
    impact: "4.8k learning actions",
    image: "/images/collaboration-portfolio.jpg",
  },
  {
    id: 4,
    title: "Secure Member Directory",
    description:
      "A role-aware profile directory for student builders, admins, events, points, and media.",
    members: "Bilal Ahmed, Zain Malik, Saad Qureshi",
    impact: "26 universities ready",
    image: "/images/event-hackathon.jpg",
  },
];

export const partners: Partner[] = [
  { id: 1, name: "GoClouds", focus: "Cloud learning paths and credits" },
  { id: 2, name: "BitsOps", focus: "DevOps partner" },
];

export const universities: University[] = [
  { id: 1, name: "BZU", location: "Multan", image: "/images/sbg-logo.png" },
  { id: 2, name: "NUST", location: "Islamabad", image: "/images/sbg-logo.png" },
  { id: 3, name: "IIUI", location: "Islamabad", image: "/images/sbg-logo.png" },
  { id: 4, name: "UET Lahore", location: "Lahore", image: "/images/sbg-logo.png" },
  { id: 5, name: "IBA Karachi", location: "Karachi", image: "/images/sbg-logo.png" },
  { id: 6, name: "GIKI", location: "Swabi", image: "/images/sbg-logo.png" },
];