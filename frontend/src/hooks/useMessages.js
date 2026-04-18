import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import api from '../lib/api';

export function useMessages(conversationId) {
  return useQuery({
    queryKey: ['messages', conversationId],
    queryFn: async () => {
      const { data } = await api.get(`/conversations/${conversationId}/messages?per_page=100`);
      return data;
    },
    enabled: !!conversationId,
    staleTime: 0,
  });
}

export function useSendMessage(conversationId) {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: (content) =>
      api.post(`/conversations/${conversationId}/messages`, { content, type: 'text' }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['messages', conversationId] });
      queryClient.invalidateQueries({ queryKey: ['conversations'] });
    },
  });
}

export function useCreateConversation() {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: (payload) => api.post('/conversations', payload),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['conversations'] });
    },
  });
}
