import { computed, ref } from 'vue'
import {
  adicionarItemCarrinho,
  buscarCarrinho,
  criarSessaoCarrinho,
  limparItensCarrinho,
  removerItemCarrinho,
  atualizarItemCarrinho,
} from '@/services/carrinho'
import { definirTokenCarrinho } from '@/services/http'

const CHAVE_TOKEN = 'eletricway:cart-token'
const TOKEN_VALIDO = /^[a-f0-9]{64}$/

export const carrinho = ref(null)
export const erroCarrinho = ref('')
export const carregandoCarrinho = ref(false)
export const sincronizandoCarrinho = ref(false)

let inicializacaoAtual = null
let carrinhoDaConta = false

export const itensDoCarrinho = computed(() => carrinho.value?.items ?? [])
export const totalUnidades = computed(() => carrinho.value?.total_units ?? 0)
export const subtotalCarrinho = computed(() => carrinho.value?.subtotal ?? 0)
export const totalCarrinho = computed(() => carrinho.value?.total ?? 0)

function definirErro(erro, padrao) {
  erroCarrinho.value = erro instanceof Error ? erro.message : padrao
}

function salvarToken(token) {
  definirTokenCarrinho(token)

  try {
    if (typeof window !== 'undefined') {
      window.localStorage.setItem(CHAVE_TOKEN, token)
    }
    return true
  } catch {
    erroCarrinho.value = 'O carrinho está ativo, mas não será mantido após fechar este navegador.'
    return false
  }
}

function apagarToken() {
  definirTokenCarrinho('')

  try {
    if (typeof window !== 'undefined') {
      window.localStorage.removeItem(CHAVE_TOKEN)
    }
  } catch {
    erroCarrinho.value = 'Não foi possível remover a sessão anterior do carrinho neste navegador.'
  }
}

function obterTokenSalvo() {
  try {
    return typeof window === 'undefined'
      ? ''
      : window.localStorage.getItem(CHAVE_TOKEN) ?? ''
  } catch {
    erroCarrinho.value = 'Não foi possível acessar a sessão salva do carrinho neste navegador.'
    return ''
  }
}

async function iniciarCarrinhoDeVisitante() {
  erroCarrinho.value = ''
  carregandoCarrinho.value = true

  try {
    const tokenSalvo = obterTokenSalvo()

    if (TOKEN_VALIDO.test(tokenSalvo)) {
      definirTokenCarrinho(tokenSalvo)
      try {
        carrinho.value = await buscarCarrinho()
        carrinhoDaConta = false
        return carrinho.value
      } catch (erro) {
        if (erro.status !== 401 && erro.status !== 404) {
          throw erro
        }
      }
    }

    apagarToken()
    const sessao = await criarSessaoCarrinho()
    salvarToken(sessao.token)
    carrinho.value = sessao.cart
    carrinhoDaConta = false
    return carrinho.value
  } catch (erro) {
    definirErro(erro, 'Não foi possível iniciar o carrinho.')
    throw erro
  } finally {
    carregandoCarrinho.value = false
  }
}

async function resolverCarrinhoDaSessao() {
  erroCarrinho.value = ''
  carregandoCarrinho.value = true

  try {
    const tokenSalvo = obterTokenSalvo()
    if (TOKEN_VALIDO.test(tokenSalvo)) {
      definirTokenCarrinho(tokenSalvo)
    }

    try {
      carrinho.value = await buscarCarrinho()
      carrinhoDaConta = false
      return carrinho.value
    } catch (erro) {
      if (erro.status !== 401 && !(erro.status === 404 && TOKEN_VALIDO.test(tokenSalvo))) {
        throw erro
      }
    }

    apagarToken()
    const sessao = await criarSessaoCarrinho()
    salvarToken(sessao.token)
    carrinho.value = sessao.cart
    carrinhoDaConta = false
    return carrinho.value
  } catch (erro) {
    definirErro(erro, 'Não foi possível iniciar o carrinho.')
    throw erro
  } finally {
    carregandoCarrinho.value = false
  }
}

