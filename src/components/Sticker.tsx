import { motion, useReducedMotion } from "framer-motion";

type StickerVariant = "decal" | "watermark";

export default function Sticker({
  src,
  className,
  floatDelay = 0,
  alt = "",
  isDark,
  tilt = 0,
  variant = "decal",
  intensity = 10, // float amplitude in px
}: {
  src: string;
  className: string;
  floatDelay?: number;
  alt?: string;
  isDark: boolean;
  tilt?: number;
  variant?: StickerVariant;
  intensity?: number;
}) {
  const reduceMotion = useReducedMotion();

  const isWatermark = variant === "watermark";

  const frame = isWatermark
    ? "bg-transparent border-transparent shadow-none"
    : isDark
      ? "bg-white/[0.035] border-white/10 shadow-[0_28px_80px_rgba(0,0,0,0.45)]"
      : "bg-black/[0.03] border-black/10 shadow-[0_18px_55px_rgba(0,0,0,0.14)]";

  const sheen = isWatermark
    ? "opacity-0"
    : isDark
      ? "opacity-60"
      : "opacity-35";

  const imgStyle = isWatermark
    ? (isDark ? "opacity-20 saturate-90" : "opacity-15 saturate-75")
    : (isDark ? "opacity-75 saturate-100" : "opacity-55 saturate-90");

  return (
    <motion.div
      aria-hidden={alt ? undefined : true}
      initial={{ opacity: 0, scale: 0.98, y: 6 }}
      animate={{ opacity: 1, scale: 1, y: 0 }}
      transition={{ duration: 0.55, delay: 0.12 + floatDelay, ease: "easeOut" }}
      className={`pointer-events-none absolute z-0 ${className}`}
      style={{ transform: "translateZ(0)" }}
    >
      <motion.div
        animate={
          reduceMotion
            ? undefined
            : {
                y: [0, -intensity, 0],
                rotate: [tilt, tilt + 1.2, tilt],
              }
        }
        transition={
          reduceMotion
            ? undefined
            : { duration: 8, repeat: Infinity, ease: "easeInOut", delay: floatDelay }
        }
        className={[
          "relative h-full w-full",
          "rounded-[26px] border backdrop-blur-md",
          frame,
          "overflow-hidden", // keeps everything clean
        ].join(" ")}
        style={{ willChange: "transform" }}
      >
        {/* subtle inner border */}
        {!isWatermark && (
          <div className="pointer-events-none absolute inset-[1px] rounded-[24px] ring-1 ring-white/10" />
        )}

        {/* soft sheen / light */}
        <div
          className={[
            "pointer-events-none absolute inset-0",
            "bg-[radial-gradient(120%_80%_at_20%_10%,rgba(255,255,255,0.22),transparent_55%)]",
            sheen,
          ].join(" ")}
        />

        {/* image */}
        <img
          src={src}
          alt={alt}
          draggable={false}
          className={[
            "h-full w-full object-contain",
            "p-4", // consistent padding looks more premium
            imgStyle,
            "drop-shadow-[0_16px_34px_rgba(0,0,0,0.22)]",
          ].join(" ")}
        />
      </motion.div>
    </motion.div>
  );
}