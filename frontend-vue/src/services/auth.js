import { http, obterCsrf } from './http'

function mensagemDoErro(error, mensagemPadrao) {
    const errosValidacao = Object.values(error.response?.data?.errors || {}).flat()
    return errosValidacao[0] || error.response?.data?.message || mensagemPadrao
}

export async function cadastrarUsuario(dados) {
    try {
        await obterCsrf()
        const response = await http.post('/register', dados)
        return response.data.data
    }
    catch (error) {
         const validationErrors = Object.values(error.response?.data?.errors || {}).flat()
         const message = validationErrors[0] || error.response?.data?.message || 'Falha ao cadastrar usuário'
         throw new Error(message)
    }
}

export async function login(credenciais) {
    try {
        await obterCsrf()
        const response = await http.post('/login', credenciais)
        return response.data.data
    }
    catch (error) {
         const validationErrors = Object.values(error.response?.data?.errors || {}).flat()
         const message = validationErrors[0] || error.response?.data?.message || 'Falha ao fazer login'
         throw new Error(message)
    }
}

export async function atualizarPerfil(dados) {
    try {
        const response = await http.put('/me', dados)
        return response.data.data
    }
    catch (error) {
        throw new Error(mensagemDoErro(error, 'Falha ao atualizar os dados da conta'))
    }
}

export async function alterarSenha(dados) {
    try {
        await http.put('/me/password', dados)
    }
    catch (error) {
        throw new Error(mensagemDoErro(error, 'Falha ao alterar a senha'))
    }
}

export async function logout() {
    try {
        await http.post('/logout')
    }
    catch (error) {
        const message = error.response?.data?.message || 'Falha ao sair da conta'
        throw new Error(message)
    }
}

export async function obterUsuarioAtual() {
    try {
        const response = await http.get('/me')
        return response.data.data
    }
    catch (error) {
        if (error.response?.status === 401) {
            return null
        }

        const message = error.response?.data?.message || 'Falha ao verificar a sessão'
        throw new Error(message)
    }
}