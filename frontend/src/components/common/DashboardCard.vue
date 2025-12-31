<script setup>
defineProps(['title', 'icon', 'value', 'colorClass']);
</script>

<template>
  <div :class="colorClass" class="p-6 rounded-[2rem] shadow-lg flex items-center justify-between transition-transform transform hover:scale-105">
    <div>
      <p class="text-[10px] font-black text-white uppercase tracking-widest">{{ title }}</p>
      <p class="text-4xl font-extrabold text-white mt-2">{{ value }}</p>
    </div>
    <div class="text-5xl opacity-50">{{ icon }}</div>
  </div>
</template>
