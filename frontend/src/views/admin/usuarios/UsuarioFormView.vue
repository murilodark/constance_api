<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '@/services/api';

// Imports dos Componentes de Abas
import AbaDadosUsuario from './tabs/AbaDadosUsuario.vue';
import AbaVinculoFornecedores from './tabs/AbaVinculoFornecedores.vue';
import AbaPedidosUsuario from './tabs/AbaPedidosUsuario.vue';
import LoadingOverlay from '@/components/common/LoadingOverlay.vue';

const route = useRoute();
const router = useRouter();
const userId = route.params.id;
const isEdit = !!userId;

const abaAtiva = ref('dados');
const usuario = ref(null);
const isLoading = ref(false);

const carregarResumo = async () => {
    if (!isEdit) return;
    isLoading.value = true;
    try {
        const res = await api.get(`/users/${userId}`);
        usuario.value = res.data.data;
    } finally { isLoading.value = false; }
};

onMounted(carregarResumo);
</script>

<template>
    <div class="max-w-6xl mx-auto py-8 px-4">
        <LoadingOverlay :show="isLoading" />
        
        <!-- Header Dashboard -->
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex justify-between items-center mb-8">
            <div class="flex items-center space-x-6">
                <div class="w-20 h-20 bg-blue-900 rounded-3xl flex items-center justify-center text-white text-3xl font-black shadow-xl">
                    {{ usuario?.name?.charAt(0) || 'N' }}
                </div>
                <div>
                    <h1 class="text-3xl font-black text-gray-800 tracking-tighter uppercase">{{ isEdit ? usuario?.name : 'Novo Usuário' }}</h1>
                    <div class="flex space-x-3 mt-1">
                        <span class="text-[10px] font-black uppercase px-2 py-1 bg-blue-100 text-blue-700 rounded-md">{{ usuario?.tipo }}</span>
                        <span class="text-[10px] font-black uppercase px-2 py-1 bg-green-100 text-green-700 rounded-md">{{ usuario?.status }}</span>
                    </div>
                </div>
            </div>
            <button @click="router.push('/admin/usuarios')" class="text-xs font-black text-gray-300 hover:text-blue-900 uppercase transition-colors">Voltar</button>
        </div>

        <!-- Tabs -->
        <div class="flex space-x-2 mb-8 bg-gray-200/50 p-2 rounded-2xl w-fit">
            <button @click="abaAtiva = 'dados'" :class="abaAtiva === 'dados' ? 'bg-white shadow text-blue-900' : 'text-gray-500'" class="px-8 py-3 rounded-xl text-xs font-black uppercase transition-all">Perfil</button>
            <button v-if="isEdit" @click="abaAtiva = 'fornecedores'" :class="abaAtiva === 'fornecedores' ? 'bg-white shadow text-blue-900' : 'text-gray-500'" class="px-8 py-3 rounded-xl text-xs font-black uppercase transition-all">Vínculos</button>
            <button v-if="isEdit" @click="abaAtiva = 'pedidos'" :class="abaAtiva === 'pedidos' ? 'bg-white shadow text-blue-900' : 'text-gray-500'" class="px-8 py-3 rounded-xl text-xs font-black uppercase transition-all">Pedidos</button>
        </div>

        <!-- Renderização da Aba -->
        <component :is="abaAtiva === 'dados' ? AbaDadosUsuario : (abaAtiva === 'fornecedores' ? AbaVinculoFornecedores : AbaPedidosUsuario)" 
                   :user-id="userId" 
                   @updated="carregarResumo" />
    </div>
</template>
