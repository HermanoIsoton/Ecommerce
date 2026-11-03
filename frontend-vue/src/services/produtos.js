import { http } from './http'

export async function listarProdutos() {
    try {
        const response = await http.get('/products')
        return response.data.data
    }
    catch (error) {
        const message = error.response?.data?.message || 'Falha para carregar os produtos'
        throw new Error(message)
       
    }
}

export async function buscarProduto(id) {
    try {
        const response = await http.get(`/products/${id}`)
        return response.data.data
    }
    catch (error) {
         const message = error.response?.data?.message || 'Falha para buscar os produtos'
        throw new Error(message)
    }
}


export async function criarProduto(dados) {
    try {
        const response = await http.post('/products', dados)
        return response.data.data
    }
    catch (error) {
         const message = error.response?.data?.message || 'Falha para criar os produtos'
        throw new Error(message)
    }
}


export async function atualizarProduto(id, dados) {
    try {
        const response = await http.put(`/products/${id}`, dados)
        return response.data.data
    }
    catch (error) {
         const message = error.response?.data?.message || 'Falha para atualizar os produtos'
        throw new Error(message)
    }
}


export async function removerProduto(id) {
    try {
        await http.delete(`/products/${id}`)
    }
    catch (error) {
        const message = error.response?.data?.message || 'Falha para deletar os produtos'
        throw new Error(message)
    }
}