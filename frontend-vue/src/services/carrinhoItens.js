import {
  adicionarItemCarrinho,
  buscarCarrinho,
  atualizarItemCarrinho,
  removerItemCarrinho,
  limparItensCarrinho,
} from './carrinho'

export async function listarCarrinhoItens() {
  return (await buscarCarrinho()).items
}

export async function buscarCarrinhoItem(id) {
  const cart = await buscarCarrinho()
  const item = cart.items.find((linha) => String(linha.id) === String(id))

  if (!item) {
    throw new Error('Este produto não está no carrinho.')
  }

  return item
}

export function criarCarrinhoItem(dados) {
  return adicionarItemCarrinho(dados)
}

export function atualizarCarrinhoItem(id, dados) {
  return atualizarItemCarrinho(id, dados)
}

export async function removerCarrinhoItem(id) {
  return removerItemCarrinho(id)
}

export async function limparCarrinhoItens() {
  return limparItensCarrinho()
}
