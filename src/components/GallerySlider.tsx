"use client";

import { useEffect, useRef, useState } from "react";

interface GallerySliderProps {
  images: string[];
  title: string;
}

export default function GallerySlider({ images, title }: GallerySliderProps) {
  const [current, setCurrent] = useState(0);
  const timerRef = useRef<ReturnType<typeof setInterval> | null>(null);

  const total = images.length;

  // Auto-advance every 3 s
  const startTimer = () => {
    if (timerRef.current) clearInterval(timerRef.current);
    timerRef.current = setInterval(() => {
      setCurrent((prev) => (prev + 1) % total);
    }, 3000);
  };

  useEffect(() => {
    if (total < 2) return;
    startTimer();
    return () => {
      if (timerRef.current) clearInterval(timerRef.current);
    };
  }, [total]);

  const go = (index: number) => {
    setCurrent((index + total) % total);
    startTimer(); // reset timer on manual nav
  };

  if (total === 0) return null;

  // Single image — just show it, no controls needed
  if (total === 1) {
    return (
      <div className="relative h-52 w-full overflow-hidden">
        <img
          src={images[0]}
          alt={`${title} gallery`}
          className="h-full w-full object-cover"
        />
        <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent" />
      </div>
    );
  }

  return (
    <div className="relative h-52 w-full overflow-hidden">
      {/* Slides */}
      {images.map((src, i) => (
        <img
          key={src + i}
          src={src}
          alt={`${title} photo ${i + 1}`}
          className={[
            "absolute inset-0 h-full w-full object-cover",
            "transition-opacity duration-700 ease-in-out",
            i === current ? "opacity-100" : "opacity-0",
          ].join(" ")}
        />
      ))}
<div className="pointer-events-none absolute left-0 top-1/2 z-[1] h-40 w-24 -translate-y-1/2 rounded-full bg-violet-600/20 blur-3xl" />
<div className="pointer-events-none absolute right-0 top-1/2 z-[1] h-40 w-24 -translate-y-1/2 rounded-full bg-fuchsia-600/20 blur-3xl" />
      {/* Prev / Next arrows */}
      <button
        onClick={(ev) => { ev.preventDefault(); go(current - 1); }}
        aria-label="Previous photo"
        className="absolute left-2 top-1/2 z-10 -translate-y-1/2 rounded-full bg-black/50 p-1.5 text-white backdrop-blur-sm transition hover:bg-black/70"
      >
        {/* left chevron */}
        <svg className="h-3.5 w-3.5" fill="none" stroke="currentColor" strokeWidth={2.5} viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
      </button>

      <button
        onClick={(ev) => { ev.preventDefault(); go(current + 1); }}
        aria-label="Next photo"
        className="absolute right-2 top-1/2 z-10 -translate-y-1/2 rounded-full bg-black/50 p-1.5 text-white backdrop-blur-sm transition hover:bg-black/70"
      >
        {/* right chevron */}
        <svg className="h-3.5 w-3.5" fill="none" stroke="currentColor" strokeWidth={2.5} viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" d="M9 5l7 7-7 7" />
        </svg>
      </button>

      {/* Dot indicators */}
      <div className="absolute bottom-2 left-1/2 z-10 flex -translate-x-1/2 gap-1.5">
        {images.map((_, i) => (
          <button
            key={i}
            onClick={(ev) => { ev.preventDefault(); go(i); }}
            aria-label={`Go to photo ${i + 1}`}
            className={[
              "h-1.5 rounded-full transition-all duration-300",
              i === current
                ? "w-5 bg-white"
                : "w-1.5 bg-white/40 hover:bg-white/70",
            ].join(" ")}
          />
        ))}
      </div>

      {/* Counter badge */}
      <span className="absolute left-3 top-3 z-10 rounded-full bg-black/50 px-2.5 py-0.5 text-[10px] font-black uppercase tracking-[0.18em] text-white backdrop-blur-sm">
        {current + 1} / {total}
      </span>
    </div>
  );
}