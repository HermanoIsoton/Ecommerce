## Checkout e pedidos pendentes

O checkout autenticado cria o pedido a partir dos preços e quantidades atuais do carrinho, reserva o estoque, salva um retrato do endereço de entrega e esvazia o carrinho. O frete demonstrativo é fixo em R$ 29,90. Pedidos aguardando pagamento expiram em 30 minutos; a ação de pagamento do frontend é apenas uma simulação e não realiza cobranças.

Quando o prazo vence, o pedido passa para `cancelado` e as unidades reservadas são devolvidas ao estoque. A expiração é processada sob demanda: a tela de pagamento consulta novamente o pedido quando o contador chega a zero, e os endpoints de consulta e tentativa de pagamento também verificam o prazo. Se ninguém fizer uma requisição, o pedido e o estoque reservado só serão atualizados na próxima consulta; não há um processo de agendamento em segundo plano.

## Área administrativa

Contas com o papel `admin` acessam o painel pelo botão de engrenagem no navbar. Para promover uma conta existente, execute no diretório `backend`:

```sh
php artisan app:promote-admin email@exemplo.com
```

O painel permite gerenciar produtos (a imagem é informada por URL), categorias e pedidos. Categorias vinculadas a produtos não podem ser excluídas. Produtos excluídos deixam os itens dos pedidos antigos preservados pelo nome e preço registrados na compra.