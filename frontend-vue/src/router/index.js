import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '@/views/HomeView.vue'
import CarrinhoView from '@/views/CarrinhoView.vue'
import ContaView from '@/views/ContaView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
      meta: { titulo: 'EletricWay | Autopropelidos e ciclomotores elétricos' },
    },
    {
      path: '/carrinho',
      name: 'carrinho',
      component: CarrinhoView,
      meta: { titulo: 'Carrinho | EletricWay' },
    },
    {
      path: '/conta',
      name: 'conta',
      component: ContaView,
      meta: { titulo: 'Minha conta | EletricWay' },
    },
    { path: '/:pathMatch(.*)*', redirect: { name: 'home' } },
  ],
  scrollBehavior(to, from, posicaoSalva) {
    if (posicaoSalva) {
      return posicaoSalva
    }
    if (to.hash) {
      return { el: to.hash, behavior: 'smooth' }
    }
    return { top: 0 }
  },
})

router.afterEach((to) => {
  document.title = to.meta.titulo ?? 'EletricWay'
})

export default router
