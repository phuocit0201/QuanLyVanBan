<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Database Info Command - Quick database overview
 *
 * Usage:
 *   php artisan db:info
 *   php artisan db:tables
 *   php artisan db:structure {table}
 */
class DbInfo extends Command
{
    protected $signature = 'db:info
                            {--tables : List all tables}
                            {--structure= : Show table structure}
                            {--seed : Seed data info}';

    protected $description = 'Show database information';

    public function handle(): int
    {
        if ($this->option('tables')) {
            return $this->listTables();
        }

        if ($table = $this->option('structure')) {
            return $this->showStructure($table);
        }

        if ($this->option('seed')) {
            return $this->showSeedData();
        }

        return $this->showOverview();
    }

    private function showOverview(): int
    {
        $driver = config('database.default');
        $database = config("database.connections.{$driver}.database");

        $this->info("=== Database Overview ===");
        $this->table(
            ['Property', 'Value'],
            [
                ['Driver', $driver],
                ['Database', $database],
                ['Host', config("database.connections.{$driver}.host")],
                ['Port', config("database.connections.{$driver}.port")],
            ]
        );

        $tables = $this->getTablesInfo();
        $this->info("\n=== Tables ({$tables['count']} total) ===");

        $tableData = array_map(fn($t) => [
            $t['name'],
            $t['columns'],
            $t['rows'],
        ], $tables['tables']);

        $this->table(['Table', 'Columns', 'Rows'], $tableData);

        return Command::SUCCESS;
    }

    private function listTables(): int
    {
        $tables = $this->getTablesInfo();

        foreach ($tables['tables'] as $table) {
            $this->line("{$table['name']} ({$table['rows']} rows)");
        }

        return Command::SUCCESS;
    }

    private function showStructure(string $table): int
    {
        $columns = DB::getSchemaBuilder()->getColumnListing($table);
        $count = DB::table($table)->count();

        $this->info("=== Table: {$table} ===");
        $this->info("Total rows: {$count}");
        $this->info("Columns: " . implode(', ', $columns));

        $sample = DB::table($table)->limit(3)->get();
        if ($sample->isNotEmpty()) {
            $this->info("\nSample data:");
            $this->table(array_keys((array) $sample[0]), array_map(fn($r) => (array) $r, $sample->toArray()));
        }

        return Command::SUCCESS;
    }

    private function showSeedData(): int
    {
        $tables = $this->getTablesInfo();

        $this->info("=== Seed Data ===");
        foreach ($tables['tables'] as $table) {
            $this->line("{$table['name']}: {$table['rows']} rows");
        }

        return Command::SUCCESS;
    }

    private function getTablesInfo(): array
    {
        $driver = config('database.default');
        $tables = [];

        if ($driver === 'sqlite') {
            $tableNames = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            foreach ($tableNames as $t) {
                $name = $t->name;
                $columns = DB::getSchemaBuilder()->getColumnListing($name);
                $count = DB::table($name)->count();
                $tables[] = [
                    'name' => $name,
                    'columns' => count($columns),
                    'rows' => $count,
                ];
            }
        }

        return [
            'count' => count($tables),
            'tables' => $tables,
        ];
    }
}
