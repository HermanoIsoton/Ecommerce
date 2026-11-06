<script setup>
import { ref, watch } from 'vue'
import { formatarData } from '@/utils/formatadores'
import { ROTULO_PAPEL } from '@/utils/rotulos'
import { atualizarPerfil } from '@/services/auth'
import { avisar } from '@/stores/toast'

const props = defineProps({
  usuario: { type: Object, required: true },
})

const emit = defineEmits(['atualizado'])

const nome = ref('')
const email = ref('')
const salvando = ref(false)

// Preenche (e repreenche, depois de salvar) o formulario com os dados do usuario
watch(
  () => props.usuario,
  (usuario) => {
    nome.value = usuario.name
    email.value = usuario.email
  },
  { immediate: true, deep: true }
)

async function enviar(evento) {
  const formulario = evento.target
  if (!formulario.checkValidity()) {
    formulario.reportValidity()
    return
  }

  salvando.value = true
  try {
    const usuario = await atualizarPerfil({
      name: nome.value.trim(),
      email: email.value.trim(),
    })
    emit('atualizado', usuario)
    avisar('Dados da conta atualizados com sucesso.')
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
      <h2 class="h5 fw-bold mb-4">Dados pessoais</h2>
      <form novalidate @submit.prevent="enviar">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="campoNome" class="form-label">Nome</label>
            <input v-model="nome" type="text" class="form-control" id="campoNome" name="name" required autocomplete="name">
          </div>
          <div class="col-md-6">
            <label for="campoEmail" class="form-label">E-mail</label>
            <input v-model="email" type="email" class="form-control" id="campoEmail" name="email" required autocomplete="email">
          </div>
          <div class="col-md-6">
            <label for="campoPapel" class="form-label">Tipo de conta</label>
            <input :value="ROTULO_PAPEL[usuario.role]" type="text" class="form-control" id="campoPapel" readonly>
          </div>
          <div class="col-md-6">
            <label for="campoMembro" class="form-label">Cliente desde</label>
            <input :value="formatarData(usuario.created_at)" type="text" class="form-control" id="campoMembro" readonly>
          </div>
        </div>
        <div class="mt-4">
          <button type="submit" class="btn btn-ew px-4" :disabled="salvando">
            {{ salvando ? 'Salvando...' : 'Salvar alterações' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
