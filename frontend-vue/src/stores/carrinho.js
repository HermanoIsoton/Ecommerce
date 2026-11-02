// Estado do carrinho, compartilhado entre o menu, o catalogo e a pagina do carrinho.
// ATENCAO: DADOS PROVISORIOS DE DEMONSTRACAO.
// Cada item representa uma linha de `cart_items` (quantity) com os dados do produto relacionado (`products`).
// Troque pelo retorno da API quando for integrar.
import { computed, ref } from 'vue'
import { produtosDemo } from '@/data/produtos'

const produtoPorId = (id) => produtosDemo.find((produto) => produto.id === id)

const itens = ref([
  { id: 1, cart_id: 1, product_id: 1, quantity: 1, product: produtoPorId(1) },
  { id: 2, cart_id: 1, product_id: 3, quantity: 1, product: produtoPorId(3) },
])

export const itensDoCarrinho = itens

export const totalUnidades = computed(() =>
  itens.value.reduce((soma, item) => soma + item.quantity, 0)
)

export const subtotalCarrinho = computed(() =>
  itens.value.reduce((soma, item) => soma + item.product.price * item.quantity, 0)
)

// Devolve false quando ja existe a quantidade maxima em estoque no carrinho.
export function adicionarAoCarrinho(produto) {
  const existente = itens.value.find((item) => item.product_id === produto.id)

  if (existente) {
    if (existente.quantity >= produto.stock) {
      return false
    }
    existente.quantity += 1
    return true
  }

  const proximoId = itens.value.reduce((maior, item) => Math.max(maior, item.id), 0) + 1
  itens.value.push({ id: proximoId, cart_id: 1, product_id: produto.id, quantity: 1, product: produto })
  return true
}

export function aumentarQuantidade(itemId) {
  const item = itens.value.find((linha) => linha.id === itemId)
  if (item && item.quantity < item.product.stock) {
    item.quantity += 1
  }
}

export function diminuirQuantidade(itemId) {
  const item = itens.value.find((linha) => linha.id === itemId)
  if (item && item.quantity > 1) {
    item.quantity -= 1
  }
}

export function removerDoCarrinho(itemId) {
  itens.value = itens.value.filter((linha) => linha.id !== itemId)
}
