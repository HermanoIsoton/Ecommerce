// Nomes legiveis para os valores do banco

export const ROTULO_TIPO = {
  autopropelido: 'Autopropelido',
  ciclomotor: 'Ciclomotor',
}

export const ROTULO_PAPEL = { user: 'Cliente', admin: 'Administrador' }

export const STATUS_PEDIDO = {
  pendente: { rotulo: 'Pendente', classe: 'text-bg-warning' },
  pago: { rotulo: 'Pago', classe: 'text-bg-info' },
  enviado: { rotulo: 'Enviado', classe: 'text-bg-primary' },
  entregue: { rotulo: 'Entregue', classe: 'text-bg-success' },
  cancelado: { rotulo: 'Cancelado', classe: 'text-bg-danger' },
}
