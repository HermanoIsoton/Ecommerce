<script setup>
import { computed, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { buscarProduto } from '@/services/produtos'
import { produtos } from '@/stores/catalogo'
import {
  adicionarAoCarrinho,
  erroCarrinho,
  sincronizandoCarrinho,
} from '@/stores/carrinho'
import { avisar } from '@/stores/toast'
import { formatarMoeda } from '@/utils/formatadores'
import { ROTULO_TIPO } from '@/utils/rotulos'

const route = useRoute()
const produto = ref(null)
const carregando = ref(true)
const erro = ref('')
const semEstoque = computed(() =>
  !produto.value?.is_active || Number(produto.value?.stock ?? 0) <= 0
)

async function carregarProduto() {
  const id = String(route.params.id)
  produto.value = null
  erro.value = ''
  carregando.value = true

  try {
    const produtoDoCatalogo = produtos.value.find(
      (item) => String(item.id) === id
    )
    const resultado = produtoDoCatalogo ?? await buscarProduto(id)
    if (String(route.params.id) === id) produto.value = resultado
  } catch (falha) {
    if (String(route.params.id) === id) erro.value = falha.message
  } finally {
    if (String(route.params.id) === id) carregando.value = false
  }
}

async function adicionar() {
  if (!produto.value || semEstoque.value) return

  if (await adicionarAoCarrinho(produto.value)) {
    avisar(`${produto.value.name} foi adicionado ao carrinho.`)
  } else {
    avisar(
      erroCarrinho.value || `Não foi possível adicionar ${produto.value.name} ao carrinho.`,
      { tipo: 'erro' }
    )
  }
}

watch(() => route.params.id, carregarProduto, { immediate: true })
</script>

<template>
  <main class="flex-grow-1 py-4 py-lg-5">
    <div class="container">
      <RouterLink :to="{ name: 'home', hash: '#catalogo' }" class="btn btn-link px-0 mb-3">
        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Continuar comprando
      </RouterLink>

      <p v-if="carregando" class="text-secondary py-5 text-center" role="status">
        Carregando produto...
      </p>

      <div v-else-if="erro" class="alert alert-danger" role="alert">
        {{ erro }}
        <RouterLink :to="{ name: 'home', hash: '#catalogo' }" class="alert-link ms-2">
          Voltar ao catálogo
        </RouterLink>
      </div>

      <div v-else-if="produto" class="card card-ew produto-detalhe">
        <div class="row g-0">
          <div class="col-lg-6">
            <div class="produto-detalhe-imagem d-flex align-items-center justify-content-center">
              <img v-if="produto.image_url" class="d-block w-100 h-100 object-fit-contain" :src="produto.image_url" :alt="produto.name">
              <i v-else class="bi bi-lightning-charge-fill" aria-hidden="true"></i>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="card-body p-4 p-xl-5">
              <span class="badge mb-2" :class="produto.type === 'ciclomotor' ? 'text-bg-warning' : 'text-bg-success'">
                {{ ROTULO_TIPO[produto.type] || produto.type }}
              </span>
              <h1 class="h2 fw-bold mb-2">{{ produto.name }}</h1>
              <p class="text-secondary mb-4">{{ produto.description }}</p>
              <p class="preco fs-3 mb-1">{{ formatarMoeda(produto.price) }}</p>
              <p class="small mb-4" :class="semEstoque ? 'text-danger' : 'text-secondary'">
                {{ semEstoque ? 'Indisponível no momento' : `${produto.stock} em estoque` }}
              </p>

              <button
                type="button"
                class="btn btn-ew-accent btn-lg w-100 mb-3"
                :disabled="semEstoque || sincronizandoCarrinho"
                @click="adicionar"
              >
                <i class="bi bi-cart-plus me-1" aria-hidden="true"></i>
                {{ sincronizandoCarrinho ? 'Adicionando...' : 'Adicionar ao carrinho' }}
              </button>
              <section class="produto-detalhe-tecnico border-top pt-3" aria-labelledby="tituloEspecificacoes">
                <h2 class="h6 fw-bold mb-2" id="tituloEspecificacoes">Detalhes técnicos</h2>
                <dl class="produto-especificacoes row row-cols-2 g-2 mb-0">
                  <div class="col bg-body-tertiary p-2 rounded-2"><dt class="small fw-medium text-secondary">Potência</dt><dd class="small fw-bold mb-0">{{ produto.power }} W</dd></div>
                  <div class="col bg-body-tertiary p-2 rounded-2"><dt class="small fw-medium text-secondary">Autonomia</dt><dd class="small fw-bold mb-0">{{ produto.range_km }} km</dd></div>
                  <div class="col bg-body-tertiary p-2 rounded-2"><dt class="small fw-medium text-secondary">Velocidade máxima</dt><dd class="small fw-bold mb-0">{{ produto.top_speed_kmh }} km/h</dd></div>
                  <div class="col bg-body-tertiary p-2 rounded-2"><dt class="small fw-medium text-secondary">Peso</dt><dd class="small fw-bold mb-0">{{ produto.weight_kg }} kg</dd></div>
                  <div class="col bg-body-tertiary p-2 rounded-2"><dt class="small fw-medium text-secondary">Capacidade da bateria</dt><dd class="small fw-bold mb-0">{{ produto.battery_capaciy }} Wh</dd></div>
                  <div class="col bg-body-tertiary p-2 rounded-2"><dt class="small fw-medium text-secondary">Tensão da bateria</dt><dd class="small fw-bold mb-0">{{ produto.battery_voltage_v }} V</dd></div>
                  <div class="col bg-body-tertiary p-2 rounded-2"><dt class="small fw-medium text-secondary">Bateria removível</dt><dd class="small fw-bold mb-0">{{ produto.is_battery_removable ? 'Sim' : 'Não' }}</dd></div>
                  <div class="col bg-body-tertiary p-2 rounded-2"><dt class="small fw-medium text-secondary">Tempo de recarga</dt><dd class="small fw-bold mb-0">{{ produto.charge_time_h }} h</dd></div>
                  <div class="col bg-body-tertiary p-2 rounded-2"><dt class="small fw-medium text-secondary">Potência máxima de recarga</dt><dd class="small fw-bold mb-0">{{ produto.max_charge_power_kw === null ? '—' : `${produto.max_charge_power_kw} kW` }}</dd></div>
                  <div class="col bg-body-tertiary p-2 rounded-2"><dt class="small fw-medium text-secondary">Garantia</dt><dd class="small fw-bold mb-0">{{ produto.warranty_months ? `${produto.warranty_months} meses` : '—' }}</dd></div>
                </dl>
              </section>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>