async function carregarCarrinhoDaConta() {
  erroCarrinho.value = ''
  carregandoCarrinho.value = true
  apagarToken()

  try {
    carrinho.value = await buscarCarrinho()
    carrinhoDaConta = true
    return carrinho.value
  } catch (erro) {
    definirErro(erro, 'Não foi possível carregar o carrinho da sua conta.')
    throw erro
  } finally {
    carregandoCarrinho.value = false
  }
}

export function iniciarCarrinho(usuario) {
  return acompanharInicializacao(() => (
    usuario === undefined
      ? resolverCarrinhoDaSessao()
      : usuario
        ? carregarCarrinhoDaConta()
        : iniciarCarrinhoDeVisitante()
  ))
}

export function marcarCarrinhoComoDaConta() {
  apagarToken()
  carrinhoDaConta = true
}

export async function sincronizarCarrinhoAposAutenticacao() {
  return acompanharInicializacao(carregarCarrinhoDaConta)
}

export async function iniciarCarrinhoAposLogout() {
  carrinho.value = null
  return acompanharInicializacao(iniciarCarrinhoDeVisitante)
}

export async function recarregarCarrinho() {
  return acompanharInicializacao(() => (
    carrinhoDaConta ? carregarCarrinhoDaConta() : iniciarCarrinhoDeVisitante()
  ))
}

export function refletirPedidoCriado() {
  if (!carrinho.value) return

  carrinho.value = {
    ...carrinho.value,
    items: [],
    total_units: 0,
    subtotal: 0,
    total: 0,
  }
}

function acompanharInicializacao(inicializar) {
  const tarefa = inicializar()
  let tarefaMonitorada

  tarefaMonitorada = tarefa.finally(() => {
    if (inicializacaoAtual === tarefaMonitorada) {
      inicializacaoAtual = null
    }
  })
  inicializacaoAtual = tarefaMonitorada
  return tarefaMonitorada
}

async function garantirCarrinhoInicializado() {
  if (inicializacaoAtual) {
    await inicializacaoAtual
  }

  if (!carrinho.value) {
    throw new Error('O carrinho ainda não está disponível. Tente novamente.')
  }
}

async function executarOperacao(operacao) {
  if (sincronizandoCarrinho.value) {
    return false
  }

  sincronizandoCarrinho.value = true
  erroCarrinho.value = ''

  try {
    await garantirCarrinhoInicializado()
    carrinho.value = await operacao()
    return true
  } catch (erro) {
    definirErro(erro, 'Não foi possível atualizar o carrinho.')
    return false
  } finally {
    sincronizandoCarrinho.value = false
  }
}

export function adicionarAoCarrinho(produto, quantidade = 1) {
  return executarOperacao(() => adicionarItemCarrinho({
    product_id: produto.id,
    quantity: quantidade,
  }))
}

export function atualizarQuantidade(itemId, quantidade) {
  return executarOperacao(() => atualizarItemCarrinho(itemId, { quantity: quantidade }))
}

export async function aumentarQuantidade(itemId) {
  const item = itensDoCarrinho.value.find((linha) => String(linha.id) === String(itemId))
  if (!item || !item.product.is_active || item.quantity >= item.product.stock) {
    return false
  }

  return atualizarQuantidade(itemId, item.quantity + 1)
}

export async function diminuirQuantidade(itemId) {
  const item = itensDoCarrinho.value.find((linha) => String(linha.id) === String(itemId))
  if (!item || item.quantity <= 1) {
    return false
  }

  return atualizarQuantidade(itemId, item.quantity - 1)
}

export function removerDoCarrinho(itemId) {
  return executarOperacao(() => removerItemCarrinho(itemId))
}

export function limparCarrinho() {
  return executarOperacao(() => limparItensCarrinho())
}
