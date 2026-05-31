<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Database Query Command - Quick Testing
 *
 * Usage:
 *   php artisan db:query "SELECT * FROM users LIMIT 5"
 *   php artisan db:count users
 *   php artisan db:find users 1
 *   php artisan db:recent users
 *   php artisan db:search users email like "%test%"
 */
class DbQuery extends Command
{
    protected $signature = 'db:query
                            {sql : The SQL query to execute}
                            {--bind=* : Query bindings}';

    protected $description = 'Execute a database query for testing';

    public function handle(): int
    {
        $sql = $this->argument('sql');
        $bindings = $this->option('bind');

        // Security check
        if (!preg_match('/^\s*(select|show|describe|desc)\s+/i', $sql)) {
            $this->error('Only SELECT/SHOW/DESCRIBE queries are allowed.');
            return Command::FAILURE;
        }

        try {
            $results = DB::select($sql, $bindings);

            if (empty($results)) {
                $this->warn('No results found.');
                return Command::SUCCESS;
            }

            $columns = array_keys((array) $results[0]);
            $this->table($columns, array_map(fn($r) => (array) $r, $results));

            $this->info('Total rows: ' . count($results));

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Query failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
