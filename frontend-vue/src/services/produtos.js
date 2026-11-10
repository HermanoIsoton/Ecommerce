import { http } from './http'

function mensagemDoErro(error, mensagemPadrao) {
    const errosValidacao = Object.values(error.response?.data?.errors || {}).flat()
    return errosValidacao[0] || error.response?.data?.message || mensagemPadrao
}

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
        throw new Error(mensagemDoErro(error, 'Falha para criar os produtos'))
    }
}


export async function atualizarProduto(id, dados) {
    try {
        const response = await http.put(`/products/${id}`, dados)
        return response.data.data
    }
    catch (error) {
        throw new Error(mensagemDoErro(error, 'Falha para atualizar os produtos'))
    }
}


export async function removerProduto(id) {
    try {
        await http.delete(`/products/${id}`)
    }
    catch (error) {
        throw new Error(mensagemDoErro(error, 'Falha para deletar os produtos'))
    }
}