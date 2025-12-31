import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        { path: '/', redirect: '/login' },
        {
            path: '/login',
            name: 'login',
            component: () => import('@/views/auth/LoginView.vue')
        },
        // Rotas do Administrador
        {
            path: '/admin',
            component: () => import('@/layouts/AppLayout.vue'),
            meta: { requiresAuth: true, role: 'admin' },
            children: [
                { path: 'dashboard', name: 'admin.dashboard', component: () => import('@/views/admin/DashboardView.vue') },
                {
    path: '/admin/pedidos',
    name: 'AdminPedidos',
    // Certifique-se de apontar para o caminho correto do arquivo que criamos
    component: () => import('@/views/admin/pedidos/PedidosIndexView.vue'), 
    meta: { requiresAuth: true, isAdmin: true }
  },
                {
                    path: 'usuarios',
                    name: 'admin.usuarios',
                    component: () => import('@/views/admin/usuarios/UsuariosIndexView.vue')
                },
                {
                    path: 'usuarios/novo',
                    name: 'admin.usuarios.create',
                    component: () => import('@/views/admin/usuarios/UsuarioFormView.vue')
                },
                {
                    path: 'usuarios/editar/:id',
                    name: 'admin.usuarios.edit',
                    component: () => import('@/views/admin/usuarios/UsuarioFormView.vue'),
                    props: true
                },
                {
                    path: 'dashboard',
                    name: 'admin.dashboard',
                    component: () => import('@/views/admin/DashboardView.vue')
                },
                // NOVO MÓDULO DE FORNECEDORES
                {
                    path: 'fornecedores',
                    name: 'admin.fornecedores',
                    component: () => import('@/views/admin/fornecedores/FornecedorIndexView.vue')
                },
                {
                    path: 'fornecedores/dashboard/:id?',
                    name: 'admin.fornecedores.dashboard',
                    component: () => import('@/views/admin/fornecedores/FornecedorDashboardView.vue')
                },

            ]
        },
        // Rotas do Vendedor
        {
            path: '/vendedor',
            component: () => import('@/layouts/AppLayout.vue'),
            meta: { requiresAuth: true, role: 'vendedor' },
            children: [
                { path: 'dashboard', name: 'vendedor.dashboard', component: () => import('@/views/vendedor/DashboardView.vue') },
                  {
            path: 'meus-fornecedores',
            name: 'vendedor.fornecedores',
            component: () => import('@/views/vendedor/fornecedores/MeusFornecedoresView.vue')
        },
        {
    path: 'pedidos',
    name: 'vendedor.pedidos',
    // Caminho: src/views/vendedor/pedidos/PedidosIndexView.vue
    component: () => import('@/views/vendedor/pedidos/PedidosIndexView.vue')
},
                {
                    path: 'report-diario',
                    name: 'vendedor.report',
                    component: () => import('@/views/vendedor/ReportDiarioView.vue')
                }

            ]
        },
        {
            path: '/403',
            name: 'forbidden',
            component: () => import('@/views/errors/403View.vue')
        }
    ]
});

// Middleware de ProteÃ§Ã£o Global
router.beforeEach((to, from, next) => {
    const auth = useAuthStore();

    if (to.meta.requiresAuth && !auth.isAuthenticated) {
        next('/login');
    } else if (to.meta.role && to.meta.role !== auth.user?.tipo) {
        next('/403'); // Bloqueia Vendedor de entrar em Admin
    } else {
        next();
    }
});

export default router;