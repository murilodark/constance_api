<script setup>
import { ref, onMounted, computed } from 'vue';
import api from '@/services/api';
import LoadingOverlay from '@/components/common/LoadingOverlay.vue';
import ToastNotification from '@/components/common/ToastNotification.vue';

// --- ESTADOS ---
const pedidos = ref([]);
const pagination = ref({});
const isLoading = ref(false);
const error = ref(null);

// Controle do Modal de Visualização
const showVisualizarModal = ref(false);
const pedidoParaVisualizar = ref(null);

// --- FUNÇÕES ---

const carregarPedidos = async (page = 1) => {
    isLoading.value = true;
    try {
        const res = await api.get('/pedidos/meus-pedidos', { params: { page } });
        const resultado = res.data.data;
        pedidos.value = resultado.data;
        pagination.value = resultado;
    } catch (e) {
        error.value = "Erro ao carregar seu histórico de pedidos.";
    } finally {
        isLoading.value = false;
    }
};

const abrirVisualizacao = (pedido) => {
    pedidoParaVisualizar.value = pedido;
    showVisualizarModal.value = true;
};

const formatarMoeda = (valor) => {
    return Number(valor).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
};

onMounted(() => carregarPedidos());
</script>

<template>
    <div class="relative min-h-[600px]">
        <LoadingOverlay :show="isLoading" />
        <ToastNotification :error="error" @close="error = null" />

        <!-- Cabeçalho da Página -->
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-black text-blue-900 uppercase tracking-tighter">Meus Pedidos</h2>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Acompanhe suas solicitações e vendas</p>
            </div>
            <div class="bg-blue-900 text-white px-6 py-2 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg">
                {{ pagination.total || 0 }} Pedidos Encontrados
            </div>
        </div>

        <!-- Tabela Principal -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-0">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Pedido</th>
                            <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Fornecedor</th>
                            <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Data</th>
                            <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Total</th>
                            <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                            <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="ped in pedidos" :key="ped.id" class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-8 py-6 text-xs font-black text-blue-900 font-mono">#00{{ ped.id }}</td>
                            <td class="px-8 py-6">
                                <p class="text-xs font-black text-gray-700 uppercase">{{ ped.fornecedor.nome }}</p>
                                <p class="text-[9px] text-gray-400 font-bold tracking-widest">{{ ped.fornecedor.cnpj }}</p>
                            </td>
                            <td class="px-8 py-6 text-xs font-bold text-gray-500">
                                {{ new Date(ped.created_at).toLocaleDateString('pt-BR') }}
                            </td>
                            <td class="px-8 py-6 text-right text-sm font-black text-gray-900">
                                {{ formatarMoeda(ped.valor_total) }}
                            </td>
                            <td class="px-8 py-6 text-center">
                                <span :class="{
                                    'bg-amber-100 text-amber-700': ped.status === 'Pendente',
                                    'bg-emerald-100 text-emerald-700': ped.status === 'Concluído'
                                }" class="text-[9px] font-black px-3 py-1 rounded-lg uppercase tracking-widest">
                                    {{ ped.status }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <button @click="abrirVisualizacao(ped)" 
                                    class="bg-blue-900 text-white p-2.5 rounded-xl transition-all active:scale-90 shadow-md hover:bg-blue-800">
                                    👁️
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div v-if="pagination.last_page > 1" class="p-8 bg-gray-50/50 border-t border-gray-100 flex justify-center items-center space-x-4">
                <button @click="carregarPedidos(pagination.current_page - 1)" :disabled="pagination.current_page === 1" 
                    class="px-4 py-2 border-2 border-gray-200 rounded-xl text-[10px] font-black uppercase disabled:opacity-30 hover:border-blue-900 transition-all">
                    Anterior
                </button>
                <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                    Página <span class="text-blue-900">{{ pagination.current_page }}</span> de {{ pagination.last_page }}
                </div>
                <button @click="carregarPedidos(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" 
                    class="px-4 py-2 border-2 border-gray-200 rounded-xl text-[10px] font-black uppercase disabled:opacity-30 hover:border-blue-900 transition-all">
                    Próxima
                </button>
            </div>

            <!-- Empty State -->
            <div v-if="pedidos.length === 0 && !isLoading" class="p-32 text-center">
                <span class="text-6xl block mb-6 grayscale">📦</span>
                <h3 class="text-xs font-black text-gray-300 uppercase tracking-[0.3em]">Nenhum pedido encontrado</h3>
            </div>
        </div>

        <!-- MODAL DE VISUALIZAÇÃO (DENTRO DO MESMO ARQUIVO) -->
        <div v-if="showVisualizarModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-blue-950/60 backdrop-blur-sm p-4">
            <div class="bg-white w-full max-w-4xl max-h-[85vh] rounded-[3rem] shadow-2xl flex flex-col overflow-hidden animate-in fade-in zoom-in duration-300">
                
                <!-- Modal Header -->
                <div class="p-8 border-b bg-gray-50 flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-900 text-white rounded-2xl flex items-center justify-center text-xl mr-4 shadow-lg">📋</div>
                        <div>
                            <h3 class="text-xl font-black text-blue-900 uppercase tracking-tighter">Pedido #00{{ pedidoParaVisualizar.id }}</h3>
                            <p class="text-[10px] font-bold text-gray-400 uppercase">Emitido em {{ new Date(pedidoParaVisualizar.created_at).toLocaleString('pt-BR') }}</p>
                        </div>
                    </div>
                    <button @click="showVisualizarModal = false" class="bg-white hover:bg-red-50 text-gray-400 hover:text-red-500 p-3 rounded-2xl transition-all font-black text-xs shadow-sm">FECHAR ✕</button>
                </div>

                <!-- Modal Body -->
                <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
                    <!-- Resumo Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                        <div class="bg-gray-50 p-6 rounded-3xl">
                            <p class="text-[10px] font-black text-gray-400 uppercase mb-2">Fornecedor</p>
                            <p class="text-sm font-black text-blue-900 uppercase">{{ pedidoParaVisualizar.fornecedor.nome }}</p>
                            <p class="text-[10px] font-bold text-gray-500 font-mono">{{ pedidoParaVisualizar.fornecedor.cnpj }}</p>
                        </div>
                        <div class="bg-gray-50 p-6 rounded-3xl">
                            <p class="text-[10px] font-black text-gray-400 uppercase mb-2">Status</p>
                            <span :class="pedidoParaVisualizar.status === 'Pendente' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700'" class="text-[10px] font-black px-3 py-1 rounded-lg uppercase tracking-widest">
                                {{ pedidoParaVisualizar.status }}
                            </span>
                        </div>
                        <div class="bg-blue-900 p-6 rounded-3xl shadow-xl">
                            <p class="text-[10px] font-black text-blue-200 uppercase mb-2">Valor Total</p>
                            <p class="text-xl font-black text-white">{{ formatarMoeda(pedidoParaVisualizar.valor_total) }}</p>
                        </div>
                    </div>

                    <!-- Tabela de Itens -->
                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 px-2">Detalhamento dos Produtos</h4>
                    <div class="border border-gray-100 rounded-[2rem] overflow-hidden mb-8">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 text-[10px] font-black text-gray-400 uppercase">
                                <tr>
                                    <th class="px-6 py-4">Produto</th>
                                    <th class="px-6 py-4 text-center">Quantidade</th>
                                    <th class="px-6 py-4 text-right">Unitário</th>
                                    <th class="px-6 py-4 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="item in pedidoParaVisualizar.itens" :key="item.id">
                                    <td class="px-6 py-4">
                                        <p class="text-xs font-black text-gray-700 uppercase">{{ item.produto.nome }}</p>
                                        <p class="text-[9px] font-bold text-gray-400 font-mono">REF: {{ item.produto.referencia }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-black text-blue-900 text-center">{{ item.quantidade }}</td>
                                    <td class="px-6 py-4 text-xs font-bold text-gray-500 text-right">{{ formatarMoeda(item.valor_unitario) }}</td>
                                    <td class="px-6 py-4 text-xs font-black text-gray-900 text-right">{{ formatarMoeda(item.quantidade * item.valor_unitario) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Observação -->
                    <div v-if="pedidoParaVisualizar.observacao" class="bg-amber-50 p-6 rounded-3xl border border-amber-100">
                        <p class="text-[10px] font-black text-amber-700 uppercase mb-2 leading-none">Observações</p>
                        <p class="text-xs text-amber-800 italic leading-relaxed">{{ pedidoParaVisualizar.observacao }}</p>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-8 border-t bg-gray-50 flex justify-end">
                    <button @click="showVisualizarModal = false" class="bg-blue-900 text-white px-10 py-3 rounded-xl font-black uppercase text-[10px] tracking-widest shadow-lg">
                        Fechar Visualização
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
