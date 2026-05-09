import React, { createContext, useContext, useMemo } from "react";
import { initialParticipants } from "@/features/participants/seed";
import { initialPosts } from "@/features/blog/seed";
import type { Participant } from "@/features/participants/types";
import type { BlogPost } from "@/features/blog/types";

type AppData = {
  participants: Participant[];
  posts: BlogPost[];
};

const Ctx = createContext<AppData | null>(null);

export function DataProvider({ children }: { children: React.ReactNode }) {
  const value = useMemo<AppData>(() => {
    return {
      participants: initialParticipants,
      posts: initialPosts,
    };
  }, []);

  return <Ctx.Provider value={value}>{children}</Ctx.Provider>;
}

export function useAppData() {
  const ctx = useContext(Ctx);
  if (!ctx) throw new Error("useAppData must be used inside DataProvider");
  return ctx;
}