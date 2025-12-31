<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '@/services/api';

// Imports das Abas (Certifique-se que estes arquivos existem em ./tabs/)
import FornecedorDadosTab from './tabs/FornecedorDadosTab.vue';
import FornecedorProdutosTab from './tabs/FornecedorProdutosTab.vue';
import FornecedorPedidosTab from './tabs/FornecedorPedidosTab.vue';
import LoadingOverlay from '@/components/common/LoadingOverlay.vue';

const route = useRoute();
const router = useRouter();
const fornecedorId = ref(route.params.id);
const abaAtiva = ref('dados');
const fornecedor = ref(null);
const isLoading = ref(false);

const carregarResumo = async (id) => {
    const targetId = id || fornecedorId.value;
    if (!targetId) return;
    
    isLoading.value = true;
    try {
        const res = await api.get(`/fornecedores/${targetId}`);
        fornecedor.value = res.data.data;
        if (!fornecedorId.value) fornecedorId.value = targetId; 
    } catch (e) {
        console.error("Erro ao carregar resumo do fornecedor", e);
    } finally {
        isLoading.value = false;
    }
};

onMounted(carregarResumo);
</script>

<template>
    <div class="max-w-6xl mx-auto py-8 px-4">
        <LoadingOverlay :show="isLoading" message="Carregando painel do fornecedor..." />

        <!-- Header Dashboard Fornecedor -->
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center mb-8 gap-6">
            <div class="flex items-center space-x-6">
                <div class="w-20 h-20 bg-amber-500 rounded-3xl flex items-center justify-center text-white text-3xl shadow-lg font-black shadow-amber-500/20">
                    {{ fornecedor?.nome?.charAt(0) || 'F' }}
                </div>
                <div>
                    <h1 class="text-3xl font-black text-gray-800 tracking-tighter uppercase">
                        {{ fornecedor?.nome || 'Novo Fornecedor' }}
                    </h1>
                    <div class="flex space-x-3 mt-1">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                            CNPJ: {{ fornecedor?.cnpj || '---' }}
                        </span>
                        <span v-if="fornecedor" class="text-[10px] font-black uppercase px-2 py-0.5 bg-green-100 text-green-700 rounded-md">
                            {{ fornecedor.status }}
                        </span>
                    </div>
                </div>
            </div>
            <button @click="router.push('/admin/fornecedores')" 
                class="text-xs font-black text-gray-300 hover:text-blue-900 uppercase transition-colors tracking-widest">
                ← Voltar para Lista
            </button>
        </div>

        <!-- Navegação por Abas (Tabs) -->
        <div class="flex space-x-2 mb-8 bg-gray-200/50 p-1.5 rounded-2xl w-fit">
            <button @click="abaAtiva = 'dados'" 
                :class="abaAtiva === 'dados' ? 'bg-white shadow-sm text-blue-900' : 'text-gray-500 hover:bg-white/50'"
                class="px-8 py-3 rounded-xl text-xs font-black uppercase transition-all">
                Dados Cadastrais
            </button>
            <button v-if="fornecedorId" @click="abaAtiva = 'produtos'" 
                :class="abaAtiva === 'produtos' ? 'bg-white shadow-sm text-blue-900' : 'text-gray-500 hover:bg-white/50'"
                class="px-8 py-3 rounded-xl text-xs font-black uppercase transition-all">
                Produtos
            </button>
            <button v-if="fornecedorId" @click="abaAtiva = 'pedidos'" 
                :class="abaAtiva === 'pedidos' ? 'bg-white shadow-sm text-blue-900' : 'text-gray-500 hover:bg-white/50'"
                class="px-8 py-3 rounded-xl text-xs font-black uppercase transition-all">
                Pedidos
            </button>
        </div>

        <!-- Renderização Dinâmica das Abas -->
        <div class="transition-all duration-300">
            <FornecedorDadosTab 
                v-if="abaAtiva === 'dados'" 
                :fornecedor-id="fornecedorId" 
                @updated="carregarResumo" 
            />
            
            <FornecedorProdutosTab 
                v-if="abaAtiva === 'produtos' && fornecedorId" 
                :fornecedor-id="fornecedorId" 
            />
            
            <FornecedorPedidosTab 
                v-if="abaAtiva === 'pedidos' && fornecedorId" 
                :fornecedor-cnpj="fornecedor?.cnpj" 
            />
        </div>
    </div>
</template>
