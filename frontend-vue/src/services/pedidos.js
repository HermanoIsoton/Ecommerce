import { http } from './http'

export async function listarPedidos() {
    try {
        const response = await http.get('/orders')
        return response.data.data
    }
    catch (error) {
        const message = error.response?.data?.message || 'Falha para carregar os produtos'
        throw new Error(message)
       
    }
}

export async function buscarPedido(id) {
    try {
        const response = await http.get(`/orders/${id}`)
        return response.data.data
    }
    catch (error) {
         const message = error.response?.data?.message || 'Falha para buscar os pedidos'
        throw new Error(message)
    }
}


export async function criarpedido(dados) {
    try {
        const response = await http.post('/orders', dados)
        return response.data.data
    }
    catch (error) {
         const message = error.response?.data?.message || 'Falha para criar os pedidos'
        throw new Error(message)
    }
}


export async function atualizarPedido(id, dados) {
    try {
        const response = await http.put(`/orders/${id}`, dados)
        return response.data.data
    }
    catch (error) {
         const message = error.response?.data?.message || 'Falha para atualizar os pedidos'
        throw new Error(message)
    }
}


export async function removerPedido(id) {
    try {
        await http.delete(`/orders/${id}`)
    }
    catch (error) {
        const message = error.response?.data?.message || 'Falha para deletar os pedidos'
        throw new Error(message)
    }
}