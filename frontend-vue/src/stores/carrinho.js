import { computed, ref } from 'vue'

const CHAVE_TOKEN = 'eletricway:cart-token'
const PREFIXO_CARRINHO = 'eletricway:guest-cart:'

function gerarToken() {
  if (!globalThis.crypto?.getRandomValues) {
    throw new Error('Este navegador não oferece suporte para criar um token seguro para o carrinho.')
  }

  const bytes = new Uint8Array(32)
  globalThis.crypto.getRandomValues(bytes)
  return Array.from(bytes, (byte) => byte.toString(16).padStart(2, '0')).join('')
}

function obterToken() {
  let token = localStorage.getItem(CHAVE_TOKEN)

  if (!/^[a-f0-9]{64}$/.test(token ?? '')) {
    token = gerarToken()
    localStorage.setItem(CHAVE_TOKEN, token)
  }

  return token
}

const tokenCarrinho = ref('')
const itens = ref([])
export const erroCarrinho = ref('')

function salvarCarrinho() {
  try {
    localStorage.setItem(`${PREFIXO_CARRINHO}${tokenCarrinho.value}`, JSON.stringify(itens.value))
    erroCarrinho.value = ''
    return true
  } catch {
    erroCarrinho.value = 'Não foi possível salvar o carrinho neste navegador.'
    return false
  }
}

function carregarCarrinho() {
  try {
    tokenCarrinho.value = obterToken()
    const salvo = localStorage.getItem(`${PREFIXO_CARRINHO}${tokenCarrinho.value}`)
    const dados = salvo ? JSON.parse(salvo) : []

    const dadosValidos = Array.isArray(dados) && dados.every((item) =>
      item &&
      Number.isInteger(Number(item.id)) &&
      Number.isInteger(Number(item.product_id)) &&
      Number.isInteger(Number(item.quantity)) &&
      Number(item.quantity) > 0 &&
      item.product &&
      Number.isFinite(Number(item.product.price)) &&
      Number.isFinite(Number(item.product.stock))
    )

    if (!dadosValidos) {
      throw new Error('Os dados salvos do carrinho são inválidos.')
    }

    itens.value = dados
  } catch (erro) {
    erroCarrinho.value = erro instanceof Error
      ? erro.message
      : 'Não foi possível carregar o carrinho salvo.'
    itens.value = []
  }
}

if (typeof window !== 'undefined') {
  carregarCarrinho()
}

export const tokenDoCarrinho = computed(() => tokenCarrinho.value)
export const itensDoCarrinho = itens

export const totalUnidades = computed(() =>
  itens.value.reduce((soma, item) => soma + Number(item.quantity), 0)
)

export const subtotalCarrinho = computed(() =>
  itens.value.reduce((soma, item) => soma + Number(item.product.price) * Number(item.quantity), 0)
)

export function adicionarAoCarrinho(produto, quantidade = 1) {
  const quantidadeSolicitada = Number(quantidade)
  const estoque = Number(produto.stock)

  if (!Number.isInteger(quantidadeSolicitada) || quantidadeSolicitada < 1 || estoque < 1) {
    return false
  }

  const existente = itens.value.find((item) => String(item.product_id) === String(produto.id))
  const quantidadeAtual = Number(existente?.quantity ?? 0)

  if (quantidadeAtual + quantidadeSolicitada > estoque) {
    return false
  }

  if (existente) {
    existente.quantity = quantidadeAtual + quantidadeSolicitada
  } else {
    const proximoId = itens.value.reduce((maior, item) => Math.max(maior, Number(item.id) || 0), 0) + 1
    itens.value.push({
      id: proximoId,
      cart_id: null,
      product_id: produto.id,
      quantity: quantidadeSolicitada,
      product: { ...produto },
    })
  }

  return salvarCarrinho()
}

export function atualizarQuantidade(itemId, quantidade) {
  const item = itens.value.find((linha) => String(linha.id) === String(itemId))
  const novaQuantidade = Number(quantidade)

  if (!item || !Number.isInteger(novaQuantidade) || novaQuantidade < 1) {
    return false
  }

  if (novaQuantidade > Number(item.product.stock)) {
    return false
  }

  item.quantity = novaQuantidade
  return salvarCarrinho()
}

export function aumentarQuantidade(itemId) {
  const item = itens.value.find((linha) => String(linha.id) === String(itemId))
  if (!item) return false
  return atualizarQuantidade(itemId, Number(item.quantity) + 1)
}

export function diminuirQuantidade(itemId) {
  const item = itens.value.find((linha) => String(linha.id) === String(itemId))
  if (!item || Number(item.quantity) <= 1) return false
  return atualizarQuantidade(itemId, Number(item.quantity) - 1)
}

export function removerDoCarrinho(itemId) {
  const quantidadeAnterior = itens.value.length
  itens.value = itens.value.filter((linha) => String(linha.id) !== String(itemId))

  if (itens.value.length === quantidadeAnterior) {
    return false
  }

  return salvarCarrinho()
}

export function limparCarrinho() {
  itens.value = []
  return salvarCarrinho()
}
