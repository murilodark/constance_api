import { defineStore } from 'pinia';
import api from '@/services/api';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: JSON.parse(localStorage.getItem('user')) || null,
        token: localStorage.getItem('token') || null,
    }),
    getters: {
        isAuthenticated: (state) => !!state.token,
        isVendedor: (state) => state.user?.tipo === 'vendedor',
        isAdmin: (state) => state.user?.tipo === 'admin',
    },
    actions: {
        async login(credentials) {
            const response = await api.post('/login', credentials);
            const { data } = response.data; // PadrÃ£o da sua API

            this.token = data.token;
            this.user = data.user;

            localStorage.setItem('token', data.token);
            localStorage.setItem('user', JSON.stringify(data.user));
        },
        logout() {
            this.user = null;
            this.token = null;
            localStorage.clear();
        }
    }
});