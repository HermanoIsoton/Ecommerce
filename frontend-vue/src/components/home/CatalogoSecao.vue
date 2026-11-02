<script setup>
import { computed } from 'vue'
import CardProduto from '@/components/home/CardProduto.vue'
import FiltrosCatalogo from '@/components/home/FiltrosCatalogo.vue'
import { filtroTipo, produtosFiltrados } from '@/stores/catalogo'
import { adicionarAoCarrinho } from '@/stores/carrinho'
import { avisar } from '@/stores/toast'

const opcoesTipo = [
  { valor: 'todos', rotulo: 'Todos' },
  { valor: 'autopropelido', rotulo: 'Autopropelidos' },
  { valor: 'ciclomotor', rotulo: 'Ciclomotores' },
]

const contagem = computed(() => {
  const total = produtosFiltrados.value.length
  return `${total} ${total === 1 ? 'veículo encontrado' : 'veículos encontrados'}`
})

function adicionar(produto) {
  if (adicionarAoCarrinho(produto)) {
    avisar(`${produto.name} foi adicionado ao carrinho (demonstração).`)
  } else {
    avisar(`Você já tem todo o estoque de ${produto.name} no carrinho.`, { tipo: 'info' })
  }
}
</script>

<template>
  <section class="py-5" id="catalogo" aria-labelledby="tituloCatalogo">
    <div class="container">
      <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
        <div>
          <h2 class="fw-bold mb-1" id="tituloCatalogo">Nossos veículos</h2>
          <p class="text-secondary mb-0">Escolha o tipo que combina com o seu dia a dia.</p>
        </div>
        <div class="btn-group" role="group" aria-label="Filtrar por tipo">
          <button
            v-for="opcao in opcoesTipo"
            :key="opcao.valor"
            type="button"
            class="btn btn-outline-ew filtro-btn"
            :class="{ active: filtroTipo === opcao.valor }"
            :aria-pressed="filtroTipo === opcao.valor"
            @click="filtroTipo = opcao.valor"
          >{{ opcao.rotulo }}</button>
        </div>
      </div>

      <div class="row g-4">
        <FiltrosCatalogo />

        <!-- ===== Cards dos veiculos ===== -->
        <div class="col-lg-9">
          <p class="small text-secondary mb-3" aria-live="polite">{{ contagem }}</p>
          <div class="row g-4" aria-live="polite">
            <CardProduto
              v-for="produto in produtosFiltrados"
              :key="produto.id"
              :produto="produto"
              @adicionar="adicionar"
            />
          </div>

          <div v-if="produtosFiltrados.length === 0" class="text-center text-secondary py-5">
            <i class="bi bi-emoji-neutral fs-1 d-block mb-2" aria-hidden="true"></i>
            Nenhum veículo encontrado com esses filtros.
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
