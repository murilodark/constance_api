import axios from 'axios';

const api = axios.create({
    // LÃª do seu .env (VITE_API_BASE_URL)
    baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8989/api/v1',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
});

// Interceptor para anexar o Token em todas as chamadas automaticamente
api.interceptors.request.use((config) => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

export default api;