import { useQuery } from '@tanstack/react-query';
import api from '../lib/api';

export function useConversations() {
  return useQuery({
    queryKey: ['conversations'],
    queryFn: async () => {
      const { data } = await api.get('/conversations?per_page=5');
      return data;
    },
  });
}

export function useConversation(id) {
  return useQuery({
    queryKey: ['conversations', id],
    queryFn: async () => {
      const { data } = await api.get(`/conversations/${id}`);
      return data.data;
    },
    enabled: !!id,
  });
}
