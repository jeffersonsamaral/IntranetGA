<?php

namespace App\Console\Commands;

use App\Services\AdGroupSyncService;
use Illuminate\Console\Command;

class SyncAdGroups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ad:sync-groups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza os grupos do Active Directory com o banco de dados local';

    /**
     * Execute the console command.
     */
    public function handle(AdGroupSyncService $service)
    {
        $this->info('Iniciando sincronização de grupos do Active Directory...');
        
        try {
            $stats = $service->syncGroups();
            
            $this->info('Sincronização concluída!');
            $this->info("Total de grupos: {$stats['total']}");
            $this->info("Grupos criados: {$stats['created']}");
            $this->info("Grupos atualizados: {$stats['updated']}");
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Erro na sincronização: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}