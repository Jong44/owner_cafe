<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Illuminate\Support\Facades\DB as FacadesDB;

class CheckDatabaseConnections extends Command
{
    protected $signature = 'db:check-connections';
    protected $description = 'Check connections to all databases';

    public function handle()
    {
        $databases = [
            'mysql' => 'Default Database',
            'mysql_1' => 'Second Database',
            'mysql_2' => 'Third Database',
            'mysql_3' => 'Third Database',
        ];

        foreach ($databases as $connection => $label) {
            try {
                FacadesDB::connection($connection)->getPdo();
                $this->info("Connection to {$label} ({$connection}) is successful!");
            } catch (\Exception $e) {
                $this->error("Failed to connect to {$label} ({$connection}): " . $e->getMessage());
            }
        }

        return Command::SUCCESS;
    }
}
