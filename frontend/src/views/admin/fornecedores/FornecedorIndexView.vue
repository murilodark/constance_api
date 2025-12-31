<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/api';

// Componentes Globais
import LoadingOverlay from '@/components/common/LoadingOverlay.vue';
import ToastNotification from '@/components/common/ToastNotification.vue';
import { handleApiError } from '@/utils/errorHandler';

const fornecedores = ref([]);
const pagination = ref({});
const isLoading = ref(false);
const apiError = ref(null);

const carregarFornecedores = async (page = 1) => {
    isLoading.value = true;
    try {
        const response = await api.get('/fornecedores', { params: { page } });
        const resultado = response.data.data;
        
        fornecedores.value = resultado.data;
        pagination.value = {
            current_page: resultado.current_page,
            last_page: resultado.last_page,
            total: resultado.total
        };
    } catch (error) {
        apiError.value = handleApiError(error);
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => carregarFornecedores());
</script>

<template>
    <div class="relative min-h-screen pb-20">
        <LoadingOverlay :show="isLoading" message="Sincronizando fornecedores..." />
        <ToastNotification :error="apiError" @close="apiError = null" />

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 gap-4">
            <div>
                <h1 class="text-3xl font-black text-gray-800 uppercase tracking-tighter">Fornecedores</h1>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Gestão de Parceiros Homologados</p>
            </div>
            <router-link :to="{ name: 'admin.fornecedores.dashboard' }" 
                class="bg-blue-900 text-white px-8 py-4 rounded-2xl font-black uppercase text-xs tracking-widest hover:scale-105 transition-all shadow-lg shadow-blue-900/20">
                + Adicionar Fornecedor
            </router-link>
        </div>

        <!-- Listagem em Linhas (Tabela) -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100">
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Fornecedor</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Documento</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Localização</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="f in fornecedores" :key="f.id" class="hover:bg-blue-50/30 transition-all group">
                            <!-- Coluna Nome -->
                            <td class="px-8 py-5">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center text-white text-xs font-black shadow-sm">
                                        {{ f.nome.charAt(0) }}
                                    </div>
                                    <div>
                                        <p class="font-black text-gray-800 uppercase text-xs tracking-tight">{{ f.nome }}</p>
                                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-tighter">ID: #{{ f.id }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Coluna Documento -->
                            <td class="px-8 py-5 text-xs font-bold text-gray-500 font-mono tracking-tighter">
                                {{ f.cnpj }}
                            </td>

                            <!-- Coluna Localização -->
                            <td class="px-8 py-5">
                                <div v-if="f.enderecos?.[0]" class="text-xs text-gray-500">
                                    <p class="font-bold text-gray-700 uppercase tracking-tighter">{{ f.enderecos[0].cidade }}</p>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase">{{ f.enderecos[0].estado }}</p>
                                </div>
                                <span v-else class="text-[10px] text-gray-300 italic">Não informado</span>
                            </td>

                            <!-- Coluna Status -->
                            <td class="px-8 py-5 text-center">
                                <span :class="f.status === 'ativo' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                                    class="text-[9px] font-black uppercase px-3 py-1 rounded-md tracking-tighter">
                                    {{ f.status }}
                                </span>
                            </td>

                            <!-- Coluna Ações -->
                            <td class="px-8 py-5 text-right">
                                <router-link 
                                    :to="{ name: 'admin.fornecedores.dashboard', params: { id: f.id } }" 
                                    class="inline-block bg-white border-2 border-blue-900 text-blue-900 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-900 hover:text-white transition-all shadow-sm"
                                >
                                    Gerenciar
                                </router-link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div v-if="fornecedores.length === 0 && !isLoading" class="p-20 text-center">
                <span class="text-4xl block mb-2">🏢</span>
                <p class="text-xs font-black text-gray-300 uppercase tracking-widest">Nenhum fornecedor na base de dados</p>
            </div>
        </div>

        <!-- Paginação -->
        <div v-if="pagination.last_page > 1" class="mt-8 flex justify-center items-center space-x-2">
            <button @click="carregarFornecedores(pagination.current_page - 1)" 
                :disabled="pagination.current_page === 1"
                class="w-10 h-10 flex items-center justify-center bg-white rounded-xl border border-gray-100 disabled:opacity-20 hover:bg-gray-50 shadow-sm">
                ◀
            </button>
            
            <div class="flex space-x-1">
                <button v-for="p in pagination.last_page" :key="p" @click="carregarFornecedores(p)"
                    :class="pagination.current_page === p ? 'bg-blue-900 text-white shadow-lg' : 'bg-white text-gray-400'"
                    class="w-10 h-10 rounded-xl font-black text-[10px] border border-gray-100 transition-all">
                    {{ p }}
                </button>
            </div>

            <button @click="carregarFornecedores(pagination.current_page + 1)" 
                :disabled="pagination.current_page === pagination.last_page"
                class="w-10 h-10 flex items-center justify-center bg-white rounded-xl border border-gray-100 disabled:opacity-20 hover:bg-gray-50 shadow-sm">
                ▶
            </button>
        </div>
    </div>
</template>
