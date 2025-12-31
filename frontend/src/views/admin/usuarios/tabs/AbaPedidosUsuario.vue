<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/api';
import LoadingOverlay from '@/components/common/LoadingOverlay.vue';

const props = defineProps(['userId']);
const pedidos = ref([]);
const isLoading = ref(false);

const carregarPedidos = async () => {
    if (!props.userId) return;
    isLoading.value = true;
    try {
        // Rota que utilizaremos para listar pedidos vinculados ao usuário
        const response = await api.get(`/users/${props.userId}/pedidos`);
        pedidos.value = response.data.data.data || response.data.data;
    } catch (error) {
        console.error("Erro ao carregar pedidos:", error);
    } finally {
        isLoading.value = false;
    }
};

onMounted(carregarPedidos);
</script>

<template>
    <div class="relative min-h-[400px]">
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-8">
                <h3 class="font-black text-gray-800 uppercase tracking-tighter">Histórico de Pedidos</h3>
                <span class="text-[10px] font-black bg-blue-100 text-blue-700 px-3 py-1 rounded-full uppercase">
                    Total: {{ pedidos.length }}
                </span>
            </div>

            <!-- Lista de Pedidos -->
            <div v-if="pedidos.length > 0" class="space-y-4">
                <div v-for="pedido in pedidos" :key="pedido.id" 
                    class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100 hover:border-blue-200 transition-all">
                    <div>
                        <p class="text-sm font-black text-gray-700 uppercase">Pedido #{{ pedido.id }}</p>
                        <p class="text-[10px] text-gray-400 font-bold uppercase">{{ pedido.data_pedido }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-black text-blue-900">R$ {{ pedido.total }}</p>
                        <p class="text-[9px] font-bold uppercase text-gray-400 italic">Fornecedor ID: {{ pedido.fornecedores_id }}</p>
                    </div>
                </div>
            </div>

            <!-- Estado Vazio (Visual para o Teste) -->
            <div v-else class="flex flex-col items-center justify-center py-20 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center text-4xl mb-4">
                    📦
                </div>
                <h4 class="text-gray-800 font-black uppercase text-sm tracking-tighter">Nenhum pedido encontrado</h4>
                <p class="text-gray-400 text-xs max-w-xs mt-2">
                    Este vendedor ainda não possui pedidos processados para os fornecedores vinculados.
                </p>
            </div>
        </div>
    </div>
</template>
