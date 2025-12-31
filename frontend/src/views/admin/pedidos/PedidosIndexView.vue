<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/api';
import LoadingOverlay from '@/components/common/LoadingOverlay.vue';
import ToastNotification from '@/components/common/ToastNotification.vue'; // Importado
import VisualizarPedidoModal from './VisualizarPedidoModal.vue';

const pedidos = ref([]);
const pagination = ref({});
const isLoading = ref(false);
const error = ref(null); // Estado para o Toast
const showModal = ref(false);
const pedidoSelecionado = ref(null);

const carregarPedidos = async (page = 1) => {
    isLoading.value = true;
    error.value = null;
    try {
        const res = await api.get('/pedidos', { params: { page, per_page: 15 } });
        pedidos.value = res.data.data.data;
        pagination.value = res.data.data;
    } catch (e) {
        error.value = "Erro ao carregar a lista de pedidos global.";
    } finally {
        setTimeout(() => { isLoading.value = false; }, 300);
    }
};

const abrirPedido = (pedido) => {
    pedidoSelecionado.value = pedido;
    showModal.value = true;
};

onMounted(() => carregarPedidos());
</script>

<template>
    <div class="p-6 relative min-h-screen bg-gray-50/50">
        <LoadingOverlay :show="isLoading" />
        
        <!-- Notificação de Erro -->
        <ToastNotification :error="error" @close="error = null" />

        <div class="mb-8 flex justify-between items-center">
            <div>
                <h2 class="text-3xl font-black text-blue-900 uppercase italic tracking-tighter">Gestão Global</h2>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Painel Administrativo 2025</p>
            </div>
            <div class="bg-blue-900 text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase shadow-lg">
                {{ pagination.total || 0 }} Pedidos
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/80 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                        <th class="px-8 py-5">Pedido</th>
                        <th class="px-8 py-5">Vendedor / Fornecedor</th>
                        <th class="px-8 py-5 text-right">Valor Total</th>
                        <th class="px-8 py-5 text-center">Status</th>
                        <th class="px-8 py-5 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr v-for="ped in pedidos" :key="ped.id" class="hover:bg-blue-50/30 transition-colors group">
                        <td class="px-8 py-6 text-xs font-black text-blue-900 font-mono italic">#00{{ ped.id }}</td>
                        <td class="px-8 py-6">
                            <p class="text-xs font-black text-gray-700 uppercase leading-none mb-1">{{ ped.vendedor.name }}</p>
                            <p class="text-[9px] text-blue-500 font-bold tracking-widest uppercase italic">{{ ped.fornecedor.nome }}</p>
                        </td>
                        <td class="px-8 py-6 text-right text-sm font-black text-gray-900 italic">
                            R$ {{ ped.valor_total }}
                        </td>
                        <td class="px-8 py-6 text-center">
                            <span :class="{
                                'bg-amber-100 text-amber-700': ped.status === 'Pendente',
                                'bg-emerald-100 text-emerald-700': ped.status === 'Concluído',
                                'bg-red-100 text-red-700': ped.status === 'Cancelado'
                            }" class="text-[9px] font-black px-4 py-1.5 rounded-xl uppercase border border-black/5">
                                {{ ped.status }}
                            </span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <button @click="abrirPedido(ped)" class="bg-gray-100 group-hover:bg-blue-900 group-hover:text-white text-gray-500 px-5 py-2.5 rounded-2xl text-[10px] font-black uppercase transition-all shadow-sm">
                                Visualizar / Editar
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <VisualizarPedidoModal 
            v-if="showModal" 
            :pedido="pedidoSelecionado" 
            @close="showModal = false" 
            @updated="carregarPedidos(pagination.current_page)"
        />
    </div>
</template>
