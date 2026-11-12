import { http } from './http'

function tratarErro(error, mensagemPadrao) {
    const errosValidacao = Object.values(error.response?.data?.errors || {}).flat()
    const mensagem = errosValidacao[0] || error.response?.data?.message || mensagemPadrao
    const falha = new Error(mensagem)
    falha.status = error.response?.status
    falha.data = error.response?.data?.data
    throw falha
}

export async function listarPedidos(status = '') {
    try {
        const response = await http.get('/orders', {
            params: status ? { status } : {},
        })
        return response.data.data
    }
    catch (error) {
        tratarErro(error, 'Falha ao carregar os pedidos.')
    }
}

export async function buscarPedido(id) {
    try {
        const response = await http.get(`/orders/${id}`)
        return response.data.data
    }
    catch (error) {
        tratarErro(error, 'Falha ao buscar o pedido.')
    }
}


export async function criarpedido(dados) {
    try {
        const response = await http.post('/orders', dados)
        return response.data.data
    }
    catch (error) {
        tratarErro(error, 'Falha para criar o pedido.')
    }
}

export async function criarPedidoCheckout(addressId) {
    try {
        const response = await http.post('/orders/checkout', { address_id: addressId })
        return response.data.data
    } catch (error) {
        tratarErro(error, 'Não foi possível criar o pedido.')
    }
}

export async function confirmarPagamentoPedido(id) {
    try {
        const response = await http.post(`/orders/${id}/simulate-payment`)
        return response.data.data
    } catch (error) {
        tratarErro(error, 'Não foi possível confirmar o pagamento.')
    }
}


export async function atualizarPedido(id, dados) {
    try {
        const response = await http.put(`/orders/${id}`, dados)
        return response.data.data
    }
    catch (error) {
        tratarErro(error, 'Falha para atualizar o pedido.')
    }
}

export async function atualizarStatusPedido(id, status) {
    try {
        const response = await http.patch(`/orders/${id}/status`, { status })
        return response.data.data
    } catch (error) {
        tratarErro(error, 'Falha ao atualizar o status do pedido.')
    }
}


export async function removerPedido(id) {
    try {
        await http.delete(`/orders/${id}`)
    }
    catch (error) {
        tratarErro(error, 'Falha para remover o pedido.')
    }
}