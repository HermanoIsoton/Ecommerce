## Checkout e pedidos pendentes

O checkout autenticado cria o pedido a partir dos preços e quantidades atuais do carrinho, reserva o estoque, salva um retrato do endereço de entrega e esvazia o carrinho. O frete demonstrativo é fixo em R$ 29,90. Pedidos aguardando pagamento expiram em 30 minutos; a ação de pagamento do frontend é apenas uma simulação e não realiza cobranças.

O serviço agenda `orders:expire-unpaid` a cada minuto. O `compose.yaml` não inicia um container de scheduler. Para executar a expiração automática em desenvolvimento, inicie o scheduler manualmente:

```sh
php artisan schedule:work
```

Quando o prazo vence, o pedido passa para `cancelado` e as unidades reservadas são devolvidas ao estoque. O endpoint do pedido também verifica a expiração ao consultar ou tentar simular o pagamento.