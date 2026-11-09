<script setup>
import { onMounted, ref } from 'vue'
import NavbarEw from '@/components/NavbarEw.vue'
import RodapeEw from '@/components/RodapeEw.vue'
import ToastAviso from '@/components/ToastAviso.vue'
import { obterUsuarioAtual } from '@/services/auth'
import { avisar } from '@/stores/toast'
import {
  iniciarCarrinho,
  iniciarCarrinhoAposLogout,
  marcarCarrinhoComoDaConta,
  sincronizarCarrinhoAposAutenticacao,
} from '@/stores/carrinho'

const usuario = ref(null)
const autenticacaoCarregada = ref(false)
const navbarRef = ref(null)

onMounted(async () => {
  const [resultadoUsuario, resultadoCarrinho] = await Promise.allSettled([
    obterUsuarioAtual(),
    iniciarCarrinho(),
  ])

  if (resultadoUsuario.status === 'fulfilled') {
    usuario.value = resultadoUsuario.value
  } else {
    avisar(resultadoUsuario.reason.message, { tipo: 'erro' })
  }

  if (resultadoCarrinho.status === 'fulfilled') {
    if (usuario.value) {
      marcarCarrinhoComoDaConta()
    }
  } else {
    avisar(resultadoCarrinho.reason.message, { tipo: 'erro' })
  }

  autenticacaoCarregada.value = true
})

async function aoAutenticar(novoUsuario) {
  usuario.value = novoUsuario

  try {
    await sincronizarCarrinhoAposAutenticacao()
  } catch (erro) {
    avisar(erro.message, { tipo: 'erro' })
  }
}

async function aoSair() {
  usuario.value = null

  try {
    await iniciarCarrinhoAposLogout()
  } catch (erro) {
    avisar(erro.message, { tipo: 'erro' })
  }
}

function abrirLoginCheckout() {
  navbarRef.value?.abrirModalAutenticacao('login')
}
</script>

<template>
  <NavbarEw
    ref="navbarRef"
    :usuario="usuario"
    :autenticacao-carregada="autenticacaoCarregada"
    @authenticated="aoAutenticar"
    @logged-out="aoSair"
  />
  <RouterView v-slot="{ Component, route }">
    <component
      :is="Component"
      @usuario-atualizado="usuario = $event"
      @solicitar-login="abrirLoginCheckout"
      v-bind="{
        usuario,
        ...(['carrinho', 'conta', 'pagamento'].includes(route.name)
          ? { autenticacaoCarregada }
          : {}),
      }"
    />
  </RouterView>
  <RodapeEw />
  <ToastAviso />
</template>
