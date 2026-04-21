import { useState } from 'react';
import { Loader2 } from 'lucide-react';
import { useAdminConversations } from '../../hooks/useAdmin';
import CustomSelect from '../../components/CustomSelect';
import './AdminConversationsPage.css';

const STATUS_COLORS = { ai: 'blue', expert: 'teal', open: 'green', closed: 'gray' };

export default function AdminConversationsPage() {
  const [status, setStatus] = useState('');
  const [page, setPage]     = useState(1);

  const { data, isLoading } = useAdminConversations({ status, page });
  const conversations = data?.data?.data ?? [];
  const meta          = data?.meta ?? {};

  return (
    <div className="admin-conversations">
      <div className="page-header">
        <h1>Conversations</h1>
      </div>

      <div className="filters-bar">
        <CustomSelect
          value={status}
          onChange={(e) => { setStatus(e.target.value); setPage(1); }}
          placeholder="Tous les statuts"
          options={[
            { value: 'ai', label: 'IA' },
            { value: 'expert', label: 'Expert' },
            { value: 'open', label: 'Ouvert' },
            { value: 'closed', label: 'Fermé' },
          ]}
        />
      </div>

      {isLoading ? (
        <div className="loading-center"><Loader2 className="spin" size={24} /></div>
      ) : (
        <>
          <table className="data-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Utilisateur</th>
                <th>Expert / IA</th>
                <th>Catégorie</th>
                <th>Statut</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              {conversations.map((c) => (
                <tr key={c.id}>
                  <td className="text-muted">{c.id}</td>
                  <td>{c.user?.name}</td>
                  <td>{c.expert ? c.expert.user?.name : <span className="ia-badge">IA</span>}</td>
                  <td>{c.category?.name}</td>
                  <td>
                    <span className={`status-badge status-${STATUS_COLORS[c.status] ?? 'gray'}`}>
                      {c.status}
                    </span>
                  </td>
                  <td className="text-muted">{new Date(c.created_at).toLocaleDateString('fr-FR')}</td>
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
