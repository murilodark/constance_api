<script setup>
import { ref, onMounted, computed } from 'vue';
import api from '@/services/api';
import { useAuthStore } from '@/stores/auth';
import DashboardCard from '@/components/common/DashboardCard.vue';
import LoadingOverlay from '@/components/common/LoadingOverlay.vue';
import ToastNotification from '@/components/common/ToastNotification.vue';

const auth = useAuthStore();
const estatisticas = ref([]);
const isLoading = ref(false);
const error = ref(null);
const periodo = ref({});
const escopo = ref('');

const carregarEstatisticas = async () => {
    isLoading.value = true;
    try {
        const res = await api.get('/pedidos/estatisticas');
        estatisticas.value = res.data.data.resumo;
        periodo.value = res.data.data.periodo;
        escopo.value = res.data.data.escopo;
    } catch (e) {
        error.value = "Erro ao carregar estatísticas.";
    } finally {
        isLoading.value = false;
    }
};

const formatarMoeda = (valor) => {
    return Number(valor).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
};

const getStatusData = (statusName) => {
    return estatisticas.value.find(s => s.status === statusName) || { quantidade: 0, total_valor: 0 };
};

// Dados Computados
const pendentes = computed(() => getStatusData('Pendente'));
const concluidos = computed(() => getStatusData('Concluído'));
const cancelados = computed(() => getStatusData('Cancelado'));

const totalPedidos = computed(() => 
    estatisticas.value.reduce((acc, curr) => acc + curr.quantidade, 0)
);

const totalFinanceiroGeral = computed(() => {
    const total = estatisticas.value.reduce((acc, curr) => acc + Number(curr.total_valor), 0);
    return formatarMoeda(total);
});

onMounted(carregarEstatisticas);
</script>

<template>
    <div class="relative min-h-[400px]">
        <LoadingOverlay :show="isLoading" />
        <ToastNotification :error="error" @close="error = null" />

        <!-- Cabeçalho Reduzido -->
        <div class="mb-6 flex justify-between items-end">
            <div>
                <h1 class="text-xl font-black text-blue-900 uppercase tracking-tighter leading-none">
                    Dashboard {{ auth.isAdmin ? 'Admin' : 'Vendedor' }}
                </h1>
                <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-1">
                    Últimos 7 dias • Escopo: {{ escopo }}
                </p>
            </div>
            <div class="bg-gray-100 px-3 py-1.5 rounded-lg text-[9px] font-black text-gray-500 border border-gray-200">
                {{ periodo.desde }} — {{ periodo.ate }}
            </div>
        </div>

        <!-- Cards de Métricas com Sub-Valores Financeiros -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            
            <!-- Pendentes -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
                <div class="flex justify-between items-start mb-2">
                    <span class="text-[10px] font-black text-gray-400 uppercase italic">Pendentes</span>
                    <span class="text-sm">🕒</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-black text-amber-600 italic">{{ pendentes.quantidade }}</span>
                    <span class="text-[10px] font-bold text-gray-400 uppercase">pedidos</span>
                </div>
                <p class="text-[10px] font-black text-amber-700/50 mt-1">{{ formatarMoeda(pendentes.total_valor) }}</p>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-amber-500/10"></div>
            </div>

            <!-- Concluídos -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
                <div class="flex justify-between items-start mb-2">
                    <span class="text-[10px] font-black text-gray-400 uppercase italic">Concluídos</span>
                    <span class="text-sm">✅</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-black text-emerald-600 italic">{{ concluidos.quantidade }}</span>
                    <span class="text-[10px] font-bold text-gray-400 uppercase">pedidos</span>
                </div>
                <p class="text-[10px] font-black text-emerald-700/50 mt-1">{{ formatarMoeda(concluidos.total_valor) }}</p>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-emerald-500/10"></div>
            </div>

            <!-- Total Quantidade -->
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
                <div class="flex justify-between items-start mb-2">
                    <span class="text-[10px] font-black text-gray-400 uppercase italic">Total Volume</span>
                    <span class="text-sm">📊</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-black text-blue-900 italic">{{ totalPedidos }}</span>
                    <span class="text-[10px] font-bold text-gray-400 uppercase">solicitações</span>
                </div>
                <p class="text-[10px] font-black text-blue-900/40 mt-1">Geral do período</p>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-blue-900/10"></div>
            </div>

            <!-- Total Financeiro -->
            <div class="bg-blue-900 p-4 rounded-2xl shadow-lg relative overflow-hidden">
                <div class="flex justify-between items-start mb-2">
                    <span class="text-[10px] font-black text-blue-200 uppercase italic">Faturamento</span>
                    <span class="text-sm">💰</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-xl font-black text-white italic tracking-tighter">{{ totalFinanceiroGeral }}</span>
                    <span class="text-[9px] font-bold text-blue-300 uppercase mt-1 italic">Total Líquido</span>
                </div>
            </div>
        </div>

        <!-- Seção Inferior Reduzida -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100">
                <h3 class="font-black text-gray-800 uppercase text-[10px] mb-4 tracking-widest border-b pb-2">Distribuição Financeira</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div v-for="item in estatisticas" :key="item.status" class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <p class="text-[9px] font-black text-gray-400 uppercase mb-1">{{ item.status }}</p>
                        <p class="text-sm font-black text-blue-900 italic">{{ formatarMoeda(item.total_valor) }}</p>
                        <p class="text-[8px] font-bold text-gray-400">{{ item.quantidade }} registros</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100">
                <h3 class="font-black text-gray-800 uppercase text-[10px] mb-4 tracking-widest border-b pb-2">Ações Rápidas</h3>
                <div class="space-y-2">
                    <router-link to="/vendedor/pedidos" class="flex items-center justify-between p-3 bg-gray-50 hover:bg-blue-50 rounded-xl transition-all group border border-transparent hover:border-blue-100">
                        <span class="text-[10px] font-black text-gray-600 uppercase group-hover:text-blue-900 italic">Ver Todos Pedidos</span>
                        <span class="text-[10px]">➜</span>
                    </router-link>
                    <router-link v-if="auth.isAdmin" to="/admin/fornecedores" class="flex items-center justify-between p-3 bg-gray-50 hover:bg-blue-50 rounded-xl transition-all group border border-transparent hover:border-blue-100">
                        <span class="text-[10px] font-black text-gray-600 uppercase group-hover:text-blue-900 italic">Gestão Fornecedores</span>
                        <span class="text-[10px]">🏢</span>
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>
