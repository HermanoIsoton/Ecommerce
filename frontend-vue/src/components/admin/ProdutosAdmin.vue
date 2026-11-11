<script setup>
import { onMounted, ref, watch } from 'vue'
import { criarProduto, listarProdutos, removerProduto, atualizarProduto } from '@/services/produtos'
import { listarCategorias } from '@/services/categorias'
import { formatarMoeda } from '@/utils/formatadores'
import { avisar } from '@/stores/toast'

const props = defineProps({ atualizacao: { type: Number, default: 0 } })
const produtos = ref([])
const categorias = ref([])
const carregando = ref(false)
const salvandoProduto = ref(false)
const produtoEmEdicao = ref(null)
const slugAutomatico = ref(true)

const novoProduto = () => ({
  category_id: '', name: '', slug: '', description: '', image_url: '', price: '', stock: '',
  is_active: true, model_year: '', seating_capacity: '', type: 'autopropelido', power: '',
  range_km: '', top_speed_kmh: '', weight_kg: '', charge_time_h: '', battery_capaciy: '',
  max_charge_power_kw: '', battery_voltage_v: '', is_battery_removable: false, warranty_months: '',
})
const produtoFormulario = ref(novoProduto())
const camposProduto = [
  { key: 'name', label: 'Nome', required: true, col: 'col-md-6' },
  { key: 'slug', label: 'Slug', required: true, col: 'col-md-6' },
  { key: 'description', label: 'Descrição', kind: 'textarea', required: true, col: 'col-12' },
  { key: 'image_url', label: 'URL da imagem', type: 'url', required: true, col: 'col-md-8' },
  { key: 'price', label: 'Preço (R$)', type: 'number', step: '0.01', min: '0', required: true },
  { key: 'stock', label: 'Estoque', type: 'number', step: '1', min: '0', required: true },
  { key: 'model_year', label: 'Ano do modelo', type: 'number', step: '1', min: '0' },
  { key: 'seating_capacity', label: 'Lugares', type: 'number', step: '1', min: '1' },
  { key: 'power', label: 'Potência', type: 'number', step: '1', min: '1', required: true },
  { key: 'range_km', label: 'Autonomia (km)', type: 'number', step: '1', min: '1', required: true },
  { key: 'top_speed_kmh', label: 'Velocidade máxima (km/h)', type: 'number', step: '1', min: '1', required: true },
  { key: 'weight_kg', label: 'Peso (kg)', type: 'number', step: '0.01', min: '0', required: true },
  { key: 'charge_time_h', label: 'Tempo de recarga (h)', type: 'number', step: '0.01', min: '0', required: true },
  { key: 'battery_capaciy', label: 'Capacidade da bateria', type: 'number', step: '0.01', min: '0', required: true },
  { key: 'max_charge_power_kw', label: 'Potência máxima de carga (kW)', type: 'number', step: '1', min: '0' },
  { key: 'battery_voltage_v', label: 'Tensão da bateria (V)', type: 'number', step: '0.1', min: '0', required: true },
  { key: 'warranty_months', label: 'Garantia (meses)', type: 'number', step: '1', min: '0' },
]

async function carregarCatalogo() {
  carregando.value = true
  try {
    const [listaProdutos, listaCategorias] = await Promise.all([listarProdutos(), listarCategorias()])
    produtos.value = listaProdutos
    categorias.value = listaCategorias
  } catch (erro) {
    avisar(erro.message, { tipo: 'erro' })
  } finally {
    carregando.value = false
  }
}

function gerarSlug(texto) {
  return texto.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '')
}
function sugerirSlugProduto() {
  if (slugAutomatico.value) produtoFormulario.value.slug = gerarSlug(produtoFormulario.value.name)
}
function aoEditarCampoProduto(campo) {
  if (campo === 'name') sugerirSlugProduto()
  else if (campo === 'slug') slugAutomatico.value = false
}
function editarProduto(produto) {
  produtoEmEdicao.value = produto.id
  slugAutomatico.value = false
  produtoFormulario.value = {
    ...novoProduto(), ...produto,
    category_id: String(produto.category_id),
    is_active: Boolean(produto.is_active),
    is_battery_removable: Boolean(produto.is_battery_removable),
  }
  window.scrollTo({ top: 0, behavior: 'smooth' })
}
function limparFormularioProduto() {
  produtoEmEdicao.value = null
  slugAutomatico.value = true
  produtoFormulario.value = novoProduto()
}
function normalizarProduto(dados) {
  const numericos = ['category_id', 'price', 'stock', 'model_year', 'seating_capacity', 'power', 'range_km', 'top_speed_kmh', 'weight_kg', 'charge_time_h', 'battery_capaciy', 'max_charge_power_kw', 'battery_voltage_v', 'warranty_months']
  const resultado = { ...dados }
  for (const campo of numericos) {
    resultado[campo] = resultado[campo] === '' || resultado[campo] === null ? null : Number(resultado[campo])
  }
  return resultado
}
async function salvarProduto() {
  salvandoProduto.value = true
  try {
    const dados = normalizarProduto(produtoFormulario.value)
    if (produtoEmEdicao.value) {
      await atualizarProduto(produtoEmEdicao.value, dados)
      avisar('Produto atualizado.')
    } else {
      await criarProduto(dados)
      avisar('Produto criado.')
    }
    limparFormularioProduto()
    await carregarCatalogo()
  } catch (erro) {
    avisar(erro.message, { tipo: 'erro' })
  } finally {
    salvandoProduto.value = false
  }
}
async function excluirProduto(produto) {
  if (!window.confirm(`Excluir o produto "${produto.name}"?`)) return
  try {
    await removerProduto(produto.id)
    avisar('Produto excluído. Os pedidos antigos continuam preservados.')
    await carregarCatalogo()
  } catch (erro) {
    avisar(erro.message, { tipo: 'erro' })
  }
}

