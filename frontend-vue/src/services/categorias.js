import { http } from './http'

export async function listarCategorias() {
    try {
        const response = await http.get('/categories')
        return response.data.data
    }   
    catch (error) {
        const message = error.response?.data?.message || 'Falha para carregar os categorias'
        throw new Error(message)
       
    }
}

export async function buscarCategorias(id) {
    try {
        const response = await http.get(`/categories/${id}`)
        return response.data.data
    }
    catch (error) {
         const message = error.response?.data?.message || 'Falha para buscar os categorias'
        throw new Error(message)
    }
}


export async function criarCategorias(dados) {
    try {
        const response = await http.post('/categories', dados)
        return response.data.data
    }
    catch (error) {
         const message = error.response?.data?.message || 'Falha para criar os categorias'
        throw new Error(message)
    }
}


export async function atualizarCategorias(id, dados) {
    try {
        const response = await http.put(`/categories/${id}`, dados)
        return response.data.data
    }
    catch (error) {
         const message = error.response?.data?.message || 'Falha para atualizar os categorias'
        throw new Error(message)
    }
}


export async function removerCategorias(id) {
    try {
        await http.delete(`/categories/${id}`)
    }
    catch (error) {
        const message = error.response?.data?.message || 'Falha para deletar os categorias'
        throw new Error(message)
    }
}