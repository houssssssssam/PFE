import { useState, useRef } from 'react';
import { Loader2, Upload, X, CheckCircle } from 'lucide-react';
import toast from 'react-hot-toast';
import { useCategories } from '../../hooks/useExperts';
import { useApplyExpert } from '../../hooks/useProfile';
import CustomSelect from '../../components/CustomSelect';
import './ApplyExpertPage.css';

const DOC_TYPES = [
  { value: 'diploma',     label: 'Diplôme' },
  { value: 'id_card',     label: 'Carte d\'identité' },
  { value: 'certificate', label: 'Certificat' },
  { value: 'other',       label: 'Autre' },
];

export default function ApplyExpertPage() {
  const { data: categories = [] }           = useCategories();
  const { mutateAsync: apply, isPending }    = useApplyExpert();

  const [categoryId, setCategoryId]   = useState('');
  const [bio, setBio]                 = useState('');
  const [hourlyRate, setHourlyRate]   = useState('');
  const [documents, setDocuments]     = useState([]);
  const [docType, setDocType]         = useState('diploma');
  const [submitted, setSubmitted]     = useState(false);
  const fileRef = useRef();

  const handleFileAdd = (e) => {
    const file = e.target.files?.[0];
    if (!file) return;
    setDocuments((prev) => [...prev, { file, type: docType }]);
    e.target.value = '';
  };

  const removeDoc = (i) => setDocuments((prev) => prev.filter((_, idx) => idx !== i));

  const handleSubmit = async (e) => {
    e.preventDefault();

    if (!categoryId) { toast.error('Veuillez sélectionner une catégorie.'); return; }
    if (documents.length === 0) { toast.error('Au moins un document est requis.'); return; }

    const formData = new FormData();
    formData.append('category_id', categoryId);
    if (bio) formData.append('bio', bio);
    if (hourlyRate) formData.append('hourly_rate', hourlyRate);

    documents.forEach((doc, i) => {
      formData.append(`documents[${i}][file]`, doc.file);
      formData.append(`documents[${i}][type]`, doc.type);
    });

    try {
      await apply(formData);
      setSubmitted(true);
    } catch (err) {
      toast.error(err.response?.data?.message || 'Erreur lors de la soumission.');
    }
  };

  if (submitted) {
    return (
      <div className="apply-page">
        <div className="apply-card success-box">
          <CheckCircle size={56} color="#22c55e" />
          <h2>Candidature envoyée !</h2>
          <p>Votre demande est en cours de révision par notre équipe.<br />Vous serez notifié par email dès que votre profil sera validé.</p>
        </div>
      </div>
    );
  }

  return (
    <div className="apply-page">
      <h1>Devenir Expert</h1>
      <p className="apply-subtitle">Remplissez ce formulaire pour soumettre votre candidature en tant qu'expert.</p>

      <form className="apply-card" onSubmit={handleSubmit}>
        <div>
          <h2 className="apply-section-title">Informations professionnelles</h2>

          <div style={{ display: 'flex', flexDirection: 'column', gap: 'var(--space-md)' }}>
            <div className="form-group">
              <label className="form-label">Domaine d'expertise *</label>
              <CustomSelect
                value={categoryId}
                onChange={(e) => setCategoryId(e.target.value)}
                placeholder="Sélectionnez une catégorie"
                required
                options={categories.map((c) => ({ value: c.id, label: c.name }))}
              />
            </div>

            <div className="form-group">
              <label className="form-label">Biographie</label>
              <textarea
                className="form-input form-textarea"
                value={bio}
                onChange={(e) => setBio(e.target.value)}
                placeholder="Décrivez votre expérience et vos compétences..."
                maxLength={2000}
              />
            </div>

            <div className="form-group">
              <label className="form-label">Tarif horaire (MAD)</label>
              <input
                className="form-input"
                type="number"
                min="0"
                step="0.01"
                value={hourlyRate}
                onChange={(e) => setHourlyRate(e.target.value)}
                placeholder="ex: 200"
              />
            </div>
          </div>
        </div>

        <div>
          <h2 className="apply-section-title">Documents justificatifs *</h2>

          {documents.length > 0 && (
            <ul className="doc-list" style={{ marginBottom: 12 }}>
              {documents.map((doc, i) => (
                <li key={i} className="doc-item">
                  <span>
                    <span className="doc-item-name">{doc.file.name}</span>
                    <span className="doc-item-type">({DOC_TYPES.find(t => t.value === doc.type)?.label})</span>
                  </span>
                  <button type="button" className="doc-remove" onClick={() => removeDoc(i)}>
                    <X size={14} />
                  </button>
                </li>
              ))}
            </ul>
          )}

          <div className="doc-add-row">
            <CustomSelect
              value={docType}
              onChange={(e) => setDocType(e.target.value)}
              options={DOC_TYPES}
            />
            <input
              ref={fileRef}
              type="file"
              accept=".pdf,.jpg,.jpeg,.png"
              style={{ display: 'none' }}
              onChange={handleFileAdd}
            />
            <button
              type="button"
              className="doc-add-btn"
              onClick={() => fileRef.current?.click()}
            >
              <Upload size={14} style={{ marginRight: 6 }} />
              Ajouter un document
            </button>
          </div>
        </div>

        <button type="submit" className="apply-submit-btn" disabled={isPending}>
          {isPending ? <Loader2 size={16} className="spin" /> : null}
          {isPending ? 'Envoi en cours...' : 'Soumettre ma candidature'}
        </button>
      </form>
    </div>
  );
}
