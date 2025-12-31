<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/api';
import LoadingOverlay from '@/components/common/LoadingOverlay.vue';

const usuarios = ref([]);
const pagination = ref({});
const isLoading = ref(false);

const carregarUsuarios = async (page = 1) => {
    isLoading.value = true;
    try {
        // A URL será http://localhost:8989/api/v1/users?page=1
        const response = await api.get('/users', { params: { page } });
        
        // Acessando a estrutura: response.data (do axios) -> data (do seu ReturnJson) -> data (do paginate)
        const resultado = response.data.data;
        
        usuarios.value = resultado.data; // O array de 6 usuários que você enviou
        pagination.value = {
            current_page: resultado.current_page,
            last_page: resultado.last_page,
            total: resultado.total
        };
    } catch (error) {
        console.error("Erro ao carregar usuários:", error);
    } finally {
        isLoading.value = false;
    }
};

const deletarUsuario = async (id) => {
    if (confirm('Tem certeza que deseja remover este usuário?')) {
        try {
            await api.delete(`/users/${id}`);
            carregarUsuarios(pagination.value.current_page);
        } catch (error) {
            alert('Erro ao excluir usuário');
        }
    }
};

onMounted(() => carregarUsuarios());
</script>

<template>
    <div class="relative">
        <!-- Adicione o componente aqui -->
        <LoadingOverlay :show="isLoading" message="Carregando listagem de usuários" />

    <div class="space-y-6">
        <div class="flex justify-between items-center bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Usuários</h1>
                <p class="text-sm text-gray-500 font-medium">Gerenciamento de acessos Constance/Trelos</p>
            </div>
            <router-link to="/admin/usuarios/novo" 
                class="bg-blue-900 hover:bg-blue-800 text-white px-5 py-2.5 rounded-lg font-bold transition-all shadow-sm">
                + Novo Usuário
            </router-link>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr class="text-gray-400 text-xs uppercase tracking-widest">
                        <th class="px-6 py-4 font-semibold">ID</th>
                        <th class="px-6 py-4 font-semibold">Nome</th>
                        <th class="px-6 py-4 font-semibold">E-mail</th>
                        <th class="px-6 py-4 font-semibold">Tipo</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 font-semibold text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="user in usuarios" :key="user.id" class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 text-sm text-gray-500">#{{ user.id }}</td>
                        <td class="px-6 py-4 font-bold text-gray-700">{{ user.name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ user.email }}</td>
                        <td class="px-6 py-4">
                            <span :class="user.tipo === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'"
                                class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-tighter">
                                {{ user.tipo }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span :class="user.status === 'ativo' ? 'text-green-600' : 'text-red-600'" class="flex items-center text-sm font-medium">
                                <span class="h-2 w-2 rounded-full mr-2" :class="user.status === 'ativo' ? 'bg-green-500' : 'bg-red-500'"></span>
                                {{ user.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-3">
                            <router-link :to="`/admin/usuarios/editar/${user.id}`" 
                                class="text-blue-600 hover:text-blue-800 font-bold text-sm">Editar</router-link>
                            <button @click="deletarUsuario(user.id)" 
                                class="text-red-500 hover:text-red-700 font-bold text-sm">Excluir</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Simples Paginação -->
            <div v-if="pagination.last_page > 1" class="p-4 bg-gray-50 border-t flex justify-center space-x-2">
                <button v-for="p in pagination.last_page" :key="p" 
                    @click="carregarUsuarios(p)"
                    :class="pagination.current_page === p ? 'bg-blue-900 text-white' : 'bg-white text-gray-600'"
                    class="px-3 py-1 border rounded font-bold text-sm">
                    {{ p }}
                </button>
            </div>
        </div>
    </div>
    </div>
</template>
