<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/api';
import { handleApiError } from '@/utils/errorHandler';

const props = defineProps(['userId']);
const emit = defineEmits(['updated']);

const form = ref({ name: '', email: '', password: '', status: 'ativo', tipo: 'vendedor' });
const loading = ref(false);
const error = ref(null);
const success = ref('');

const carregar = async () => {
    if (!props.userId) return;
    loading.value = true;
    try {
        const res = await api.get(`/users/${props.userId}`);
        form.value = { ...res.data.data, password: '' };
    } finally { loading.value = false; }
};

const salvar = async () => {
    loading.value = true;
    error.value = null;
    success.value = '';
    try {
        const payload = { ...form.value };
        if (!payload.password) delete payload.password;
        
        const res = await api.put(`/users/${props.userId}`, payload);
        success.value = res.data.message;
        emit('updated');
    } catch (e) { error.value = handleApiError(e); }
    finally { loading.value = false; }
};

onMounted(carregar);
</script>

<template>
    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
        <form @submit.prevent="salvar" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase">Nome Completo</label>
                    <input v-model="form.name" type="text" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 outline-none focus:border-blue-900 transition-all" />
                </div>
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase">E-mail</label>
                    <input v-model="form.email" type="email" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 outline-none focus:border-blue-900 transition-all" />
                </div>
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase">Senha (opcional)</label>
                    <input v-model="form.password" type="password" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 outline-none focus:border-blue-900 transition-all" />
                </div>
            </div>
            <div class="flex justify-end pt-6">
                <button type="submit" class="bg-blue-900 text-white px-10 py-3 rounded-xl font-black uppercase text-xs tracking-widest shadow-lg shadow-blue-900/20">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</template>
