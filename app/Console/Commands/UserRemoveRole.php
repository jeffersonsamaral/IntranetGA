<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class UserRemoveRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:remove-role {username} {role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove uma role de um usuário';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $username = $this->argument('username');
        $roleName = $this->argument('role');
        
        // Buscar o usuário
        $user = User::where('username', $username)->first();
        
        if (!$user) {
            $this->error("Usuário '{$username}' não encontrado!");
            return Command::FAILURE;
        }
        
        // Buscar a role
        $role = Role::where('slug', $roleName)->orWhere('name', $roleName)->first();
        
        if (!$role) {
            $this->error("Role '{$roleName}' não encontrada!");
            return Command::FAILURE;
        }
        
        // Verificar se o usuário tem a role
        if (!$user->hasRole($role->slug)) {
            $this->info("O usuário '{$username}' não possui a role '{$role->name}'.");
            return Command::SUCCESS;
        }
        
        // Remover a role do usuário
        $user->roles()->detach($role->id);
        
        $this->info("Role '{$role->name}' removida do usuário '{$username}' com sucesso!");
        
        return Command::SUCCESS;
    }
}