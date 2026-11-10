<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class PromoteUserToAdmin extends Command
{
    protected $signature = 'app:promote-admin {email : E-mail da conta que receberá o papel de administrador}';

    protected $description = 'Promove uma conta existente para administradora';

    public function handle(): int
    {
        $user = User::query()->where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error('Nenhuma conta foi encontrada com esse e-mail.');

            return self::FAILURE;
        }

        $user->forceFill(['role' => 'admin'])->save();

        $this->info("{$user->email} agora é administrador.");

        return self::SUCCESS;
    }
}
