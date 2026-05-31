# Database Access Skill for AI

## Purpose

This skill provides AI assistants with easy access to the application's database for:
- Reading data to verify operations
- Testing new functions and features
- Debugging issues
- Understanding data structure

---

## Database Commands

### 1. Artisan Commands

```bash
# Show database overview
php artisan db:info

# List all tables
php artisan db:info --tables

# Show table structure
php artisan db:info --structure=users

# Execute a query
php artisan db:query "SELECT * FROM users WHERE id = 1"

# Quick table count
php artisan db:count users
```

### 2. Interactive Shell

```bash
# Start interactive shell
php artisan db:shell

# Directly open a table
php artisan db:shell --table=users

# Execute specific query
php artisan db:shell --query="SELECT * FROM users LIMIT 10"
```

---

## DatabaseService Methods

### Static Methods for Easy Access

```php
use App\Services\DatabaseService;

// Get all rows from a table
DatabaseService::getTable('users');                    // First 100 rows
DatabaseService::getTable('users', ['id', 'name']);    // Specific columns

// Find by ID
DatabaseService::find('users', 1);                     // User with ID 1
DatabaseService::find('users', 1, ['id', 'name']);     // Specific columns

// Find by column value
DatabaseService::findBy('users', 'email', 'test@example.com');

// Where conditions
DatabaseService::where('users', 'role', 'admin');     // role = 'admin'
DatabaseService::where('users', 'id', '>', 5);        // id > 5

// Count rows
DatabaseService::count('users');                       // Total users
DatabaseService::count('users', 'role', 'admin');     // Admin count

// Get recent records
DatabaseService::getRecent('users', 10);              // Last 10 users

// Pagination
DatabaseService::paginate('users', 1, 15);           // Page 1, 15 per page

// Raw query (SELECT only)
DatabaseService::query("SELECT * FROM users WHERE active = 1");

// Get table columns
DatabaseService::getColumns('users');                 // ['id', 'name', 'email', ...]

// Check if table exists
DatabaseService::tableExists('users');                // true/false

// Get database info
DatabaseService::getInfo();                           // Driver, database, tables
```

---

## Usage Examples

### Testing a New Function

```php
// 1. Create a new function in AuthService
public function testRegister(string $email): bool
{
    // ... your code
}

// 2. Test it via DatabaseService
$user = DatabaseService::findBy('users', 'email', 'new@example.com');

// 3. Verify it exists
if ($user) {
    echo "User created: " . $user->name;
}
```

### Verifying CRUD Operations

```php
// After creating a task
$task = DatabaseService::find('tasks', $taskId);
echo "Created task: " . $task->title;

// After updating
$task = DatabaseService::find('tasks', $taskId);
echo "Updated status: " . $task->status;

// After deleting
$task = DatabaseService::find('tasks', $taskId);
echo ($task === null) ? "Deleted successfully" : "Delete failed";
```

### Debugging Queries

```php
// Check what data exists
$users = DatabaseService::getTable('users');
$admins = DatabaseService::where('users', 'role', 'admin');
$inactive = DatabaseService::where('users', 'active', 0);

// Find related data
$userTasks = DatabaseService::where('tasks', 'user_id', $userId);
```

---

## Current Database Schema

### Users Table
```
Columns: id, name, email, email_verified_at, password, remember_token, created_at, updated_at
```

### Tasks Table (if exists)
```
Columns: id, user_id, title, description, status, priority, due_date, created_at, updated_at
```

---

## Safety Rules

1. **READ ONLY**: DatabaseService only allows SELECT queries
2. **No destructive operations**: No UPDATE, DELETE, or DROP
3. **Query validation**: Raw queries must start with SELECT
4. **Limit results**: Default limit is 100 rows

---

## Tips for AI

### Quick Data Check
```php
// Always check if data exists before assuming
$user = DatabaseService::findBy('users', 'email', $email);
if ($user) {
    // Data exists
} else {
    // Data not found
}
```

### Count Before Loop
```php
// Check count to avoid empty loops
$count = DatabaseService::count('users', 'active', 1);
if ($count > 0) {
    $users = DatabaseService::where('users', 'active', 1);
}
```

### Pagination for Large Tables
```php
// Don't load all data
$page = DatabaseService::paginate('users', 1, 50); // First 50
$page = DatabaseService::paginate('users', 2, 50); // Next 50
```
