<script setup>
import { nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { cadastrarUsuario, login } from '@/services/auth'
import { avisar } from '@/stores/toast';

const props = defineProps({
  mode: {
    type: String,
    required: true,
    validator: (value) => ['login', 'register'].includes(value),
  },
})

const emit = defineEmits(['close', 'change-mode', 'authenticated'])
const aviso = ref('')
const campoInicial = ref(null)
const nome = ref('')
const email = ref('')
const senha = ref('')
const emailCadastro = ref('')
const senhaCadastro = ref('')
const confirmarSenha = ref('')
const enviando = ref(false)
let overflowAnterior = ''

async function focarCampoInicial() {
  await nextTick()
  campoInicial.value?.focus()
}

async function logar() {
  aviso.value = ''
  enviando.value = true

  try {
    const usuario = await login({
      email: email.value,
      password: senha.value,
    })
    emit('authenticated', usuario)
    emit('close')
    avisar('Você entrou com sua conta com sucesso!')
  } catch (erro) {
    aviso.value = erro.message
  } finally {
    enviando.value = false
  }
}

async function cadastrar() {
  aviso.value = ''
  enviando.value = true

  try {
    const usuario = await cadastrarUsuario({
      name: nome.value,
      email: emailCadastro.value,
      password: senhaCadastro.value,
      password_confirmation: confirmarSenha.value,
    })
    emit('authenticated', usuario)
    emit('close')
    avisar('Conta cadastrada com exito!')
  } catch (erro) {
    aviso.value = erro.message
  } finally {
    enviando.value = false
  }
}

watch(() => props.mode, () => {
  aviso.value = ''
  nome.value = ''
  email.value = ''
  senha.value = ''
  emailCadastro.value = ''
  senhaCadastro.value = ''
  confirmarSenha.value = ''
  focarCampoInicial()
})

onMounted(() => {
  overflowAnterior = document.body.style.overflow
  document.body.style.overflow = 'hidden'
  focarCampoInicial()
})

onBeforeUnmount(() => {
  document.body.style.overflow = overflowAnterior
})
</script>

<template>
  <div class="ew-modal-backdrop position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center p-3">
    <section
      class="ew-auth-modal position-relative overflow-auto bg-white rounded-4 shadow"
      role="dialog"
      aria-modal="true"
      :aria-labelledby="mode === 'login' ? 'tituloLogin' : 'tituloCadastro'"
    >
      <button
        class="ew-modal-close position-absolute top-0 end-0 m-2 d-flex align-items-center justify-content-center border-0 rounded-circle"
        type="button"
        aria-label="Fechar"
        @click="emit('close')"
      >
        <i class="bi bi-x-lg" aria-hidden="true"></i>
      </button>

      <template v-if="mode === 'login'">
        <h2 id="tituloLogin" class="h3 fw-bold mb-1">Entrar</h2>
        <p class="text-secondary mb-4">Acesse sua conta EletricWay.</p>

        <form @submit.prevent="logar">
          <div class="mb-3">
            <label for="loginEmail" class="form-label">E-mail</label>
            <input id="loginEmail" v-model="email" ref="campoInicial" class="form-control" type="email" autocomplete="email" required>
          </div>
          <div class="mb-3">
            <label for="loginSenha" class="form-label">Senha</label>
            <input id="loginSenha" v-model="senha" class="form-control" type="password" autocomplete="current-password" required>
          </div>
          <p v-if="aviso" class="alert alert-danger small py-2 mb-3" role="alert">{{ aviso }}</p>
          <button class="btn btn-ew w-100" type="submit" :disabled="enviando">
            {{ enviando ? 'Entrando...' : 'Entrar' }}
          </button>
        </form>

        <p class="text-center small mt-4 mb-0">
          Ainda não tem uma conta?
          <a href="#" @click.prevent="emit('change-mode', 'register')">Cadastre-se</a>
        </p>
      </template>

      <template v-else>
        <h2 id="tituloCadastro" class="h3 fw-bold mb-1">Criar conta</h2>
        <p class="text-secondary mb-4">Cadastre-se na EletricWay.</p>

        <form @submit.prevent="cadastrar">
          <div class="mb-3">
            <label for="cadastroNome" class="form-label">Nome</label>
            <input id="cadastroNome" v-model="nome" ref="campoInicial" class="form-control" type="text" autocomplete="name" maxlength="255" required>
          </div>
          <div class="mb-3">
            <label for="cadastroEmail" class="form-label">E-mail</label>
            <input id="cadastroEmail" v-model="emailCadastro" class="form-control" type="email" autocomplete="email" maxlength="255" required>
          </div>
          <div class="mb-3">
            <label for="cadastroSenha" class="form-label">Senha</label>
            <input id="cadastroSenha" v-model="senhaCadastro" class="form-control" type="password" autocomplete="new-password" minlength="8" maxlength="255" required>
          </div>
          <div class="mb-3">
            <label for="confirmarSenha" class="form-label">Confirmar senha</label>
            <input id="confirmarSenha" v-model="confirmarSenha" class="form-control" type="password" autocomplete="new-password" minlength="8" maxlength="255" required>
          </div>
          <p v-if="aviso" class="alert alert-danger small py-2 mb-3" role="alert">{{ aviso }}</p>
          <button class="btn btn-ew w-100" type="submit" :disabled="enviando">
            {{ enviando ? 'Cadastrando...' : 'Cadastrar-se' }}
          </button>
        </form>

        <p class="text-center small mt-4 mb-0">
          Já tem uma conta?
          <a href="#" @click.prevent="emit('change-mode', 'login')">Faça login</a>
        </p>
      </template>
    </section>
  </div>
</template>
