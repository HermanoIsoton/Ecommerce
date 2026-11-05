<script setup>
import { nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue'

const props = defineProps({
  mode: {
    type: String,
    required: true,
    validator: (value) => ['login', 'register'].includes(value),
  },
})

const emit = defineEmits(['close', 'change-mode'])
const aviso = ref('')
const campoInicial = ref(null)
let overflowAnterior = ''

async function focarCampoInicial() {
  await nextTick()
  campoInicial.value?.focus()
}

function mostrarAviso() {
  aviso.value = 'A autenticação ainda não está conectada.'
}

function fecharComEscape(evento) {
  if (evento.key === 'Escape') {
    emit('close')
  }
}

watch(() => props.mode, () => {
  aviso.value = ''
  focarCampoInicial()
})

onMounted(() => {
  overflowAnterior = document.body.style.overflow
  document.body.style.overflow = 'hidden'
  document.addEventListener('keydown', fecharComEscape)
  focarCampoInicial()
})

onBeforeUnmount(() => {
  document.body.style.overflow = overflowAnterior
  document.removeEventListener('keydown', fecharComEscape)
})
</script>

<template>
  <div class="ew-modal-backdrop" @click.self="emit('close')">
    <section
      class="ew-auth-modal"
      role="dialog"
      aria-modal="true"
      :aria-labelledby="mode === 'login' ? 'tituloLogin' : 'tituloCadastro'"
    >
      <button
        class="ew-modal-close"
        type="button"
        aria-label="Fechar"
        @click="emit('close')"
      >
        <i class="bi bi-x-lg" aria-hidden="true"></i>
      </button>

      <template v-if="mode === 'login'">
        <h2 id="tituloLogin" class="h3 fw-bold mb-1">Entrar</h2>
        <p class="text-secondary mb-4">Acesse sua conta EletricWay.</p>

        <form @submit.prevent="mostrarAviso">
          <div class="mb-3">
            <label for="loginEmail" class="form-label">E-mail</label>
            <input id="loginEmail" ref="campoInicial" class="form-control" type="email" autocomplete="email" required>
          </div>
          <div class="mb-3">
            <label for="loginSenha" class="form-label">Senha</label>
            <input id="loginSenha" class="form-control" type="password" autocomplete="current-password" required>
          </div>
          <p v-if="aviso" class="small text-secondary" role="status">{{ aviso }}</p>
          <button class="btn btn-ew w-100" type="submit">Entrar</button>
        </form>

        <p class="text-center small mt-4 mb-0">
          Ainda não tem uma conta?
          <a href="#" @click.prevent="emit('change-mode', 'register')">Cadastre-se</a>
        </p>
      </template>

      <template v-else>
        <h2 id="tituloCadastro" class="h3 fw-bold mb-1">Criar conta</h2>
        <p class="text-secondary mb-4">Cadastre-se na EletricWay.</p>

        <form @submit.prevent="mostrarAviso">
          <div class="mb-3">
            <label for="cadastroNome" class="form-label">Nome</label>
            <input id="cadastroNome" ref="campoInicial" class="form-control" type="text" autocomplete="name" required>
          </div>
          <div class="mb-3">
            <label for="cadastroEmail" class="form-label">E-mail</label>
            <input id="cadastroEmail" class="form-control" type="email" autocomplete="email" required>
          </div>
          <div class="mb-3">
            <label for="cadastroSenha" class="form-label">Senha</label>
            <input id="cadastroSenha" class="form-control" type="password" autocomplete="new-password" required>
          </div>
          <div class="mb-3">
            <label for="confirmarSenha" class="form-label">Confirmar senha</label>
            <input id="confirmarSenha" class="form-control" type="password" autocomplete="new-password" required>
          </div>
          <p v-if="aviso" class="small text-secondary" role="status">{{ aviso }}</p>
          <button class="btn btn-ew w-100" type="submit">Cadastrar-se</button>
        </form>

        <p class="text-center small mt-4 mb-0">
          Já tem uma conta?
          <a href="#" @click.prevent="emit('change-mode', 'login')">Faça login</a>
        </p>
      </template>
    </section>
  </div>
</template>
