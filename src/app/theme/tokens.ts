export function getTokens(isDark: boolean) {
  return {
    bg: isDark ? "bg-[#05030a] text-white" : "bg-[#f9f7ff] text-[#160f24]",
    panel: isDark
      ? "border-white/10 bg-white/[0.045] shadow-2xl shadow-violet-950/25"
      : "border-violet-950/10 bg-white/80 shadow-2xl shadow-violet-200/50",
    mutedText: isDark ? "text-zinc-300" : "text-zinc-600",
    softText: isDark ? "text-zinc-400" : "text-zinc-500",
    lineColor: isDark ? "border-white/10" : "border-violet-950/10",
  };
}