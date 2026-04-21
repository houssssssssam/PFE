import { useState } from 'react';
import { Search, ToggleLeft, ToggleRight, Loader2 } from 'lucide-react';
import { useAdminUsers, useToggleUser } from '../../hooks/useAdmin';
import CustomSelect from '../../components/CustomSelect';
import './AdminUsersPage.css';

export default function AdminUsersPage() {
  const [search, setSearch]   = useState('');
  const [role, setRole]       = useState('');
  const [page, setPage]       = useState(1);

  const { data, isLoading } = useAdminUsers({ search, role, page });
  const toggleUser          = useToggleUser();

  const users = data?.data?.data ?? [];
  const meta  = data?.meta ?? {};

  return (
    <div className="admin-users">
      <div className="page-header">
        <h1>Utilisateurs</h1>
      </div>

      <div className="filters-bar">
        <div className="search-box">
          <Search size={16} />
          <input
            placeholder="Rechercher par nom ou email..."
            value={search}
            onChange={(e) => { setSearch(e.target.value); setPage(1); }}
          />
        </div>
        <CustomSelect
          value={role}
          onChange={(e) => { setRole(e.target.value); setPage(1); }}
          placeholder="Tous les rôles"
          options={[
            { value: 'user', label: 'Utilisateur' },
            { value: 'expert', label: 'Expert' },
            { value: 'admin', label: 'Admin' },
          ]}
        />
      </div>

      {isLoading ? (
        <div className="table-loading"><Loader2 className="spin" size={24} /></div>
      ) : (
        <>
          <table className="data-table">
            <thead>
              <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Statut</th>
                <th>Inscrit le</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              {users.map((u) => (
                <tr key={u.id}>
                  <td>{u.name}</td>
                  <td className="text-muted">{u.email}</td>
                  <td><span className={`role-badge role-${u.role}`}>{u.role}</span></td>
                  <td>
                    <span className={`status-dot ${u.is_active ? 'active' : 'inactive'}`}>
                      {u.is_active ? 'Actif' : 'Inactif'}
                    </span>
                  </td>
                  <td className="text-muted">{new Date(u.created_at).toLocaleDateString('fr-FR')}</td>
                  <td>
                    <button
                      className="toggle-btn"
                      onClick={() => toggleUser.mutate(u.id)}
                      disabled={toggleUser.isPending}
                      title={u.is_active ? 'Désactiver' : 'Activer'}
                    >
                      {u.is_active ? <ToggleRight size={20} className="icon-active" /> : <ToggleLeft size={20} className="icon-inactive" />}
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>

          {meta.last_page > 1 && (
            <div className="pagination">
              <button disabled={page <= 1} onClick={() => setPage(p => p - 1)}>Préc.</button>
              <span>{page} / {meta.last_page}</span>
              <button disabled={page >= meta.last_page} onClick={() => setPage(p => p + 1)}>Suiv.</button>
            </div>
          )}
        </>
      )}
    </div>
  );
}
