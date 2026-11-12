<script setup>
import { ref } from 'vue'
import { formatarData, formatarDataHora, formatarMoeda } from '@/utils/formatadores'
import { STATUS_PEDIDO } from '@/utils/rotulos'
import { cancelarPedido } from '@/services/pedidos'
import { avisar } from '@/stores/toast'

const props = defineProps({
  pedidos: { type: Array, required: true },
})
const emit = defineEmits(['pedido-atualizado'])
const pedidoCancelando = ref(null)

// Acordeao: so um pedido aberto por vez (o primeiro comeca aberto)
const pedidoAberto = ref(props.pedidos[0]?.id ?? null)

function alternar(id) {
  pedidoAberto.value = pedidoAberto.value === id ? null : id
}

function linhaTempo(pedido) {
  return [
    { rotulo: 'Pedido criado', data: pedido.created_at },
    { rotulo: 'Pagamento confirmado', data: pedido.paid_at },
    { rotulo: 'Enviado', data: pedido.shipped_at },
    { rotulo: 'Entregue', data: pedido.delivered_at },
    { rotulo: 'Cancelado', data: pedido.cancelled_at },
  ].filter((evento) => evento.data)
}

async function cancelar(pedido) {
  if (!window.confirm(`Cancelar o pedido #${pedido.id}? O estoque será liberado.`)) return

  pedidoCancelando.value = pedido.id
  try {
    const pedidoAtualizado = await cancelarPedido(pedido.id)
    emit('pedido-atualizado', pedidoAtualizado)
    avisar(`Pedido #${pedido.id} cancelado com sucesso.`)
  } catch (erro) {
    avisar(erro.message, { tipo: 'erro' })
  } finally {
    pedidoCancelando.value = null
  }
}
</script>

<template>
  <div class="card card-ew">
    <div class="card-body p-4">
      <h2 class="h5 fw-bold mb-4">Meus pedidos</h2>

      <div class="accordion">
        <div v-for="pedido in pedidos" :key="pedido.id" class="accordion-item pedido-item">
          <h3 class="accordion-header">
            <button
              class="accordion-button"
              :class="{ collapsed: pedidoAberto !== pedido.id }"
              type="button"
              :aria-expanded="pedidoAberto === pedido.id"
              :aria-controls="`pedido-${pedido.id}`"
              @click="alternar(pedido.id)"
            >
              <span class="d-flex flex-wrap align-items-center gap-2 gap-md-4 w-100 me-3">
                <strong>Pedido #{{ pedido.id }}</strong>
                <span class="text-secondary small">{{ formatarData(pedido.created_at) }}</span>
                <span class="badge" :class="STATUS_PEDIDO[pedido.status].classe">{{ STATUS_PEDIDO[pedido.status].rotulo }}</span>
                <strong class="ms-md-auto">{{ formatarMoeda(pedido.total_cents) }}</strong>
              </span>
            </button>
          </h3>
          <div :id="`pedido-${pedido.id}`" class="accordion-collapse collapse" :class="{ show: pedidoAberto === pedido.id }">
            <div class="accordion-body">
              <div class="table-responsive">
                <table class="table table-sm align-middle">
                  <thead>
                    <tr>
                      <th scope="col">Produto</th>
                      <th scope="col" class="text-end">Preço</th>
                      <th scope="col" class="text-center">Qtd.</th>
                      <th scope="col" class="text-end">Subtotal</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="item in pedido.items" :key="item.id">
                      <td>{{ item.product_name }}</td>
                      <td class="text-end">{{ formatarMoeda(item.unit_price) }}</td>
                      <td class="text-center">{{ item.quantity }}</td>
                      <td class="text-end">{{ formatarMoeda(item.subtotal_cents) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div v-if="pedido.status === 'pendente'" class="alert alert-warning d-flex flex-wrap justify-content-between align-items-center gap-2">
                <span>
                  Aguardando pagamento
                  <template v-if="pedido.payment_expires_at">
                    até {{ formatarDataHora(pedido.payment_expires_at) }}.
                  </template>
                </span>
                <span class="d-flex flex-wrap gap-2">
                  <RouterLink :to="{ name: 'pagamento', params: { id: pedido.id } }" class="btn btn-sm btn-ew">
                    Abrir pagamento
                  </RouterLink>
                  <button
                    class="btn btn-sm btn-outline-danger"
                    type="button"
                    :disabled="pedidoCancelando === pedido.id"
                    @click="cancelar(pedido)"
                  >
                    {{ pedidoCancelando === pedido.id ? 'Cancelando...' : 'Cancelar pedido' }}
                  </button>
                </span>
              </div>
              <div class="row g-4 mt-1">
                <div class="col-md-4">
                  <h4 class="h6 fw-bold">Resumo</h4>
                  <dl class="row small mb-0">
                    <dt class="col-6 fw-normal text-secondary">Subtotal</dt><dd class="col-6 text-end mb-1">{{ formatarMoeda(pedido.subtotal_cents) }}</dd>
                    <dt class="col-6 fw-normal text-secondary">Frete</dt><dd class="col-6 text-end mb-1">{{ formatarMoeda(pedido.shipping_cents) }}</dd>
                    <dt class="col-6">Total</dt><dd class="col-6 text-end fw-bold mb-0">{{ formatarMoeda(pedido.total_cents) }}</dd>
                  </dl>
                </div>
                <div class="col-md-4">
                  <h4 class="h6 fw-bold">Entrega</h4>
                  <p class="small text-secondary mb-0">
                    {{ pedido.ship_recipient }}<br>
                    {{ pedido.ship_street }}, {{ pedido.ship_number }}<template v-if="pedido.ship_complement"> - {{ pedido.ship_complement }}</template><br>
                    {{ pedido.ship_district }} · {{ pedido.ship_city }}/{{ pedido.ship_state }}<br>
                    CEP {{ pedido.ship_cep }}
                  </p>
                </div>
                <div class="col-md-4">
                  <h4 class="h6 fw-bold">Andamento</h4>
                  <ul class="linha-tempo list-unstyled p-0 mb-0 d-grid gap-2">
                    <li v-for="evento in linhaTempo(pedido)" :key="evento.rotulo" class="d-flex align-items-center gap-2 py-1 small">
                      <i class="bi bi-check-circle-fill text-success" aria-hidden="true"></i>
                      <span>{{ evento.rotulo }} · {{ formatarDataHora(evento.data) }}</span>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <p v-if="pedidos.length === 0" class="text-secondary text-center py-4 mb-0">Você ainda não fez nenhum pedido.</p>
    </div>
  </div>
</template>
