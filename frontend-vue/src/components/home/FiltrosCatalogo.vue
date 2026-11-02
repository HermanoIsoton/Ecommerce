<script setup>
// Filtros laterais do catalogo (um para cada campo da tabela products).
// O estado dos filtros fica em stores/catalogo.js.
import { reactive, ref } from 'vue'
import {
  anosDisponiveis,
  categoriasDisponiveis,
  filtros,
  limparFiltros,
  quantidadeFiltrosAtivos,
  tensoesDisponiveis,
} from '@/stores/catalogo'

// No celular o painel inteiro abre e fecha
const painelAberto = ref(false)

// Secoes do acordeao
const secoes = reactive({
  preco: true,
  veiculo: true,
  desempenho: false,
  bateria: false,
  garantia: false,
})

function alternar(secao) {
  secoes[secao] = !secoes[secao]
}
</script>

<template>
  <aside class="col-lg-3" aria-label="Filtros do catálogo">
    <button
      class="btn btn-outline-ew w-100 d-lg-none mb-3"
      type="button"
      :aria-expanded="painelAberto"
      aria-controls="painelFiltros"
      @click="painelAberto = !painelAberto"
    >
      <i class="bi bi-funnel me-1" aria-hidden="true"></i>Filtros
      <span v-if="quantidadeFiltrosAtivos > 0" class="badge text-bg-success">{{ quantidadeFiltrosAtivos }}</span>
    </button>

    <div class="collapse d-lg-block" :class="{ show: painelAberto }" id="painelFiltros">
      <form class="card card-ew filtros-painel" novalidate @submit.prevent>
        <div class="card-body p-3">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <h3 class="h6 fw-bold mb-0">
              <i class="bi bi-funnel me-1" aria-hidden="true"></i>Filtros
              <span v-if="quantidadeFiltrosAtivos > 0" class="badge text-bg-success">{{ quantidadeFiltrosAtivos }}</span>
            </h3>
            <button type="button" class="btn btn-link btn-sm p-0" @click="limparFiltros">Limpar</button>
          </div>

          <div class="accordion accordion-flush">

            <!-- Preco -->
            <div class="accordion-item">
              <h4 class="accordion-header">
                <button class="accordion-button px-1" :class="{ collapsed: !secoes.preco }" type="button" :aria-expanded="secoes.preco" aria-controls="ac-preco" @click="alternar('preco')">Preço</button>
              </h4>
              <div id="ac-preco" class="accordion-collapse collapse" :class="{ show: secoes.preco }">
                <div class="accordion-body px-1">
                  <label class="form-label small" for="f-price-min">Faixa de preço (R$)</label>
                  <div class="input-group input-group-sm">
                    <input v-model="filtros.faixas.price.min" type="number" min="0" step="any" class="form-control" id="f-price-min" placeholder="Mín." aria-label="Preço mínimo">
                    <span class="input-group-text">a</span>
                    <input v-model="filtros.faixas.price.max" type="number" min="0" step="any" class="form-control" placeholder="Máx." aria-label="Preço máximo">
                  </div>
                </div>
              </div>
            </div>

            <!-- Veiculo -->
            <div class="accordion-item">
              <h4 class="accordion-header">
                <button class="accordion-button px-1" :class="{ collapsed: !secoes.veiculo }" type="button" :aria-expanded="secoes.veiculo" aria-controls="ac-veiculo" @click="alternar('veiculo')">Veículo</button>
              </h4>
              <div id="ac-veiculo" class="accordion-collapse collapse" :class="{ show: secoes.veiculo }">
                <div class="accordion-body px-1">
                  <fieldset class="mb-3">
                    <legend class="form-label small">Categoria</legend>
                    <div v-for="categoria in categoriasDisponiveis" :key="categoria.id" class="form-check">
                      <input v-model="filtros.listas.category_id" class="form-check-input" type="checkbox" :value="categoria.id" :id="`f-categoria-${categoria.id}`">
                      <label class="form-check-label small" :for="`f-categoria-${categoria.id}`">{{ categoria.nome }}</label>
                    </div>
                  </fieldset>
                  <div class="mb-3">
                    <label class="form-label small" for="f-model_year">Ano do modelo (a partir de)</label>
                    <select v-model="filtros.faixas.model_year.min" class="form-select form-select-sm" id="f-model_year">
                      <option value="">Qualquer</option>
                      <option v-for="ano in anosDisponiveis" :key="ano" :value="ano">{{ ano }}</option>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label class="form-label small" for="f-seating_capacity">Lugares (mínimo)</label>
                    <select v-model="filtros.faixas.seating_capacity.min" class="form-select form-select-sm" id="f-seating_capacity">
                      <option value="">Qualquer</option>
                      <option :value="1">1</option>
                      <option :value="2">2</option>
                      <option :value="3">3</option>
                      <option :value="4">4</option>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label class="form-label small" for="f-doors_count">Portas (mínimo)</label>
                    <select v-model="filtros.faixas.doors_count.min" class="form-select form-select-sm" id="f-doors_count">
                      <option value="">Qualquer</option>
                      <option :value="2">2</option>
                      <option :value="3">3</option>
                      <option :value="4">4</option>
                      <option :value="5">5</option>
                    </select>
                  </div>
                  <div>
                    <label class="form-label small" for="f-trunk_capacity_l">Porta-malas (mínimo)</label>
                    <div class="input-group input-group-sm">
                      <input v-model="filtros.faixas.trunk_capacity_l.min" type="number" min="0" class="form-control" id="f-trunk_capacity_l" placeholder="0">
                      <span class="input-group-text">L</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Desempenho -->
            <div class="accordion-item">
              <h4 class="accordion-header">
                <button class="accordion-button px-1" :class="{ collapsed: !secoes.desempenho }" type="button" :aria-expanded="secoes.desempenho" aria-controls="ac-desempenho" @click="alternar('desempenho')">Desempenho</button>
              </h4>
              <div id="ac-desempenho" class="accordion-collapse collapse" :class="{ show: secoes.desempenho }">
                <div class="accordion-body px-1">
                  <div class="mb-3">
                    <label class="form-label small" for="f-power-min">Potência (W)</label>
                    <div class="input-group input-group-sm">
                      <input v-model="filtros.faixas.power.min" type="number" min="0" class="form-control" id="f-power-min" placeholder="Mín." aria-label="Potência mínima">
                      <span class="input-group-text">a</span>
                      <input v-model="filtros.faixas.power.max" type="number" min="0" class="form-control" placeholder="Máx." aria-label="Potência máxima">
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label small" for="f-range_km">Autonomia (mínima)</label>
                    <div class="input-group input-group-sm">
                      <input v-model="filtros.faixas.range_km.min" type="number" min="0" class="form-control" id="f-range_km" placeholder="0">
                      <span class="input-group-text">km</span>
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label small" for="f-top_speed_kmh-min">Velocidade máxima (km/h)</label>
                    <div class="input-group input-group-sm">
                      <input v-model="filtros.faixas.top_speed_kmh.min" type="number" min="0" class="form-control" id="f-top_speed_kmh-min" placeholder="Mín." aria-label="Velocidade mínima">
                      <span class="input-group-text">a</span>
                      <input v-model="filtros.faixas.top_speed_kmh.max" type="number" min="0" class="form-control" placeholder="Máx." aria-label="Velocidade máxima">
                    </div>
                  </div>
                  <div>
                    <label class="form-label small" for="f-weight_kg-min">Peso (kg)</label>
                    <div class="input-group input-group-sm">
                      <input v-model="filtros.faixas.weight_kg.min" type="number" min="0" step="any" class="form-control" id="f-weight_kg-min" placeholder="Mín." aria-label="Peso mínimo">
                      <span class="input-group-text">a</span>
                      <input v-model="filtros.faixas.weight_kg.max" type="number" min="0" step="any" class="form-control" placeholder="Máx." aria-label="Peso máximo">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Bateria -->
            <div class="accordion-item">
              <h4 class="accordion-header">
                <button class="accordion-button px-1" :class="{ collapsed: !secoes.bateria }" type="button" :aria-expanded="secoes.bateria" aria-controls="ac-bateria" @click="alternar('bateria')">Bateria</button>
              </h4>
              <div id="ac-bateria" class="accordion-collapse collapse" :class="{ show: secoes.bateria }">
                <div class="accordion-body px-1">
                  <div class="mb-3">
                    <label class="form-label small" for="f-battery_capaciy-min">Capacidade (Wh)</label>
                    <div class="input-group input-group-sm">
                      <input v-model="filtros.faixas.battery_capaciy.min" type="number" min="0" step="any" class="form-control" id="f-battery_capaciy-min" placeholder="Mín." aria-label="Capacidade mínima">
                      <span class="input-group-text">a</span>
                      <input v-model="filtros.faixas.battery_capaciy.max" type="number" min="0" step="any" class="form-control" placeholder="Máx." aria-label="Capacidade máxima">
                    </div>
                  </div>
                  <fieldset class="mb-3">
                    <legend class="form-label small">Tensão</legend>
                    <div v-for="tensao in tensoesDisponiveis" :key="tensao" class="form-check">
                      <input v-model="filtros.listas.battery_voltage_v" class="form-check-input" type="checkbox" :value="tensao" :id="`f-tensao-${tensao}`">
                      <label class="form-check-label small" :for="`f-tensao-${tensao}`">{{ tensao }} V</label>
                    </div>
                  </fieldset>
                  <div class="mb-3">
                    <label class="form-label small" for="f-charge_time_h">Tempo de carga (máximo)</label>
                    <div class="input-group input-group-sm">
                      <input v-model="filtros.faixas.charge_time_h.max" type="number" min="0" step="any" class="form-control" id="f-charge_time_h" placeholder="Sem limite">
                      <span class="input-group-text">h</span>
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label small" for="f-max_charge_power_kw">Potência de recarga (mínima)</label>
                    <div class="input-group input-group-sm">
                      <input v-model="filtros.faixas.max_charge_power_kw.min" type="number" min="0" class="form-control" id="f-max_charge_power_kw" placeholder="0">
                      <span class="input-group-text">kW</span>
                    </div>
                  </div>
                  <div>
                    <label class="form-label small" for="f-is_battery_removable">Bateria removível</label>
                    <select v-model="filtros.is_battery_removable" class="form-select form-select-sm" id="f-is_battery_removable">
                      <option value="">Tanto faz</option>
                      <option value="1">Sim</option>
                      <option value="0">Não</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <!-- Garantia e estoque -->
            <div class="accordion-item">
              <h4 class="accordion-header">
                <button class="accordion-button px-1" :class="{ collapsed: !secoes.garantia }" type="button" :aria-expanded="secoes.garantia" aria-controls="ac-garantia" @click="alternar('garantia')">Garantia e estoque</button>
              </h4>
              <div id="ac-garantia" class="accordion-collapse collapse" :class="{ show: secoes.garantia }">
                <div class="accordion-body px-1">
                  <div class="mb-3">
                    <label class="form-label small" for="f-warranty_months">Garantia (mínima)</label>
                    <select v-model="filtros.faixas.warranty_months.min" class="form-select form-select-sm" id="f-warranty_months">
                      <option value="">Qualquer</option>
                      <option :value="12">12 meses</option>
                      <option :value="24">24 meses</option>
                      <option :value="36">36 meses</option>
                    </select>
                  </div>
                  <div class="form-check form-switch">
                    <input v-model="filtros.somenteEmEstoque" class="form-check-input" type="checkbox" role="switch" id="filtroEstoque">
                    <label class="form-check-label small" for="filtroEstoque">Mostrar só o que tem em estoque</label>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </form>
    </div>
  </aside>
</template>
