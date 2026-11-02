<script setup>
import { ref } from 'vue'
import ModalEndereco from '@/components/conta/ModalEndereco.vue'

defineProps({
  enderecos: { type: Array, required: true },
})

const emit = defineEmits(['salvar', 'remover'])

const modalAberto = ref(false)
const enderecoEditado = ref(null) // null = novo endereco

function abrirModal(endereco) {
  enderecoEditado.value = endereco
  modalAberto.value = true
}

function salvar(dados, id) {
  emit('salvar', dados, id)
  modalAberto.value = false
}
</script>

<template>
  <div class="card card-ew">
    <div class="card-body p-4">
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <h2 class="h5 fw-bold mb-0">Endereços de entrega</h2>
        <button type="button" class="btn btn-ew-accent" @click="abrirModal(null)">
          <i class="bi bi-plus-lg me-1"></i>Novo endereço
        </button>
      </div>

      <div class="row g-3">
        <div v-for="endereco in enderecos" :key="endereco.id" class="col-md-6">
          <article class="endereco-card h-100 p-3 d-flex flex-column gap-2">
            <h3 class="h6 fw-bold mb-0">
              <i class="bi bi-geo-alt-fill text-success me-1" aria-hidden="true"></i>{{ endereco.recipient }}
            </h3>
            <p class="small text-secondary mb-0">
              {{ endereco.street }}, {{ endereco.number }}<template v-if="endereco.complement"> - {{ endereco.complement }}</template><br>
              {{ endereco.district }} · {{ endereco.city }}/{{ endereco.state }}<br>
              CEP {{ endereco.cep }}
            </p>
            <div class="mt-auto d-flex gap-2 pt-2">
              <button type="button" class="btn btn-sm btn-outline-ew" @click="abrirModal(endereco)">
                <i class="bi bi-pencil me-1"></i>Editar
              </button>
              <button type="button" class="btn btn-sm btn-outline-danger" @click="$emit('remover', endereco.id)">
                <i class="bi bi-trash me-1"></i>Remover
              </button>
            </div>
          </article>
        </div>
      </div>
      <p v-if="enderecos.length === 0" class="text-secondary text-center py-4 mb-0">Você ainda não cadastrou nenhum endereço.</p>
    </div>
  </div>

  <ModalEndereco
    :aberto="modalAberto"
    :endereco="enderecoEditado"
    @fechar="modalAberto = false"
    @salvar="salvar"
  />
</template>
