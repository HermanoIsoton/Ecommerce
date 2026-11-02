// Estado do catalogo (filtro por tipo, busca do menu e filtros laterais).
// Fica fora do componente porque o menu e a pagina inicial usam os mesmos valores.
import { computed, reactive, ref } from 'vue'
import { produtosDemo } from '@/data/produtos'

export const filtroTipo = ref('todos') // 'todos' | 'autopropelido' | 'ciclomotor'
export const termoBusca = ref('')

// Um campo para cada coluna filtravel da tabela `products`.
// Faixas: { min, max }. Campos vazios ('') nao filtram nada.
export function criarFiltros() {
  return {
    faixas: {
      price: { min: '', max: '' },
      model_year: { min: '' },
      seating_capacity: { min: '' },
      doors_count: { min: '' },
      trunk_capacity_l: { min: '' },
      power: { min: '', max: '' },
      range_km: { min: '' },
      top_speed_kmh: { min: '', max: '' },
      weight_kg: { min: '', max: '' },
      battery_capaciy: { min: '', max: '' },
      charge_time_h: { max: '' },
      max_charge_power_kw: { min: '' },
      warranty_months: { min: '' },
    },
    listas: {
      category_id: [],
      battery_voltage_v: [],
    },
    is_battery_removable: '', // '' (tanto faz) | '1' (sim) | '0' (nao)
    somenteEmEstoque: false,
  }
}

export const filtros = reactive(criarFiltros())

export function limparFiltros() {
  Object.assign(filtros, criarFiltros())
}

// Opcoes que dependem dos dados: categorias, tensoes e anos
export const categoriasDisponiveis = computed(() => {
  const categorias = new Map()
  produtosDemo.forEach((produto) => categorias.set(produto.category.id, produto.category.name))
  return [...categorias].map(([id, nome]) => ({ id, nome }))
})

export const tensoesDisponiveis = computed(() => {
  const tensoes = new Set(produtosDemo.map((produto) => Number(produto.battery_voltage_v)))
  return [...tensoes].sort((a, b) => a - b)
})

export const anosDisponiveis = computed(() => {
  const anos = new Set(produtosDemo.filter((produto) => produto.model_year).map((produto) => produto.model_year))
  return [...anos].sort((a, b) => b - a)
})

function valorDoCampo(produto, campo) {
  return campo === 'category_id' ? produto.category.id : produto[campo]
}

function produtoPassaNosFiltros(produto) {
  const respeitaFaixas = Object.entries(filtros.faixas).every(([campo, limites]) => {
    return Object.entries(limites).every(([lado, texto]) => {
      if (texto === '' || texto === null) {
        return true
      }
      const valor = valorDoCampo(produto, campo)
      if (valor === null || valor === undefined) {
        return false
      }
      return lado === 'min' ? Number(valor) >= Number(texto) : Number(valor) <= Number(texto)
    })
  })

  const respeitaListas = Object.entries(filtros.listas).every(([campo, valores]) => {
    return valores.length === 0 || valores.map(String).includes(String(valorDoCampo(produto, campo)))
  })

  const respeitaBateria =
    filtros.is_battery_removable === '' ||
    Boolean(produto.is_battery_removable) === (filtros.is_battery_removable === '1')

  const respeitaEstoque = !filtros.somenteEmEstoque || Number(produto.stock) > 0

  return respeitaFaixas && respeitaListas && respeitaBateria && respeitaEstoque
}

export const produtosFiltrados = computed(() => {
  const termo = termoBusca.value.trim().toLowerCase()

  return produtosDemo.filter((produto) => {
    if (!produto.is_active) {
      return false
    }
    if (filtroTipo.value !== 'todos' && produto.type !== filtroTipo.value) {
      return false
    }
    const combinaComBusca =
      !termo || produto.name.toLowerCase().includes(termo) || produto.category.name.toLowerCase().includes(termo)
    return combinaComBusca && produtoPassaNosFiltros(produto)
  })
})

export const quantidadeFiltrosAtivos = computed(() => {
  const faixas = Object.values(filtros.faixas)
    .flatMap((limites) => Object.values(limites))
    .filter((valor) => valor !== '' && valor !== null).length
  const listas = Object.values(filtros.listas).filter((valores) => valores.length > 0).length
  const bateria = filtros.is_battery_removable !== '' ? 1 : 0
  const estoque = filtros.somenteEmEstoque ? 1 : 0
  return faixas + listas + bateria + estoque
})
