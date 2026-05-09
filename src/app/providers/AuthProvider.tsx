//sessionStorage

import React, { createContext, useContext, useMemo, useState } from "react";
import type { AdminUser } from "@/features/participants/types";

type Role = "guest" | "admin";
const ADMIN_USER: AdminUser = { email: "admin@awsbuilders.com", name: "Admin" };
const ADMIN_PASS = "aws2026";
const SS_ROLE = "role";
const SS_USER = "user";

const Ctx = createContext<{
  role: Role;
  user: AdminUser | null;
  canAdmin: boolean;
  enterGuest: () => void;
  loginAdmin: (email: string, password: string) => { ok: boolean; error?: string };
  logout: () => void;
} | null>(null);

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [role, setRole] = useState<Role>(() => (sessionStorage.getItem(SS_ROLE) as Role) || "guest");
  const [user, setUser] = useState<AdminUser | null>(() => {
    const raw = sessionStorage.getItem(SS_USER);
    return raw ? JSON.parse(raw) : null;
  });

  const canAdmin = role === "admin" && user?.email === ADMIN_USER.email;

  const api = useMemo(() => ({
    role,
    user,
    canAdmin,
    enterGuest() {
      setRole("guest"); setUser(null);
      sessionStorage.setItem(SS_ROLE, "guest");
      sessionStorage.removeItem(SS_USER);
    },
    loginAdmin(email: string, password: string) {
      if (email.trim().toLowerCase() === ADMIN_USER.email && password === ADMIN_PASS) {
        setRole("admin"); setUser(ADMIN_USER);
        sessionStorage.setItem(SS_ROLE, "admin");
        sessionStorage.setItem(SS_USER, JSON.stringify(ADMIN_USER));
        return { ok: true };
      }
      return { ok: false, error: "Invalid credentials." };
    },
    logout() {
      setRole("guest"); setUser(null);
      sessionStorage.setItem(SS_ROLE, "guest");
      sessionStorage.removeItem(SS_USER);
    },
  }), [role, user, canAdmin]);

  return <Ctx.Provider value={api}>{children}</Ctx.Provider>;
}

export function useAuth() {
  const ctx = useContext(Ctx);
  if (!ctx) throw new Error("useAuth must be used inside AuthProvider");
  return ctx;
}