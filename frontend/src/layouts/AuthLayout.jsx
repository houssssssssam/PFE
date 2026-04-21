import { Outlet } from 'react-router-dom';
import { motion } from 'framer-motion';
import NexoraBackground from '../components/NexoraBackground';
import './AuthLayout.css';

export default function AuthLayout() {
  return (
    <div className="auth-layout">
      <NexoraBackground />

      <div className="auth-container">
        {/* Left — branding panel */}
        <motion.div
          className="auth-branding"
          initial={{ opacity: 0, x: -30 }}
          animate={{ opacity: 1, x: 0 }}
          transition={{ duration: 0.6 }}
        >
          <div className="auth-brand-logo">
            <img src="/logo.png" alt="Nexora" className="auth-brand-img" />
            <div className="auth-brand-text">
              <h1 className="auth-brand-name">NEXORA</h1>
              <span className="auth-brand-slogan">Connect to Expertise</span>
            </div>
          </div>
          <p className="auth-brand-tagline">
            Plateforme intelligente de consultations
            <br />
            <span className="auth-brand-highlight">propulsée par l'IA</span>
          </p>
          <div className="auth-brand-features">
            <div className="auth-feature">
              <div className="auth-feature-dot" />
              <span>Assistance IA 24/7</span>
            </div>
            <div className="auth-feature">
              <div className="auth-feature-dot" />
              <span>Experts certifiés</span>
            </div>
            <div className="auth-feature">
              <div className="auth-feature-dot" />
              <span>Communication en temps réel</span>
            </div>
          </div>
        </motion.div>

        {/* Right — form panel */}
        <motion.div
          className="auth-form-panel"
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5, delay: 0.2 }}
        >
          <Outlet />
        </motion.div>
      </div>
    </div>
  );
}
