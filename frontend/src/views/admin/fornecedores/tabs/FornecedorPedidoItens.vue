<script setup>
defineProps(['itens']);
</script>

<template>
  <div class="p-6 bg-blue-50/30 rounded-2xl border border-blue-100/50 mt-2 mb-4">
    <h4 class="text-[10px] font-black text-blue-900 uppercase tracking-widest mb-4 flex items-center">
      <span class="mr-2 text-lg">📦</span> Detalhamento do Pedido
    </h4>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div v-for="item in itens" :key="item.id" 
        class="flex items-center bg-white p-4 rounded-xl shadow-sm border border-blue-100">
        <div class="w-12 h-12 bg-blue-900 rounded-lg flex items-center justify-center text-xl shadow-md text-white font-mono">
          {{ item.produto.referencia.charAt(0) }}
        </div>
        <div class="ml-4 flex-1">
          <p class="text-[11px] font-black text-gray-800 uppercase truncate">{{ item.produto.nome }}</p>
          <p class="text-[9px] text-gray-400 font-bold uppercase tracking-tighter">
            REF: {{ item.produto.referencia }} | COR: {{ item.produto.cor }}
          </p>
        </div>
        <div class="text-right ml-4">
          <p class="text-[10px] font-black text-blue-900">{{ item.quantidade }}x</p>
          <p class="text-[9px] font-bold text-gray-400">R$ {{ item.valor_unitario }}</p>
        </div>
      </div>
    </div>
  </div>
</template>
