import { http } from './http'

export async function listarCarrinhoItens() {
    try {
        const response = await http.get('/cart_items')
        return response.data.data
    }
    catch (error) {
        const message = error.response?.data?.message || 'Falha para carregar os itens do carrinho'
        throw new Error(message)
    }
}

export async function buscarCarrinhoItem(id) {
    try {
        const response = await http.get(`/cart_items/${id}`)
        return response.data.data
    }
    catch (error) {
        const message = error.response?.data?.message || 'Falha para buscar o item do carrinho'
        throw new Error(message)
    }
}

export async function criarCarrinhoItem(dados) {
    try {
        const response = await http.post('/cart_items', dados)
        return response.data.data
    }
    catch (error) {
        const message = error.response?.data?.message || 'Falha para criar o item do carrinho'
        throw new Error(message)
    }
}

export async function atualizarCarrinhoItem(id, dados) {
    try {
        const response = await http.put(`/cart_items/${id}`, dados)
        return response.data.data
    }
    catch (error) {
        const message = error.response?.data?.message || 'Falha para atualizar o item do carrinho'
        throw new Error(message)
    }
}

export async function removerCarrinhoItem(id) {
    try {
        await http.delete(`/cart_items/${id}`)
    }
    catch (error) {
        const message = error.response?.data?.message || 'Falha para deletar o item do carrinho'
        throw new Error(message)
    }
}
