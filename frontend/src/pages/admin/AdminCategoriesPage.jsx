import { useState } from 'react';
import { Plus, Pencil, Trash2, X, Loader2 } from 'lucide-react';
import { useAdminCategories, useCreateCategory, useUpdateCategory, useDeleteCategory } from '../../hooks/useAdmin';
import './AdminCategoriesPage.css';

const emptyForm = { name: '', icon: '', description: '', sort_order: 0, is_active: true };

export default function AdminCategoriesPage() {
  const { data: categories, isLoading } = useAdminCategories();
  const createCategory = useCreateCategory();
  const updateCategory = useUpdateCategory();
  const deleteCategory = useDeleteCategory();

  const [modal, setModal]   = useState(null); // null | 'create' | 'edit'
  const [form, setForm]     = useState(emptyForm);
  const [editId, setEditId] = useState(null);

  const openCreate = () => { setForm(emptyForm); setModal('create'); };
  const openEdit   = (cat) => { setForm({ name: cat.name, icon: cat.icon ?? '', description: cat.description ?? '', sort_order: cat.sort_order ?? 0, is_active: cat.is_active }); setEditId(cat.id); setModal('edit'); };
  const closeModal = () => { setModal(null); setEditId(null); };

  const handleSubmit = (e) => {
    e.preventDefault();
    if (modal === 'create') {
      createCategory.mutate(form, { onSuccess: closeModal });
    } else {
      updateCategory.mutate({ id: editId, ...form }, { onSuccess: closeModal });
    }
  };

  return (
    <div className="admin-categories">
      <div className="page-header-row">
        <h1>Catégories</h1>
        <button className="btn-primary" onClick={openCreate}><Plus size={16} /> Nouvelle catégorie</button>
      </div>

      {isLoading ? (
        <div className="loading-center"><Loader2 className="spin" size={24} /></div>
      ) : (
        <div className="cat-grid">
          {categories?.map((cat) => (
            <div key={cat.id} className="cat-card">
              <div className="cat-card-header">
                <span className="cat-icon">{cat.icon || '📁'}</span>
                <span className={`cat-status ${cat.is_active ? 'active' : 'inactive'}`}>
                  {cat.is_active ? 'Actif' : 'Inactif'}
                </span>
              </div>
              <h3 className="cat-name">{cat.name}</h3>
              {cat.description && <p className="cat-desc">{cat.description}</p>}
              <div className="cat-meta">{cat.experts_count ?? 0} expert(s)</div>
              <div className="cat-actions">
                <button className="icon-btn" onClick={() => openEdit(cat)}><Pencil size={15} /></button>
                <button className="icon-btn danger" onClick={() => deleteCategory.mutate(cat.id)}><Trash2 size={15} /></button>
              </div>
            </div>
          ))}
        </div>
      )}

      {modal && (
        <div className="modal-overlay" onClick={closeModal}>
          <div className="modal" onClick={(e) => e.stopPropagation()}>
            <div className="modal-header">
              <h2>{modal === 'create' ? 'Nouvelle catégorie' : 'Modifier la catégorie'}</h2>
              <button className="modal-close" onClick={closeModal}><X size={18} /></button>
            </div>
            <form onSubmit={handleSubmit} className="modal-form">
              <label>Nom *<input required value={form.name} onChange={(e) => setForm(f => ({ ...f, name: e.target.value }))} /></label>
              <label>Icône (emoji)<input value={form.icon} onChange={(e) => setForm(f => ({ ...f, icon: e.target.value }))} placeholder="💊" /></label>
              <label>Description<textarea rows={2} value={form.description} onChange={(e) => setForm(f => ({ ...f, description: e.target.value }))} /></label>
              <label>Ordre de tri<input type="number" min={0} value={form.sort_order} onChange={(e) => setForm(f => ({ ...f, sort_order: +e.target.value }))} /></label>
              <label className="checkbox-label">
                <input type="checkbox" checked={form.is_active} onChange={(e) => setForm(f => ({ ...f, is_active: e.target.checked }))} />
                Catégorie active
              </label>
              <button type="submit" className="btn-primary" disabled={createCategory.isPending || updateCategory.isPending}>
                {modal === 'create' ? 'Créer' : 'Enregistrer'}
              </button>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}