watch(() => props.atualizacao, carregarCatalogo)
onMounted(carregarCatalogo)
</script>

<template>
  <div class="card card-ew mb-4">
    <div class="card-body p-4">
      <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
        <div>
          <h2 class="h5 fw-bold mb-1">{{ produtoEmEdicao ? 'Editar produto' : 'Novo produto' }}</h2>
          <p class="text-secondary small mb-0">Informe uma URL pública de imagem JPG, JPEG ou WebP.</p>
        </div>
        <button v-if="produtoEmEdicao" class="btn btn-sm btn-outline-secondary" type="button" @click="limparFormularioProduto">Cancelar edição</button>
      </div>
      <form class="row g-3" @submit.prevent="salvarProduto">
        <div class="col-md-6">
          <label class="form-label" for="produto-categoria">Categoria</label>
          <select id="produto-categoria" v-model="produtoFormulario.category_id" class="form-select" required>
            <option value="" disabled>Selecione uma categoria</option>
            <option v-for="categoria in categorias" :key="categoria.id" :value="String(categoria.id)">{{ categoria.name }}</option>
          </select>
        </div>
        <div class="col-md-6 d-flex align-items-end">
          <div class="form-check form-switch mb-2">
            <input id="produto-ativo" v-model="produtoFormulario.is_active" class="form-check-input" type="checkbox" role="switch">
            <label class="form-check-label" for="produto-ativo">Produto ativo no catálogo</label>
          </div>
        </div>
        <div v-for="campo in camposProduto" :key="campo.key" :class="campo.col || 'col-md-4'">
          <label class="form-label" :for="`produto-${campo.key}`">{{ campo.label }}</label>
          <textarea v-if="campo.kind === 'textarea'" :id="`produto-${campo.key}`" v-model="produtoFormulario[campo.key]" class="form-control" rows="3" :required="campo.required || false"></textarea>
          <input v-else :id="`produto-${campo.key}`" v-model="produtoFormulario[campo.key]" class="form-control" :type="campo.type || 'text'" :step="campo.step" :min="campo.min" :required="campo.required || false" @input="aoEditarCampoProduto(campo.key)">
        </div>
        <div class="col-md-4">
          <label class="form-label" for="produto-tipo">Tipo</label>
          <select id="produto-tipo" v-model="produtoFormulario.type" class="form-select" required>
            <option value="autopropelido">Autopropelido</option>
            <option value="ciclomotor">Ciclomotor</option>
          </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
          <div class="form-check mb-2">
            <input id="produto-bateria-removivel" v-model="produtoFormulario.is_battery_removable" class="form-check-input" type="checkbox">
            <label class="form-check-label" for="produto-bateria-removivel">Bateria removível</label>
          </div>
        </div>
        <div class="col-12">
          <button class="btn btn-ew" type="submit" :disabled="salvandoProduto || categorias.length === 0">
            {{ salvandoProduto ? 'Salvando...' : produtoEmEdicao ? 'Salvar produto' : 'Criar produto' }}
          </button>
        </div>
      </form>
    </div>
  </div>

  <div class="card card-ew">
    <div class="card-body p-0">
      <div class="p-4 pb-2"><h2 class="h5 fw-bold mb-0">Produtos cadastrados</h2></div>
      <div v-if="carregando" class="p-4 text-secondary">Carregando produtos...</div>
      <div v-else-if="produtos.length === 0" class="p-4 text-secondary">Nenhum produto cadastrado.</div>
      <div v-else class="table-responsive">
        <table class="table align-middle mb-0">
          <thead><tr>
            <th scope="col">Produto</th><th scope="col">Categoria</th><th scope="col">Preço</th>
            <th scope="col">Estoque</th><th scope="col">Status</th><th scope="col" class="text-end">Ações</th>
          </tr></thead>
          <tbody>
            <tr v-for="produto in produtos" :key="produto.id">
              <td><strong>{{ produto.name }}</strong><div class="small text-secondary">{{ produto.slug }}</div></td>
              <td>{{ produto.category?.name || '—' }}</td>
              <td>{{ formatarMoeda(produto.price) }}</td>
              <td>{{ produto.stock }}</td>
              <td><span class="badge" :class="produto.is_active ? 'text-bg-success' : 'text-bg-secondary'">{{ produto.is_active ? 'Ativo' : 'Inativo' }}</span></td>
              <td class="text-end text-nowrap">
                <button class="btn btn-sm btn-outline-primary me-2" type="button" @click="editarProduto(produto)">Editar / estoque</button>
                <button class="btn btn-sm btn-outline-danger" type="button" @click="excluirProduto(produto)">Excluir</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>