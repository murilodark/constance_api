<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/api';
import LoadingOverlay from '@/components/common/LoadingOverlay.vue';
import FornecedorPedidoItens from './FornecedorPedidoItens.vue';

const props = defineProps(['fornecedorCnpj']);
const pedidos = ref([]);
const pagination = ref({});
const isLoading = ref(false);
const pedidoExpandido = ref(null); // Controla qual pedido mostra os itens

const carregarPedidos = async (page = 1) => {
    if (!props.fornecedorCnpj) return;
    isLoading.value = true;
    try {
        const res = await api.get(`/fornecedor/${props.fornecedorCnpj}/pedidos`, { params: { page } });
        // Estrutura baseada no seu JSON: res.data.data (objeto de paginação)
        const resultado = res.data.data;
        pedidos.value = resultado.data;
        pagination.value = resultado;
    } catch (e) {
        console.error("Erro ao buscar pedidos do fornecedor");
    } finally {
        isLoading.value = false;
    }
};

const toggleDetalhes = (id) => {
    pedidoExpandido.value = pedidoExpandido.value === id ? null : id;
};

onMounted(carregarPedidos);
</script>

<template>
    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 relative min-h-[500px]">
        <LoadingOverlay :show="isLoading" />

        <div class="flex justify-between items-center mb-10">
            <div>
                <h3 class="font-black text-gray-800 uppercase tracking-tighter text-xl">Histórico de Vendas</h3>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Dados filtrados por CNPJ</p>
            </div>
            <div class="text-right">
                <span class="text-[10px] font-black bg-blue-900 text-white px-4 py-2 rounded-xl uppercase tracking-widest shadow-lg">
                    Total: {{ pagination.total || 0 }} Pedidos
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-separate border-spacing-y-3">
                <thead>
                    <tr class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                        <th class="px-6 pb-2">ID</th>
                        <th class="px-6 pb-2">Data</th>
                        <th class="px-6 pb-2">Status</th>
                        <th class="px-6 pb-2 text-right">Valor Total</th>
                        <th class="px-6 pb-2 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="ped in pedidos" :key="ped.id">
                        <!-- Linha Principal do Pedido -->
                        <tr class="bg-gray-50/50 hover:bg-gray-50 transition-all group">
                            <td class="px-6 py-5 rounded-l-2xl text-xs font-black text-blue-900">#{{ ped.id }}</td>
                            <td class="px-6 py-5 text-xs font-bold text-gray-500">
                                {{ new Date(ped.created_at).toLocaleDateString('pt-BR') }}
                            </td>
                            <td class="px-6 py-5">
                                <span :class="ped.status === 'Pendente' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700'" 
                                      class="text-[9px] font-black px-3 py-1 rounded-lg uppercase tracking-widest">
                                    {{ ped.status }}
                                </span>
                            </td>
                            <td class="px-6 py-5 text-sm font-black text-gray-700 text-right font-mono">
                                R$ {{ Number(ped.valor_total).toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }}
                            </td>
                            <td class="px-6 py-5 rounded-r-2xl text-right">
                                <button @click="toggleDetalhes(ped.id)" 
                                    class="bg-white border-2 border-gray-100 p-2 rounded-xl hover:border-blue-900 transition-all shadow-sm group-hover:scale-110">
                                    <span v-if="pedidoExpandido === ped.id">❌</span>
                                    <span v-else>👁️</span>
                                </button>
                            </td>
                        </tr>

                        <!-- Componente de Itens (Aparece quando clicado) -->
                        <tr v-if="pedidoExpandido === ped.id">
                            <td colspan="5" class="px-2">
                                <FornecedorPedidoItens :itens="ped.itens" />
                                <div v-if="ped.observacao" class="px-6 pb-4">
                                    <p class="text-[9px] font-black text-gray-400 uppercase mb-1">Observações:</p>
                                    <p class="text-xs text-gray-500 italic bg-gray-50 p-3 rounded-lg border-l-4 border-blue-900">
                                        {{ ped.observacao }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Paginação customizada para 2025 -->
        <div v-if="pagination.last_page > 1" class="mt-12 flex justify-center items-center space-x-6">
            <button @click="carregarPedidos(pagination.current_page - 1)" :disabled="pagination.current_page === 1" 
                class="w-10 h-10 flex items-center justify-center border-2 border-gray-100 rounded-xl hover:border-blue-900 disabled:opacity-20 transition-all">
                ⬅️
            </button>
            <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">
                Página <span class="text-blue-900">{{ pagination.current_page }}</span> de {{ pagination.last_page }}
            </div>
            <button @click="carregarPedidos(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" 
                class="w-10 h-10 flex items-center justify-center border-2 border-gray-100 rounded-xl hover:border-blue-900 disabled:opacity-20 transition-all">
                ➡️
            </button>
        </div>

        <!-- Estado Vazio -->
        <div v-if="pedidos.length === 0 && !isLoading" class="py-24 text-center">
            <span class="text-5xl block mb-4 grayscale">📁</span>
            <p class="text-xs font-black text-gray-300 uppercase tracking-widest">Nenhuma transação comercial encontrada</p>
        </div>
    </div>
</template>
