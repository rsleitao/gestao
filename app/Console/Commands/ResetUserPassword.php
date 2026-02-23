<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ResetUserPassword extends Command
{
    protected $signature = 'user:reset-password {email : Email do utilizador} {password? : Nova password (se omitido, será pedida)}';

    protected $description = 'Redefine a password de um utilizador (útil quando o email não está configurado)';

    public function handle(): int
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("Utilizador com email «{$email}» não encontrado.");

            return self::FAILURE;
        }

        if (! $password) {
            $password = $this->secret('Nova password');
            $confirm = $this->secret('Confirmar nova password');
            if ($password !== $confirm) {
                $this->error('As passwords não coincidem.');

                return self::FAILURE;
            }
        }

        if (strlen($password) < 8) {
            $this->error('A password deve ter pelo menos 8 caracteres.');

            return self::FAILURE;
        }

        $user->password = $password;
        $user->must_change_password = false;
        $user->save();

        $this->info("Password do utilizador «{$user->name}» ({$email}) foi alterada com sucesso.");

        return self::SUCCESS;
    }
}
