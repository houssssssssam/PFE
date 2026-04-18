import { motion } from 'framer-motion';
import { MessageSquare, Users, Clock, Bell, ChevronRight, Loader2 } from 'lucide-react';
import { Link, useNavigate } from 'react-router-dom';
import useAuthStore from '../stores/authStore';
import { useConversations } from '../hooks/useConversations';
import { useNotifications } from '../hooks/useNotifications';
import './DashboardPage.css';

function StatCard({ label, value, icon: Icon, color, bg, delay }) {
  return (
    <motion.div
      className="stat-card"
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.4, delay }}
    >
      <div className="stat-card-icon" style={{ background: bg, color }}>
        <Icon size={22} />
      </div>
      <div className="stat-card-info">
        <span className="stat-card-value">{value}</span>
        <span className="stat-card-label">{label}</span>
      </div>
    </motion.div>
  );
}

function StatusBadge({ status }) {
  const map = {
    ai:     { label: 'IA',      color: '#8b5cf6', bg: 'rgba(139,92,246,0.12)' },
    expert: { label: 'Expert',  color: '#14b8a6', bg: 'rgba(20,184,166,0.12)' },
    open:   { label: 'Ouvert',  color: '#3b82f6', bg: 'rgba(59,130,246,0.12)' },
    closed: { label: 'Fermé',   color: '#6b7280', bg: 'rgba(107,114,128,0.12)' },
  };
  const s = map[status] ?? map.open;
  return (
    <span style={{ padding: '2px 10px', borderRadius: 20, fontSize: 12, fontWeight: 600, color: s.color, background: s.bg }}>
      {s.label}
    </span>
  );
}

export default function DashboardPage() {
  const user = useAuthStore((s) => s.user);
  const navigate = useNavigate();
  const { data: convsData, isLoading: convsLoading } = useConversations();
  const { data: notifsData, isLoading: notifsLoading } = useNotifications({ per_page: 5 });

  const conversations = convsData?.data ?? [];
  const notifications = notifsData?.data ?? [];
  const unreadCount = notifsData?.meta?.unread_count ?? 0;

  const stats = [
    { label: 'Conversations', value: convsData?.meta?.total ?? '—', icon: MessageSquare, color: 'var(--primary-500)', bg: 'rgba(139,92,246,0.1)', delay: 0 },
    { label: 'Experts disponibles', value: '—', icon: Users, color: '#14b8a6', bg: 'rgba(20,184,166,0.1)', delay: 0.1 },
    { label: 'Non lues', value: unreadCount, icon: Bell, color: '#f59e0b', bg: 'rgba(245,158,11,0.1)', delay: 0.2 },
    { label: 'Temps moyen', value: '—', icon: Clock, color: '#10b981', bg: 'rgba(16,185,129,0.1)', delay: 0.3 },
  ];

  return (
    <div className="dashboard">
      <motion.div initial={{ opacity: 0, y: 10 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.4 }}>

        {/* Header */}
        <div className="dashboard-header">
          <div>
            <h1 className="dashboard-title">Bonjour, {user?.name?.split(' ')[0]} 👋</h1>
            <p className="dashboard-subtitle">Voici un aperçu de votre activité</p>
          </div>
          <button className="btn btn-primary" onClick={() => navigate('/conversations/new')}>
            <MessageSquare size={18} />
            Nouvelle conversation
          </button>
        </div>

        {/* Stats */}
        <div className="dashboard-stats">
          {stats.map((s) => <StatCard key={s.label} {...s} />)}
        </div>

        {/* Grid */}
        <div className="dashboard-grid">

          {/* Recent conversations */}
          <div className="card">
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 'var(--space-md)' }}>
              <h3 style={{ fontWeight: 600 }}>Conversations récentes</h3>
              <Link to="/conversations" style={{ fontSize: 13, color: 'var(--primary-500)', display: 'flex', alignItems: 'center', gap: 4 }}>
                Voir tout <ChevronRight size={14} />
              </Link>
            </div>

            {convsLoading ? (
              <div style={{ display: 'flex', justifyContent: 'center', padding: 'var(--space-lg)' }}>
                <Loader2 size={24} className="spin" style={{ color: 'var(--primary-500)' }} />
              </div>
            ) : conversations.length === 0 ? (
              <p className="text-muted text-sm">
                Aucune conversation pour le moment.<br />
                Commencez une nouvelle conversation pour obtenir de l'aide.
              </p>
            ) : (
              <ul style={{ listStyle: 'none', padding: 0, margin: 0, display: 'flex', flexDirection: 'column', gap: 'var(--space-sm)' }}>
                {conversations.map((conv) => (
                  <li key={conv.id}>
                    <Link
                      to={`/conversations/${conv.id}`}
                      style={{ display: 'flex', alignItems: 'center', gap: 'var(--space-sm)', padding: '10px', borderRadius: 'var(--radius-md)', textDecoration: 'none', color: 'inherit', transition: 'background 0.15s' }}
                      onMouseEnter={e => e.currentTarget.style.background = 'var(--bg-hover)'}
                      onMouseLeave={e => e.currentTarget.style.background = 'transparent'}
                    >
                      <div style={{ flex: 1 }}>
                        <p style={{ margin: 0, fontWeight: 500, fontSize: 14 }}>{conv.title ?? `Conversation #${conv.id}`}</p>
                        <p style={{ margin: 0, fontSize: 12, color: 'var(--text-muted)' }}>{conv.category?.name}</p>
                      </div>
                      <StatusBadge status={conv.status} />
                    </Link>
                  </li>
                ))}
              </ul>
            )}
          </div>

          {/* Notifications */}
          <div className="card">
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 'var(--space-md)' }}>
              <h3 style={{ fontWeight: 600 }}>Notifications</h3>
              <Link to="/notifications" style={{ fontSize: 13, color: 'var(--primary-500)', display: 'flex', alignItems: 'center', gap: 4 }}>
                Voir tout <ChevronRight size={14} />
              </Link>
            </div>

            {notifsLoading ? (
              <div style={{ display: 'flex', justifyContent: 'center', padding: 'var(--space-lg)' }}>
                <Loader2 size={24} className="spin" style={{ color: 'var(--primary-500)' }} />
              </div>
            ) : notifications.length === 0 ? (
              <p className="text-muted text-sm">Aucune notification.</p>
            ) : (
              <ul style={{ listStyle: 'none', padding: 0, margin: 0, display: 'flex', flexDirection: 'column', gap: 'var(--space-sm)' }}>
                {notifications.map((notif) => (
                  <li key={notif.id} style={{ padding: '10px', borderRadius: 'var(--radius-md)', background: notif.read_at ? 'transparent' : 'rgba(139,92,246,0.07)', borderLeft: notif.read_at ? 'none' : '3px solid var(--primary-500)' }}>
                    <p style={{ margin: 0, fontWeight: 500, fontSize: 14 }}>{notif.title}</p>
                    <p style={{ margin: 0, fontSize: 12, color: 'var(--text-muted)' }}>{notif.body}</p>
                  </li>
                ))}
              </ul>
            )}
          </div>
        </div>
      </motion.div>
    </div>
  );
}
