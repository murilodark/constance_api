<script setup>
import { ref } from 'vue';
import api from '@/services/api';
import { handleApiError } from '@/utils/errorHandler';
import LoadingOverlay from '@/components/common/LoadingOverlay.vue';
import ToastNotification from '@/components/common/ToastNotification.vue';

const isLoading = ref(false);
const apiError = ref(null);
const successMsg = ref('');

const dispararReport = async () => {
    isLoading.value = true;
    apiError.value = null;
    successMsg.value = '';

    try {
        const res = await api.post('/pedidos/report-diario');
        if (res.data.status) {
            successMsg.value = res.data.message;
        }
    } catch (e) {
        apiError.value = handleApiError(e);
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <div class="max-w-4xl mx-auto relative min-h-[400px]">
        <LoadingOverlay :show="isLoading" />
        <ToastNotification :error="apiError" :success="successMsg" @close="apiError = null; successMsg = ''" />

        <div class="bg-white rounded-[2.5rem] p-12 shadow-sm border border-gray-100 text-center">
            <div class="w-24 h-24 bg-blue-50 rounded-3xl flex items-center justify-center mx-auto mb-8">
                <span class="text-4xl">📧</span>
            </div>
            
            <h2 class="text-3xl font-black text-blue-900 uppercase tracking-tighter mb-4">
                Relatório Diário de Pedidos
            </h2>
            
            <p class="text-gray-500 font-medium max-w-md mx-auto mb-10 leading-relaxed">
                Ao clicar no botão abaixo, o sistema irá processar o resumo dos últimos <span class="text-blue-900 font-black">7 dias</span> de vendas e enviará um relatório detalhado para o seu e-mail cadastrado.
            </p>

            <button 
                @click="dispararReport"
                :disabled="isLoading"
                class="bg-blue-900 hover:bg-blue-800 text-white px-12 py-5 rounded-2xl font-black uppercase text-xs tracking-[0.2em] shadow-xl shadow-blue-900/20 transition-all active:scale-95 disabled:opacity-50"
            >
                {{ isLoading ? 'Processando...' : 'Solicitar Report por E-mail' }}
            </button>

            <div class="mt-12 pt-8 border-t border-gray-50 flex justify-center space-x-8 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                <div class="flex items-center"><span class="mr-2 text-blue-900">●</span> Processamento em Job</div>
                <div class="flex items-center"><span class="mr-2 text-blue-900">●</span> Envio via SMTP</div>
            </div>
        </div>
    </div>
</template>
