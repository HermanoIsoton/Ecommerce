<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { buscarPedido, simularPagamentoPedido } from '@/services/pedidos'
import { avisar } from '@/stores/toast'
import { formatarDataHora, formatarMoeda } from '@/utils/formatadores'

const props = defineProps({
  usuario: { type: Object, default: null },
  autenticacaoCarregada: { type: Boolean, default: false },
})

const route = useRoute()
const router = useRouter()
const pedido = ref(null)
const carregando = ref(true)
const pagando = ref(false)
const agora = ref(Date.now())
let relogio

const segundosRestantes = computed(() => {
  if (!pedido.value?.payment_expires_at) return 0
  return Math.max(0, Math.ceil((Date.parse(pedido.value.payment_expires_at) - agora.value) / 1000))
})

const prazoFormatado = computed(() => {
  const minutos = Math.floor(segundosRestantes.value / 60)
  const segundos = segundosRestantes.value % 60
  return `${String(minutos).padStart(2, '0')}:${String(segundos).padStart(2, '0')}`
})

async function carregarPedido() {
  carregando.value = true
  try {
    pedido.value = await buscarPedido(route.params.id)
  } catch (erro) {
    avisar(erro.message, { tipo: 'erro' })
    pedido.value = erro.data ?? null
  } finally {
    carregando.value = false
  }
}

watch(
  () => [props.autenticacaoCarregada, props.usuario?.id, route.params.id],
  async ([autenticacaoCarregada, userId]) => {
    if (!autenticacaoCarregada) return
    if (!userId) {
      avisar('Entre na sua conta para acessar o pagamento.', { tipo: 'info' })
      await router.replace({ name: 'carrinho' })
      return
    }

    await carregarPedido()
  },
  { immediate: true }
)

watch(segundosRestantes, async (segundos) => {
  if (segundos === 0 && pedido.value?.status === 'pendente') {
    await carregarPedido()
  }
})

async function simularPagamento() {
  if (!pedido.value || pedido.value.status !== 'pendente' || segundosRestantes.value === 0) {
    await carregarPedido()
    return
  }

  pagando.value = true
  try {
    pedido.value = await simularPagamentoPedido(pedido.value.id)
    avisar('Pagamento simulado e pedido confirmado.')
  } catch (erro) {
    if (erro.data) pedido.value = erro.data
    avisar(erro.message, { tipo: 'erro' })
  } finally {
    pagando.value = false
  }
}

onMounted(() => {
  relogio = window.setInterval(() => {
    agora.value = Date.now()
  }, 1000)
})

onBeforeUnmount(() => {
  window.clearInterval(relogio)
})
</script>

<template>
  <main class="flex-grow-1 py-4 py-lg-5">
    <div class="container">
      <RouterLink :to="{ name: 'carrinho' }" class="btn btn-link px-0 mb-3">
        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Voltar ao carrinho
      </RouterLink>

      <p v-if="carregando" class="text-secondary py-5 text-center" role="status">
        Carregando os dados do pedido...
      </p>

      <div v-else-if="!pedido" class="card card-ew text-center py-5">
        <div class="card-body">
          <h1 class="h4 fw-bold">Não foi possível abrir o pagamento</h1>
          <p class="text-secondary">Confira seus pedidos na área da conta.</p>
          <RouterLink :to="{ name: 'conta', query: { aba: 'pedidos' } }" class="btn btn-ew-accent">
            Ver meus pedidos
          </RouterLink>
        </div>
      </div>

      <div v-else class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
          <section class="card card-ew">
            <div class="card-body p-4 p-md-5">
              <template v-if="pedido.status === 'pendente' && segundosRestantes > 0">
                <h1 class="h3 fw-bold mb-2">Pedido #{{ pedido.id }} aguardando pagamento</h1>
                <p class="text-secondary mb-4">
                  O pedido e os itens estão reservados até o fim do prazo. Esta é uma simulação: nenhum valor será cobrado.
                </p>

                <div class="border rounded-3 p-4 mb-4 text-center">
                  <p class="small text-secondary mb-1">Tempo para concluir</p>
                  <p class="display-5 fw-bold mb-2" role="timer" aria-live="polite">{{ prazoFormatado }}</p>
                  <p class="small text-secondary mb-0">
                    Prazo final: {{ formatarDataHora(pedido.payment_expires_at) }}
                  </p>
                </div>

                <dl class="row">
                  <dt class="col-6 text-secondary fw-normal">Produtos</dt>
                  <dd class="col-6 text-end">{{ formatarMoeda(pedido.subtotal_cents) }}</dd>
                  <dt class="col-6 text-secondary fw-normal">Frete fictício</dt>
                  <dd class="col-6 text-end">{{ formatarMoeda(pedido.shipping_cents) }}</dd>
                  <dt class="col-6 fw-bold">Total</dt>
                  <dd class="col-6 text-end fw-bold">{{ formatarMoeda(pedido.total_cents) }}</dd>
                </dl>

                <button
                  type="button"
                  class="btn btn-ew-accent btn-lg w-100 mt-2"
                  :disabled="pagando || segundosRestantes === 0"
                  @click="simularPagamento"
                >
                  {{ pagando ? 'Processando simulação...' : 'Simular pagamento' }}
                </button>
                <p class="small text-secondary text-center mt-3 mb-0">
                  Este botão apenas atualiza o status do pedido; não há cobrança real.
                </p>
              </template>

              <template v-else-if="pedido.status === 'pago'">
                <div class="text-center py-3">
                  <i class="bi bi-check-circle-fill display-3 text-success" aria-hidden="true"></i>
                  <h1 class="h3 fw-bold mt-3">Pagamento simulado com sucesso</h1>
                  <p class="text-secondary">O pedido #{{ pedido.id }} está confirmado.</p>
                </div>
              </template>

              <template v-else>
                <div class="text-center py-3">
                  <i class="bi bi-clock-history display-3 text-secondary" aria-hidden="true"></i>
                  <h1 class="h3 fw-bold mt-3">O prazo de pagamento terminou</h1>
                  <p class="text-secondary">
                    O pedido #{{ pedido.id }} foi cancelado e os itens voltaram ao estoque.
                  </p>
                </div>
              </template>

              <div v-if="pedido.status !== 'pendente' || segundosRestantes === 0" class="d-flex flex-wrap justify-content-center gap-2 mt-3">
                <RouterLink :to="{ name: 'conta', query: { aba: 'pedidos' } }" class="btn btn-ew">
                  Ver meus pedidos
                </RouterLink>
                <RouterLink :to="{ name: 'home', hash: '#catalogo' }" class="btn btn-outline-ew">
                  Voltar ao catálogo
                </RouterLink>
              </div>
            </div>
          </section>
        </div>
      </div>
    </div>
  </main>
</template>
