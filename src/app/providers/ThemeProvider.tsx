//localStorage

import React, { createContext, useContext, useEffect, useMemo, useState } from "react";
type Theme = "dark" | "light";

const Ctx = createContext<{ theme: Theme; isDark: boolean; toggle: () => void } | null>(null);

export function ThemeProvider({ children }: { children: React.ReactNode }) {
  const [theme, setTheme] = useState<Theme>(() => (localStorage.getItem("theme") as Theme) || "dark");
  useEffect(() => localStorage.setItem("theme", theme), [theme]);

  const value = useMemo(() => ({
    theme,
    isDark: theme === "dark",
    toggle: () => setTheme(t => (t === "dark" ? "light" : "dark")),
  }), [theme]);

  return <Ctx.Provider value={value}>{children}</Ctx.Provider>;
}

export function useTheme() {
  const ctx = useContext(Ctx);
  if (!ctx) throw new Error("useTheme must be used inside ThemeProvider");
  return ctx;
}