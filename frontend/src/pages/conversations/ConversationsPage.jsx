import { motion } from 'framer-motion';
import { MessageSquare, Plus, Loader2, ChevronRight } from 'lucide-react';
import { Link, useNavigate } from 'react-router-dom';
import { useConversations } from '../../hooks/useConversations';
import './ConversationsPage.css';

function StatusBadge({ status }) {
  const map = {
    ai:     { label: 'IA',      color: '#8b5cf6', bg: 'rgba(139,92,246,0.12)' },
    expert: { label: 'Expert',  color: '#14b8a6', bg: 'rgba(20,184,166,0.12)' },
    open:   { label: 'Ouvert',  color: '#3b82f6', bg: 'rgba(59,130,246,0.12)' },
    closed: { label: 'Fermé',   color: '#6b7280', bg: 'rgba(107,114,128,0.12)' },
  };
  const s = map[status] ?? map.open;
  return (
    <span className="conv-status" style={{ color: s.color, background: s.bg }}>
      {s.label}
    </span>
  );
}

export default function ConversationsPage() {
  const navigate = useNavigate();
  const { data, isLoading } = useConversations({ per_page: 20 });
  const conversations = data?.data ?? [];

  return (
    <div className="convs-page">
      <motion.div initial={{ opacity: 0, y: 10 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.4 }}>

        <div className="convs-header">
          <div>
            <h1 className="page-title">Conversations</h1>
            <p className="page-subtitle">Toutes vos conversations avec l'IA et les experts</p>
          </div>
          <button className="btn btn-primary" onClick={() => navigate('/conversations/new')}>
            <Plus size={18} />
            Nouvelle conversation
          </button>
        </div>

        {isLoading ? (
          <div className="convs-loading">
            <Loader2 size={32} className="spin" style={{ color: 'var(--primary-500)' }} />
          </div>
        ) : conversations.length === 0 ? (
          <div className="convs-empty card">
            <MessageSquare size={48} style={{ color: 'var(--text-muted)', marginBottom: 16 }} />
            <p style={{ color: 'var(--text-muted)', margin: 0 }}>Aucune conversation pour le moment.</p>
            <button className="btn btn-primary" style={{ marginTop: 16 }} onClick={() => navigate('/conversations/new')}>
              Commencer une conversation
            </button>
          </div>
        ) : (
          <div className="convs-list">
            {conversations.map((conv, i) => (
              <motion.div
                key={conv.id}
                initial={{ opacity: 0, x: -10 }}
                animate={{ opacity: 1, x: 0 }}
                transition={{ duration: 0.3, delay: i * 0.04 }}
              >
                <Link to={`/conversations/${conv.id}`} className="conv-item card">
                  <div className="conv-icon">
                    <MessageSquare size={20} />
                  </div>
                  <div className="conv-info">
                    <p className="conv-title">{conv.title ?? `Conversation #${conv.id}`}</p>
                    <p className="conv-meta">
                      {conv.category?.name}
                      {conv.expert && ` · ${conv.expert.user?.name}`}
                    </p>
                  </div>
                  <div className="conv-right">
                    <StatusBadge status={conv.status} />
                    <ChevronRight size={16} style={{ color: 'var(--text-muted)' }} />
                  </div>
                </Link>
              </motion.div>
            ))}
          </div>
        )}
      </motion.div>
    </div>
  );
}
