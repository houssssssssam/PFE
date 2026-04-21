import { useState, useRef, useEffect } from 'react';
import { ChevronDown, Check } from 'lucide-react';
import './CustomSelect.css';

export default function CustomSelect({ options = [], value, onChange, placeholder = 'Sélectionnez...', required }) {
  const [open, setOpen] = useState(false);
  const ref = useRef();

  const selected = options.find((o) => String(o.value) === String(value));

  useEffect(() => {
    const handler = (e) => { if (!ref.current?.contains(e.target)) setOpen(false); };
    document.addEventListener('mousedown', handler);
    return () => document.removeEventListener('mousedown', handler);
  }, []);

  const choose = (val) => {
    onChange({ target: { value: val } });
    setOpen(false);
  };

  return (
    <div className={`cselect ${open ? 'cselect--open' : ''}`} ref={ref}>
      <button type="button" className="cselect-trigger" onClick={() => setOpen(!open)}>
        <span className={selected ? 'cselect-value' : 'cselect-placeholder'}>
          {selected ? selected.label : placeholder}
        </span>
        <ChevronDown size={16} className="cselect-chevron" />
      </button>

      {open && (
        <div className="cselect-dropdown">
          {options.map((opt) => (
            <button
              key={opt.value}
              type="button"
              className={`cselect-option ${String(opt.value) === String(value) ? 'cselect-option--active' : ''}`}
              onClick={() => choose(opt.value)}
            >
              <span>{opt.label}</span>
              {String(opt.value) === String(value) && <Check size={14} className="cselect-check" />}
            </button>
          ))}
        </div>
      )}

      {required && (
        <select
          tabIndex={-1}
          aria-hidden="true"
          value={value}
          onChange={() => {}}
          required
          style={{ position: 'absolute', bottom: 0, left: 0, width: '100%', height: 0, opacity: 0, border: 'none', padding: 0, pointerEvents: 'none' }}
        >
          <option value="" />
          {options.map((o) => <option key={o.value} value={o.value}>{o.label}</option>)}
        </select>
      )}
    </div>
  );
}
