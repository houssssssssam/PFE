import { Outlet, NavLink, useNavigate } from 'react-router-dom';
import { motion } from 'framer-motion';
import {
  LayoutDashboard, MessageSquare, Users, Bell,
  Settings, LogOut, Menu, X, Wallet, UserCog,
  ShieldCheck, CreditCard, FolderOpen,
} from 'lucide-react';
import { useState } from 'react';
import useAuthStore from '../stores/authStore';
import './AppLayout.css';

const userNavItems = [
  { to: '/dashboard',     icon: LayoutDashboard, label: 'Tableau de bord' },
  { to: '/conversations', icon: MessageSquare,   label: 'Conversations' },
  { to: '/experts',       icon: Users,           label: 'Experts' },
  { to: '/notifications', icon: Bell,            label: 'Notifications' },
  { to: '/settings',      icon: Settings,        label: 'Paramètres' },
];

const expertNavItems = [
  { to: '/expert/dashboard', icon: LayoutDashboard, label: 'Tableau de bord' },
  { to: '/conversations',    icon: MessageSquare,   label: 'Conversations' },
  { to: '/expert/profile',   icon: UserCog,         label: 'Mon profil' },
  { to: '/expert/wallet',    icon: Wallet,          label: 'Portefeuille' },
  { to: '/notifications',    icon: Bell,            label: 'Notifications' },
];

const adminNavItems = [
  { to: '/admin/dashboard',     icon: LayoutDashboard, label: 'Tableau de bord' },
  { to: '/admin/users',         icon: Users,           label: 'Utilisateurs' },
  { to: '/admin/experts',       icon: ShieldCheck,     label: 'Experts' },
  { to: '/admin/conversations', icon: MessageSquare,   label: 'Conversations' },
  { to: '/admin/categories',    icon: FolderOpen,      label: 'Catégories' },
  { to: '/admin/payments',      icon: CreditCard,      label: 'Paiements' },
];

export default function AppLayout() {
  const { user, logout } = useAuthStore();
  const navigate = useNavigate();
  const [sidebarOpen, setSidebarOpen] = useState(false);

  const isExpert = user?.role === 'expert';
  const isAdmin  = user?.role === 'admin';
  const navItems = isAdmin ? adminNavItems : isExpert ? expertNavItems : userNavItems;

  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  return (
    <div className="app-layout">
      <aside className={`sidebar ${sidebarOpen ? 'sidebar--open' : ''}`}>
        <div className="sidebar-header">
          <div className="sidebar-logo">
            <img src="/logo.png" alt="Nexora" className="sidebar-logo-img" />
            <span className="sidebar-logo-text">NEXORA</span>
          </div>
          <button className="sidebar-close" onClick={() => setSidebarOpen(false)}>
            <X size={20} />
          </button>
        </div>

        <nav className="sidebar-nav">
          {navItems.map(({ to, icon: Icon, label }) => (
            <NavLink
              key={to}
              to={to}
              className={({ isActive }) => `sidebar-link ${isActive ? 'sidebar-link--active' : ''}`}
              onClick={() => setSidebarOpen(false)}
            >
              <Icon size={20} />
              <span>{label}</span>
            </NavLink>
          ))}
        </nav>

        <div className="sidebar-footer">
          <div className="sidebar-user">
            <div className="sidebar-avatar">
              {user?.name?.charAt(0)?.toUpperCase() || 'U'}
            </div>
            <div className="sidebar-user-info">
              <span className="sidebar-user-name">{user?.name}</span>
              <span className="sidebar-user-role">{user?.role}</span>
            </div>
          </div>
          <button className="sidebar-link sidebar-logout" onClick={handleLogout}>
            <LogOut size={20} />
            <span>Déconnexion</span>
          </button>
        </div>
      </aside>

      {sidebarOpen && <div className="sidebar-overlay" onClick={() => setSidebarOpen(false)} />}

      <main className="app-main">
        <header className="app-header">
          <button className="app-menu-btn" onClick={() => setSidebarOpen(true)}>
            <Menu size={22} />
          </button>
          <div style={{ flex: 1 }} />
          <div className="app-header-actions">
            <NavLink to="/notifications" className="app-header-notif">
              <Bell size={20} />
            </NavLink>
          </div>
        </header>

        <motion.div
          className="app-content"
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          transition={{ duration: 0.3 }}
        >
          <Outlet />
        </motion.div>
      </main>
    </div>
  );
}
