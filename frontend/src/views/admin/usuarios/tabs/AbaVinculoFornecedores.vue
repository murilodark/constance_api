<script setup>
import { ref, onMounted, watch } from 'vue';
import api from '@/services/api';

const props = defineProps(['userId']);
const fornecedores = ref([]);
const vinculadosIds = ref([]);
const filtro = ref('');
const pagina = ref(1);
const totalPaginas = ref(1);
const loading = ref(false);

const carregarGlobal = async (p = 1) => {
    loading.value = true;
    const res = await api.get('/fornecedores', { params: { filter: filtro.value, page: p } });
    fornecedores.value = res.data.data.data;
    totalPaginas.value = res.data.data.last_page;
    pagina.value = res.data.data.current_page;
    loading.value = false;
};

const carregarVinculados = async () => {
    const res = await api.get(`/users/${props.userId}/fornecedores`);
    vinculadosIds.value = res.data.data.map(f => f.id);
};

const toggle = async (id) => {
    const vinculado = vinculadosIds.value.includes(id);
    if (vinculado) {
        await api.delete(`/users/${props.userId}/fornecedores`, { data: { fornecedores_id: id } });
        vinculadosIds.value = vinculadosIds.value.filter(v => v !== id);
    } else {
        await api.post(`/users/${props.userId}/fornecedores`, { fornecedores_id: id });
        vinculadosIds.value.push(id);
    }
};

watch(filtro, () => carregarGlobal(1));
onMounted(() => { carregarGlobal(); carregarVinculados(); });
</script>

<template>
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b bg-gray-50/50 flex justify-between items-center">
            <input v-model.lazy="filtro" placeholder="Buscar fornecedor..." class="px-4 py-2 border rounded-xl text-sm outline-none focus:border-blue-900" />
            <span class="text-[10px] font-black uppercase text-gray-400">Total Vinculados: {{ vinculadosIds.length }}</span>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="f in fornecedores" :key="f.id" @click="toggle(f.id)"
                :class="vinculadosIds.includes(f.id) ? 'border-blue-600 bg-blue-50' : 'border-gray-100'"
                class="p-4 border-2 rounded-2xl cursor-pointer flex justify-between items-center transition-all">
                <span class="font-bold text-gray-700 text-sm uppercase">{{ f.nome }}</span>
                <div :class="vinculadosIds.includes(f.id) ? 'bg-blue-600' : 'bg-gray-200'" class="w-5 h-5 rounded-full flex items-center justify-center">
                    <span v-if="vinculadosIds.includes(f.id)" class="text-white text-[10px]">✓</span>
                </div>
            </div>
        </div>
        <!-- Paginação Simples -->
        <div class="p-4 flex justify-center space-x-2" v-if="totalPaginas > 1">
            <button v-for="p in totalPaginas" :key="p" @click="carregarGlobal(p)" :class="pagina === p ? 'text-blue-900 font-black' : 'text-gray-400'" class="px-2">{{ p }}</button>
        </div>
    </div>
</template>
