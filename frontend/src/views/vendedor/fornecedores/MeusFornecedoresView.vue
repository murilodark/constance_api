<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/api';
import { useAuthStore } from '@/stores/auth'; // Importado para pegar o ID do user
import LoadingOverlay from '@/components/common/LoadingOverlay.vue';
import NovoPedidoModal from './NovoPedidoModal.vue';

const auth = useAuthStore(); // Instância da store
const fornecedores = ref([]);
const isLoading = ref(false);
const showPedidoModal = ref(false);
const fornecedorSelecionado = ref(null);

const carregarFornecedores = async () => {
    if (!auth.user?.id) {
        console.error("ID do usuário não encontrado");
        return;
    }

    isLoading.value = true;
    try {
        // Rota corrigida para: /api/users/{id}/fornecedores
        const res = await api.get(`/users/${auth.user.id}/fornecedores`); 
        
        // Ajuste para garantir que pegamos os dados independente da estrutura de retorno
        fornecedores.value = res.data.data.data || res.data.data;
    } catch (e) {
        console.error("Erro ao carregar seus fornecedores");
    } finally {
        isLoading.value = false;
    }
};

const abrirModalPedido = (fornecedor) => {
    fornecedorSelecionado.value = fornecedor;
    showPedidoModal.value = true;
};

onMounted(carregarFornecedores);
</script>

<template>
    <div class="relative min-h-[500px]">
        <LoadingOverlay :show="isLoading" />
        
        <div class="mb-8">
            <h2 class="text-2xl font-black text-blue-900 uppercase tracking-tighter">Meus Fornecedores</h2>
            <p class="text-xs text-gray-400 font-bold uppercase">Selecione um parceiro para iniciar um novo pedido</p>
        </div>

        <div v-if="fornecedores.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div v-for="f in fornecedores" :key="f.id" class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 hover:shadow-xl transition-all group">
                <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">
                    🏢
                </div>
                <h3 class="font-black text-gray-800 uppercase text-sm mb-1 truncate">{{ f.nome }}</h3>
                <p class="text-[10px] text-gray-400 font-mono mb-6">{{ f.cnpj }}</p>
                
                <button 
                    @click="abrirModalPedido(f)" 
                    class="w-full bg-blue-900 hover:bg-emerald-600 text-white py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-md active:scale-95"
                >
                    🛒 Iniciar Novo Pedido
                </button>
            </div>
        </div>

        <!-- Feedback caso não tenha fornecedores vinculados -->
        <div v-else-if="!isLoading" class="bg-white p-20 rounded-[3rem] text-center border-2 border-dashed border-gray-100">
            <span class="text-5xl block mb-4 grayscale">🤝</span>
            <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Nenhum fornecedor vinculado ao seu perfil.</p>
        </div>

        <!-- Modal de Pedido -->
        <NovoPedidoModal 
            v-if="showPedidoModal" 
            :fornecedor="fornecedorSelecionado" 
            @close="showPedidoModal = false" 
        />
    </div>
</template>
