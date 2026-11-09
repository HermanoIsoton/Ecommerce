<?php

namespace App\Console\Commands;

use App\Services\CheckoutService;
use Illuminate\Console\Command;

class ExpireUnpaidOrders extends Command
{
    protected $signature = 'orders:expire-unpaid';

    protected $description = 'Cancela pedidos pendentes expirados e devolve o estoque reservado.';

    public function handle(CheckoutService $checkoutService): int
    {
        $expiredCount = $checkoutService->expirePendingOrders();
        $this->info("Pedidos expirados processados: {$expiredCount}.");

        return self::SUCCESS;
    }
}
