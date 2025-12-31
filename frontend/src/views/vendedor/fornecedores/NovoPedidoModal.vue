<script setup>
import { ref, onMounted, computed } from 'vue';
import api from '@/services/api';
import LoadingOverlay from '@/components/common/LoadingOverlay.vue';
import ToastNotification from '@/components/common/ToastNotification.vue';

const props = defineProps(['fornecedor']);
const emit = defineEmits(['close']);

const produtosDisponiveis = ref([]);
const carrinho = ref([]);
const observacao = ref('');
const isLoading = ref(false);
const error = ref(null);
const success = ref('');

// 1. Carregar produtos do fornecedor específico
const carregarProdutos = async () => {
    isLoading.value = true;
    try {
        const res = await api.get(`/fornecedores/${props.fornecedor.id}/produtos`);
        // Lida com resposta paginada ou simples
        produtosDisponiveis.value = res.data.data.data || res.data.data || [];
    } catch (e) {
        error.value = "Erro ao carregar catálogo do fornecedor.";
    } finally {
        isLoading.value = false;
    }
};

// 2. Adicionar ao carrinho com tratamento de tipos
const adicionarAoCarrinho = (prod) => {
    const item = carrinho.value.find(i => i.produtos_id === prod.id);
    if (item) {
        item.quantidade++;
    } else {
        carrinho.value.push({
            produtos_id: prod.id,
            nome: prod.nome,
            referencia: prod.referencia,
            preco: Number(prod.preco), // Força tipo numérico
            quantidade: 1
        });
    }
};

// 3. Remover item do carrinho
const removerDoCarrinho = (index) => {
    carrinho.value.splice(index, 1);
};

// 4. Cálculo do total reativo
const totalPedido = computed(() => {
    return carrinho.value.reduce((acc, item) => acc + (item.preco * item.quantidade), 0);
});

// 5. Finalizar o pedido (POST)
const finalizarPedido = async () => {
    if (carrinho.value.length === 0) return;
    isLoading.value = true;
    error.value = null;
    try {
        const payload = {
            fornecedores_id: props.fornecedor.id,
            observacao: observacao.value,
            produtos: carrinho.value.map(i => ({ 
                produtos_id: i.produtos_id, 
                quantidade: i.quantidade 
            }))
        };
        const res = await api.post('/pedidos', payload);
        success.value = res.data.message;
        
        // Fecha o modal após 2 segundos de sucesso
        setTimeout(() => emit('close'), 2000);
    } catch (e) {
        error.value = e.response?.data?.message || "Erro ao salvar pedido.";
    } finally {
        isLoading.value = false;
    }
};

onMounted(carregarProdutos);
</script>

