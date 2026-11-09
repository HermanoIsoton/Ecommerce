import { http } from './http'

function tratarErro(error, mensagemPadrao) {
  const errosValidacao = Object.values(error.response?.data?.errors || {}).flat()
  const mensagem = errosValidacao[0] || error.response?.data?.message || mensagemPadrao
  const erro = new Error(mensagem)
  erro.status = error.response?.status
  throw erro
}

export async function criarSessaoCarrinho() {
  try {
    const response = await http.post('/cart/session')
    return {
      cart: response.data.data,
      token: response.data.token,
    }
  } catch (error) {
    tratarErro(error, 'Não foi possível iniciar o carrinho.')
  }
}

export async function buscarCarrinho() {
  try {
    const response = await http.get('/cart')
    return response.data.data
  } catch (error) {
    tratarErro(error, 'Não foi possível carregar o carrinho.')
  }
}

export async function adicionarItemCarrinho(dados) {
  try {
    const response = await http.post('/cart/items', dados)
    return response.data.data
  } catch (error) {
    tratarErro(error, 'Não foi possível adicionar este produto ao carrinho.')
  }
}

export async function atualizarItemCarrinho(id, dados) {
  try {
    const response = await http.put(`/cart/items/${id}`, dados)
    return response.data.data
  } catch (error) {
    tratarErro(error, 'Não foi possível atualizar a quantidade.')
  }
}

export async function removerItemCarrinho(id) {
  try {
    const response = await http.delete(`/cart/items/${id}`)
    return response.data.data
  } catch (error) {
    tratarErro(error, 'Não foi possível remover este produto do carrinho.')
  }
}

export async function limparItensCarrinho() {
  try {
    const response = await http.delete('/cart/items')
    return response.data.data
  } catch (error) {
    tratarErro(error, 'Não foi possível limpar o carrinho.')
  }
}
