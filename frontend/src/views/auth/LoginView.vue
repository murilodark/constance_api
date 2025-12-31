<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';

const auth = useAuthStore();
const router = useRouter();

// Dados do formulário (Iniciando com os dados do seu seeder para facilitar o teste)
const email = ref('teste2@teste.com');
const password = ref('123456');
const isLoading = ref(false);
const errorMsg = ref('');

const handleLogin = async () => {
    isLoading.value = true;
    errorMsg.value = '';
    
    try {
        await auth.login({
            email: email.value,
            password: password.value
        });

        // Redirecionamento baseado no tipo do usuário vindo da sua API
        if (auth.user.tipo === 'admin') {
            router.push('/admin/dashboard');
        } else {
            router.push('/vendedor/dashboard');
        }
    } catch (err) {
        errorMsg.value = err || 'Falha na autenticação. Verifique suas credenciais.';
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
        <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-2xl shadow-xl border border-gray-100">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-blue-900">
                    Constance / Trelos
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Acesse o painel do ERP
                </p>
            </div>

            <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
                <div class="rounded-md shadow-sm space-y-4">
                    <div>
                        <label for="email-address" class="block text-sm font-medium text-gray-700">E-mail</label>
                        <input v-model="email" id="email-address" name="email" type="email" required 
                            class="appearance-none rounded-lg relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                            placeholder="seu@email.com" />
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
                        <input v-model="password" id="password" name="password" type="password" required 
                            class="appearance-none rounded-lg relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                            placeholder="******" />
                    </div>
                </div>

                <div v-if="errorMsg" class="bg-red-50 p-3 rounded-lg border border-red-200">
                    <p class="text-sm text-red-600 text-center">{{ errorMsg }}</p>
                </div>

                <div>
                    <button type="submit" :disabled="isLoading"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-lg text-white bg-blue-900 hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 transition-all">
                        {{ isLoading ? 'Autenticando...' : 'ENTRAR NO SISTEMA' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
