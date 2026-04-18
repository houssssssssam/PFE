import { motion } from 'framer-motion';
import { Star, MessageSquare, Wallet, ToggleLeft, ToggleRight, Loader2, TrendingUp } from 'lucide-react';
import { Link } from 'react-router-dom';
import { useExpertDashboard, useToggleAvailability } from '../../hooks/useExpertPanel';
import { useConversations } from '../../hooks/useConversations';
import useAuthStore from '../../stores/authStore';
import toast from 'react-hot-toast';
import './ExpertDashboardPage.css';

function StatCard({ label, value, icon: Icon, color, bg, delay }) {
  return (
    <motion.div
      className="estat-card"
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.4, delay }}
    >
      <div className="estat-icon" style={{ background: bg, color }}>
        <Icon size={22} />
      </div>
      <div className="estat-info">
        <span className="estat-value">{value ?? '—'}</span>
        <span className="estat-label">{label}</span>
      </div>
    </motion.div>
  );
}

export default function ExpertDashboardPage() {
  const user = useAuthStore((s) => s.user);
  const { data: stats, isLoading } = useExpertDashboard();
  const { data: convsData } = useConversations({ per_page: 5 });
  const { mutateAsync: toggleAvailability, isPending: toggling } = useToggleAvailability();

  const handleToggle = async () => {
    try {
      await toggleAvailability(!stats?.is_available);
      toast.success(stats?.is_available ? 'Vous êtes maintenant occupé.' : 'Vous êtes maintenant disponible.');
    } catch {
      toast.error('Impossible de mettre à jour la disponibilité.');
    }
  };

  const conversations = convsData?.data ?? [];

  return (
    <div className="expert-dashboard">
      <motion.div initial={{ opacity: 0, y: 10 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.4 }}>

        <div className="expert-dash-header">
          <div>
            <h1 className="page-title">Bonjour, {user?.name?.split(' ')[0]} 👋</h1>
            <p className="page-subtitle">Tableau de bord expert</p>
          </div>
          <button
            className={`availability-btn ${stats?.is_available ? 'availability-btn--on' : 'availability-btn--off'}`}
            onClick={handleToggle}
            disabled={toggling || isLoading}
          >
            {toggling ? (
              <Loader2 size={18} className="spin" />
            ) : stats?.is_available ? (
              <ToggleRight size={22} />
            ) : (
              <ToggleLeft size={22} />
            )}
            {stats?.is_available ? 'Disponible' : 'Occupé'}
          </button>
        </div>

        {isLoading ? (
          <div style={{ display: 'flex', justifyContent: 'center', padding: 60 }}>
            <Loader2 size={32} className="spin" style={{ color: 'var(--primary-500)' }} />
          </div>
        ) : (
          <>
            <div className="expert-stats-grid">
              <StatCard label="Conversations totales" value={stats?.total_conversations} icon={MessageSquare} color="var(--primary-500)" bg="rgba(139,92,246,0.1)" delay={0} />
              <StatCard label="Conversations actives" value={stats?.active_conversations} icon={TrendingUp} color="#14b8a6" bg="rgba(20,184,166,0.1)" delay={0.08} />
              <StatCard label="Note moyenne" value={stats?.rating_avg ? Number(stats.rating_avg).toFixed(1) + ' ★' : '—'} icon={Star} color="#f59e0b" bg="rgba(245,158,11,0.1)" delay={0.16} />
              <StatCard label="Solde wallet" value={stats?.wallet_balance ? `${stats.wallet_balance} MAD` : '0 MAD'} icon={Wallet} color="#10b981" bg="rgba(16,185,129,0.1)" delay={0.24} />
            </div>

            <div className="expert-dash-grid">
              {/* Recent conversations */}
              <div className="card">
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 'var(--space-md)' }}>
                  <h3 style={{ fontWeight: 600, margin: 0 }}>Conversations récentes</h3>
                  <Link to="/expert/conversations" style={{ fontSize: 13, color: 'var(--primary-500)' }}>Voir tout</Link>
                </div>
                {conversations.length === 0 ? (
                  <p style={{ color: 'var(--text-muted)', fontSize: 14, margin: 0 }}>Aucune conversation assignée.</p>
                ) : (
                  <ul style={{ listStyle: 'none', padding: 0, margin: 0, display: 'flex', flexDirection: 'column', gap: 'var(--space-sm)' }}>
                    {conversations.map((conv) => (
                      <li key={conv.id}>
                        <Link
                          to={`/conversations/${conv.id}`}
                          style={{ display: 'flex', alignItems: 'center', gap: 'var(--space-sm)', padding: 10, borderRadius: 'var(--radius-md)', textDecoration: 'none', color: 'inherit', transition: 'background 0.15s' }}
                          onMouseEnter={e => e.currentTarget.style.background = 'var(--bg-hover)'}
                          onMouseLeave={e => e.currentTarget.style.background = 'transparent'}
                        >
                          <div style={{ flex: 1 }}>
                            <p style={{ margin: 0, fontWeight: 500, fontSize: 14 }}>{conv.title ?? `Conversation #${conv.id}`}</p>
                            <p style={{ margin: 0, fontSize: 12, color: 'var(--text-muted)' }}>{conv.category?.name}</p>
                          </div>
                        </Link>
                      </li>
                    ))}
                  </ul>
                )}
              </div>

              {/* Quick links */}
              <div className="card">
                <h3 style={{ fontWeight: 600, margin: '0 0 var(--space-md)' }}>Accès rapide</h3>
                <div style={{ display: 'flex', flexDirection: 'column', gap: 'var(--space-sm)' }}>
                  {[
                    { to: '/expert/profile', label: 'Modifier mon profil', color: 'var(--primary-500)', bg: 'rgba(139,92,246,0.08)' },
                    { to: '/expert/wallet',  label: 'Mon portefeuille',    color: '#10b981',            bg: 'rgba(16,185,129,0.08)' },
                    { to: '/conversations',  label: 'Mes conversations',   color: '#14b8a6',            bg: 'rgba(20,184,166,0.08)' },
                  ].map(({ to, label, color, bg }) => (
                    <Link key={to} to={to} style={{ display: 'flex', alignItems: 'center', padding: '12px 16px', borderRadius: 'var(--radius-md)', textDecoration: 'none', fontWeight: 500, fontSize: 14, color, background: bg, transition: 'opacity 0.15s' }}>
                      {label}
                    </Link>
                  ))}
                </div>
              </div>
            </div>
          </>
        )}
      </motion.div>
    </div>
  );
}
