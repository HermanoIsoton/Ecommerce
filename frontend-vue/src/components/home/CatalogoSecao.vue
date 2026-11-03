<script setup>
import { computed, onMounted  } from 'vue'
import CardProduto from '@/components/home/CardProduto.vue'
import {
  carregarCategorias,
  carregarProdutos,
  categoriasDisponiveis,
  categoriaSelecionada,
  erroCatalogo,
  filtroTipo,
  produtos,
  produtosFiltrados,
} from '@/stores/catalogo'
import { adicionarAoCarrinho } from '@/stores/carrinho'
import { avisar } from '@/stores/toast'

onMounted(() => {
  if (produtos.value.length === 0) {
    carregarProdutos()
  }
  carregarCategorias()
})

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
          <!-- filtro por tipo -->
          <button
            v-for="opcao in opcoesTipo"
            :key="opcao.valor"
            type="button"
            class="btn btn-outline-ew filtro-btn"
            :class="{ active: filtroTipo === opcao.valor }"
            :aria-pressed="filtroTipo === opcao.valor"
            @click="filtroTipo = opcao.valor"
          >{{ opcao.rotulo }}</button>
          <!-- filtro de categoria  -->
            <select
              id="filtroCategoria"
              v-model="categoriaSelecionada"
              class="btn btn-outline-ew filtro-btn"
              aria-label="Filtrar por categoria"
            >
              <option value="">Todas as categorias</option>
              <option
                v-for="categoria in categoriasDisponiveis"
                :key="categoria.id"
                :value="categoria.id"
              >
                {{ categoria.nome }}
              </option>
          </select>
        </div>

      </div>
        
      <!-- mensagem de erro -->
      <div class="row g-4">
        <p v-if="erroCatalogo" class="col-12 alert alert-danger" role="alert">
          {{ erroCatalogo }}
        </p>


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
            <!-- mensagem de erro se nao tiver veiculo encontrados -->
          <div v-if="produtosFiltrados.length === 0" class="text-center text-secondary py-5">
            <i class="bi bi-emoji-neutral fs-1 d-block mb-2" aria-hidden="true"></i>
            Nenhum veículo encontrado com esses filtros.
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
