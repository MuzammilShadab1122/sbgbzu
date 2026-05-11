import type { Partner, Portfolio, University } from "./types";

export const portfolios: Portfolio[] = [
  {
    id: 1,
    title: "Campus Cloud Portal",
    description:
      "A multi-tenant portal for club members, events, and learning resources hosted on AWS.",

    image: "/images/collaboration-portfolio.jpg",
  },
  {
    id: 2,
    title: "Event Registration Engine",
    description:
      "A practical registration and attendance flow for workshops, labs, and campus meetups.",
    image: "/images/event-workshop.jpg",
  },
  {
    id: 3,
    title: "Cloud Learning Tracker",
    description:
      "A student progress system for AWS labs, badges, learning paths, and certification readiness.",
    image: "/images/collaboration-portfolio.jpg",
  },
  {
    id: 4,
    title: "Secure Member Directory",
    description:
      "A role-aware profile directory for student builders, admins, events, points, and media.",
    image: "/images/event-hackathon.jpg",
  },
];

export const partners: Partner[] = [
  { id: 1, name: "GoClouds", focus: "Cloud learning paths and credits" },
  { id: 2, name: "BitsOps", focus: "DevOps partner" },
];

export const universities: University[] = [
  { id: 1, name: "BZU", location: "Multan", image: "/images/partners/bzu.png" },
];