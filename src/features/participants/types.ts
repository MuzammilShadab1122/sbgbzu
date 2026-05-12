export type TeamKey =
  | "Core"
  | "Technical"
  | "Events"
  | "Operations"
  | "Marketing"
  | "Media"
  | "Design"
  | "Media & Design"
  | "Events & Operations"
  | "General";

export type Level = "Lead" | "Core" | "Builder" | "Developer";

export interface Participant {
  id: number;
  name: string;
  role: string;
  points?: number;

  team: TeamKey;        // REQUIRED for static team directory
  level: Level;         // Lead/Core/Builder

  campus?: string;      // "BZU"
  responsibilities?: string;       // optional, good for profiles
  image?: string;       // /images/...
  desc?: string;

  swags?: string[];     // optional (future UI)
}

export interface AdminUser { email: string; name: string; }