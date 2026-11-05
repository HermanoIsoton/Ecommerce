import { http, obterCsrf } from './http'

export async function cadastrarUsuario(dados) {
    try {
        const response = await http.post('/register', dados)
        return response.data.data
    }
    catch (error) {
         const message = error.response?.data?.message || 'Falha ao cadastrar usuário'
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
         const message = error.response?.data?.message || 'Falha ao fazer login'
         throw new Error(message)
    }
}