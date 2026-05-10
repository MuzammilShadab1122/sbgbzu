import { NavLink, Outlet, useNavigate } from "react-router-dom";
import { useTheme } from "@/app/providers/ThemeProvider";
import { useAuth } from "@/app/providers/AuthProvider";
import { getTokens } from "@/app/theme/tokens";
import { FaGithub, FaLinkedin, FaInstagram, FaMeetup } from 'react-icons/fa'; 
import Sticker from "@/components/Sticker";
export default function DashboardLayout() {
  const { isDark, toggle } = useTheme();
  const { canAdmin, logout } = useAuth();
  const nav = useNavigate();

  const { bg, softText } = getTokens(isDark);

  const headerBg = isDark
    ? "border-white/10 bg-black/45 shadow-2xl shadow-black/40"
    : "border-violet-950/10 bg-white/75 shadow-2xl shadow-violet-200/50";

  const navBtn = ({ isActive }: { isActive: boolean }) =>
    `rounded-full px-4 py-2 text-[10px] font-black uppercase tracking-[0.16em] transition ${
      isActive
        ? (isDark ? "bg-white text-black" : "bg-violet-950 text-white")
        : (isDark ? "text-zinc-400 hover:text-white" : "text-zinc-600 hover:text-violet-900")
    }`;

  return (
    <main className={`${bg} min-h-screen overflow-x-hidden font-sans antialiased selection:bg-violet-400/30`}>
      {/* HEADER */}
      <header className="fixed inset-x-0 top-0 z-40 px-1 py-1 lg:px-8">
        <div className={`mx-auto flex max-w-7xl items-center gap-3 rounded-full border px-4 py-2.5 backdrop-blur-2xl lg:px-5 ${headerBg}`}>
          {/* Exit */}
          <button
            onClick={() => nav("/login")}
            className={`flex items-center gap-1.5 rounded-full border px-3 py-2 text-[10px] font-black uppercase tracking-[0.16em] transition ${
              isDark
                ? "border-white/10 text-zinc-400 hover:text-white hover:bg-white/5"
                : "border-violet-950/10 text-zinc-500 hover:text-violet-900 hover:bg-violet-50"
            }`}
          >
            <svg className="h-3 w-3" fill="none" stroke="currentColor" strokeWidth={2.5} viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Exit
          </button>

          {/* Logo */}
          <button onClick={() => nav("/")} className="flex items-center gap-2.5">
            <img src="/images/sbg-logo.png" alt="Community logo" className="h-10 w-10 rounded-full object-cover" />
            <span className="hidden sm:block">
              <span className="block text-xs font-black uppercase tracking-[0.28em] text-violet-400">
                AWS Student Builder Group
              </span>
              <span className={`block text-[10px] font-bold uppercase tracking-[0.18em] ${softText}`}>
                Bahauddin Zakariya University
              </span>
            </span>
          </button>

          {/* Nav */}
            <div className="pointer-events-none absolute inset-0 z-0 hidden lg:block">
  </div>
        <nav className="ml-auto hidden items-center gap-1 rounded-full border border-white/10 bg-white/[0.04] p-1 lg:flex">
  <NavLink to="/" end className={navBtn}>Home</NavLink>
  <NavLink to="/team" className={navBtn}>Teams</NavLink>
  <NavLink to="/leaderboard" className={navBtn}>Leaderboard</NavLink>
  <NavLink to="/events" className={navBtn}>Events</NavLink>
  <NavLink to="/blog" className={navBtn}>Blog</NavLink>
  {canAdmin && <NavLink to="/admin" className={navBtn}>Admin</NavLink>}
</nav>

          {/* Right actions */}
          <div className="flex items-center gap-2">
            {/* Role badge */}
            <span
              className={`hidden sm:flex items-center gap-2 rounded-full border px-3 py-1.5 text-[10px] font-bold uppercase tracking-widest ${
                canAdmin ? "border-violet-400/30 text-violet-300" : "border-white/10 text-zinc-500"
              }`}
            >
              <span className={`h-1.5 w-1.5 rounded-full ${canAdmin ? "bg-violet-400" : "bg-zinc-500"}`} />
              {canAdmin ? "Admin" : "Guest"}
            </span>

            {canAdmin ? (
              <button
                onClick={() => { logout(); nav("/"); }}
                className="flex cursor-pointer items-center gap-1.5 rounded-full border border-red-400/30 bg-red-500/10 px-4 py-2 text-[10px] font-black uppercase tracking-[0.16em] text-red-300 transition hover:bg-red-500/20"
              >
                Logout
              </button>
            ) : (
              <button
                onClick={() => nav("/login")}
                className="flex cursor-pointer items-center gap-1.5 rounded-full border border-violet-400/30 px-4 py-2 text-[10px] font-black uppercase tracking-[0.16em] text-violet-300 transition hover:bg-violet-500/10"
              >
                Admin login
              </button>
            )}

            {/* <button
              onClick={toggle}
              className={`rounded-full border px-3 py-2 text-[10px] font-black uppercase tracking-[0.14em] transition ${
                isDark
                  ? "border-white/10 text-zinc-400 hover:text-white hover:bg-white/5"
                  : "border-violet-950/10 text-zinc-500 hover:text-violet-900 hover:bg-violet-50"
              }`}
            >
              {isDark ? "Light" : "Dark"}
            </button> */}
          </div>
        </div>
      </header>

      {/* PAGE */}
      <div className="pt-16">
        
        <Outlet />
      </div>

      {/* FOOTER */}
      <footer
  className={`relative overflow-hidden border-t ${
    isDark ? "border-white/10" : "border-violet-950/10"
  }`}
>
  {/* ✅ Stickers behind footer content */}
  <div className="pointer-events-none absolute inset-0 z-0 hidden lg:block">
    <Sticker
      isDark={isDark}
      src="/images/stickers/aws.png"
      className="-right-8 -top-3 h-24 w-24 opacity-20"
      floatDelay={0.05}
      tilt={8}
    />
    <Sticker
      isDark={isDark}
      src="/images/stickers/cloud.png"
      className="-right-7 bottom-15 h-24 w-24 opacity-16"
      floatDelay={0.18}
      tilt={-10}
    /> 
  </div>

  <div className="mx-auto max-w-7xl px-6 py-12 lg:px-8">
    <div className="grid grid-cols-1 gap-10 md:grid-cols-4">
      {/* Brand */}
      <div className="md:col-span-2">
        <div className="flex items-center gap-3">
          <img
            src="/images/sbg-logo.png"
            alt="AWS SBG BZU logo"
            className="h-10 w-10 rounded-full object-contain"
          />
          <div>
            <p className={`text-base font-black tracking-tight ${isDark ? "text-white" : "text-violet-950"}`}>
              AWS Student Builder Group BZU
            </p>
            <p className={`mt-1 text-xs font-bold uppercase tracking-[0.22em] ${softText}`}>
              Empowering innovation through AWS
            </p>
          </div>
        </div>

        <p className={`mt-4 max-w-xl text-sm leading-7 ${softText}`}>
          A comprehensive platform for tracking excellence, celebrating grinders, and sharing cloud learning updates across the community.
        </p>
      </div>

      {/* Links */}
      <div>
        <h3 className={`mb-4 text-sm font-black uppercase tracking-[0.24em] ${isDark ? "text-white" : "text-violet-950"}`}>
          Links
        </h3>

        <ul className={`space-y-2 text-sm ${softText}`}>
          <li>
            <button onClick={() => nav("/")} className="hover:text-violet-400 transition">
              Home
            </button>
          </li>
          <li>
            <button onClick={() => nav("/team")} className="hover:text-violet-400 transition">
              Teams
            </button>
          </li>
          <li>
            <button onClick={() => nav("/leaderboard")} className="hover:text-violet-400 transition">
              Leaderboard
            </button>
          </li>
          <li>
            <button onClick={() => nav("/events")} className="hover:text-violet-400 transition">
              Events
            </button>
          </li>
          <li>
            <button onClick={() => nav("/blog")} className="hover:text-violet-400 transition">
              Blog
            </button>
          </li>

          <li className="pt-2">
            {canAdmin ? (
              <button
                onClick={() => { logout(); nav("/"); }}
                className="text-red-400 hover:text-red-300 transition font-bold"
              >
                Sign out
              </button>
            ) : (
              <button
                onClick={() => nav("/login")}
                className="text-violet-400 hover:text-violet-300 transition font-bold"
              >
                Admin login
              </button>
            )}
          </li>
        </ul>
      </div>

      {/* Connect */}
      <div>
        <h3 className={`mb-4 text-sm font-black uppercase tracking-[0.24em] ${isDark ? "text-white" : "text-violet-950"}`}>
          Connect
        </h3>

        <ul className={`space-y-2 text-sm ${softText} align-middle`}>
          {/* <li>
            <a href="#" className="hover:text-orange-400 transition-colors" target="_blank" rel="noreferrer">
              <FaGithub className="text-lg opacity-90 group-hover:opacity-100 text-purple-400" />
            </a>
          </li> */}
          <li>
            <a
            href="https://www.linkedin.com/company/aws-cloud-club-bzu-multan/"
              className="hover:text-orange-400 transition-colors"
              target="_blank"
              rel="noreferrer"
            >
              <FaLinkedin  className="text-lg text-[#0A66C2]" />
            </a>
          </li>
          <li>
            <a
             href="https://www.instagram.com/aws_sbg_bzu" 
              className="hover:text-orange-400 transition-colors"
              target="_blank"
              rel="noreferrer"
            >
              <FaInstagram className="text-lg text-pink-400"/>
            </a>
          </li>
          <li>
            <a
             href="https://www.meetup.com/aws-sbg-at-bahauddinzakariya-univ-multan"
              className="hover:text-orange-400 transition-colors"
              target="_blank"
              rel="noreferrer"
            >
              <FaMeetup className="text-lg text-red-400" />
            </a>
        </li>
        </ul>
      </div>
    </div>

    {/* Bottom bar */}
    <div className={`mt-10 pt-6 border-t ${isDark ? "border-white/10" : "border-violet-950/10"} flex flex-col gap-2 md:flex-row md:items-center md:justify-between`}>
      <p className={`text-xs ${softText}`}>© 2026 AWS Student Builder Group, BZU. All rights reserved.</p>
      <p className={`text-xs ${softText}`}>Designed & Developed by Technical Team.</p>
    </div>
  </div>
</footer>

    </main>
  );
}