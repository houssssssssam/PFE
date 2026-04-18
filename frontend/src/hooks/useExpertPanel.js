import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import api from '../lib/api';

export function useExpertDashboard() {
  return useQuery({
    queryKey: ['expert-dashboard'],
    queryFn: async () => {
      const { data } = await api.get('/expert/dashboard');
      return data.data;
    },
  });
}

export function useExpertProfile() {
  return useQuery({
    queryKey: ['expert-profile'],
    queryFn: async () => {
      const { data } = await api.get('/expert/profile');
      return data.data;
    },
  });
}

export function useUpdateExpertProfile() {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: (payload) => api.put('/expert/profile', payload),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['expert-profile'] });
    },
  });
}

export function useToggleAvailability() {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: (is_available) => api.put('/expert/availability', { is_available }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['expert-dashboard'] });
      queryClient.invalidateQueries({ queryKey: ['expert-profile'] });
    },
  });
}

export function useExpertWallet() {
  return useQuery({
    queryKey: ['expert-wallet'],
    queryFn: async () => {
      const { data } = await api.get('/expert/wallet');
      return data.data;
    },
  });
}

export function useExpertTransactions(page = 1) {
  return useQuery({
    queryKey: ['expert-transactions', page],
    queryFn: async () => {
      const { data } = await api.get(`/expert/wallet/transactions?page=${page}&per_page=10`);
      return data;
    },
  });
}
