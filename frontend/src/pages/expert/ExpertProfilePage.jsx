import { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { Loader2, Save, Plus, X } from 'lucide-react';
import { useExpertProfile, useUpdateExpertProfile } from '../../hooks/useExpertPanel';
import { useCategories } from '../../hooks/useExperts';
import CustomSelect from '../../components/CustomSelect';
import toast from 'react-hot-toast';
import './ExpertProfilePage.css';

export default function ExpertProfilePage() {
  const { data: expert, isLoading } = useExpertProfile();
  const { data: categories = [] } = useCategories();
  const { mutateAsync: updateProfile, isPending } = useUpdateExpertProfile();

  const [bio, setBio] = useState('');
  const [hourlyRate, setHourlyRate] = useState('');
  const [categoryId, setCategoryId] = useState('');
  const [certifications, setCertifications] = useState([]);
  const [newCert, setNewCert] = useState('');

  useEffect(() => {
    if (expert) {
      setBio(expert.bio ?? '');
      setHourlyRate(expert.hourly_rate ?? '');
      setCategoryId(expert.category?.id ?? '');
      const certs = expert.certifications;
      if (Array.isArray(certs)) setCertifications(certs);
      else if (typeof certs === 'string') {
        try { setCertifications(JSON.parse(certs)); } catch { setCertifications([]); }
      }
    }
  }, [expert]);

  const addCert = () => {
    const val = newCert.trim();
    if (val && !certifications.includes(val)) {
      setCertifications([...certifications, val]);
      setNewCert('');
    }
  };

  const removeCert = (i) => setCertifications(certifications.filter((_, idx) => idx !== i));

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      await updateProfile({
        bio: bio || undefined,
        hourly_rate: hourlyRate || undefined,
        category_id: categoryId || undefined,
        certifications: certifications.length ? certifications : undefined,
      });
      toast.success('Profil mis à jour avec succès.');
    } catch {
      toast.error('Impossible de mettre à jour le profil.');
    }
  };

  if (isLoading) {
    return (
      <div style={{ display: 'flex', justifyContent: 'center', padding: 80 }}>
        <Loader2 size={32} className="spin" style={{ color: 'var(--primary-500)' }} />
      </div>
    );
  }

  return (
    <div className="expert-profile-page">
      <motion.div initial={{ opacity: 0, y: 10 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.4 }}>

        <div style={{ marginBottom: 'var(--space-lg)' }}>
          <h1 className="page-title">Mon profil expert</h1>
          <p className="page-subtitle">Mettez à jour vos informations professionnelles</p>
        </div>

        <form onSubmit={handleSubmit} className="eprofile-form card">

          <div className="eprofile-section">
            <h3 className="eprofile-section-title">Informations générales</h3>

            <div className="form-group">
              <label className="form-label">Catégorie</label>
              <CustomSelect
                value={categoryId}
                onChange={e => setCategoryId(e.target.value)}
                placeholder="Sélectionner..."
                options={categories.map(cat => ({ value: cat.id, label: cat.name }))}
              />
            </div>

            <div className="form-group">
              <label className="form-label">Tarif horaire (MAD)</label>
              <input
                type="number"
                className="form-input"
                placeholder="Ex: 200"
                value={hourlyRate}
                onChange={e => setHourlyRate(e.target.value)}
                min={0}
              />
            </div>

            <div className="form-group">
              <label className="form-label">Bio / À propos</label>
              <textarea
                className="form-input"
                rows={4}
                placeholder="Décrivez votre expertise, votre expérience..."
                value={bio}
                onChange={e => setBio(e.target.value)}
                style={{ resize: 'vertical' }}
              />
            </div>
          </div>

          <div className="eprofile-section">
            <h3 className="eprofile-section-title">Certifications</h3>

            {certifications.length > 0 && (
              <ul className="cert-list">
                {certifications.map((cert, i) => (
                  <li key={i} className="cert-item">
                    <span>{cert}</span>
                    <button type="button" className="cert-remove" onClick={() => removeCert(i)}>
                      <X size={14} />
                    </button>
                  </li>
                ))}
              </ul>
            )}

            <div className="cert-add-row">
              <input
                type="text"
                className="form-input"
                placeholder="Ajouter une certification..."
                value={newCert}
                onChange={e => setNewCert(e.target.value)}
                onKeyDown={e => { if (e.key === 'Enter') { e.preventDefault(); addCert(); } }}
              />
              <button type="button" className="btn btn-secondary" onClick={addCert}>
                <Plus size={16} />
              </button>
            </div>
          </div>

          <div style={{ display: 'flex', justifyContent: 'flex-end' }}>
            <button type="submit" className="btn btn-primary" disabled={isPending}>
              {isPending ? <Loader2 size={16} className="spin" /> : <Save size={16} />}
              {isPending ? 'Enregistrement...' : 'Enregistrer'}
            </button>
          </div>

        </form>
      </motion.div>
    </div>
  );
}
