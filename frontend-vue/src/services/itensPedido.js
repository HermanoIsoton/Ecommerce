import { http } from './http'

export async function listarItensPedido() {
    try {
        const response = await http.get('/order_items')
        return response.data.data
    }
    catch (error) {
        const message = error.response?.data?.message || 'Falha para carregar os itens do pedido'
        throw new Error(message)
    }
}

export async function buscarItemPedido(id) {
    try {
        const response = await http.get(`/order_items/${id}`)
        return response.data.data
    }
    catch (error) {
        const message = error.response?.data?.message || 'Falha para buscar o item do pedido'
        throw new Error(message)
    }
}

export async function criarItemPedido(dados) {
    try {
        const response = await http.post('/order_items', dados)
        return response.data.data
    }
    catch (error) {
        const message = error.response?.data?.message || 'Falha para criar o item do pedido'
        throw new Error(message)
    }
}

export async function atualizarItemPedido(id, dados) {
    try {
        const response = await http.put(`/order_items/${id}`, dados)
        return response.data.data
    }
    catch (error) {
        const message = error.response?.data?.message || 'Falha para atualizar o item do pedido'
        throw new Error(message)
    }
}

export async function removerItemPedido(id) {
    try {
        await http.delete(`/order_items/${id}`)
    }
    catch (error) {
        const message = error.response?.data?.message || 'Falha para deletar o item do pedido'
        throw new Error(message)
    }
}
