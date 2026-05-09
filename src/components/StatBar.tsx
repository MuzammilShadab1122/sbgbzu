import { motion } from "framer-motion";

export default function StatBar({
  label, display, value, max, color, isAnimated, index, panel,
}: {
  label: string;
  display: string;
  value: number;
  max: number;
  color: string;
  isAnimated: boolean;
  index: number;
  panel: string;
}) {
  const pct = Math.min(100, (value / max) * 100);

  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.5, delay: index * 0.08 }}
      className={`rounded-3xl border ${panel} p-6`}
    >
      <div className="mb-4 flex items-end justify-between gap-4">
        <p className="text-sm font-bold">{label}</p>
        <p className="text-3xl font-black tracking-[-0.05em]">{display}</p>
      </div>

      <div className="h-2.5 overflow-hidden rounded-full bg-white/10">
        <motion.div
          initial={{ width: 0 }}
          animate={{ width: isAnimated ? `${pct}%` : "0%" }}
          transition={{ duration: 1.3, ease: "easeOut", delay: 0.1 + index * 0.1 }}
          className={`h-full rounded-full bg-gradient-to-r ${color}`}
        />
      </div>

      <p className="mt-2 text-right text-[10px] font-black uppercase tracking-[0.2em] text-zinc-600">
        Target {max}
      </p>
    </motion.div>
  );
}