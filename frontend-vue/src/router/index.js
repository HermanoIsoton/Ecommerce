import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '@/views/HomeView.vue'
import CarrinhoView from '@/views/CarrinhoView.vue'
import ContaView from '@/views/ContaView.vue'
import ProdutoView from '@/views/ProdutoView.vue'
import PagamentoView from '@/views/PagamentoView.vue'
import AdminView from '@/views/AdminView.vue'
import { obterUsuarioAtual } from '@/services/auth'
import { avisar } from '@/stores/toast'

async function exigirAdministrador() {
  try {
    const usuario = await obterUsuarioAtual()
    return usuario?.role === 'admin' ? true : { name: 'home' }
  } catch (erro) {
    avisar(erro.message, { tipo: 'erro' })
    return { name: 'home' }
  }
}

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
      path: '/produto/:id(\\d+)',
      name: 'produto',
      component: ProdutoView,
      meta: { titulo: 'Detalhes do produto | EletricWay' },
    },
    {
      path: '/conta',
      name: 'conta',
      component: ContaView,
      meta: { titulo: 'Minha conta | EletricWay' },
    },
    {
      path: '/pagamento/:id(\\d+)',
      name: 'pagamento',
      component: PagamentoView,
      meta: { titulo: 'Pagamento | EletricWay' },
    },
    {
      path: '/admin',
      name: 'admin',
      component: AdminView,
      beforeEnter: exigirAdministrador,
      meta: { titulo: 'Administração | EletricWay' },
    },
    { path: '/:pathMatch(.*)*', redirect: { name: 'home' } },
  ],
  scrollBehavior(to, from, posicaoSalva) {
    if (from.matched.length === 0) {
      return posicaoSalva || false
    }
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
