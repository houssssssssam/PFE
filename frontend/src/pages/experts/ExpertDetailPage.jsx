import { motion } from 'framer-motion';
import { Star, Clock, ArrowLeft, CheckCircle, Loader2, MessageSquare } from 'lucide-react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import { useExpert, useExpertReviews } from '../../hooks/useExperts';
import './ExpertDetailPage.css';

function StarRow({ rating }) {
  return (
    <span className="star-row">
      {[1, 2, 3, 4, 5].map((s) => (
        <Star key={s} size={14} fill={s <= rating ? '#f59e0b' : 'none'} stroke="#f59e0b" />
      ))}
    </span>
  );
}

export default function ExpertDetailPage() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { data: expert, isLoading } = useExpert(id);
  const { data: reviewsData, isLoading: reviewsLoading } = useExpertReviews(id);

  if (isLoading) {
    return (
      <div style={{ display: 'flex', justifyContent: 'center', padding: 80 }}>
        <Loader2 size={32} className="spin" style={{ color: 'var(--primary-500)' }} />
      </div>
    );
  }

  if (!expert) {
    return <div style={{ padding: 'var(--space-lg)', color: 'var(--text-muted)' }}>Expert introuvable.</div>;
  }

  return (
    <div className="expert-detail">
      <motion.div initial={{ opacity: 0, y: 10 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.4 }}>

        <button className="back-btn" onClick={() => navigate(-1)}>
          <ArrowLeft size={16} /> Retour
        </button>

        {/* Profile card */}
        <div className="expert-profile-card card">
          <div className="expert-profile-top">
            <div className="expert-detail-avatar">
              {expert.user?.name?.charAt(0)?.toUpperCase() ?? 'E'}
            </div>
            <div className="expert-profile-info">
              <h1 className="expert-detail-name">{expert.user?.name}</h1>
              <span className="expert-detail-category">{expert.category?.name}</span>
              <div className="expert-detail-stats">
                <span className="expert-stat-chip">
                  <Star size={14} fill="#f59e0b" stroke="#f59e0b" />
                  {Number(expert.rating_avg).toFixed(1)} ({expert.total_reviews} avis)
                </span>
                {expert.hourly_rate && (
                  <span className="expert-stat-chip">
                    <Clock size={14} />
                    {expert.hourly_rate} MAD/h
                  </span>
                )}
                <span className={`expert-badge ${expert.is_available ? 'expert-badge--available' : 'expert-badge--busy'}`}>
                  {expert.is_available ? 'Disponible' : 'Occupé'}
                </span>
              </div>
            </div>

            <button
              className="btn btn-primary"
              disabled={!expert.is_available}
              onClick={() => navigate('/conversations/new', { state: { expert_id: expert.id } })}
            >
              <MessageSquare size={16} />
              Démarrer une conversation
            </button>
          </div>

          {expert.bio && (
            <div className="expert-bio-section">
              <h3>À propos</h3>
              <p>{expert.bio}</p>
            </div>
          )}

          {expert.certifications?.length > 0 && (
            <div className="expert-certs-section">
              <h3>Certifications</h3>
              <ul className="expert-certs-list">
                {expert.certifications.map((cert, i) => (
                  <li key={i}>
                    <CheckCircle size={15} style={{ color: '#10b981' }} />
                    {cert}
                  </li>
                ))}
              </ul>
            </div>
          )}
        </div>

        {/* Reviews */}
        <div className="expert-reviews-section">
          <h2 style={{ marginBottom: 'var(--space-md)' }}>Avis clients</h2>

          {reviewsLoading ? (
            <div style={{ display: 'flex', justifyContent: 'center', padding: 40 }}>
              <Loader2 size={24} className="spin" style={{ color: 'var(--primary-500)' }} />
            </div>
          ) : reviewsData?.data?.length === 0 ? (
            <div className="card" style={{ color: 'var(--text-muted)', textAlign: 'center', padding: 'var(--space-xl)' }}>
              Aucun avis pour le moment.
            </div>
          ) : (
            <div className="reviews-list">
              {reviewsData?.data?.map((review) => (
                <motion.div
                  key={review.id}
                  className="review-card card"
                  initial={{ opacity: 0 }}
                  animate={{ opacity: 1 }}
                >
                  <div className="review-header">
                    <div className="review-avatar">
                      {review.user?.name?.charAt(0)?.toUpperCase()}
                    </div>
                    <div>
                      <p className="review-author">{review.user?.name}</p>
                      <StarRow rating={review.rating} />
                    </div>
                  </div>
                  {review.comment && <p className="review-comment">{review.comment}</p>}
                </motion.div>
              ))}
            </div>
          )}
        </div>

      </motion.div>
    </div>
  );
}
