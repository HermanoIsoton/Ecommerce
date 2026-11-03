import { computed, ref } from 'vue'
import { listarProdutos } from '@/services/produtos'
import { listarCategorias } from '@/services/categorias'

//requisiçoes http produtos
export const filtroTipo = ref('todos') 
export const termoBusca = ref('')
export const categoriaSelecionada = ref('') // vazio = todas
export const produtos = ref([])
export const erroCatalogo = ref('')

export async function carregarProdutos() {

  erroCatalogo.value = ''
  try {
    produtos.value = await listarProdutos()
  } catch (erro) {
    erroCatalogo.value = erro.message
  } 
}
// requisição http categorias
export const categorias = ref([])

export async function carregarCategorias() {
  erroCatalogo.value = ''
  try {
    categorias.value = await listarCategorias()
  } catch (erro) {
    erroCatalogo.value = erro.message
  }
}


export const categoriasDisponiveis = computed(() => {
  return categorias.value.map((categoria) => ({
    id: categoria.id,
    nome: categoria.name,
  }))
})

export const produtosFiltrados = computed(() => {
  const termo = termoBusca.value.trim().toLowerCase()

  return produtos.value.filter((produto) => {
    // Garante que só pega produtos ativos
    if (!produto.is_active) {
      return false
    }

    // Filtro por tipo (todos, autopropelido, ciclomotor)
    if (filtroTipo.value !== 'todos' && produto.type !== filtroTipo.value) {
      return false
    }

    // Filtro simples por categoria (se houver alguma selecionada)
    if (categoriaSelecionada.value !== '' && String(produto.category?.id) !== String(categoriaSelecionada.value)) {
      return false
    }

    // Filtro por termo de busca (nome do produto ou categoria)
    const combinaComBusca =
      !termo ||
      produto.name.toLowerCase().includes(termo) ||
      produto.category?.name.toLowerCase().includes(termo)

    return combinaComBusca
  })
})

export function limparFiltros() {
  filtroTipo.value = 'todos'
  termoBusca.value = ''
  categoriaSelecionada.value = ''
}