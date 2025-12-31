<script setup>
defineProps({
    show: Boolean,
    message: { type: String, default: 'Processando requisição...' }
});
</script>

<template>
    <Transition name="fade">
        <div v-if="show" class="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-blue-900/40 backdrop-blur-sm">
            <div class="bg-white p-8 rounded-2xl shadow-2xl flex flex-col items-center space-y-4 border border-blue-100">
                <!-- Spinner Animado -->
                <div class="w-12 h-12 border-4 border-blue-200 border-t-blue-900 rounded-full animate-spin"></div>
                
                <div class="text-center">
                    <p class="text-blue-900 font-black text-xs uppercase tracking-widest">{{ message }}</p>
                    <p class="text-gray-400 text-[10px] mt-1 italic">Constance ERP - Sistema Trelos</p>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
