import { motion } from 'framer-motion';
import { Users, Briefcase, Clock, MessageSquare, TrendingUp, DollarSign } from 'lucide-react';
import { Link } from 'react-router-dom';
import { useAdminDashboard } from '../../hooks/useAdmin';
import './AdminDashboardPage.css';

const cards = [
  { key: 'total_users',          label: 'Utilisateurs',          icon: Users,         color: 'blue' },
  { key: 'total_experts',        label: 'Experts validés',        icon: Briefcase,     color: 'purple' },
  { key: 'pending_experts',      label: 'Experts en attente',     icon: Clock,         color: 'orange' },
  { key: 'total_conversations',  label: 'Conversations',          icon: MessageSquare, color: 'teal' },
  { key: 'active_conversations', label: 'Conv. actives',          icon: TrendingUp,    color: 'green' },
  { key: 'total_revenue',        label: 'Revenus (MAD)',          icon: DollarSign,    color: 'gold' },
];

export default function AdminDashboardPage() {
  const { data: stats, isLoading } = useAdminDashboard();

  return (
    <div className="admin-dashboard">
      <div className="page-header">
        <h1>Tableau de bord Admin</h1>
        <p>Vue d'ensemble de la plateforme</p>
      </div>

      {isLoading ? (
        <div className="loading-grid">
          {[...Array(6)].map((_, i) => <div key={i} className="stat-card skeleton" />)}
        </div>
      ) : (
        <div className="stats-grid">
          {cards.map(({ key, label, icon: Icon, color }, i) => (
            <motion.div
              key={key}
              className={`stat-card stat-card--${color}`}
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: i * 0.07 }}
            >
              <div className="stat-icon"><Icon size={22} /></div>
              <div className="stat-info">
                <span className="stat-value">
                  {key === 'total_revenue'
                    ? `${parseFloat(stats?.[key] || 0).toFixed(2)}`
                    : (stats?.[key] ?? 0)}
                </span>
                <span className="stat-label">{label}</span>
              </div>
            </motion.div>
          ))}
        </div>
      )}

      <div className="quick-nav">
        <h2>Navigation rapide</h2>
        <div className="quick-nav-grid">
          {[
            { to: '/admin/users',         label: 'Gérer les utilisateurs' },
            { to: '/admin/experts',        label: 'Valider les experts' },
            { to: '/admin/conversations',  label: 'Voir les conversations' },
            { to: '/admin/categories',     label: 'Gérer les catégories' },
            { to: '/admin/payments',       label: 'Suivi des paiements' },
          ].map(({ to, label }) => (
            <Link key={to} to={to} className="quick-nav-item">{label}</Link>
          ))}
        </div>
      </div>
    </div>
  );
}
