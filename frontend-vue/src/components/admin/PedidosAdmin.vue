<script setup>
import { onMounted, ref, watch } from 'vue'
import { atualizarStatusPedido, listarPedidos } from '@/services/pedidos'
import { formatarData, formatarMoeda } from '@/utils/formatadores'
import { STATUS_PEDIDO } from '@/utils/rotulos'
import { avisar } from '@/stores/toast'

const props = defineProps({ atualizacao: { type: Number, default: 0 } })
const pedidos = ref([])
const filtroStatus = ref('')
const pedidoAtualizando = ref(null)

async function carregarPedidos() {
  try {
    pedidos.value = await listarPedidos(filtroStatus.value)
  } catch (erro) {
    avisar(erro.message, { tipo: 'erro' })
  }
}
async function avancarPedido(pedido) {
  const status = pedido.status === 'pago' ? 'enviado' : 'entregue'
  pedidoAtualizando.value = pedido.id
  try {
    await atualizarStatusPedido(pedido.id, status)
    avisar(`Pedido #${pedido.id} atualizado para ${STATUS_PEDIDO[status].rotulo.toLowerCase()}.`)
    await carregarPedidos()
  } catch (erro) {
    avisar(erro.message, { tipo: 'erro' })
  } finally {
    pedidoAtualizando.value = null
  }
}

watch(() => props.atualizacao, carregarPedidos)
onMounted(carregarPedidos)
</script>

<template>
  <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3">
    <h2 class="h5 fw-bold mb-0">Pedidos da loja</h2>
    <div>
      <label class="form-label visually-hidden" for="filtro-status">Filtrar por status</label>
      <select id="filtro-status" v-model="filtroStatus" class="form-select" @change="carregarPedidos">
        <option value="">Todos os status</option>
        <option v-for="(status, chave) in STATUS_PEDIDO" :key="chave" :value="chave">{{ status.rotulo }}</option>
      </select>
    </div>
  </div>
  <div class="card card-ew">
    <div v-if="pedidos.length === 0" class="card-body p-4 text-center text-secondary">Nenhum pedido encontrado para este filtro.</div>
    <div v-else class="table-responsive">
      <table class="table align-middle mb-0">
        <thead><tr>
          <th scope="col">Pedido</th><th scope="col">Cliente</th><th scope="col">Data</th>
          <th scope="col">Status</th><th scope="col">Total</th><th scope="col">Itens</th><th scope="col" class="text-end">Ação</th>
        </tr></thead>
        <tbody>
          <tr v-for="pedido in pedidos" :key="pedido.id">
            <td><strong>#{{ pedido.id }}</strong></td>
            <td>{{ pedido.customer?.name || `Cliente #${pedido.user_id}` }}<div v-if="pedido.customer?.email" class="small text-secondary">{{ pedido.customer.email }}</div></td>
            <td>{{ formatarData(pedido.created_at) }}</td>
            <td><span class="badge" :class="STATUS_PEDIDO[pedido.status]?.classe">{{ STATUS_PEDIDO[pedido.status]?.rotulo || pedido.status }}</span></td>
            <td>{{ formatarMoeda(pedido.total_cents) }}</td>
            <td><details>
              <summary>{{ pedido.items?.length || 0 }} produto(s)</summary>
              <ul class="small mb-0 mt-1 ps-3"><li v-for="item in pedido.items" :key="item.id">{{ item.product_name }} × {{ item.quantity }}</li></ul>
            </details></td>
            <td class="text-end text-nowrap">
              <button v-if="pedido.status === 'pago' || pedido.status === 'enviado'" class="btn btn-sm btn-ew" type="button" :disabled="pedidoAtualizando === pedido.id" @click="avancarPedido(pedido)">
                {{ pedidoAtualizando === pedido.id ? 'Atualizando...' : pedido.status === 'pago' ? 'Marcar enviado' : 'Marcar entregue' }}
              </button>
              <span v-else class="text-secondary small">—</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>