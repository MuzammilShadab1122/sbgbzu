import { useState } from "react";
import { AnimatePresence, motion } from "framer-motion";
import { useNavigate } from "react-router-dom";
import { useAuth } from "@/app/providers/AuthProvider";
import Sticker from "@/components/Sticker";
import { useTheme } from "@/app/providers/ThemeProvider";
export default function GatePage() {
  const nav = useNavigate();
  const { enterGuest, loginAdmin } = useAuth();
const { isDark } = useTheme();
  const [showAdminForm, setShowAdminForm] = useState(false);
  const [gateEmail, setGateEmail] = useState("");
  const [gatePassword, setGatePassword] = useState("");
  const [gateError, setGateError] = useState("");

  function enterAsGuest() {
    enterGuest();
    nav("/");
  }

  function handleGateLogin(e: React.FormEvent) {
    e.preventDefault();
    const res = loginAdmin(gateEmail, gatePassword);
    if (!res.ok) {
      setGateError(res.error || "Invalid credentials");
      return;
    }
    setGateEmail(""); setGatePassword(""); setGateError("");
    nav("/admin");
  }

  return (
    <motion.div
      key="gate"
      initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }}
      transition={{ duration: 0.4 }}
      className="relative flex min-h-screen items-center justify-center overflow-hidden p-6"
      style={{ background: "linear-gradient(110deg,#05030a 0%,#150826 60%,#05030a 100%)" }}
    >
      {/* ambient glows */}
      <div className="pointer-events-none absolute left-1/4 top-1/4 h-96 w-96 rounded-full bg-purple-700/20 blur-3xl" />
      <div className="pointer-events-none absolute bottom-1/4 right-1/4 h-96 w-96 rounded-full bg-fuchsia-500/15 blur-3xl" />

   {/* ✅ Stickers layer  */}
      <div className="pointer-events-none absolute inset-0 z-0 hidden lg:block">
        <Sticker
          isDark={isDark ?? true}
          src="/images/stickers/aws.png"
          className="-left-1 -top-5 h-28 w-28 opacity-55"
          floatDelay={0.0}
          tilt={-10}
        />
        <Sticker
          isDark={isDark ?? true}
          src="/images/stickers/cloud.png"
          className="-right-6 top-20 h-24 w-24 opacity-35"
          floatDelay={0.15}
          tilt={12}
        />
      </div>

      <div className="relative z-10 w-full max-w-4xl text-center">
        <img src={"/images/sbg-logo.png"} alt="Community logo" className="mx-auto mb-8 h-32 w-auto rounded-full" />

        <h1 className="text-4xl font-black leading-none tracking-[-0.06em] text-white sm:text-6xl">
          AWS Student Builder Group, BZU
        </h1>
        <p className="mx-auto mt-4 max-w-xl text-sm leading-7 text-zinc-400">
          Select your access role to enter the platform.
        </p>

        <div className="mt-12 grid gap-6 text-left sm:grid-cols-2">
          {/* Guest */}
          <motion.div
            whileHover={{ y: -4 }}
            transition={{ type: "spring", stiffness: 300 }}
            className="flex flex-col justify-between rounded-3xl border border-white/10 bg-white/[0.045] p-8 shadow-2xl shadow-violet-950/25"
          >
            <div>
              <div className="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-500/10 border border-violet-400/20">
                <svg className="h-6 w-6 text-violet-300" fill="none" stroke="currentColor" strokeWidth={2} viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" d="M15 12H9m6 0a6 6 0 11-12 0 6 6 0 0112 0z" />
                  <path strokeLinecap="round" strokeLinejoin="round" d="M21 21l-4.35-4.35" />
                </svg>
              </div>
              <h3 className="text-2xl font-black tracking-[-0.04em] text-white">Guest Login</h3>
              <p className="mt-3 text-sm leading-7 text-zinc-400">
                Browse and explore where innovation begins.
              </p>
            </div>

            <button
              onClick={enterAsGuest}
              className="mt-8 w-full cursor-pointer rounded-full border border-white/20 bg-white/10 px-5 py-3 text-xs font-black uppercase tracking-[0.18em] text-white transition hover:bg-white/20"
            >
              Enter as guest →
            </button>
          </motion.div>

          {/* Admin */}
          <motion.div
            whileHover={{ y: showAdminForm ? 0 : -4 }}
            transition={{ type: "spring", stiffness: 300 }}
            className="flex flex-col justify-between rounded-3xl border border-white/10 bg-white/[0.045] p-8 shadow-2xl shadow-violet-950/25"
          >
            <div>
              <div className="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl border border-violet-400/20 bg-violet-500/10">
                <svg className="h-6 w-6 text-violet-300" fill="none" stroke="currentColor" strokeWidth={2} viewBox="0 0 24 24">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                  <path strokeLinecap="round" strokeLinejoin="round" d="M7 11V7a5 5 0 0110 0v4" />
                </svg>
              </div>
              <h3 className="text-2xl font-black tracking-[-0.04em] text-white">Admin Login</h3>
              <p className="mt-3 text-sm leading-7 text-zinc-400">
                Authorized access allowed only for chapter leads.
              </p>

              <AnimatePresence>
                {showAdminForm && (
                  <motion.form
                    key="admin-form"
                    initial={{ opacity: 0, height: 0 }}
                    animate={{ opacity: 1, height: "auto" }}
                    exit={{ opacity: 0, height: 0 }}
                    transition={{ duration: 0.3 }}
                    onSubmit={handleGateLogin}
                    className="mt-6 space-y-3 overflow-hidden"
                  >
                    <input
                      type="email"
                      required
                      value={gateEmail}
                      onChange={e => setGateEmail(e.target.value)}
                      placeholder="Enter admin email:"
                      className="w-full rounded-full border border-white/10 bg-black/35 px-4 py-2.5 text-xs text-white outline-none focus:border-violet-400 placeholder:text-zinc-600"
                    />
                    <input
                      type="password"
                      required
                      value={gatePassword}
                      onChange={e => setGatePassword(e.target.value)}
                      placeholder="Enter password:"
                      className="w-full rounded-full border border-white/10 bg-black/35 px-4 py-2.5 text-xs text-white outline-none focus:border-violet-400 placeholder:text-zinc-600"
                    />
                    {gateError && (
                      <p className="rounded-xl bg-red-500/10 border border-red-500/20 px-3 py-2 text-xs text-red-300">
                        {gateError}
                      </p>
                    )}
                    <div className="flex gap-2">
                      <button
                        type="submit"
                        className="flex-1 cursor-pointer rounded-full bg-violet-500 py-2.5 text-xs font-black uppercase tracking-[0.16em] text-white hover:bg-violet-400"
                      >
                        Verify & Enter
                      </button>
                      <button
                        type="button"
                        onClick={() => { setShowAdminForm(false); setGateError(""); setGateEmail(""); setGatePassword(""); }}
                        className="cursor-pointer rounded-full border border-white/10 px-4 py-2.5 text-xs font-bold text-zinc-400 hover:text-white"
                      >
                        Cancel
                      </button>
                    </div>
                  </motion.form>
                )}
              </AnimatePresence>
            </div>

            {!showAdminForm && (
              <button
                onClick={() => setShowAdminForm(true)}
                className="mt-8 w-full cursor-pointer rounded-full bg-violet-500 px-5 py-3 text-xs font-black uppercase tracking-[0.18em] text-white transition hover:bg-violet-400"
              >
                Admin sign in →
              </button>
            )}
          </motion.div>
        </div>

        <p className="mt-10 text-xs text-zinc-600">
          All Rights Reserved © 2026 • AWS Student Builder Group, BZU
        </p>
      </div>
    </motion.div>
  );
}