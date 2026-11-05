<script setup>
// EletricWay - pagina do carrinho.
// ATENCAO: FRETE DE DEMONSTRACAO. Troque `FRETE_DEMO` pelo calculo da API quando for integrar.
import { computed, ref } from 'vue'
import CarrinhoItem from '@/components/CarrinhoItem.vue'
import {
  aumentarQuantidade,
  diminuirQuantidade,
  erroCarrinho,
  itensDoCarrinho,
  limparCarrinho,
  removerDoCarrinho,
  subtotalCarrinho,
  totalUnidades,
} from '@/stores/carrinho'
import { avisar } from '@/stores/toast'
import { formatarMoeda, mascararCep } from '@/utils/formatadores'

const FRETE_DEMO = 89.9 // valor fixo so para demonstrar o calculo

const valorFrete = ref(null) // null = ainda nao calculado
const cep = ref('')
const cepInvalido = ref(false)
const textoFrete = ref('Informe o CEP de entrega.')
const campoCep = ref(null)

const vazio = computed(() => itensDoCarrinho.value.length === 0)

const resumoQuantidade = computed(() => {
  if (vazio.value) {
    return ''
  }
  return `${totalUnidades.value} ${totalUnidades.value === 1 ? 'item' : 'itens'} no carrinho`
})

const total = computed(() => subtotalCarrinho.value + (valorFrete.value || 0))

function calcularFrete() {
  const digitos = cep.value.replace(/\D/g, '')

  if (digitos.length !== 8) {
    valorFrete.value = null
    textoFrete.value = 'Digite um CEP válido com 8 números.'
    cepInvalido.value = true
  } else {
    valorFrete.value = FRETE_DEMO
    textoFrete.value = 'Frete de demonstração para o CEP informado.'
    cepInvalido.value = false
  }
}

function finalizarCompra() {
  if (valorFrete.value === null) {
    avisar('Calcule o frete pelo CEP antes de finalizar.', { tipo: 'info', duracao: 2800 })
    campoCep.value?.focus()
    return
  }
  avisar('Aqui entra a criação do pedido quando a API estiver ligada.', { tipo: 'info', duracao: 2800 })
}
</script>

<template>
  <main class="py-4 py-lg-5">
    <div class="container">
      <h1 class="fw-bold mb-1">Seu carrinho</h1>
      <p class="text-secondary mb-4">{{ resumoQuantidade }}</p>

      <p v-if="erroCarrinho" class="alert alert-warning" role="alert">
        {{ erroCarrinho }}
      </p>

      <div v-if="!vazio" class="row g-4">
        <!-- Itens (tabela cart_items) -->
        <div class="col-lg-8">
          <div class="card card-ew">
            <div class="d-flex justify-content-end px-3 pt-3">
              <button type="button" class="btn btn-sm btn-outline-danger" @click="limparCarrinho">
                Limpar carrinho
              </button>
            </div>
            <ul class="list-group list-group-flush rounded-4" aria-live="polite">
              <CarrinhoItem
                v-for="item in itensDoCarrinho"
                :key="item.id"
                :item="item"
                @aumentar="aumentarQuantidade(item.id)"
                @diminuir="diminuirQuantidade(item.id)"
                @remover="removerDoCarrinho(item.id)"
              />
            </ul>
          </div>
          <RouterLink :to="{ name: 'home', hash: '#catalogo' }" class="btn btn-link mt-3 px-0">
            <i class="bi bi-arrow-left me-1"></i>Continuar comprando
          </RouterLink>
        </div>

        <!-- Resumo -->
        <div class="col-lg-4">
          <aside class="card card-ew resumo-sticky" aria-labelledby="tituloResumo">
            <div class="card-body p-4">
              <h2 class="h5 fw-bold mb-3" id="tituloResumo">Resumo do pedido</h2>

              <form class="mb-3" novalidate @submit.prevent="calcularFrete">
                <label for="campoCepFrete" class="form-label small fw-semibold">Calcular frete</label>
                <div class="input-group">
                  <input
                    ref="campoCep"
                    :value="cep"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': cepInvalido }"
                    id="campoCepFrete"
                    inputmode="numeric"
                    maxlength="9"
                    placeholder="00000-000"
                    autocomplete="postal-code"
                    @input="cep = mascararCep($event)"
                  >
                  <button class="btn btn-outline-ew" type="submit">Calcular</button>
                </div>
                <div class="form-text">{{ textoFrete }}</div>
              </form>

              <dl class="row mb-0">
                <dt class="col-6 fw-normal text-secondary">Subtotal</dt>
                <dd class="col-6 text-end">{{ formatarMoeda(subtotalCarrinho) }}</dd>
                <dt class="col-6 fw-normal text-secondary">Frete</dt>
                <dd class="col-6 text-end">{{ valorFrete === null ? '—' : formatarMoeda(valorFrete) }}</dd>
              </dl>
              <hr>
              <div class="d-flex justify-content-between align-items-baseline mb-4">
                <span class="fw-bold">Total</span>
                <span class="preco">{{ formatarMoeda(total) }}</span>
              </div>

              <button type="button" class="btn btn-ew-accent btn-lg w-100" @click="finalizarCompra">
                <i class="bi bi-lock-fill me-1" aria-hidden="true"></i>Finalizar compra
              </button>
              <p class="small text-secondary text-center mt-3 mb-0">É preciso estar logado para concluir o pedido.</p>
            </div>
          </aside>
        </div>
      </div>

      <!-- Carrinho vazio -->
      <div v-else class="card card-ew text-center py-5">
        <div class="card-body">
          <i class="bi bi-cart-x display-3 text-secondary" aria-hidden="true"></i>
          <h2 class="h4 fw-bold mt-3">Seu carrinho está vazio</h2>
          <p class="text-secondary">Que tal conhecer os autopropelidos e ciclomotores da EletricWay?</p>
          <RouterLink :to="{ name: 'home', hash: '#catalogo' }" class="btn btn-ew-accent btn-lg px-4">Ver veículos</RouterLink>
        </div>
      </div>
    </div>
  </main>
</template>
