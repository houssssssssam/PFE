import { useState } from 'react';
import { CheckCircle, XCircle, Loader2, User } from 'lucide-react';
import { useAdminPendingExperts, useValidateExpert, useRejectExpert } from '../../hooks/useAdmin';
import './AdminExpertsPage.css';

export default function AdminExpertsPage() {
  const { data: experts, isLoading } = useAdminPendingExperts();
  const validateExpert = useValidateExpert();
  const rejectExpert   = useRejectExpert();
  const [rejectingId, setRejectingId] = useState(null);
  const [rejectReason, setRejectReason] = useState('');

  const handleReject = (id) => {
    rejectExpert.mutate({ id, reason: rejectReason }, {
      onSuccess: () => { setRejectingId(null); setRejectReason(''); },
    });
  };

  return (
    <div className="admin-experts">
      <div className="page-header">
        <h1>Experts en attente</h1>
        <p>Validez ou rejetez les candidatures d'experts.</p>
      </div>

      {isLoading ? (
        <div className="loading-center"><Loader2 className="spin" size={24} /></div>
      ) : experts?.length === 0 ? (
        <div className="empty-state">Aucune candidature en attente.</div>
      ) : (
        <div className="expert-cards">
          {experts?.map((expert) => (
            <div key={expert.id} className="expert-review-card">
              <div className="expert-review-header">
                <div className="avatar-circle"><User size={20} /></div>
                <div>
                  <h3>{expert.user?.name}</h3>
                  <span className="category-tag">{expert.category?.name}</span>
                </div>
                <span className="rate-tag">{expert.hourly_rate ? `${expert.hourly_rate} MAD/h` : 'Tarif N/D'}</span>
              </div>
              {expert.bio && <p className="expert-bio">{expert.bio}</p>}
              <div className="expert-review-meta">
                <span>Soumis le {new Date(expert.created_at).toLocaleDateString('fr-FR')}</span>
              </div>

              {rejectingId === expert.id ? (
                <div className="reject-form">
                  <textarea
                    placeholder="Raison du rejet (optionnel)..."
                    value={rejectReason}
                    onChange={(e) => setRejectReason(e.target.value)}
                    rows={2}
                  />
                  <div className="reject-form-actions">
                    <button className="btn-confirm-reject" onClick={() => handleReject(expert.id)} disabled={rejectExpert.isPending}>
                      Confirmer le rejet
                    </button>
                    <button className="btn-cancel" onClick={() => setRejectingId(null)}>Annuler</button>
                  </div>
                </div>
              ) : (
                <div className="expert-review-actions">
                  <button className="btn-validate" onClick={() => validateExpert.mutate(expert.id)} disabled={validateExpert.isPending}>
                    <CheckCircle size={16} /> Valider
                  </button>
                  <button className="btn-reject" onClick={() => setRejectingId(expert.id)}>
                    <XCircle size={16} /> Rejeter
                  </button>
                </div>
              )}
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
