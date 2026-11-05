import { http } from './http'

export async function listarCarrinhos() {
    try {
        const response = await http.get('/carts')
        return response.data.data
    }
    catch (error) {
        const message = error.response?.data?.message || 'Falha para carregar os carrinhos'
        throw new Error(message)
    }
}

export async function buscarCarrinho(id) {
    try {
        const response = await http.get(`/carts/${id}`)
        return response.data.data
    }
    catch (error) {
        const message = error.response?.data?.message || 'Falha para buscar o carrinho'
        throw new Error(message)
    }
}

export async function criarCarrinho(dados) {
    try {
        const response = await http.post('/carts', dados)
        return response.data.data
    }
    catch (error) {
        const message = error.response?.data?.message || 'Falha para criar o carrinho'
        throw new Error(message)
    }
}

export async function atualizarCarrinho(id, dados) {
    try {
        const response = await http.put(`/carts/${id}`, dados)
        return response.data.data
    }
    catch (error) {
        const message = error.response?.data?.message || 'Falha para atualizar o carrinho'
        throw new Error(message)
    }
}

export async function removerCarrinho(id) {
    try {
        await http.delete(`/carts/${id}`)
    }
    catch (error) {
        const message = error.response?.data?.message || 'Falha para deletar o carrinho'
        throw new Error(message)
    }
}
