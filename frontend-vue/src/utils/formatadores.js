// EletricWay - funcoes de apoio usadas por todas as paginas.
// Sem chamadas de API aqui: a integracao com o backend deve ficar em src/services/.

const formatadorMoeda = new Intl.NumberFormat('pt-BR', {
  style: 'currency',
  currency: 'BRL',
})

export function formatarMoeda(valor) {
  return formatadorMoeda.format(Number(valor))
}

export function formatarData(textoData) {
  return new Date(textoData).toLocaleDateString('pt-BR')
}

export function formatarDataHora(textoData) {
  return new Date(textoData).toLocaleString('pt-BR', {
    dateStyle: 'short',
    timeStyle: 'short',
  })
}

export function formatarCep(cep) {
  const digitos = String(cep).replace(/\D/g, '').slice(0, 8)
  return digitos.length > 5 ? digitos.slice(0, 5) + '-' + digitos.slice(5) : digitos
}

// Usado em @input de campos de CEP: formata o texto digitado e devolve o valor formatado.
export function mascararCep(evento) {
  const valor = formatarCep(evento.target.value)
  evento.target.value = valor
  return valor
}
