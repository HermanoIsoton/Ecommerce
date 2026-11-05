// Aviso (toast) compartilhado por todas as paginas.
// Use `avisar('mensagem')` de qualquer componente; o <ToastAviso /> no App.vue exibe o aviso.
import { reactive } from 'vue'

export const toast = reactive({
  visivel: false,
  texto: '',
  tipo: 'sucesso', // 'sucesso' | 'info' | 'erro'
})

let temporizador = null

export function avisar(mensagem, { tipo = 'sucesso', duracao = 2500 } = {}) {
  toast.texto = mensagem
  toast.tipo = tipo
  toast.visivel = true
  clearTimeout(temporizador)
  temporizador = setTimeout(fecharAviso, duracao)
}

export function fecharAviso() {
  clearTimeout(temporizador)
  toast.visivel = false
}
