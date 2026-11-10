<script setup>
import { ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import ContaCabecalho from '@/components/conta/ContaCabecalho.vue'
import AbaDadosPessoais from '@/components/conta/AbaDadosPessoais.vue'
import AbaEnderecos from '@/components/conta/AbaEnderecos.vue'
import AbaPedidos from '@/components/conta/AbaPedidos.vue'
import AbaSeguranca from '@/components/conta/AbaSeguranca.vue'
import { avisar } from '@/stores/toast'
import { enderecos, iniciarEnderecosDoUsuario } from '@/stores/enderecos'
import { listarItensPedido } from '@/services/itensPedido'
import { listarPedidos } from '@/services/pedidos'
import {
  atualizarEndereco,
  criarEndereco,
  listarEnderecos,
  removerEndereco as apagarEndereco,
} from '@/services/enderecos'

const props = defineProps({
  usuario: { type: Object, default: null },
  autenticacaoCarregada: { type: Boolean, default: false },
})

const emit = defineEmits(['usuario-atualizado'])
const router = useRouter()
const route = useRoute()
const usuario = ref(null)
const pedidos = ref([])

watch(
  () => props.usuario,
  (novoUsuario) => {
    usuario.value = novoUsuario ? { ...novoUsuario } : null
  },
  { immediate: true }
)

watch(
  () => [props.autenticacaoCarregada, props.usuario?.id],
  async (userId) => {
    const [autenticacaoCarregada, id] = userId
    iniciarEnderecosDoUsuario(id ?? null)
    pedidos.value = []

    if (!autenticacaoCarregada) return
    if (!id) {
      await router.replace({ name: 'home' })
      return
    }

    try {
      const [, pedidosUsuario, itensUsuario] = await Promise.all([
        listarEnderecos(id),
        listarPedidos(),
        listarItensPedido(),
      ])
      if (props.usuario?.id !== id) return

      pedidos.value = pedidosUsuario.map((pedido) => ({
        ...pedido,
        items: itensUsuario.filter((item) => item.order_id === pedido.id),
      }))
    } catch (erro) {
      avisar(erro.message, { tipo: 'erro' })
    }
  },
  { immediate: true }
)

const abas = [
  { id: 'dados', rotulo: 'Dados pessoais', icone: 'bi-person' },
  { id: 'enderecos', rotulo: 'Endereços', icone: 'bi-geo-alt' },
  { id: 'pedidos', rotulo: 'Meus pedidos', icone: 'bi-bag-check' },
  { id: 'seguranca', rotulo: 'Segurança', icone: 'bi-shield-lock' },
]
const abaAtiva = ref('dados')

function atualizarUsuario(usuarioAtualizado) {
  usuario.value = usuarioAtualizado
  emit('usuario-atualizado', usuarioAtualizado)
}

watch(
  () => route.query.aba,
  (aba) => {
    abaAtiva.value = abas.some((item) => item.id === aba) ? aba : 'dados'
  },
  { immediate: true }
)

function sair() {
  avisar('Use o menu da conta para sair.')
}

async function salvarEndereco(dados, id) {
  if (!props.usuario?.id) {
    avisar('Entre na sua conta para salvar um endereço.', { tipo: 'erro' })
    return
  }

  try {
    if (id) {
      await atualizarEndereco(id, dados)
    } else {
      await criarEndereco(dados)
    }

    avisar('Endereço salvo com sucesso.')
    if (route.query.checkout === '1' && !id) {
      await router.push({ name: 'carrinho' })
    }
  } catch (erro) {
    avisar(erro.message, { tipo: 'erro' })
  }
}

async function removerEndereco(id) {
  try {
    await apagarEndereco(id)
    avisar('Endereço removido com sucesso.')
  } catch (erro) {
    avisar(erro.message, { tipo: 'erro' })
  }
}

async function definirEnderecoPrincipal(id) {
  try {
    await atualizarEndereco(id, { is_primary: true })
    avisar('Endereço principal atualizado.')
  } catch (erro) {
    avisar(erro.message, { tipo: 'erro' })
  }
}

/*
 * O template só é montado quando a API confirmou que existe uma sessão.
 * Isso evita exibir dados fictícios ou componentes que exigem um usuário enquanto
 * a verificação de login ainda está em andamento.
 */
</script>

<template>
  <main v-if="usuario && autenticacaoCarregada" class="conta-page flex-grow-1 py-4 py-lg-5">
    <div class="container">

      <!-- Cabecalho da conta (tabela users) -->
      <ContaCabecalho :usuario="usuario" @sair="sair" />

      <div class="row g-4">
        <!-- Menu lateral -->
        <div class="col-lg-3">
          <div class="card card-ew">
            <div class="card-body p-2">
              <div class="nav flex-column nav-pills conta-menu" role="tablist" aria-orientation="vertical" aria-label="Seções da conta">
                <button
                  v-for="aba in abas"
                  :key="aba.id"
                  class="nav-link d-flex align-items-center gap-2 px-3 py-2 fw-semibold rounded-3"
                  :class="{ active: abaAtiva === aba.id }"
                  :id="`tab-${aba.id}`"
                  type="button"
                  role="tab"
                  :aria-controls="`aba-${aba.id}`"
                  :aria-selected="abaAtiva === aba.id"
                  @click="abaAtiva = aba.id"
                >
                  <i class="bi" :class="aba.icone"></i>{{ aba.rotulo }}
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Conteudo -->
        <div class="col-lg-9">
          <div class="tab-content">
            <div class="tab-pane fade" :class="{ show: abaAtiva === 'dados', active: abaAtiva === 'dados' }" id="aba-dados" role="tabpanel" aria-labelledby="tab-dados" tabindex="0">
              <AbaDadosPessoais :usuario="usuario" @atualizado="atualizarUsuario" />
            </div>

            <div class="tab-pane fade" :class="{ show: abaAtiva === 'enderecos', active: abaAtiva === 'enderecos' }" id="aba-enderecos" role="tabpanel" aria-labelledby="tab-enderecos" tabindex="0">
              <AbaEnderecos
                :enderecos="enderecos"
                @salvar="salvarEndereco"
                @remover="removerEndereco"
                @principal="definirEnderecoPrincipal"
              />
            </div>

            <div class="tab-pane fade" :class="{ show: abaAtiva === 'pedidos', active: abaAtiva === 'pedidos' }" id="aba-pedidos" role="tabpanel" aria-labelledby="tab-pedidos" tabindex="0">
              <AbaPedidos :pedidos="pedidos" />
            </div>

            <div class="tab-pane fade" :class="{ show: abaAtiva === 'seguranca', active: abaAtiva === 'seguranca' }" id="aba-seguranca" role="tabpanel" aria-labelledby="tab-seguranca" tabindex="0">
              <AbaSeguranca />
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>
