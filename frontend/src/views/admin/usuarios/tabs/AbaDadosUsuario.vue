<script setup>
import { ref, onMounted, watch } from 'vue';
import api from '@/services/api';
import { handleApiError } from '@/utils/errorHandler';
import LoadingOverlay from '@/components/common/LoadingOverlay.vue';
import ToastNotification from '@/components/common/ToastNotification.vue'; // Para erro global
import AlertMessage from '@/components/common/AlertMessage.vue'; // Para mensagens no form

const props = defineProps(['userId']); 
const emit = defineEmits(['updated', 'close']);

const form = ref({ 
    name: '', 
    email: '', 
    password: '', 
    status: 'ativo', 
    tipo: 'vendedor' 
});

const loading = ref(false);
const error = ref(null);
const success = ref(null);

const carregar = async () => {
    if (!props.userId) return;
    loading.value = true;
    error.value = null;
    try {
        const res = await api.get(`/users/${props.userId}`);
        const dados = res.data.data;
        form.value = { 
            name: dados.name,
            email: dados.email,
            status: dados.status,
            tipo: dados.tipo,
            password: '' 
        };
    } catch (e) { 
        error.value = handleApiError(e); 
    } finally { 
        loading.value = false; 
    }
};

const salvar = async () => {
    loading.value = true;
    error.value = null;
    success.value = null;
    
    try {
        const payload = { ...form.value };
        let response;

        if (props.userId) {
            if (!payload.password) delete payload.password;
            response = await api.put(`/users/${props.userId}`, payload);
        } else {
            response = await api.post('/users', payload);
        }

        // Sucesso conforme seu JSON: response.data.message
        success.value = response.data.message;
        
        // Emite o evento de atualização para a lista pai
        setTimeout(() => {
            emit('updated');
            if (!props.userId) {
                // Se for novo cadastro, limpa o form
                form.value = { name: '', email: '', password: '', status: 'ativo', tipo: 'vendedor' };
            }
        }, 500);

    } catch (e) { 
        // Utiliza o seu utilitário que trata o array de erros do Laravel
        error.value = handleApiError(e); 
    } finally { 
        loading.value = false; 
    }
};

onMounted(carregar);
watch(() => props.userId, carregar);
</script>

<template>
    <div class="relative bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 min-h-[400px]">
        <LoadingOverlay :show="loading" />
        
        <!-- Notificações -->
        <ToastNotification v-if="error" :error="error" @close="error = null" />
        
        <div class="mb-8 flex justify-between items-center">
            <h3 class="text-xl font-black text-blue-900 uppercase italic tracking-tighter leading-none">
                {{ userId ? 'Editar Registro' : 'Novo Cadastro Vendedor' }}
            </h3>
            <button @click="$emit('close')" class="text-gray-300 hover:text-red-500 transition-colors">✕</button>
        </div>

        <!-- Mensagem de Sucesso Interna -->
        <AlertMessage v-if="success" type="success" :message="success" @close="success = null" class="mb-6" />

        <form @submit.prevent="salvar" class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Nome Completo</label>
                    <input v-model="form.name" type="text" placeholder="Ex: Murilo Constance"
                        class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-3.5 outline-none focus:border-blue-900 transition-all text-sm font-bold text-gray-700" />
                </div>

                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">E-mail Corporativo</label>
                    <input v-model="form.email" type="email" placeholder="vendedor@constance.com"
                        class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-3.5 outline-none focus:border-blue-900 transition-all text-sm font-bold text-gray-700" />
                </div>

                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">
                        {{ userId ? 'Nova Senha (Opcional)' : 'Senha de Acesso' }}
                    </label>
                    <input v-model="form.password" type="password" 
                        class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-3.5 outline-none focus:border-blue-900 transition-all text-sm font-bold font-mono text-gray-700" />
                </div>

                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Status da Conta</label>
                    <select v-model="form.status" class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-3.5 outline-none focus:border-blue-900 transition-all text-xs font-black uppercase text-gray-600 appearance-none cursor-pointer">
                        <option value="ativo">🟢 Ativo</option>
                        <option value="inativo">🔴 Inativo</option>
                    </select>
                </div>

                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Nível de Acesso</label>
                    <select v-model="form.tipo" class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-5 py-3.5 outline-none focus:border-blue-900 transition-all text-xs font-black uppercase text-gray-600 appearance-none cursor-pointer">
                        <option value="vendedor">Vendedor</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-8 border-t border-gray-50">
                <button type="button" @click="$emit('close')" class="px-6 py-3 text-[10px] font-black uppercase text-gray-400 hover:text-gray-600 transition-all tracking-widest">
                    Descartar
                </button>
                <button type="submit" :disabled="loading"
                    class="bg-blue-900 text-white px-10 py-3.5 rounded-2xl font-black uppercase text-[10px] tracking-[0.2em] shadow-xl shadow-blue-900/20 active:scale-95 transition-all disabled:opacity-50 flex items-center gap-2">
                    <span v-if="loading" class="w-3 h-3 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    {{ userId ? 'Atualizar Dados' : 'Concluir Cadastro' }}
                </button>
            </div>
        </form>
    </div>
</template>
