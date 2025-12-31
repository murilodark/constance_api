<script setup>
import { ref, onMounted, watch } from 'vue';
import api from '@/services/api';
import { handleApiError } from '@/utils/errorHandler';
// 1. Importar os componentes de feedback
import LoadingOverlay from '@/components/common/LoadingOverlay.vue';
import ToastNotification from '@/components/common/ToastNotification.vue';

const props = defineProps(['fornecedorId']);
const emit = defineEmits(['updated']);

const form = ref({
    nome: '',
    cnpj: '',
    status: 'ativo',
    endereco: {
        cep: '', logradouro: '', numero: '', complemento: '', bairro: '', cidade: '', estado: ''
    }
});

const loading = ref(false);
const error = ref(null);
const successMsg = ref(''); // Para mensagens de sucesso

// Consulta de CEP Automática
const consultarCEP = async () => {
    const cepLimpo = form.value.endereco.cep.replace(/\D/g, '');
    if (cepLimpo.length !== 8) return;

    loading.value = true;
    try {
        const res = await api.get(`/utils/cep/${cepLimpo}`);
        const dados = res.data.data;
        form.value.endereco.logradouro = dados.logradouro;
        form.value.endereco.bairro = dados.bairro;
        form.value.endereco.cidade = dados.cidade;
        form.value.endereco.estado = dados.estado;
    } catch (e) {
        console.error("CEP não encontrado");
    } finally {
        loading.value = false;
    }
};

watch(() => form.value.endereco.cep, (newCep) => {
    if (newCep?.length === 8 || newCep?.length === 9) consultarCEP();
});

const salvar = async () => {
    loading.value = true;
    error.value = null;
    try {
        const res = props.fornecedorId 
            ? await api.put(`/fornecedores/${props.fornecedorId}`, form.value)
            : await api.post('/fornecedores', form.value);
        
        successMsg.value = "Dados salvos com sucesso!";
        emit('updated', res.data.data.id);
    } catch (e) { 
        error.value = handleApiError(e); 
    } finally { 
        loading.value = false; 
    }
};

onMounted(async () => {
    if (props.fornecedorId) {
        loading.value = true;
        try {
            const res = await api.get(`/fornecedores/${props.fornecedorId}`);
            const dadosApi = res.data.data;

            form.value.nome = dadosApi.nome;
            form.value.cnpj = dadosApi.cnpj;
            form.value.status = dadosApi.status;

            if (dadosApi.enderecos && dadosApi.enderecos.length > 0) {
                const end = dadosApi.enderecos[0];
                form.value.endereco = {
                    cep: end.cep || '',
                    logradouro: end.logradouro || '',
                    numero: end.numero || '',
                    complemento: end.complemento || '',
                    bairro: end.bairro || '',
                    cidade: end.cidade || '',
                    estado: end.estado || ''
                };
            }
        } catch (e) {
            error.value = "Erro ao carregar dados do fornecedor";
        } finally {
            loading.value = false;
        }
    }
});
</script>

<template>
    <!-- 2. Adicionar 'relative' ao container e incluir os componentes -->
    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 relative min-h-[400px]">
        
        <LoadingOverlay :show="loading" />
        
        <ToastNotification 
            :error="error" 
            :success="successMsg" 
            @close="error = null; successMsg = ''" 
        />

        <form @submit.prevent="salvar" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase">Nome / Razão Social</label>
                    <input v-model="form.nome" type="text" required class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 focus:border-blue-900 outline-none transition-all" />
                </div>
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase">CNPJ</label>
                    <input v-model="form.cnpj" type="text" required class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 focus:border-blue-900 outline-none transition-all" />
                </div>

                <!-- Bloco de Endereço -->
                <div class="md:col-span-3 pt-4 border-t border-gray-50">
                    <h4 class="text-xs font-black text-blue-900 uppercase mb-4">Localização</h4>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase">CEP (Autocompletar)</label>
                            <input v-model="form.endereco.cep" type="text" maxlength="9" class="w-full bg-blue-50 border-2 border-blue-100 rounded-xl px-4 py-3 focus:border-blue-900 outline-none transition-all font-bold" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase">Logradouro</label>
                            <input v-model="form.endereco.logradouro" type="text" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 outline-none" />
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase">Número</label>
                            <input v-model="form.endereco.numero" type="text" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 outline-none" />
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase">Bairro</label>
                            <input v-model="form.endereco.bairro" type="text" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 outline-none" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase">Cidade</label>
                            <input v-model="form.endereco.cidade" type="text" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 outline-none" />
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase">Estado</label>
                            <input v-model="form.endereco.estado" type="text" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 outline-none" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-6">
                <button type="submit" :disabled="loading" class="bg-blue-900 text-white px-10 py-4 rounded-2xl font-black uppercase text-xs tracking-widest shadow-lg hover:scale-105 transition-all disabled:opacity-50">
                    {{ fornecedorId ? 'Atualizar Fornecedor' : 'Cadastrar Fornecedor' }}
                </button>
            </div>
        </form>
    </div>
</template>
