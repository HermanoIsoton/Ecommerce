import { http } from './http'
import {
    adicionarOuAtualizarEndereco,
    definirCarregamentoEnderecos,
    definirEnderecos,
    iniciarEnderecosDoUsuario,
    removerEnderecoDaLista,
} from '@/stores/enderecos'

function mensagemDoErro(error, mensagemPadrao) {
    const errosValidacao = Object.values(error.response?.data?.errors || {}).flat()
    return errosValidacao[0] || error.response?.data?.message || mensagemPadrao
}

export async function listarEnderecos(userId) {
    iniciarEnderecosDoUsuario(userId)
    definirCarregamentoEnderecos(userId, true)
    try {
        const response = await http.get('/addresses')
        const lista = response.data.data
        definirEnderecos(userId, lista)
        return lista
    } catch (error) {
        throw new Error(mensagemDoErro(error, 'Falha ao carregar os endereços'))
    } finally {
        definirCarregamentoEnderecos(userId, false)
    }
}

export async function buscarEndereco(id) {
    try {
        const response = await http.get(`/addresses/${id}`)
        return response.data.data
    } catch (error) {
        throw new Error(mensagemDoErro(error, 'Falha ao buscar o endereço'))
    }
}

export async function criarEndereco(dados) {
    try {
        const response = await http.post('/addresses', dados)
        adicionarOuAtualizarEndereco(response.data.data)
        return response.data.data
    } catch (error) {
        throw new Error(mensagemDoErro(error, 'Falha ao criar o endereço'))
    }
}

export async function atualizarEndereco(id, dados) {
    try {
        const response = await http.put(`/addresses/${id}`, dados)
        adicionarOuAtualizarEndereco(response.data.data)
        return response.data.data
    } catch (error) {
        throw new Error(mensagemDoErro(error, 'Falha ao atualizar o endereço'))
    }
}

export async function removerEndereco(id) {
    try {
        await http.delete(`/addresses/${id}`)
        removerEnderecoDaLista(id)
    } catch (error) {
        throw new Error(mensagemDoErro(error, 'Falha ao remover o endereço'))
    }
}
