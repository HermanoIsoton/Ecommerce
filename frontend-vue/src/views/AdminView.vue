<script setup>
import { ref } from 'vue'
import ProdutosAdmin from '@/components/admin/ProdutosAdmin.vue'
import CategoriasAdmin from '@/components/admin/CategoriasAdmin.vue'
import PedidosAdmin from '@/components/admin/PedidosAdmin.vue'

const secao = ref('produtos')
const atualizacao = ref(0)

function atualizarDados() {
  atualizacao.value += 1
}
</script>

<template>
  <main class="container flex-grow-1 py-4 py-lg-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
      <div>
        <p class="text-uppercase text-secondary small fw-bold mb-1">Painel restrito</p>
        <h1 class="h2 fw-bold mb-0">Administração da loja</h1>
      </div>
      <button class="btn btn-outline-secondary" type="button" @click="atualizarDados">
        <i class="bi bi-arrow-clockwise me-1" aria-hidden="true"></i>Atualizar dados
      </button>
    </div>

    <ul class="nav nav-tabs mb-4" aria-label="Seções administrativas">
      <li class="nav-item">
        <button class="nav-link" :class="{ active: secao === 'produtos' }" type="button" @click="secao = 'produtos'">
          Produtos
        </button>
      </li>
      <li class="nav-item">
        <button class="nav-link" :class="{ active: secao === 'categorias' }" type="button" @click="secao = 'categorias'">
          Categorias
        </button>
      </li>
      <li class="nav-item">
        <button class="nav-link" :class="{ active: secao === 'pedidos' }" type="button" @click="secao = 'pedidos'">
          Pedidos
        </button>
      </li>
    </ul>

    <section v-show="secao === 'produtos'" aria-label="Produtos">
      <ProdutosAdmin :atualizacao="atualizacao" />
    </section>
    <section v-show="secao === 'categorias'" aria-label="Categorias">
      <CategoriasAdmin :atualizacao="atualizacao" @alterado="atualizarDados" />
    </section>
    <section v-show="secao === 'pedidos'" aria-label="Pedidos">
      <PedidosAdmin :atualizacao="atualizacao" />
    </section>
  </main>
</template>