// ATENCAO: DADOS PROVISORIOS DE DEMONSTRACAO.
// Os dados abaixo sao ficticios e seguem os campos das tabelas
// `users`, `addresses`, `orders` e `order_items`. Troque pelas chamadas de API depois.

export const usuarioDemo = {
  id: 1,
  name: 'Maria Souza',
  email: 'maria.souza@exemplo.com',
  role: 'user', // 'user' | 'admin'
  created_at: '2026-03-14T10:20:00',
}

export const enderecosDemo = [
  {
    id: 1,
    user_id: 1,
    recipient: 'Maria Souza',
    cep: '85010-000',
    street: 'Rua das Palmeiras',
    number: '120',
    complement: 'Apto 31',
    district: 'Centro',
    city: 'Guarapuava',
    state: 'PR',
  },
  {
    id: 2,
    user_id: 1,
    recipient: 'João Souza',
    cep: '80010-010',
    street: 'Avenida Central',
    number: '845',
    complement: '',
    district: 'Batel',
    city: 'Curitiba',
    state: 'PR',
  },
]

export const pedidosDemo = [
  {
    id: 1063,
    user_id: 1,
    status: 'pendente', // 'pendente' | 'pago' | 'enviado' | 'entregue' | 'cancelado'
    subtotal_cents: 2890.0,
    shipping_cents: 89.9,
    total_cents: 2979.9,
    ship_recipient: 'Maria Souza',
    ship_cep: '85010-000',
    ship_street: 'Rua das Palmeiras',
    ship_number: '120',
    ship_complement: 'Apto 31',
    ship_district: 'Centro',
    ship_city: 'Guarapuava',
    ship_state: 'PR',
    paid_at: null,
    shipped_at: null,
    delivered_at: null,
    cancelled_at: null,
    created_at: '2026-10-28T14:05:00',
    items: [
      { id: 1, product_id: 1, product_name: 'Patinete Urban Sprint', unit_price: 2890.0, quantity: 1, subtotal_cents: 2890.0 },
    ],
  },
  {
    id: 1057,
    user_id: 1,
    status: 'enviado',
    subtotal_cents: 6490.0,
    shipping_cents: 120.0,
    total_cents: 6610.0,
    ship_recipient: 'João Souza',
    ship_cep: '80010-010',
    ship_street: 'Avenida Central',
    ship_number: '845',
    ship_complement: '',
    ship_district: 'Batel',
    ship_city: 'Curitiba',
    ship_state: 'PR',
    paid_at: '2026-10-20T09:12:00',
    shipped_at: '2026-10-22T16:40:00',
    delivered_at: null,
    cancelled_at: null,
    created_at: '2026-10-20T09:00:00',
    items: [
      { id: 2, product_id: 2, product_name: 'Bike Elétrica Trilha 500', unit_price: 6490.0, quantity: 1, subtotal_cents: 6490.0 },
    ],
  },
  {
    id: 1042,
    user_id: 1,
    status: 'entregue',
    subtotal_cents: 8780.0,
    shipping_cents: 150.0,
    total_cents: 8930.0,
    ship_recipient: 'Maria Souza',
    ship_cep: '85010-000',
    ship_street: 'Rua das Palmeiras',
    ship_number: '120',
    ship_complement: 'Apto 31',
    ship_district: 'Centro',
    ship_city: 'Guarapuava',
    ship_state: 'PR',
    paid_at: '2026-09-02T11:30:00',
    shipped_at: '2026-09-04T08:15:00',
    delivered_at: '2026-09-09T15:50:00',
    cancelled_at: null,
    created_at: '2026-09-02T11:20:00',
    items: [
      { id: 3, product_id: 5, product_name: 'Patinete Max Cruiser', unit_price: 4390.0, quantity: 2, subtotal_cents: 8780.0 },
    ],
  },
]
