<script setup>
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';

const auth = useAuthStore();
const router = useRouter();

const handleLogout = () => {
  auth.logout();
  router.push('/login');
};
</script>

<template>
  <div class="flex h-screen bg-gray-100 font-sans">
    <!-- Sidebar -->
    <aside class="w-72 bg-blue-900 text-white flex flex-col shadow-2xl z-20">
      <!-- Logo -->
      <div class="p-8 text-2xl font-black border-b border-blue-800 tracking-tighter uppercase">
        Constance <span class="text-blue-400 font-light">ERP</span>
      </div>
      
      <nav class="flex-grow p-4 space-y-2 overflow-y-auto custom-scrollbar">
        <!-- Links Comuns -->
        <p class="px-4 text-[10px] font-black text-blue-400 uppercase tracking-widest mb-2 mt-4">Geral</p>
        <router-link :to="auth.isAdmin ? '/admin/dashboard' : '/vendedor/dashboard'" 
          class="flex items-center p-3 rounded-xl hover:bg-blue-800 transition-all group"
          active-class="bg-blue-700 shadow-lg shadow-blue-950/50">
          <span class="text-xl">📊</span>
          <span class="ml-3 text-xs font-black uppercase tracking-tight text-blue-100 group-hover:text-white">Dashboard</span>
        </router-link>

        <!-- Menu Exclusivo do Admin (Gerenciamento) -->
        <div v-if="auth.isAdmin" class="pt-6 mt-6 border-t border-blue-800/50">
          <p class="px-4 text-[10px] font-black text-blue-400 uppercase tracking-widest mb-4">Administração</p>
          
          <router-link to="/admin/pedidos" 
  class="flex items-center p-3 rounded-xl hover:bg-blue-800 transition-all group mb-1"
  active-class="bg-blue-700 shadow-lg shadow-blue-950/50 text-white">
  <span class="text-xl">📦</span>
  <span class="ml-3 text-xs font-black uppercase tracking-tight text-blue-100 group-hover:text-white">Gerência de Pedidos</span>
</router-link>

          <router-link to="/admin/fornecedores" 
            class="flex items-center p-3 rounded-xl hover:bg-blue-800 transition-all group mb-1"
            active-class="bg-blue-700 shadow-lg shadow-blue-950/50 text-white">
            <span class="text-xl">🏢</span>
            <span class="ml-3 text-xs font-black uppercase tracking-tight text-blue-100 group-hover:text-white">Gerência de Fornecedores</span>
          </router-link>

          <router-link to="/admin/usuarios" 
            class="flex items-center p-3 rounded-xl hover:bg-blue-800 transition-all group mb-1"
            active-class="bg-blue-700 shadow-lg shadow-blue-950/50 text-white">
            <span class="text-xl">👥</span>
            <span class="ml-3 text-xs font-black uppercase tracking-tight text-blue-100 group-hover:text-white">Controle de Usuários</span>
          </router-link>

        </div>

        <!-- Menu do Vendedor / Operacional -->
       <div class="pt-6 mt-6 border-t border-blue-800/50">
  <p class="px-4 text-[10px] font-black text-blue-400 uppercase tracking-widest mb-4">Operacional</p>
  
 <router-link to="/vendedor/meus-fornecedores" 
    class="flex items-center p-3 rounded-xl hover:bg-blue-800 transition-all group mb-1"
    active-class="bg-blue-700 shadow-lg shadow-blue-950/50 text-white">
    <span class="text-xl">🏢</span>
    <span class="ml-3 text-xs font-black uppercase tracking-tight text-blue-100 group-hover:text-white">Meus Fornecedores</span>
  </router-link>

 <router-link to="/vendedor/pedidos" 
    class="flex items-center p-3 rounded-xl hover:bg-blue-800 transition-all group mb-1"
    active-class="bg-blue-700 shadow-lg shadow-blue-950/50 text-white">
    <span class="text-xl">📦</span>
    <span class="ml-3 text-xs font-black uppercase tracking-tight text-blue-100 group-hover:text-white">Meus Pedidos</span>
  </router-link>


  <!-- MOVIDO E AJUSTADO PARA O VENDEDOR -->
  <router-link to="/vendedor/report-diario" 
    class="flex items-center p-3 rounded-xl hover:bg-blue-800 transition-all group"
    active-class="bg-blue-700 shadow-lg shadow-blue-950/50 text-white">
    <span class="text-xl">📥</span>
    <span class="ml-3 text-xs font-black uppercase tracking-tight text-blue-100 group-hover:text-white">Report Diário</span>
  </router-link>
</div>
      </nav>

      <!-- Rodapé da Sidebar (User Info) -->
      <div class="p-6 border-t border-blue-800 bg-blue-950/50">
        <div class="flex items-center mb-6">
          <div class="w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center font-black text-white shadow-lg">
            {{ auth.user?.name.charAt(0).toUpperCase() }}
          </div>
          <div class="ml-4 overflow-hidden">
            <p class="text-sm font-black truncate text-white uppercase tracking-tighter">{{ auth.user?.name }}</p>
            <p class="text-[10px] text-blue-400 font-bold uppercase tracking-widest">{{ auth.user?.tipo }}</p>
          </div>
        </div>
        <button @click="handleLogout" 
          class="w-full flex items-center justify-center bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white py-3 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] transition-all border border-red-500/20">
          Sair do Sistema
        </button>
      </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col overflow-hidden">
      <!-- Topbar -->
      <header class="h-20 bg-white shadow-sm flex items-center justify-between px-10 border-b border-gray-100">
        <div>
          <h2 class="text-sm font-black text-gray-400 uppercase tracking-[0.3em]">Painel de Controle</h2>
          <p class="text-xs font-bold text-blue-900 uppercase">Ambiente {{ auth.user?.tipo === 'admin' ? 'Administrativo' : 'Vendas' }}</p>
        </div>
        <div class="flex items-center space-x-6">
          <div class="text-right">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Data de Acesso</p>
            <p class="text-xs font-bold text-gray-700">{{ new Date().toLocaleDateString('pt-BR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}</p>
          </div>
          <div class="h-10 w-[1px] bg-gray-100"></div>
          <span class="text-xl">🔔</span>
        </div>
      </header>

      <!-- Conteúdo das Views -->
      <section class="flex-1 overflow-y-auto p-10 bg-gray-50/50">
        <router-view />
      </section>
    </main>
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.2);
}
</style>
