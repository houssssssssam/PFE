import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { User, Mail, Lock, Eye, EyeOff, ArrowRight } from 'lucide-react';
import toast from 'react-hot-toast';
import useAuthStore from '../../stores/authStore';
import './Auth.css';

export default function RegisterPage() {
  const navigate = useNavigate();
  const { register, error, clearError } = useAuthStore();

  const [form, setForm] = useState({
    name: '',
    email: '',
    password: '',
    passwordConfirmation: '',
  });
  const [showPassword, setShowPassword] = useState(false);
  const [loading, setLoading] = useState(false);
  const [fieldErrors, setFieldErrors] = useState({});

  const update = (field) => (e) => {
    setForm({ ...form, [field]: e.target.value });
    setFieldErrors({ ...fieldErrors, [field]: null });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    clearError();
    setLoading(true);

    const result = await register(
      form.name,
      form.email,
      form.password,
      form.passwordConfirmation
    );

    if (result.success) {
      toast.success(result.message || 'Inscription réussie ! Vérifiez votre email.');
      navigate('/login');
    } else if (result.errors) {
      setFieldErrors(result.errors);
    }
    setLoading(false);
  };

  return (
    <div className="auth-page">
      <h2 className="auth-title">Créer un compte</h2>
      <p className="auth-subtitle">Rejoignez Nexora en quelques secondes</p>

      <form onSubmit={handleSubmit} className="auth-form">
        <div className="form-group">
          <label className="form-label">Nom complet</label>
          <div className="input-wrapper">
            <User size={18} className="input-icon" />
            <input
              type="text"
              className="form-input input-with-icon"
              placeholder="Jean Dupont"
              value={form.name}
              onChange={update('name')}
              required
            />
          </div>
          {fieldErrors.name && <p className="form-error">{fieldErrors.name[0]}</p>}
        </div>

        <div className="form-group">
          <label className="form-label">Email</label>
          <div className="input-wrapper">
            <Mail size={18} className="input-icon" />
            <input
              type="email"
              className="form-input input-with-icon"
              placeholder="votre@email.com"
              value={form.email}
              onChange={update('email')}
              required
            />
          </div>
          {fieldErrors.email && <p className="form-error">{fieldErrors.email[0]}</p>}
        </div>

        <div className="form-group">
          <label className="form-label">Mot de passe</label>
          <div className="input-wrapper">
            <Lock size={18} className="input-icon" />
            <input
              type={showPassword ? 'text' : 'password'}
              className="form-input input-with-icon input-with-action"
              placeholder="Min. 8 caractères"
              value={form.password}
              onChange={update('password')}
              required
            />
            <button
              type="button"
              className="input-action"
              onClick={() => setShowPassword(!showPassword)}
            >
              {showPassword ? <EyeOff size={18} /> : <Eye size={18} />}
            </button>
          </div>
          {fieldErrors.password && <p className="form-error">{fieldErrors.password[0]}</p>}
        </div>

        <div className="form-group">
          <label className="form-label">Confirmer le mot de passe</label>
          <div className="input-wrapper">
            <Lock size={18} className="input-icon" />
            <input
              type="password"
              className="form-input input-with-icon"
              placeholder="Répétez le mot de passe"
              value={form.passwordConfirmation}
              onChange={update('passwordConfirmation')}
              required
            />
          </div>
        </div>

        {error && <p className="form-error">{error}</p>}

        <button type="submit" className="btn btn-primary btn-lg w-full" disabled={loading}>
          {loading ? (
            <span className="spinner" />
          ) : (
            <>
              Créer mon compte
              <ArrowRight size={18} />
            </>
          )}
        </button>
      </form>

      <p className="auth-switch">
        Déjà un compte ?{' '}
        <Link to="/login">Se connecter</Link>
      </p>
    </div>
  );
}
