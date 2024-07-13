import { createRouter, createWebHistory } from 'vue-router'
import EditTarefa from '@/views/Todos/EditTarefa.vue'
import Login from '@/views/Todos/Login.vue';
import AddTarefa from '@/views/Todos/AddTarefa.vue';
import Tarefas from '@/views/Todos/Tarefas.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'login.index',
      component: Login
    },
    {
      path: '/register',
      name: 'todo.register',
      component: Login
    },
    {
      path: '/tarefas',
      name: 'todo.index',
      component: Tarefas
    },
    {
      path: '/todo/create',
      name: 'todo.create',
      component: AddTarefa
    },
    {
      path: '/todo/:id/edit',
      name: 'todo.edit',
      props: true,
      component: EditTarefa
    },
  ]
})
router.beforeEach((to, from, next) => {
  if (to.matched.some(record => record.meta.requiresAuth)) {
    // Verifique se o usuário está autenticado
    if (!store.getters.isAuthenticated) {
      // Redirecionar para a página de login
      next({ name: 'login.index' });
    } else {
      next();
    }
  } else {
    next();
  }
})
export default router
