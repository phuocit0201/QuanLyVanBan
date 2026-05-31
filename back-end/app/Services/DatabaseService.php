<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Database Service - AI Tool for Database Access
 *
 * This service provides easy database access for testing and verification.
 * AI can use this to read data, verify operations, and debug issues.
 */
class DatabaseService
{
    /**
     * Get all rows from a table.
     *
     * @example DatabaseService::getTable('users')
     * @example DatabaseService::getTable('users', ['id', 'name', 'email'])
     */
    public static function getTable(string $table, array $columns = ['*'], int $limit = 100): array
    {
        return DB::table($table)->select($columns)->limit($limit)->get()->toArray();
    }

    /**
     * Get a single row by ID.
     */
    public static function find(string $table, int $id, array $columns = ['*']): ?object
    {
        return DB::table($table)->where('id', $id)->select($columns)->first();
    }

    /**
     * Find a row by specific column value.
     *
     * @example DatabaseService::findBy('users', 'email', 'test@example.com')
     */
    public static function findBy(string $table, string $column, mixed $value, array $columns = ['*']): ?object
    {
        return DB::table($table)->where($column, $value)->select($columns)->first();
    }

    /**
     * Get rows where condition matches.
     *
     * @example DatabaseService::where('users', 'role', 'admin')
     */
    public static function where(string $table, string $column, mixed $operator, mixed $value = null, int $limit = 50): array
    {
        if ($value === null) {
            return DB::table($table)->where($column, $operator)->limit($limit)->get()->toArray();
        }

        return DB::table($table)->where($column, $operator, $value)->limit($limit)->get()->toArray();
    }

    /**
     * Count rows in a table.
     */
    public static function count(string $table, ?string $column = null, ?string $operator = null, mixed $value = null): int
    {
        $query = DB::table($table);

        if ($column && $operator) {
            if ($value === null) {
                $query->where($column, $operator);
            } else {
                $query->where($column, $operator, $value);
            }
        }

        return $query->count();
    }

    /**
     * Execute raw SQL query (SELECT only).
     *
     * @example DatabaseService::query("SELECT * FROM users WHERE active = 1")
     */
    public static function query(string $sql, array $bindings = []): array
    {
        // Only allow SELECT queries for safety
        if (!str_starts_with(strtolower(trim($sql)), 'select')) {
            throw new \InvalidArgumentException('Only SELECT queries are allowed');
        }

        return DB::select($sql, $bindings);
    }

    /**
     * Get table schema information.
     */
    public static function getColumns(string $table): array
    {
        return DB::getSchemaBuilder()->getColumnListing($table);
    }

    /**
     * Get all tables in database.
     */
    public static function getTables(): array
    {
        return DB::connection()->getDoctrineSchemaManager()->listTableNames();
    }

    /**
     * Check if table exists.
     */
    public static function tableExists(string $table): bool
    {
        return DB::getSchemaBuilder()->hasTable($table);
    }

    /**
     * Get recent records (ordered by id desc).
     */
    public static function getRecent(string $table, int $limit = 10, array $columns = ['*']): array
    {
        return DB::table($table)->select($columns)->orderByDesc('id')->limit($limit)->get()->toArray();
    }

    /**
     * Get paginated results.
     */
    public static function paginate(string $table, int $page = 1, int $perPage = 15, array $columns = ['*']): array
    {
        $offset = ($page - 1) * $perPage;

        return [
            'data' => DB::table($table)->select($columns)->offset($offset)->limit($perPage)->get()->toArray(),
            'total' => DB::table($table)->count(),
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil(DB::table($table)->count() / $perPage),
        ];
    }

    /**
     * Get database info.
     */
    public static function getInfo(): array
    {
        return [
            'driver' => config('database.default'),
            'database' => config('database.connections.' . config('database.default') . '.database'),
            'tables' => self::getTables(),
            'connected' => true,
        ];
    }
}
