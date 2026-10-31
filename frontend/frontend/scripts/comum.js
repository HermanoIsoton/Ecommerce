// EletricWay - funcoes de apoio usadas por todas as paginas.
// Sem chamadas de API aqui: a integracao com o backend fica em frontend/api/.

const formatadorMoeda = new Intl.NumberFormat('pt-BR', {
  style: 'currency',
  currency: 'BRL',
});

function formatarMoeda(valor) {
  return formatadorMoeda.format(Number(valor));
}

function formatarData(textoData) {
  return new Date(textoData).toLocaleDateString('pt-BR');
}

function formatarDataHora(textoData) {
  return new Date(textoData).toLocaleString('pt-BR', {
    dateStyle: 'short',
    timeStyle: 'short',
  });
}

function formatarCep(cep) {
  const digitos = String(cep).replace(/\D/g, '').slice(0, 8);
  return digitos.length > 5 ? digitos.slice(0, 5) + '-' + digitos.slice(5) : digitos;
}

// Nomes legiveis para os valores do banco
const ROTULO_TIPO = {
  autopropelido: 'Autopropelido',
  ciclomotor: 'Ciclomotor',
};
