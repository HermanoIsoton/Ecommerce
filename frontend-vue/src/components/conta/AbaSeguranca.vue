<script setup>
import { ref } from 'vue'
import { alterarSenha } from '@/services/auth'
import { avisar } from '@/stores/toast'

const senhaAtual = ref('')
const senhaNova = ref('')
const senhaConfirmar = ref('')
const confirmacaoInvalida = ref(false)
const salvando = ref(false)

async function enviar(evento) {
  const formulario = evento.target
  const senhasIguais = senhaNova.value === senhaConfirmar.value

  confirmacaoInvalida.value = !senhasIguais
  if (!formulario.checkValidity()) {
    formulario.reportValidity()
    return
  }
  if (!senhasIguais) {
    return
  }

  salvando.value = true
  try {
    await alterarSenha({
      current_password: senhaAtual.value,
      password: senhaNova.value,
      password_confirmation: senhaConfirmar.value,
    })
    senhaAtual.value = ''
    senhaNova.value = ''
    senhaConfirmar.value = ''
    confirmacaoInvalida.value = false
    avisar('Senha atualizada com sucesso.')
  } catch (erro) {
    avisar(erro.message, { tipo: 'erro' })
  } finally {
    salvando.value = false
  }
}
</script>

<template>
  <div class="card card-ew">
    <div class="card-body p-4">
      <h2 class="h5 fw-bold mb-4">Alterar senha</h2>
      <form class="col-md-8 px-0" novalidate @submit.prevent="enviar">
        <div class="mb-3">
          <label for="senhaAtual" class="form-label">Senha atual</label>
          <input v-model="senhaAtual" type="password" class="form-control" id="senhaAtual" autocomplete="current-password" required>
        </div>
        <div class="mb-3">
          <label for="senhaNova" class="form-label">Nova senha</label>
          <input v-model="senhaNova" type="password" class="form-control" id="senhaNova" autocomplete="new-password" minlength="8" required>
          <div class="form-text">Use pelo menos 8 caracteres.</div>
        </div>
        <div class="mb-4">
          <label for="senhaConfirmar" class="form-label">Confirmar nova senha</label>
          <input v-model="senhaConfirmar" type="password" class="form-control" :class="{ 'is-invalid': confirmacaoInvalida }" id="senhaConfirmar" autocomplete="new-password" required>
          <div class="invalid-feedback">As senhas não coincidem.</div>
        </div>
        <button type="submit" class="btn btn-ew px-4" :disabled="salvando">
          {{ salvando ? 'Atualizando...' : 'Atualizar senha' }}
        </button>
      </form>
    </div>
  </div>
</template>
