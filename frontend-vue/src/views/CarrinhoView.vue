<script setup>
import { computed, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import CarrinhoItem from '@/components/CarrinhoItem.vue'
import {
  aumentarQuantidade,
  carregandoCarrinho,
  carrinho,
  diminuirQuantidade,
  erroCarrinho,
  itensDoCarrinho,
  limparCarrinho,
  recarregarCarrinho,
  refletirPedidoCriado,
  removerDoCarrinho,
  sincronizandoCarrinho,
  subtotalCarrinho,
  totalCarrinho,
  totalUnidades,
} from '@/stores/carrinho'
import { listarEnderecos } from '@/services/enderecos'
import { criarPedidoCheckout } from '@/services/pedidos'
import { carregandoEnderecos, enderecos } from '@/stores/enderecos'
import { avisar } from '@/stores/toast'
import { formatarMoeda } from '@/utils/formatadores'

const FRETE_FIXO = 29.9

const props = defineProps({
  usuario: { type: Object, default: null },
  autenticacaoCarregada: { type: Boolean, default: false },
})
const emit = defineEmits(['solicitar-login'])
const router = useRouter()
const enderecoSelecionadoId = ref(null)
const etapaCheckout = ref('')
const criandoPedido = ref(false)

const vazio = computed(() => itensDoCarrinho.value.length === 0)
const enderecoSelecionado = computed(() =>
  enderecos.value.find((endereco) => endereco.id === enderecoSelecionadoId.value) ?? null
)

const resumoQuantidade = computed(() => {
  if (vazio.value) {
    return ''
  }
  return `${totalUnidades.value} ${totalUnidades.value === 1 ? 'item' : 'itens'} no carrinho`
})

const valorFrete = computed(() => enderecoSelecionado.value ? FRETE_FIXO : null)
const total = computed(() => totalCarrinho.value + (valorFrete.value ?? 0))
const possuiItemIndisponivel = computed(() =>
  itensDoCarrinho.value.some((item) => !item.is_available)
)

watch(
  () => props.usuario?.id,
  async (userId) => {
    enderecoSelecionadoId.value = null
    etapaCheckout.value = ''

    if (!userId) return

    try {
      await listarEnderecos(userId)
      const enderecoPadrao = enderecos.value.find((endereco) => endereco.is_primary)
        ?? enderecos.value[0]
      enderecoSelecionadoId.value = enderecoPadrao?.id ?? null
    } catch (erro) {
      avisar(erro.message, { tipo: 'erro' })
    }
  },
  { immediate: true }
)

async function continuarCompra() {
  if (possuiItemIndisponivel.value) {
    avisar('Ajuste ou remova os produtos indisponíveis antes de continuar.', {
      tipo: 'erro',
      duracao: 3200,
    })
    return
  }

  if (!props.autenticacaoCarregada) return
  if (!props.usuario) {
    emit('solicitar-login')
    return
  }

  if (carregandoEnderecos.value) return
  if (enderecos.value.length === 0) {
    await router.push({
      name: 'conta',
      query: { aba: 'enderecos', checkout: '1' },
    })
    return
  }

  etapaCheckout.value = 'endereco'
}

async function confirmarEnderecoEContinuar() {
  if (criandoPedido.value) return

  if (!enderecoSelecionado.value) {
    avisar('Selecione um endereço de entrega.', { tipo: 'erro' })
    return
  }

  criandoPedido.value = true
  try {
    const pedido = await criarPedidoCheckout(enderecoSelecionado.value.id)
    refletirPedidoCriado()
    await router.push({ name: 'pagamento', params: { id: pedido.id } })
  } catch (erro) {
    avisar(erro.message, { tipo: 'erro' })
  } finally {
    criandoPedido.value = false
  }
}
</script>

<template>
  <main class="flex-grow-1 py-4 py-lg-5">
    <div class="container">
      <h1 class="fw-bold mb-1">Seu carrinho</h1>
      <p class="text-secondary mb-4">{{ resumoQuantidade }}</p>

      <p v-if="erroCarrinho" class="alert alert-warning" role="alert">
        {{ erroCarrinho }}
      </p>

      <p v-if="carregandoCarrinho" class="text-secondary py-5 text-center" role="status">
        Carregando seu carrinho...
      </p>

      <div v-else-if="!carrinho" class="card card-ew text-center py-5">
        <div class="card-body">
          <h2 class="h4 fw-bold">Não foi possível carregar o carrinho</h2>
          <p class="text-secondary">Verifique sua conexão e tente novamente.</p>
          <button type="button" class="btn btn-ew-accent" @click="recarregarCarrinho">
            Tentar novamente
          </button>
        </div>
      </div>

      <div v-else-if="!vazio" class="row g-4">
        <!-- Itens do carrinho -->
        <div class="col-lg-8">
          <div class="card card-ew">
            <div class="d-flex justify-content-end px-3 pt-3">
              <button
                type="button"
                class="btn btn-sm btn-outline-danger"
                :disabled="sincronizandoCarrinho"
                @click="limparCarrinho"
              >
                {{ sincronizandoCarrinho ? 'Atualizando...' : 'Limpar carrinho' }}
              </button>
            </div>
            <ul class="list-group list-group-flush rounded-4" aria-live="polite">
              <CarrinhoItem
                v-for="item in itensDoCarrinho"
                :key="item.id"
                :item="item"
                :busy="sincronizandoCarrinho"
                @aumentar="aumentarQuantidade(item.id)"
                @diminuir="diminuirQuantidade(item.id)"
                @remover="removerDoCarrinho(item.id)"
              />
            </ul>
          </div>
          <section v-if="etapaCheckout === 'endereco'" class="card card-ew mt-3" aria-labelledby="tituloConfirmarEndereco">
            <div class="card-body p-4">
              <h2 class="h5 fw-bold" id="tituloConfirmarEndereco">Confirme o endereço de entrega</h2>
              <p class="text-secondary">O pedido será enviado para o endereço escolhido abaixo.</p>
              <fieldset class="d-grid gap-2">
                <legend class="visually-hidden">Escolha um endereço de entrega</legend>
                <label
                  v-for="endereco in enderecos"
                  :key="endereco.id"
                  class="endereco-card p-3 d-flex align-items-start gap-3"
                >
                  <input v-model="enderecoSelecionadoId" type="radio" :value="endereco.id" name="enderecoEntrega">
                  <span>
                    <strong>{{ endereco.recipient }}</strong>
                    <span v-if="endereco.is_primary" class="badge text-bg-success ms-2">Principal</span><br>
                    {{ endereco.street }}, {{ endereco.number }}<template v-if="endereco.complement"> - {{ endereco.complement }}</template><br>
                    {{ endereco.district }} · {{ endereco.city }}/{{ endereco.state }} · CEP {{ endereco.cep }}
                  </span>
                </label>
              </fieldset>
              <RouterLink :to="{ name: 'conta', query: { aba: 'enderecos' } }" class="btn btn-link px-0 mt-2">
                Gerenciar endereços
              </RouterLink>
            </div>
          </section>
          <RouterLink :to="{ name: 'home', hash: '#catalogo' }" class="btn btn-link mt-3 px-0">
            <i class="bi bi-arrow-left me-1"></i>Continuar comprando
          </RouterLink>
        </div>

        <!-- Resumo -->
        <div class="col-lg-4">
          <aside class="card card-ew resumo-sticky" aria-labelledby="tituloResumo">
            <div class="card-body p-4">
              <h2 class="h5 fw-bold mb-3" id="tituloResumo">Resumo do pedido</h2>

              <div v-if="enderecoSelecionado" class="small text-secondary mb-3">
                Endereço de entrega: CEP {{ enderecoSelecionado.cep }}.
              </div>
              <div v-else-if="props.usuario && carregandoEnderecos" class="small text-secondary mb-3" role="status">
                Carregando endereço de entrega...
              </div>
              <div v-else class="small text-secondary mb-3">
                Entre e selecione um endereço para calcular o frete.
              </div>

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

              <button
                type="button"
                class="btn btn-ew-accent btn-lg w-100"
                :disabled="possuiItemIndisponivel || sincronizandoCarrinho || criandoPedido || carregandoEnderecos || !autenticacaoCarregada"
                @click="etapaCheckout === 'endereco' ? confirmarEnderecoEContinuar() : continuarCompra()"
              >
                <i class="bi bi-lock-fill me-1" aria-hidden="true"></i>
                <template v-if="criandoPedido">Criando pedido...</template>
                <template v-else-if="etapaCheckout === 'endereco'">Confirmar endereço e ir ao pagamento</template>
                <template v-else-if="!usuario">Entrar para continuar</template>
                <template v-else-if="enderecos.length === 0">Cadastrar endereço para continuar</template>
                <template v-else>Continuar para pagamento</template>
              </button>
              <p v-if="possuiItemIndisponivel" class="small text-danger text-center mt-2 mb-0">
                Ajuste os produtos indisponíveis para continuar.
              </p>
              <p class="small text-secondary text-center mt-3 mb-0">
                É preciso entrar na conta. O pedido fica reservado por 30 minutos enquanto aguarda pagamento.
              </p>
            </div>
          </aside>
        </div>
      </div>

      <!-- Carrinho vazio -->
      <div v-else-if="carrinho && vazio" class="card card-ew text-center py-5">
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
