<?php

namespace App\Services;

use App\Models\AdGroup;
use LdapRecord\Models\ActiveDirectory\Group;
use Illuminate\Support\Facades\Log;

class AdGroupSyncService
{
    /**
     * Converte um SID binário para formato de string legível
     * 
     * @param string $binarySid SID binário do Active Directory
     * @return string SID convertido para formato legível
     */
    private function convertBinarySidToString($binarySid): string
    {
        try {
            // Verificar se o SID é realmente binário
            if (!is_string($binarySid)) {
                Log::warning('SID não é uma string binária', ['sid' => $binarySid]);
                return '';
            }

            // Decompor o SID binário
            $sidHex = bin2hex($binarySid);
            
            // Verificar se a conversão foi bem-sucedida
            if (empty($sidHex)) {
                Log::warning('Conversão de SID binário falhou', ['original' => $binarySid]);
                return '';
            }

            return $sidHex;
        } catch (\Exception $e) {
            Log::error('Erro ao converter SID binário', [
                'error' => $e->getMessage(),
                'sid' => bin2hex($binarySid)
            ]);
            return '';
        }
    }

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
            'skipped' => 0,
            'errors' => 0,
        ];
    
        try {
            // Buscar todos os grupos do AD
            $ldapGroups = Group::get();
            
            Log::info('Grupos encontrados no AD: ' . count($ldapGroups));
            
            foreach ($ldapGroups as $ldapGroup) {
                $stats['total']++;
                
                $name = $ldapGroup->getName();
                $dn = $ldapGroup->getDn();
                
                // Converter SID binário para string
                $binarySid = $ldapGroup->getObjectSid();
                $sidString = $this->convertBinarySidToString($binarySid);
                
                // Verificar se o grupo já existe no banco
                $adGroup = AdGroup::where('dn', $dn)->first();
                
                $groupData = [
                    'name' => $name,
                    'dn' => $dn,
                    'sid' => $sidString,
                    // Usar getAttribute para recuperar a descrição
                    'description' => $ldapGroup->getAttribute('description')[0] ?? '',
                ];
                
                // Log detalhado para cada grupo
                Log::info("Processando grupo: {$name}", [
                    'dn' => $dn, 
                    'sid' => $sidString
                ]);
                
                try {
                    if ($adGroup) {
                        // Atualizar grupo existente
                        $adGroup->update($groupData);
                        $stats['updated']++;
                        Log::info("Grupo atualizado: {$name}");
                    } else {
                        // Criar novo grupo
                        AdGroup::create($groupData);
                        $stats['created']++;
                        Log::info("Grupo criado: {$name}");
                    }
                } catch (\Exception $createUpdateEx) {
                    $stats['errors']++;
                    Log::error("Erro ao salvar grupo: {$name}", [
                        'error' => $createUpdateEx->getMessage(),
                        'group_data' => $groupData
                    ]);
                }
                
                // Condição para evitar grupos de sistema
                if (stripos($name, 'builtin') !== false) {
                    $stats['skipped']++;
                    Log::info("Grupo de sistema ignorado: {$name}");
                }
            }
            
            Log::info('Sincronização de grupos AD concluída', $stats);
            
        } catch (\Exception $e) {
            Log::error('Erro na sincronização de grupos AD: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
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