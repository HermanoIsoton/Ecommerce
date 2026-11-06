import { ref } from 'vue'

export const enderecos = ref([])
export const carregandoEnderecos = ref(false)

let usuarioEnderecosId = null

export function iniciarEnderecosDoUsuario(userId) {
  if (usuarioEnderecosId === userId) return

  usuarioEnderecosId = userId
  enderecos.value = []
  carregandoEnderecos.value = false
}

export function definirEnderecos(userId, lista) {
  if (usuarioEnderecosId === userId) {
    enderecos.value = lista
  }
}

export function definirCarregamentoEnderecos(userId, carregando) {
  if (usuarioEnderecosId === userId) {
    carregandoEnderecos.value = carregando
  }
}

export function adicionarOuAtualizarEndereco(endereco) {
  if (usuarioEnderecosId !== endereco.user_id) return

  const index = enderecos.value.findIndex((item) => item.id === endereco.id)
  if (endereco.is_primary) {
    enderecos.value = enderecos.value.map((item) => ({ ...item, is_primary: false }))
  }

  if (index === -1) {
    enderecos.value.push(endereco)
  } else {
    enderecos.value[index] = endereco
  }
}

export function removerEnderecoDaLista(id) {
  const removido = enderecos.value.find((endereco) => endereco.id === id)
  enderecos.value = enderecos.value.filter((endereco) => endereco.id !== id)

  if (removido?.is_primary && enderecos.value.length > 0) {
    const proximoPrincipal = enderecos.value.reduce((maisAntigo, endereco) =>
      endereco.id < maisAntigo.id ? endereco : maisAntigo
    )
    enderecos.value = enderecos.value.map((endereco) => ({
      ...endereco,
      is_primary: endereco.id === proximoPrincipal.id,
    }))
  }
}
