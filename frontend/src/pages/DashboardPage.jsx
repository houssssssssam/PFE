import { motion } from 'framer-motion';
import { MessageSquare, Users, TrendingUp, Clock } from 'lucide-react';
import useAuthStore from '../stores/authStore';
import './DashboardPage.css';

const statsCards = [
  {
    label: 'Conversations',
    value: '—',
    icon: MessageSquare,
    color: 'var(--primary-500)',
    bg: 'rgba(139, 92, 246, 0.1)',
  },
  {
    label: 'Experts actifs',
    value: '—',
    icon: Users,
    color: 'var(--accent-500)',
    bg: 'rgba(20, 184, 166, 0.1)',
  },
  {
    label: 'Taux de résolution IA',
    value: '—',
    icon: TrendingUp,
    color: 'var(--success)',
    bg: 'rgba(16, 185, 129, 0.1)',
  },
  {
    label: 'Temps moyen',
    value: '—',
    icon: Clock,
    color: 'var(--warning)',
    bg: 'rgba(245, 158, 11, 0.1)',
  },
];

export default function DashboardPage() {
  const user = useAuthStore((s) => s.user);

  return (
    <div className="dashboard">
      <motion.div
        initial={{ opacity: 0, y: 10 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.4 }}
      >
        <div className="dashboard-header">
          <div>
            <h1 className="dashboard-title">
              Bonjour, {user?.name?.split(' ')[0]} 👋
            </h1>
            <p className="dashboard-subtitle">
              Voici un aperçu de votre activité
            </p>
          </div>
          <button className="btn btn-primary">
            <MessageSquare size={18} />
            Nouvelle conversation
          </button>
        </div>

        <div className="dashboard-stats">
          {statsCards.map((card, i) => (
            <motion.div
              key={card.label}
              className="stat-card"
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.4, delay: i * 0.1 }}
            >
              <div className="stat-card-icon" style={{ background: card.bg, color: card.color }}>
                <card.icon size={22} />
              </div>
              <div className="stat-card-info">
                <span className="stat-card-value">{card.value}</span>
                <span className="stat-card-label">{card.label}</span>
              </div>
            </motion.div>
          ))}
        </div>

        <div className="dashboard-grid">
          <div className="card">
            <h3 style={{ marginBottom: 'var(--space-md)', fontWeight: 600 }}>
              Conversations récentes
            </h3>
            <p className="text-muted text-sm">
              Aucune conversation pour le moment.
              <br />Commencez une nouvelle conversation pour obtenir de l'aide.
            </p>
          </div>
          <div className="card">
            <h3 style={{ marginBottom: 'var(--space-md)', fontWeight: 600 }}>
              Notifications
            </h3>
            <p className="text-muted text-sm">
              Aucune notification.
            </p>
          </div>
        </div>
      </motion.div>
    </div>
  );
}
