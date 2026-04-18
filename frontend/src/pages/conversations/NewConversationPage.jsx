import { useState } from 'react';
import { motion } from 'framer-motion';
import { ArrowLeft, Loader2, MessageSquare } from 'lucide-react';
import { useNavigate, useLocation } from 'react-router-dom';
import { useCategories } from '../../hooks/useExperts';
import { useCreateConversation } from '../../hooks/useMessages';
import toast from 'react-hot-toast';
import './NewConversationPage.css';

export default function NewConversationPage() {
  const navigate = useNavigate();
  const location = useLocation();
  const expertId = location.state?.expert_id ?? '';

  const { data: categories = [] } = useCategories();
  const { mutateAsync: createConversation, isPending } = useCreateConversation();

  const [title, setTitle] = useState('');
  const [categoryId, setCategoryId] = useState('');

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!categoryId) { toast.error('Veuillez sélectionner une catégorie.'); return; }
    try {
      const { data } = await createConversation({
        category_id: categoryId,
        title: title || undefined,
        expert_id: expertId || undefined,
      });
      navigate(`/conversations/${data.data.id}`);
    } catch {
      toast.error('Impossible de créer la conversation.');
    }
  };

  return (
    <div className="new-conv-page">
      <motion.div initial={{ opacity: 0, y: 10 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.4 }}>

        <button className="back-btn" onClick={() => navigate(-1)}>
          <ArrowLeft size={16} /> Retour
        </button>

        <div className="new-conv-card card">
          <div className="new-conv-icon">
            <MessageSquare size={28} />
          </div>
          <h1 className="new-conv-title">Nouvelle conversation</h1>
          <p className="new-conv-subtitle">
            Notre IA va analyser votre demande. Si nécessaire, elle escalade vers un expert humain.
          </p>

          <form onSubmit={handleSubmit} className="new-conv-form">
            <div className="form-group">
              <label className="form-label">Catégorie *</label>
              <select
                className="form-input"
                value={categoryId}
                onChange={(e) => setCategoryId(e.target.value)}
                required
              >
                <option value="">Sélectionnez un domaine...</option>
                {categories.map((cat) => (
                  <option key={cat.id} value={cat.id}>{cat.name}</option>
                ))}
              </select>
            </div>

            <div className="form-group">
              <label className="form-label">Titre (optionnel)</label>
              <input
                type="text"
                className="form-input"
                placeholder="Ex: Question sur mon contrat de travail"
                value={title}
                onChange={(e) => setTitle(e.target.value)}
                maxLength={255}
              />
            </div>

            {expertId && (
              <p className="new-conv-expert-hint">
                Un expert spécifique a été sélectionné pour cette conversation.
              </p>
            )}

            <button type="submit" className="btn btn-primary btn-full" disabled={isPending}>
              {isPending ? <Loader2 size={18} className="spin" /> : <MessageSquare size={18} />}
              {isPending ? 'Création...' : 'Démarrer la conversation'}
            </button>
          </form>
        </div>

      </motion.div>
    </div>
  );
}
