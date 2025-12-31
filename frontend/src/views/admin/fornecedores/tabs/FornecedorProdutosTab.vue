<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/api';
import { handleApiError } from '@/utils/errorHandler';
import LoadingOverlay from '@/components/common/LoadingOverlay.vue';
import ToastNotification from '@/components/common/ToastNotification.vue';

const props = defineProps(['fornecedorId']);

const produtos = ref([]);
const pagination = ref({});
const isLoading = ref(false);
const showModal = ref(false);
const apiError = ref(null);
const successMsg = ref('');

const novoProduto = ref({
    referencia: '',
    nome: '',
    cor: '',
    preco: 0,
    fornecedores_id: null
});

// 1. Listagem (http://localhost:8989/api/v1/fornecedores/{id}/produtos)
const carregarProdutos = async (page = 1) => {
    isLoading.value = true;
    try {
        const res = await api.get(`/fornecedores/${props.fornecedorId}/produtos`, { params: { page } });
        const resultado = res.data.data;
        produtos.value = resultado.data;
        pagination.value = resultado;
    } catch (e) {
        apiError.value = handleApiError(e);
    } finally {
        isLoading.value = false;
    }
};

// 2. Cadastro Manual
const salvarProduto = async () => {
    isLoading.value = true;
    apiError.value = null;
    try {
        const dadosParaEnviar = {
            ...novoProduto.value,
            fornecedores_id: props.fornecedorId
        };
        const res = await api.post('/produtos', dadosParaEnviar);
        if (res.data.status) {
            successMsg.value = res.data.message;
            showModal.value = false;
            novoProduto.value = { referencia: '', nome: '', cor: '', preco: 0, fornecedores_id: props.fornecedorId };
            carregarProdutos();
        }
    } catch (e) { 
        apiError.value = handleApiError(e); 
    } finally { 
        isLoading.value = false; 
    }
};

// 3. Upload de CSV (Novo)
const handleUploadCSV = async (event) => {
    const arquivo = event.target.files[0];
    if (!arquivo) return;

    const formData = new FormData();
    formData.append('arquivo', arquivo);

    isLoading.value = true;
    apiError.value = null;

    try {
        // Rota: http://localhost:8989/api/v1/fornecedores/{id}/produtos/upload
        const res = await api.post(`/fornecedores/${props.fornecedorId}/produtos/upload`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        
        if (res.data.status) {
            successMsg.value = res.data.message;
            event.target.value = ''; // Limpa o input de arquivo
        }
    } catch (e) {
        apiError.value = handleApiError(e);
    } finally {
        isLoading.value = false;
    }
};

onMounted(carregarProdutos);
</script>

<template>
    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 relative">
        <LoadingOverlay :show="isLoading" />
        <ToastNotification :error="apiError" :success="successMsg" @close="apiError = null; successMsg = ''" />

        <div class="flex justify-between items-center mb-8">
            <h3 class="font-black text-gray-800 uppercase tracking-tighter">Catálogo de Produtos</h3>
            
            <div class="flex gap-3">
                <!-- Botão Upload CSV -->
                <label class="cursor-pointer bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest shadow-lg transition-all flex items-center">
                    <svg xmlns="www.w3.org" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Upload CSV
                    <input type="file" class="hidden" accept=".csv" @change="handleUploadCSV" />
                </label>

                <!-- Botão Novo Produto -->
                <button @click="showModal = true" class="bg-blue-900 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest shadow-lg">
                    + Novo Produto
                </button>
            </div>
        </div>

        <!-- Tabela de Produtos -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[10px] font-black text-gray-400 uppercase border-b border-gray-50">
                        <th class="pb-4">Ref</th>
                        <th class="pb-4">Nome</th>
                        <th class="pb-4">Cor</th>
                        <th class="pb-4 text-right">Preço</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr v-for="prod in produtos" :key="prod.id" class="hover:bg-gray-50 transition-colors">
                        <td class="py-4 text-xs font-bold text-gray-400 font-mono">{{ prod.referencia }}</td>
                        <td class="py-4 text-sm font-black text-gray-700 uppercase">{{ prod.nome }}</td>
                        <td class="py-4 text-xs font-bold text-gray-500 uppercase">{{ prod.cor }}</td>
                        <td class="py-4 text-sm font-black text-blue-900 text-right">
                            {{ Number(prod.preco).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        <div v-if="pagination.last_page > 1" class="mt-8 flex justify-center space-x-2">
            <button @click="carregarProdutos(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="px-3 py-1 border rounded-lg text-xs">Anterior</button>
            <span class="text-xs font-bold py-1">Página {{ pagination.current_page }} de {{ pagination.last_page }}</span>
            <button @click="carregarProdutos(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="px-3 py-1 border rounded-lg text-xs">Próxima</button>
        </div>

        <!-- Modal de Cadastro -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-blue-900/50 backdrop-blur-sm p-4">
            <div class="bg-white w-full max-w-md rounded-[2rem] shadow-2xl p-8">
                <h4 class="text-xl font-black text-blue-900 mb-6 uppercase tracking-tighter">Novo Produto</h4>
                <div class="space-y-4">
                    <input v-model="novoProduto.referencia" placeholder="Referência (Ex: CALC-2025)" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 outline-none focus:border-blue-900 text-sm" />
                    <input v-model="novoProduto.nome" placeholder="Nome do Produto" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 outline-none focus:border-blue-900 text-sm" />
                    <input v-model="novoProduto.cor" placeholder="Cor" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 outline-none focus:border-blue-900 text-sm" />
                    <input v-model.number="novoProduto.preco" type="number" step="0.01" placeholder="Preço" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 outline-none focus:border-blue-900 text-sm" />
                </div>
                <div class="flex justify-end mt-8 space-x-4">
                    <button @click="showModal = false" class="text-xs font-black text-gray-400 uppercase">Cancelar</button>
                    <button @click="salvarProduto" class="bg-blue-900 text-white px-8 py-3 rounded-xl font-black uppercase text-xs">Salvar Produto</button>
                </div>
            </div>
        </div>
    </div>
</template>
