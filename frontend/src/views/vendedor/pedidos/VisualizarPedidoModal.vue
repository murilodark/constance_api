<script setup>
const props = defineProps(['pedido']);
const emit = defineEmits(['close']);

const formatarMoeda = (valor) => {
    return Number(valor).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
};
</script>

<template>
    <div class="fixed inset-0 z-[70] flex items-center justify-center bg-blue-950/50 backdrop-blur-sm p-4">
        <div class="bg-white w-full max-w-4xl max-h-[85vh] rounded-[3rem] shadow-2xl flex flex-col overflow-hidden animate-in fade-in zoom-in duration-300">
            
            <!-- Header -->
            <div class="p-8 border-b bg-gray-50 flex justify-between items-center">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-900 text-white rounded-2xl flex items-center justify-center text-xl mr-4 shadow-lg">📦</div>
                    <div>
                        <h3 class="text-xl font-black text-blue-900 uppercase tracking-tighter">Detalhes do Pedido #{{ pedido.id }}</h3>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Emitido em: {{ new Date(pedido.created_at).toLocaleString('pt-BR') }}</p>
                    </div>
                </div>
                <button @click="emit('close')" class="bg-white hover:bg-red-50 text-gray-400 hover:text-red-500 p-3 rounded-2xl transition-all font-black text-xs shadow-sm">FECHAR ✕</button>
            </div>

            <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
                <!-- Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                    <div class="bg-gray-50 p-6 rounded-3xl">
                        <p class="text-[10px] font-black text-gray-400 uppercase mb-2">Fornecedor</p>
                        <p class="text-sm font-black text-blue-900 uppercase">{{ pedido.fornecedor.nome }}</p>
                        <p class="text-[10px] font-bold text-gray-500 font-mono">{{ pedido.fornecedor.cnpj }}</p>
                    </div>
                    <div class="bg-gray-50 p-6 rounded-3xl">
                        <p class="text-[10px] font-black text-gray-400 uppercase mb-2">Status do Processamento</p>
                        <span :class="pedido.status === 'Pendente' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700'" 
                              class="text-[10px] font-black px-3 py-1 rounded-lg uppercase tracking-widest">
                            {{ pedido.status }}
                        </span>
                    </div>
                    <div class="bg-blue-900 p-6 rounded-3xl shadow-lg">
                        <p class="text-[10px] font-black text-blue-200 uppercase mb-2">Valor Total</p>
                        <p class="text-xl font-black text-white">{{ formatarMoeda(pedido.valor_total) }}</p>
                    </div>
                </div>

                <!-- Tabela de Itens -->
                <div class="mb-8">
                    <h4 class="text-xs font-black text-gray-800 uppercase tracking-widest mb-4 px-2">Itens do Pedido ({{ pedido.itens.length }})</h4>
                    <div class="border border-gray-100 rounded-[2rem] overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-[10px] font-black text-gray-400 uppercase">
                                    <th class="px-6 py-4">Produto</th>
                                    <th class="px-6 py-4">Ref / Cor</th>
                                    <th class="px-6 py-4 text-center">Qtd</th>
                                    <th class="px-6 py-4 text-right">Unitário</th>
                                    <th class="px-6 py-4 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="item in pedido.itens" :key="item.id" class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 text-xs font-black text-gray-700 uppercase">{{ item.produto.nome }}</td>
                                    <td class="px-6 py-4 text-[10px] font-bold text-gray-400 font-mono">{{ item.produto.referencia }} / {{ item.produto.cor }}</td>
                                    <td class="px-6 py-4 text-xs font-black text-blue-900 text-center">{{ item.quantidade }}</td>
                                    <td class="px-6 py-4 text-xs font-bold text-gray-500 text-right">{{ formatarMoeda(item.valor_unitario) }}</td>
                                    <td class="px-6 py-4 text-xs font-black text-gray-900 text-right">{{ formatarMoeda(item.quantidade * item.valor_unitario) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Observações -->
                <div v-if="pedido.observacao" class="bg-amber-50 p-6 rounded-3xl border border-amber-100">
                    <p class="text-[10px] font-black text-amber-700 uppercase mb-2">Observações Internas</p>
                    <p class="text-xs text-amber-800 italic leading-relaxed">{{ pedido.observacao }}</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-8 border-t bg-gray-50 flex justify-end">
                <button @click="emit('close')" class="bg-blue-900 text-white px-10 py-3 rounded-xl font-black uppercase text-[10px] tracking-widest shadow-lg active:scale-95 transition-all">
                    Fechar Visualização
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
