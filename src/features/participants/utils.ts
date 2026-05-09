import type { Participant, TeamScope, TeamKey } from "./types";

export function getScope(p: Participant): TeamScope {
  // If explicitly set, trust it
  if (p.scope) return p.scope;

  // Lead/Core are cross-functional by default (your rule)
  if (p.level === "Lead" || p.level === "Core") return "ALL";

  return "TEAM";
}

export function isCrossFunctional(p: Participant): boolean {
  return getScope(p) === "ALL";
}

export function getTeam(p: Participant): TeamKey {
  return p.team || "General";
}