import type { Participant } from "@/features/participants/types";
import { initials } from "@/lib/format";
import { gradients } from "@/features/participants/seed";

export default function Avatar({ p, size = "md" }: { p: Participant; size?: "sm" | "md" | "lg" }) {
  const sz = size === "lg" ? "h-16 w-16 text-lg" : size === "sm" ? "h-10 w-10 text-xs" : "h-12 w-12 text-sm";
  return (
    <div
      className={`${sz} grid shrink-0 place-items-center rounded-full border border-white/20 font-black text-white shadow-lg shadow-violet-950/30`}
      style={{ backgroundImage: gradients[p.id % gradients.length] }}
    >
      {initials(p.name)}
    </div>
  );
}