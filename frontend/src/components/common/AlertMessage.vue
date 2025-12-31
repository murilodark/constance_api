<script setup>
defineProps({
    error: Object // { title: string, details: Array }
});
</script>

<template>
    <div v-if="error?.title" class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded-r-lg shadow-sm">
        <div class="flex items-start">
            <div class="ml-3">
                <h3 class="text-sm font-bold text-red-800 uppercase tracking-tight">
                    {{ error.title }}
                </h3>
                <div v-if="error.details.length" class="mt-2 text-xs text-red-700 space-y-1">
                    <ul class="list-disc list-inside">
                        <li v-for="(msg, index) in error.details" :key="index">{{ msg }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>
