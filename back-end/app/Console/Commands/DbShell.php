<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Database Shell Command - AI Interactive Database Access
 *
 * Usage:
 *   php artisan db:shell
 *   php artisan db:shell --table=users
 *   php artisan db:shell --query="SELECT * FROM users WHERE active = 1"
 */
class DbShell extends Command
{
    protected $signature = 'db:shell
                            {--table= : Specific table to query}
                            {--query= : Direct SQL query (SELECT only)}
                            {--info : Show database information}';

    protected $description = 'Interactive database shell for AI testing and debugging';

    public function handle(): int
    {
        // Show database info
        if ($this->option('info')) {
            return $this->showDatabaseInfo();
        }

        // Direct query
        if ($query = $this->option('query')) {
            return $this->executeQuery($query);
        }

        // Table query
        if ($table = $this->option('table')) {
            return $this->showTable($table);
        }

        // Interactive mode
        return $this->interactiveMode();
    }

    private function showDatabaseInfo(): int
    {
        $driver = config('database.default');
        $database = config("database.connections.{$driver}.database");

        $this->info("=== Database Information ===");
        $this->table(
            ['Property', 'Value'],
            [
                ['Driver', $driver],
                ['Database', $database],
                ['Tables', count(Schema::getTables())],
            ]
        );

        $tables = Schema::getTables();
        $tableData = array_map(fn($t) => [$t['name'], $t['columns_count'] ?? 'N/A', $t['rows_count'] ?? 'N/A'], $tables);

        $this->info("\n=== Tables ===");
        $this->table(['Table', 'Columns', 'Rows'], $tableData);

        return Command::SUCCESS;
    }

    private function showTable(string $table): int
    {
        if (!Schema::hasTable($table)) {
            $this->error("Table '{$table}' does not exist.");
            return Command::FAILURE;
        }

        $columns = Schema::getColumnListing($table);
        $rows = DB::table($table)->limit(20)->get();

        $this->info("=== Table: {$table} ===");
        $this->info("Columns: " . implode(', ', $columns));
        $this->info("Total rows: " . DB::table($table)->count());

        if ($rows->isNotEmpty()) {
            $this->table($columns, $rows->toArray());
        }

        return Command::SUCCESS;
    }

    private function executeQuery(string $query): int
    {
        $query = trim($query);

        // Security: Only allow SELECT
        if (!preg_match('/^\s*select\s+/i', $query)) {
            $this->error('Only SELECT queries are allowed for safety.');
            return Command::FAILURE;
        }

        try {
            $results = DB::select($query);

            if (empty($results)) {
                $this->warn('No results found.');
                return Command::SUCCESS;
            }

            // Auto-detect columns from first result
            $columns = array_keys((array) $results[0]);
            $this->table($columns, array_map(fn($r) => (array) $r, $results));

            $this->info('Total rows: ' . count($results));

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Query error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function interactiveMode(): int
    {
        $this->info('=== Database Shell ===');
        $this->info('Commands:');
        $this->line('  info              - Show database info');
        $this->line('  tables            - List all tables');
        $this->line('  table <name>      - Show table structure and data');
        $this->line('  query <sql>       - Execute SELECT query');
        $this->line('  count <table>     - Count rows in table');
        $this->line('  exit              - Exit shell');
        $this->line('');

        while (true) {
            $input = $this->ask('db>');

            if ($input === 'exit' || $input === 'quit' || $input === 'q') {
                break;
            }

            $this->processCommand($input);
        }

        return Command::SUCCESS;
    }

    private function processCommand(string $input): void
    {
        $input = trim($input);
        $parts = preg_split('/\s+/', $input, 2);
        $command = strtolower($parts[0] ?? '');
        $arg = $parts[1] ?? '';

        match ($command) {
            'info' => $this->showDatabaseInfo(),
            'tables' => $this->listTables(),
            'table' => $this->showTable($arg ?: 'users'),
            'count' => $this->countTable($arg ?: 'users'),
            'query' => $this->executeQuery($arg),
            default => $this->executeQuery($input),
        };
    }

    private function listTables(): void
    {
        $tables = Schema::getTables();
        $data = array_map(fn($t) => [$t['name'], $t['columns_count'] ?? 'N/A'], $tables);
        $this->table(['Table', 'Columns'], $data);
    }

    private function countTable(string $table): void
    {
        if (!Schema::hasTable($table)) {
            $this->error("Table '{$table}' does not exist.");
            return;
        }

        $count = DB::table($table)->count();
        $this->info("Table '{$table}' has {$count} rows.");
    }
}
