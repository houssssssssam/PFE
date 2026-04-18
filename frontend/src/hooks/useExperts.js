import { useQuery } from '@tanstack/react-query';
import api from '../lib/api';

export function useExperts(params = {}) {
  return useQuery({
    queryKey: ['experts', params],
    queryFn: async () => {
      const { data } = await api.get('/experts', { params });
      return data;
    },
  });
}

export function useExpert(id) {
  return useQuery({
    queryKey: ['experts', id],
    queryFn: async () => {
      const { data } = await api.get(`/experts/${id}`);
      return data.data;
    },
    enabled: !!id,
  });
}

export function useExpertReviews(id, params = {}) {
  return useQuery({
    queryKey: ['experts', id, 'reviews', params],
    queryFn: async () => {
      const { data } = await api.get(`/experts/${id}/reviews`, { params });
      return data;
    },
    enabled: !!id,
  });
}

export function useCategories() {
  return useQuery({
    queryKey: ['categories'],
    queryFn: async () => {
      const { data } = await api.get('/categories');
      return data.data;
    },
    staleTime: 1000 * 60 * 10,
  });
}
