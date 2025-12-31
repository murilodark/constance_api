export const handleApiError = (error) => {
    const response = error.response?.data;
    
    // 1. Erro de Validação (422) - Extrai o primeiro erro de cada campo
    if (response?.code === 422 && typeof response.data === 'object' && response.data !== null) {
        const messages = Object.values(response.data).flat();
        return {
            title: response.message,
            details: messages, // Array com todas as mensagens específicas
            type: 'error'
        };
    }

    // 2. Erros genéricos (401, 404, 500)
    return {
        title: response?.message || 'Erro inesperado no servidor',
        details: [],
        type: 'error'
    };
};
