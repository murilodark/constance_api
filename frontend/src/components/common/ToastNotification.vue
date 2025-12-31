<script setup>
defineProps({
    error: Object,
    success: String
});

const emit = defineEmits(['close']);
</script>

<template>
    <Transition name="slide-up">
        <!-- Container Fixo no Rodapé -->
        <div v-if="error?.title || success" 
             class="fixed bottom-6 left-1/2 -translate-x-1/2 z-100 w-full max-w-md px-4">
            
            <div :class="[
                'p-5 rounded-2xl shadow-2xl border flex items-start space-x-4 transition-all duration-500',
                error?.title ? 'bg-red-600 border-red-500 text-white' : 'bg-green-600 border-green-500 text-white'
            ]">
                <!-- Ícone -->
                <div class="flex-shrink-0 mt-1">
                    <span v-if="error?.title" class="text-2xl">⚠️</span>
                    <span v-else class="text-2xl">✅</span>
                </div>

                <!-- Conteúdo -->
                <div class="flex-1">
                    <h3 class="font-black text-sm uppercase tracking-wider mb-1">
                        {{ error?.title || 'Sucesso!' }}
                    </h3>
                    <p v-if="success" class="text-sm opacity-90">{{ success }}</p>
                    
                    <ul v-if="error?.details?.length" class="text-xs opacity-90 space-y-1 list-disc list-inside">
                        <li v-for="(msg, i) in error.details" :key="i">{{ msg }}</li>
                    </ul>
                </div>

                <!-- Botão Fechar -->
                <button @click="emit('close')" class="text-white/50 hover:text-white transition-colors">
                    <span class="text-xl">&times;</span>
                </button>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.slide-up-enter-active, .slide-up-leave-active {
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.slide-up-enter-from {
    transform: translate(-50%, 100%);
    opacity: 0;
}
.slide-up-leave-to {
    transform: translate(-50%, 100%);
    opacity: 0;
}
</style>
