<script setup>
import { ref } from 'vue';
import api from '@/services/api';
import AlertMessage from '@/components/common/AlertMessage.vue'; // Importado

const props = defineProps(['pedido']);
const emit = defineEmits(['close', 'updated']);

const statusEdit = ref(props.pedido.status);
const isSaving = ref(false);
const message = ref(null); // Para o AlertMessage { type: 'success', text: '...' }

const salvarStatus = async () => {
    if (statusEdit.value === props.pedido.status) return emit('close');

    isSaving.value = true;
    message.value = null;
    try {
        await api.put(`/pedidos/${props.pedido.id}/status`, { 
            status: statusEdit.value 
        });
        
        // Exibe sucesso antes de fechar (opcional) ou emite direto
        emit('updated');
        emit('close');
    } catch (e) {
        message.value = { 
            type: 'error', 
            text: e.response?.data?.message || "Erro ao atualizar status do pedido." 
        };
    } finally {
        isSaving.value = false;
    }
};

const formatCurrency = (val) => Number(val).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
</script>

<template>
    <div class="fixed inset-0 z-[100] flex items-center justify-center bg-blue-950/60 backdrop-blur-sm p-4">
        <div class="bg-white w-full max-w-5xl max-h-[90vh] rounded-[3rem] shadow-2xl flex flex-col overflow-hidden animate-in zoom-in duration-200">
            
            <!-- Header -->
            <div class="p-8 border-b bg-gray-50 flex justify-between items-center">
                <div class="flex items-center">
                    <div class="w-14 h-14 bg-blue-900 text-white rounded-[1.5rem] flex items-center justify-center text-2xl mr-5 shadow-xl italic font-black text-white">P</div>
                    <div>
                        <h3 class="text-2xl font-black text-blue-900 uppercase tracking-tighter italic leading-none mb-1">Pedido #00{{ pedido.id }}</h3>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ new Date(pedido.created_at).toLocaleString() }}</p>
                    </div>
                </div>
                <button @click="emit('close')" class="bg-white hover:bg-red-50 text-gray-400 hover:text-red-500 p-4 rounded-2xl transition-all font-black text-xs border border-gray-100 shadow-sm">FECHAR ✕</button>
            </div>

            <!-- Body -->
            <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
                
                <!-- Componente de Alerta -->
                <AlertMessage v-if="message" :type="message.type" :message="message.text" class="mb-6" @close="message = null" />

                <!-- Edit Status Card -->
                <div class="mb-10 p-8 bg-blue-900 rounded-[2.5rem] flex flex-wrap items-center justify-between gap-6 shadow-2xl">
                    <div class="flex-1 min-w-[250px]">
                        <h4 class="text-[10px] font-black text-blue-300 uppercase tracking-widest mb-3 italic">Alterar Status Administrativo</h4>
                        <div class="flex gap-3">
                            <select v-model="statusEdit" :disabled="isSaving"
                                class="flex-1 bg-blue-800 border-2 border-blue-700 text-white text-xs font-black uppercase rounded-2xl px-6 py-3 outline-none transition-all cursor-pointer">
                                <option value="Pendente">Pendente</option>
                                <option value="Concluído">Concluído</option>
                                <option value="Cancelado">Cancelado</option>
                            </select>
                            <button @click="salvarStatus" :disabled="isSaving"
                                class="bg-white text-blue-900 px-8 py-3 rounded-2xl text-[10px] font-black uppercase shadow-lg hover:bg-blue-50 transition-all flex items-center gap-2">
                                <span v-if="isSaving" class="w-3 h-3 border-2 border-blue-900 border-t-transparent rounded-full animate-spin"></span>
                                {{ isSaving ? 'Salvando...' : 'Confirmar' }}
                            </button>
                        </div>
                    </div>
                    <div class="text-right border-l border-blue-800 pl-8 hidden md:block">
                        <p class="text-[10px] font-black text-blue-300 uppercase mb-1 italic">Total Líquido</p>
                        <p class="text-4xl font-black text-white italic tracking-tighter">{{ formatCurrency(pedido.valor_total) }}</p>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                    <div class="p-6 bg-gray-50 rounded-[2rem] border border-gray-100">
                        <span class="text-[10px] font-black text-gray-400 uppercase mb-3 block italic tracking-widest">Responsável (Vendedor)</span>
                        <p class="text-lg font-black text-gray-800 uppercase">{{ pedido.vendedor.name }}</p>
                        <p class="text-xs text-gray-500 font-bold italic underline">{{ pedido.vendedor.email }}</p>
                    </div>
                    <div class="p-6 bg-gray-50 rounded-[2rem] border border-gray-100">
                        <span class="text-[10px] font-black text-gray-400 uppercase mb-3 block italic tracking-widest">Empresa (Fornecedor)</span>
                        <p class="text-lg font-black text-gray-800 uppercase">{{ pedido.fornecedor.nome }}</p>
                        <p class="text-[10px] font-bold text-gray-400 font-mono uppercase tracking-widest">CNPJ: {{ pedido.fornecedor.cnpj }}</p>
                    </div>
                </div>

                <!-- Itens -->
                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.4em] mb-6 px-2 italic underline decoration-blue-900 underline-offset-8">Produtos do Pedido</h4>
                <div class="space-y-3 mb-8">
                    <div v-for="item in pedido.itens" :key="item.id" class="flex justify-between items-center p-6 bg-white border border-gray-50 rounded-[2rem] hover:shadow-md transition-all">
                        <div class="flex items-center gap-6">
                            <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center text-xl italic font-black text-gray-300 shadow-inner">#</div>
                            <div>
                                <p class="text-sm font-black text-gray-800 uppercase italic leading-none mb-1">{{ item.produto.nome }}</p>
                                <p class="text-[10px] text-blue-600 font-bold uppercase tracking-tighter">{{ item.produto.referencia }} | {{ item.produto.cor }}</p>
                                <p class="text-[9px] text-gray-400 font-bold uppercase mt-1">{{ item.quantidade }} un x {{ formatCurrency(item.valor_unitario) }}</p>
                            </div>
                        </div>
                        <p class="text-xl font-black text-blue-900 italic tracking-tighter">{{ formatCurrency(item.quantidade * item.valor_unitario) }}</p>
                    </div>
                </div>

                <!-- Observação -->
                <div v-if="pedido.observacao" class="mt-8 p-8 bg-amber-50 rounded-[2.5rem] border border-amber-100 border-dashed">
                    <p class="text-[10px] font-black text-amber-700 uppercase tracking-widest mb-2 italic">Observações do Vendedor:</p>
                    <p class="text-xs text-amber-900 italic font-medium leading-relaxed">"{{ pedido.observacao }}"</p>
                </div>
            </div>
        </div>
    </div>
</template>
