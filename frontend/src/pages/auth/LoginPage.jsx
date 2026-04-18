import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { motion } from 'framer-motion';
import { Mail, Lock, Eye, EyeOff, ArrowRight } from 'lucide-react';
import toast from 'react-hot-toast';
import useAuthStore from '../../stores/authStore';
import './Auth.css';

export default function LoginPage() {
  const navigate = useNavigate();
  const { login, verify2fa, error, clearError } = useAuthStore();

  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [loading, setLoading] = useState(false);

  // 2FA state
  const [show2fa, setShow2fa] = useState(false);
  const [twoFactorToken, setTwoFactorToken] = useState('');
  const [otpCode, setOtpCode] = useState('');

  const handleLogin = async (e) => {
    e.preventDefault();
    clearError();
    setLoading(true);

    const result = await login(email, password);

    if (result.requires2fa) {
      setTwoFactorToken(result.token);
      setShow2fa(true);
      setLoading(false);
      return;
    }

    if (result.success) {
      toast.success('Bienvenue !');
      navigate('/dashboard');
    }
    setLoading(false);
  };

  const handle2faVerify = async (e) => {
    e.preventDefault();
    setLoading(true);
    const result = await verify2fa(twoFactorToken, otpCode);
    if (result.success) {
      toast.success('Bienvenue !');
      navigate('/dashboard');
    }
    setLoading(false);
  };

  if (show2fa) {
    return (
      <div className="auth-page">
        <h2 className="auth-title">Vérification 2FA</h2>
        <p className="auth-subtitle">Entrez le code de votre application d'authentification</p>

        <form onSubmit={handle2faVerify} className="auth-form">
          <div className="form-group">
            <label className="form-label">Code TOTP</label>
            <div className="input-wrapper">
              <Lock size={18} className="input-icon" />
              <input
                type="text"
                className="form-input input-with-icon"
                placeholder="000000"
                value={otpCode}
                onChange={(e) => setOtpCode(e.target.value)}
                maxLength={6}
                autoFocus
                required
              />
            </div>
          </div>

          {error && <p className="form-error">{error}</p>}

          <button type="submit" className="btn btn-primary btn-lg w-full" disabled={loading}>
            {loading ? <span className="spinner" /> : 'Vérifier'}
          </button>
        </form>
      </div>
    );
  }

  return (
    <div className="auth-page">
      <h2 className="auth-title">Bon retour !</h2>
      <p className="auth-subtitle">Connectez-vous à votre compte Nexora</p>

      <form onSubmit={handleLogin} className="auth-form">
        <div className="form-group">
          <label className="form-label">Email</label>
          <div className="input-wrapper">
            <Mail size={18} className="input-icon" />
            <input
              type="email"
              className="form-input input-with-icon"
              placeholder="votre@email.com"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
            />
          </div>
        </div>

        <div className="form-group">
          <label className="form-label">Mot de passe</label>
          <div className="input-wrapper">
            <Lock size={18} className="input-icon" />
            <input
              type={showPassword ? 'text' : 'password'}
              className="form-input input-with-icon input-with-action"
              placeholder="••••••••"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
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
        </div>

        <div className="auth-forgot">
          <Link to="/forgot-password">Mot de passe oublié ?</Link>
        </div>

        {error && <p className="form-error">{error}</p>}

        <button type="submit" className="btn btn-primary btn-lg w-full" disabled={loading}>
          {loading ? (
            <span className="spinner" />
          ) : (
            <>
              Se connecter
              <ArrowRight size={18} />
            </>
          )}
        </button>
      </form>

      <div className="auth-divider">
        <span>ou</span>
      </div>

      <div className="auth-social">
        <button className="btn btn-secondary w-full" type="button">
          <img src="https://www.google.com/favicon.ico" alt="" width={18} height={18} />
          Continuer avec Google
        </button>
      </div>

      <p className="auth-switch">
        Pas encore de compte ?{' '}
        <Link to="/register">Créer un compte</Link>
      </p>
    </div>
  );
}
