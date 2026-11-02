<script setup>
// EletricWay - pagina da conta do usuario.
// ATENCAO: DADOS PROVISORIOS DE DEMONSTRACAO (veja src/data/conta.js).
import { ref } from 'vue'
import ContaCabecalho from '@/components/conta/ContaCabecalho.vue'
import AbaDadosPessoais from '@/components/conta/AbaDadosPessoais.vue'
import AbaEnderecos from '@/components/conta/AbaEnderecos.vue'
import AbaPedidos from '@/components/conta/AbaPedidos.vue'
import AbaSeguranca from '@/components/conta/AbaSeguranca.vue'
import { enderecosDemo, pedidosDemo, usuarioDemo } from '@/data/conta'
import { avisar } from '@/stores/toast'

const usuario = ref({ ...usuarioDemo })
const enderecos = ref(enderecosDemo.map((endereco) => ({ ...endereco })))
const pedidos = pedidosDemo

const abas = [
  { id: 'dados', rotulo: 'Dados pessoais', icone: 'bi-person' },
  { id: 'enderecos', rotulo: 'Endereços', icone: 'bi-geo-alt' },
  { id: 'pedidos', rotulo: 'Meus pedidos', icone: 'bi-bag-check' },
  { id: 'seguranca', rotulo: 'Segurança', icone: 'bi-shield-lock' },
]
const abaAtiva = ref('dados')

function salvarDados({ name, email }) {
  usuario.value.name = name
  usuario.value.email = email
  avisar('Dados atualizados (demonstração).')
}

function sair() {
  avisar('Aqui entra o logout quando a API estiver ligada.')
}

function salvarEndereco(dados, id) {
  if (id) {
    enderecos.value = enderecos.value.map((item) => (item.id === id ? { ...item, ...dados } : item))
  } else {
    const proximoId = enderecos.value.reduce((maior, item) => Math.max(maior, item.id), 0) + 1
    enderecos.value.push({ id: proximoId, user_id: usuario.value.id, ...dados })
  }
  avisar('Endereço salvo (demonstração).')
}

function removerEndereco(id) {
  enderecos.value = enderecos.value.filter((item) => item.id !== id)
  avisar('Endereço removido (demonstração).')
}
</script>

<template>
  <main class="py-4 py-lg-5">
    <div class="container">

      <!-- Cabecalho da conta (tabela users) -->
      <ContaCabecalho :usuario="usuario" @sair="sair" />

      <div class="row g-4">
        <!-- Menu lateral -->
        <div class="col-lg-3">
          <div class="card card-ew">
            <div class="card-body p-2">
              <div class="nav flex-column nav-pills conta-menu" role="tablist" aria-orientation="vertical" aria-label="Seções da conta">
                <button
                  v-for="aba in abas"
                  :key="aba.id"
                  class="nav-link"
                  :class="{ active: abaAtiva === aba.id }"
                  :id="`tab-${aba.id}`"
                  type="button"
                  role="tab"
                  :aria-controls="`aba-${aba.id}`"
                  :aria-selected="abaAtiva === aba.id"
                  @click="abaAtiva = aba.id"
                >
                  <i class="bi" :class="aba.icone"></i>{{ aba.rotulo }}
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Conteudo -->
        <div class="col-lg-9">
          <div class="tab-content">
            <div class="tab-pane fade" :class="{ show: abaAtiva === 'dados', active: abaAtiva === 'dados' }" id="aba-dados" role="tabpanel" aria-labelledby="tab-dados" tabindex="0">
              <AbaDadosPessoais :usuario="usuario" @salvar="salvarDados" />
            </div>

            <div class="tab-pane fade" :class="{ show: abaAtiva === 'enderecos', active: abaAtiva === 'enderecos' }" id="aba-enderecos" role="tabpanel" aria-labelledby="tab-enderecos" tabindex="0">
              <AbaEnderecos :enderecos="enderecos" @salvar="salvarEndereco" @remover="removerEndereco" />
            </div>

            <div class="tab-pane fade" :class="{ show: abaAtiva === 'pedidos', active: abaAtiva === 'pedidos' }" id="aba-pedidos" role="tabpanel" aria-labelledby="tab-pedidos" tabindex="0">
              <AbaPedidos :pedidos="pedidos" />
            </div>

            <div class="tab-pane fade" :class="{ show: abaAtiva === 'seguranca', active: abaAtiva === 'seguranca' }" id="aba-seguranca" role="tabpanel" aria-labelledby="tab-seguranca" tabindex="0">
              <AbaSeguranca />
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>
