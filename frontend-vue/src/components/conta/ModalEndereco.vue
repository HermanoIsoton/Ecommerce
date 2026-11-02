<script setup>
// Modal de endereco feito so com Vue + classes do Bootstrap (sem o JS do Bootstrap).
import { nextTick, onBeforeUnmount, reactive, ref, watch } from 'vue'
import { mascararCep } from '@/utils/formatadores'

const props = defineProps({
  aberto: { type: Boolean, default: false },
  endereco: { type: Object, default: null }, // null = novo endereco
})

const emit = defineEmits(['fechar', 'salvar'])

const campoDestinatario = ref(null)

const formVazio = () => ({
  recipient: '',
  cep: '',
  street: '',
  number: '',
  complement: '',
  district: '',
  city: '',
  state: '',
})
const form = reactive(formVazio())

function aoPressionarTecla(evento) {
  if (evento.key === 'Escape') {
    emit('fechar')
  }
}

watch(
  () => props.aberto,
  async (aberto) => {
    document.body.classList.toggle('modal-open', aberto)

    if (aberto) {
      const { id, user_id, ...dados } = props.endereco ?? {}
      Object.assign(form, formVazio(), dados)
      window.addEventListener('keydown', aoPressionarTecla)
      await nextTick()
      campoDestinatario.value?.focus()
    } else {
      window.removeEventListener('keydown', aoPressionarTecla)
    }
  }
)

onBeforeUnmount(() => {
  document.body.classList.remove('modal-open')
  window.removeEventListener('keydown', aoPressionarTecla)
})

function enviar(evento) {
  const formulario = evento.target
  if (!formulario.checkValidity()) {
    formulario.reportValidity()
    return
  }

  emit(
    'salvar',
    {
      recipient: form.recipient.trim(),
      cep: form.cep,
      street: form.street.trim(),
      number: form.number.trim(),
      complement: form.complement.trim(),
      district: form.district.trim(),
      city: form.city.trim(),
      state: form.state.trim().toUpperCase(),
    },
    props.endereco ? props.endereco.id : null
  )
}
</script>

<template>
  <Teleport to="body">
    <template v-if="aberto">
      <div
        class="modal fade show d-block"
        tabindex="-1"
        role="dialog"
        aria-modal="true"
        aria-labelledby="tituloModalEndereco"
        @click.self="$emit('fechar')"
      >
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
          <form class="modal-content" novalidate @submit.prevent="enviar">
            <div class="modal-header">
              <h2 class="modal-title h5 fw-bold" id="tituloModalEndereco">{{ endereco ? 'Editar endereço' : 'Novo endereço' }}</h2>
              <button type="button" class="btn-close" aria-label="Fechar" @click="$emit('fechar')"></button>
            </div>
            <div class="modal-body">
              <div class="row g-3">
                <div class="col-12">
                  <label for="enderecoDestinatario" class="form-label">Destinatário</label>
                  <input ref="campoDestinatario" v-model="form.recipient" type="text" class="form-control" id="enderecoDestinatario" name="recipient" required>
                </div>
                <div class="col-md-4">
                  <label for="enderecoCep" class="form-label">CEP</label>
                  <input :value="form.cep" type="text" class="form-control" id="enderecoCep" name="cep" inputmode="numeric" maxlength="9" placeholder="00000-000" required @input="form.cep = mascararCep($event)">
                </div>
                <div class="col-md-8">
                  <label for="enderecoRua" class="form-label">Rua</label>
                  <input v-model="form.street" type="text" class="form-control" id="enderecoRua" name="street" required>
                </div>
                <div class="col-md-4">
                  <label for="enderecoNumero" class="form-label">Número</label>
                  <input v-model="form.number" type="text" class="form-control" id="enderecoNumero" name="number" required>
                </div>
                <div class="col-md-8">
                  <label for="enderecoComplemento" class="form-label">Complemento <span class="text-secondary">(opcional)</span></label>
                  <input v-model="form.complement" type="text" class="form-control" id="enderecoComplemento" name="complement">
                </div>
                <div class="col-md-5">
                  <label for="enderecoBairro" class="form-label">Bairro</label>
                  <input v-model="form.district" type="text" class="form-control" id="enderecoBairro" name="district" required>
                </div>
                <div class="col-md-5">
                  <label for="enderecoCidade" class="form-label">Cidade</label>
                  <input v-model="form.city" type="text" class="form-control" id="enderecoCidade" name="city" required>
                </div>
                <div class="col-md-2">
                  <label for="enderecoUf" class="form-label">UF</label>
                  <input v-model="form.state" type="text" class="form-control text-uppercase" id="enderecoUf" name="state" maxlength="2" minlength="2" required>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" @click="$emit('fechar')">Cancelar</button>
              <button type="submit" class="btn btn-ew px-4">Salvar endereço</button>
            </div>
          </form>
        </div>
      </div>
      <div class="modal-backdrop fade show"></div>
    </template>
  </Teleport>
</template>
