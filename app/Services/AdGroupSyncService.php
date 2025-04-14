<?php

namespace App\Services;

use App\Models\AdGroup;
use LdapRecord\Models\ActiveDirectory\Group;
use Illuminate\Support\Facades\Log;

class AdGroupSyncService
{
    /**
     * Sincroniza os grupos do Active Directory com o banco de dados local
     *
     * @return array Estatísticas da sincronização
     */
    public function syncGroups(): array
    {
        $stats = [
            'created' => 0,
            'updated' => 0,
            'total' => 0,
        ];
    
        try {
            // Buscar todos os grupos do AD
            $ldapGroups = Group::get();
            
            foreach ($ldapGroups as $ldapGroup) {
                $stats['total']++;
                
                // Verificar se o grupo já existe no banco
                $adGroup = AdGroup::where('dn', $ldapGroup->getDn())->first();
                
                $groupData = [
                    'name' => $ldapGroup->getName(),
                    'dn' => $ldapGroup->getDn(),
                    'sid' => $ldapGroup->getObjectSid(),
                    'description' => $ldapGroup->getDescription() ?? '',
                ];
                
                if ($adGroup) {
                    // Atualizar grupo existente
                    $adGroup->update($groupData);
                    $stats['updated']++;
                } else {
                    // Criar novo grupo
                    AdGroup::create($groupData);
                    $stats['created']++;
                }
            }
            
            \Log::info('Sincronização de grupos AD concluída', $stats);
            
        } catch (\Exception $e) {
            \Log::error('Erro na sincronização de grupos AD: ' . $e->getMessage());
            throw $e;
        }
        
        return $stats;
    }

    /**
     * Busca os grupos de um usuário do AD e sincroniza suas roles
     *
     * @param \App\Models\User $user O usuário local
     * @param \LdapRecord\Models\ActiveDirectory\User $ldapUser O usuário do LDAP
     * @return array Roles sincronizadas
     */
    public function syncUserGroups($user, $ldapUser): array
    {
        try {
            // Buscar grupos do usuário no AD
            $ldapGroups = $ldapUser->groups()->get();
            $groupDns = $ldapGroups->map(function ($group) {
                return $group->getDn();
            })->toArray();
            
            // Sincronizar as roles com base nos grupos
            $user->syncRolesFromLdapGroups($groupDns);
            
            // Retornar as roles atualizadas
            return $user->roles()->pluck('slug')->toArray();
            
        } catch (\Exception $e) {
            Log::error('Erro ao sincronizar grupos do usuário: ' . $e->getMessage());
            throw $e;
        }
    }
}