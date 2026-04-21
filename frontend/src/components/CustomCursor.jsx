import { useEffect, useRef } from 'react';
import './CustomCursor.css';

export default function CustomCursor() {
  const dotRef  = useRef();
  const ringRef = useRef();

  useEffect(() => {
    const dot  = dotRef.current;
    const ring = ringRef.current;
    if (!dot || !ring) return;

    let ringX = -100, ringY = -100;
    let dotX  = -100, dotY  = -100;
    let visible = false;
    let rafId;

    const onMove = (e) => {
      dotX = e.clientX;
      dotY = e.clientY;
      if (!visible) {
        visible = true;
        dot.style.opacity  = '1';
        ring.style.opacity = '1';
      }
    };

    const lerp = (a, b, t) => a + (b - a) * t;

    const animate = () => {
      ringX = lerp(ringX, dotX, 0.12);
      ringY = lerp(ringY, dotY, 0.12);

      dot.style.transform  = `translate(${dotX - 4}px, ${dotY - 4}px)`;
      ring.style.transform = `translate(${ringX - 16}px, ${ringY - 16}px)`;

      rafId = requestAnimationFrame(animate);
    };

    const onEnter = () => ring.classList.add('cursor-ring--hover');
    const onLeave = () => ring.classList.remove('cursor-ring--hover');

    window.addEventListener('mousemove', onMove);
    document.querySelectorAll('a,button,[role=button]').forEach(el => {
      el.addEventListener('mouseenter', onEnter);
      el.addEventListener('mouseleave', onLeave);
    });

    animate();

    return () => {
      cancelAnimationFrame(rafId);
      window.removeEventListener('mousemove', onMove);
    };
  }, []);

  return (
    <>
      <div ref={dotRef}  className="cursor-dot" />
      <div ref={ringRef} className="cursor-ring" />
    </>
  );
}
