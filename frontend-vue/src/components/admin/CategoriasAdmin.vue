<script setup>
import { onMounted, ref, watch } from 'vue'
import { criarCategorias, listarCategorias, removerCategorias, atualizarCategorias } from '@/services/categorias'
import { avisar } from '@/stores/toast'

const props = defineProps({ atualizacao: { type: Number, default: 0 } })
const emit = defineEmits(['alterado'])
const categorias = ref([])
const salvandoCategoria = ref(false)
const categoriaEmEdicao = ref(null)
const categoriaFormulario = ref({ name: '', slug: '' })

async function carregarCategorias() {
  try {
    categorias.value = await listarCategorias()
  } catch (erro) {
    avisar(erro.message, { tipo: 'erro' })
  }
}
function gerarSlug(texto) {
  return texto.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '')
}
function editarCategoria(categoria) {
  categoriaEmEdicao.value = categoria.id
  categoriaFormulario.value = { name: categoria.name, slug: categoria.slug }
  window.scrollTo({ top: 0, behavior: 'smooth' })
}
function sugerirSlugCategoria() {
  if (!categoriaEmEdicao.value) categoriaFormulario.value.slug = gerarSlug(categoriaFormulario.value.name)
}
function limparFormularioCategoria() {
  categoriaEmEdicao.value = null
  categoriaFormulario.value = { name: '', slug: '' }
}
async function salvarCategoria() {
  salvandoCategoria.value = true
  try {
    if (categoriaEmEdicao.value) {
      await atualizarCategorias(categoriaEmEdicao.value, categoriaFormulario.value)
      avisar('Categoria atualizada.')
    } else {
      await criarCategorias(categoriaFormulario.value)
      avisar('Categoria criada.')
    }
    limparFormularioCategoria()
    emit('alterado')
  } catch (erro) {
    avisar(erro.message, { tipo: 'erro' })
  } finally {
    salvandoCategoria.value = false
  }
}
async function excluirCategoria(categoria) {
  if (!window.confirm(`Excluir a categoria "${categoria.name}"?`)) return
  try {
    await removerCategorias(categoria.id)
    avisar('Categoria excluída.')
    emit('alterado')
  } catch (erro) {
    avisar(erro.message, { tipo: 'erro' })
  }
}

watch(() => props.atualizacao, carregarCategorias)
onMounted(carregarCategorias)
</script>

<template>
  <div class="card card-ew mb-4">
    <div class="card-body p-4">
      <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
        <h2 class="h5 fw-bold">{{ categoriaEmEdicao ? 'Editar categoria' : 'Nova categoria' }}</h2>
        <button v-if="categoriaEmEdicao" class="btn btn-sm btn-outline-secondary" type="button" @click="limparFormularioCategoria">Cancelar edição</button>
      </div>
      <form class="row g-3" @submit.prevent="salvarCategoria">
        <div class="col-md-5">
          <label class="form-label" for="categoria-nome">Nome</label>
          <input id="categoria-nome" v-model="categoriaFormulario.name" class="form-control" required @input="sugerirSlugCategoria">
        </div>
        <div class="col-md-5">
          <label class="form-label" for="categoria-slug">Slug</label>
          <input id="categoria-slug" v-model="categoriaFormulario.slug" class="form-control" required>
        </div>
        <div class="col-md-2 d-flex align-items-end">
          <button class="btn btn-ew w-100" type="submit" :disabled="salvandoCategoria">{{ salvandoCategoria ? 'Salvando...' : categoriaEmEdicao ? 'Salvar' : 'Criar' }}</button>
        </div>
      </form>
    </div>
  </div>
  <div class="card card-ew">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead><tr><th scope="col" class="ps-4">Categoria</th><th scope="col">Slug</th><th scope="col" class="text-end pe-4">Ações</th></tr></thead>
          <tbody>
            <tr v-for="categoria in categorias" :key="categoria.id">
              <td class="ps-4">{{ categoria.name }}</td><td>{{ categoria.slug }}</td>
              <td class="text-end pe-4 text-nowrap">
                <button class="btn btn-sm btn-outline-primary me-2" type="button" @click="editarCategoria(categoria)">Editar</button>
                <button class="btn btn-sm btn-outline-danger" type="button" @click="excluirCategoria(categoria)">Excluir</button>
              </td>
            </tr>
            <tr v-if="categorias.length === 0"><td colspan="3" class="p-4 text-center text-secondary">Nenhuma categoria cadastrada.</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>