import { AnimatePresence } from "framer-motion";
import { Navigate, Route, Routes, useLocation } from "react-router-dom";
import { useAuth } from "@/app/providers/AuthProvider";

import GatePage from "@/pages/gate/GatePage";
import DashboardLayout from "@/layouts/DashboardLayout";
import HomePage from "@/pages/dashboard/HomePage";
import TeamPage from "@/pages/dashboard/TeamPage";
import LeaderboardPage from "@/pages/dashboard/LeaderboardPage";
import EventsPage from "@/pages/dashboard/EventsPage";
import BlogPage from "@/pages/dashboard/BlogPage";
import AdminPage from "@/pages/dashboard/AdminPage";

function RequireAdmin({ children }: { children: React.ReactNode }) {
  const { canAdmin } = useAuth();
  return canAdmin ? <>{children}</> : <Navigate to="/login" replace />;
}

export default function App() {
  const location = useLocation();

  return (
    <AnimatePresence mode="wait">
      <Routes location={location} key={location.pathname}>
        <Route path="/login" element={<GatePage />} />

        <Route path="/" element={<DashboardLayout />}>
          <Route index element={<HomePage />} />
          <Route path="team" element={<TeamPage />} />
          <Route path="leaderboard" element={<LeaderboardPage />} />
          <Route path="events" element={<EventsPage />} />
          <Route path="blog" element={<BlogPage />} />
          <Route
            path="admin"
            element={
              <RequireAdmin>
                <AdminPage />
              </RequireAdmin>
            }
          />
        </Route>

        <Route path="*" element={<Navigate to="/" replace />} />
      </Routes>
    </AnimatePresence>
  );
}