<template>
    <div class="fixed inset-0 z-[60] flex items-center justify-center bg-blue-950/40 backdrop-blur-md p-4">
        <div class="bg-white w-full max-w-6xl h-[90vh] rounded-[3rem] shadow-2xl flex flex-col overflow-hidden relative">
            
            <LoadingOverlay :show="isLoading" />
            <ToastNotification :error="error" :success="success" @close="error = null" />

            <!-- Header -->
            <div class="p-8 border-b flex justify-between items-center bg-gray-50">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-900 text-white rounded-2xl flex items-center justify-center text-xl mr-4 shadow-lg">🏢</div>
                    <div>
                        <h3 class="text-xl font-black text-blue-900 uppercase tracking-tighter">Novo Pedido: {{ fornecedor.nome }}</h3>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">CNPJ: {{ fornecedor.cnpj }}</p>
                    </div>
                </div>
                <button @click="emit('close')" class="bg-gray-100 hover:bg-red-50 text-gray-400 hover:text-red-500 p-3 rounded-2xl transition-all font-black text-xs">FECHAR ✕</button>
            </div>

            <div class="flex-1 flex overflow-hidden">
                <!-- Coluna: Produtos Disponíveis -->
                <div class="w-2/3 p-8 overflow-y-auto border-r custom-scrollbar bg-white">
                    <div class="grid grid-cols-2 gap-6">
                        <div v-for="p in produtosDisponiveis" :key="p.id" class="p-6 border-2 border-gray-50 rounded-[2rem] transition-all bg-white flex flex-col justify-between">
                            <div>
                                <p class="text-[10px] font-black text-blue-900 uppercase mb-1 tracking-widest">{{ p.referencia }}</p>
                                <h4 class="text-sm font-bold text-gray-700 uppercase mb-4 leading-tight">{{ p.nome }}</h4>
                            </div>
                            
                            <div class="flex justify-between items-center mt-4">
                                <div class="text-sm font-black text-gray-900">
                                    R$ {{ Number(p.preco).toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }}
                                </div>
                                <button 
                                    @click="adicionarAoCarrinho(p)" 
                                    class="bg-blue-900 hover:bg-emerald-600 text-white px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all active:scale-90 shadow-md"
                                >
                                    + Adicionar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div v-if="produtosDisponiveis.length === 0 && !isLoading" class="text-center py-20 opacity-20 italic">
                        Nenhum produto cadastrado para este fornecedor.
                    </div>
                </div>

                <!-- Coluna: Carrinho / Resumo -->
                <div class="w-1/3 p-8 bg-gray-50 flex flex-col">
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="font-black text-blue-900 uppercase text-xs tracking-widest">Resumo do Pedido</h4>
                        <span class="bg-blue-200 text-blue-900 text-[10px] font-black px-2 py-0.5 rounded-md">{{ carrinho.length }} itens</span>
                    </div>
                    
                    <!-- Lista de Itens no Carrinho -->
                    <div class="flex-1 overflow-y-auto space-y-3 mb-6 pr-2 custom-scrollbar">
                        <div v-for="(item, index) in carrinho" :key="item.id" class="bg-white p-4 rounded-2xl shadow-sm border border-transparent hover:border-gray-200 transition-all flex justify-between items-center">
                            <div class="max-w-[140px]">
                                <p class="text-[10px] font-black uppercase truncate text-gray-800">{{ item.nome }}</p>
                                <p class="text-[9px] text-gray-400 font-bold">R$ {{ item.preco.toFixed(2) }}</p>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                <!-- Qtd -->
                                <div class="flex items-center bg-gray-100 rounded-lg p-1">
                                    <button @click="item.quantidade--" :disabled="item.quantidade <= 1" class="w-5 h-5 flex items-center justify-center text-xs font-bold disabled:opacity-20">-</button>
                                    <span class="px-2 text-[10px] font-black">{{ item.quantidade }}</span>
                                    <button @click="item.quantidade++" class="w-5 h-5 flex items-center justify-center text-xs font-bold">+</button>
                                </div>
                                <!-- Remover -->
                                <button @click="removerDoCarrinho(index)" class="text-gray-300 hover:text-red-500 transition-colors">
                                    <svg xmlns="www.w3.org" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div v-if="carrinho.length === 0" class="text-center py-20">
                            <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.2em] italic">O carrinho está vazio</p>
                        </div>
                    </div>

                    <!-- Rodapé do Carrinho -->
                    <div class="space-y-4 pt-4 border-t border-gray-200">
                        <textarea 
                            v-model="observacao" 
                            placeholder="Observações adicionais para este pedido..." 
                            class="w-full p-4 rounded-2xl border-2 border-transparent bg-white text-[11px] outline-none focus:border-blue-900 h-24 resize-none transition-all shadow-sm"
                        ></textarea>
                        
                        <div class="bg-blue-900 p-6 rounded-[2rem] shadow-xl">
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-[10px] font-black text-blue-300 uppercase tracking-widest">Total Geral</span>
                                <span class="text-xl font-black text-white">R$ {{ totalPedido.toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }}</span>
                            </div>
                            <button 
                                @click="finalizarPedido" 
                                :disabled="carrinho.length === 0 || isLoading" 
                                class="w-full bg-emerald-500 hover:bg-emerald-400 text-white py-4 rounded-xl font-black uppercase text-xs tracking-[0.2em] shadow-lg transition-all active:scale-95 disabled:opacity-30 disabled:grayscale"
                            >
                                {{ isLoading ? 'Enviando...' : 'Finalizar Pedido' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e5e7eb;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #d1d5db;
}
</style>
