---
description: Security best practices and guidelines
globs: ["**/*.php"]
---

# Security Best Practices

## Input Validation & Sanitization

### Always Validate User Input
- Never trust user input
- Validate ALL incoming data
- Use Laravel's Form Request for validation
- Sanitize before processing
- Return clear error messages in JSON format for API

**Example - Form Request:**
```php
// app/Http/Requests/RegisterRequest.php
public function rules(): array
{
    return [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
    ];
}
```

### XSS Prevention
- Sanitize user inputs to prevent XSS attacks
- Use Laravel's `{{ }}` Blade syntax (auto-escapes)
- Use `{!! !!}` only for trusted HTML
- Validate and filter HTML inputs

**Example:**
```php
// Use HTMLPurifier or similar for HTML content
$cleanContent = clean($request->input('content'));
```

## SQL Injection Prevention

### Use Eloquent/Query Builder
- Eloquent and Query Builder use parameterized queries automatically
- Never concatenate SQL strings with user input
- Always use parameter binding

**Good:**
```php
User::where('email', $email)->first();
DB::table('users')->where('id', $id)->get();
```

**Bad:**
```php
DB::select("SELECT * FROM users WHERE email = '$email'"); // Never do this!
```

## Authentication & Passwords (JWT)

### JWT Token Security
- Use tymon/jwt-auth package for Laravel
- Set appropriate token expiration time (TTL)
- Use HTTPS in production
- Implement token blacklisting for logout

**JWT Configuration (config/jwt.php):**
```php
'ttl' => env('JWT_TTL', 60), // Token lifetime in minutes
'refresh_ttl' => env('JWT_REFRESH_TTL', 20160), // Refresh token lifetime
'blacklist_enabled' => env('JWT_BLACKLIST_ENABLED', true),
```

### Password Handling
- Always hash passwords using Laravel's Hash facade
- Never store plain text passwords
- Use bcrypt or argon2 hashing (Laravel default is bcrypt via `hashed` cast)

**Example:**
```php
// User model with automatic hashing
protected function casts(): array
{
    return [
        'password' => 'hashed',
    ];
}

// Manual hashing when needed
$hashedPassword = Hash::make($request->password);
```

### Authentication Implementation
- Use custom AuthController for JWT authentication
- Return JWT token on successful login
- Implement rate limiting on login attempts
- Use HTTPS in production

**Example - AuthController:**
```php
public function login(Request $request): JsonResponse
{
    $credentials = $request->only('email', 'password');

    if (!$token = auth('api')->attempt($credentials)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials',
        ], 401);
    }

    return $this->respondWithToken($token);
}

protected function respondWithToken(string $token): JsonResponse
{
    return response()->json([
        'success' => true,
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => auth('api')->factory()->getTTL() * 60,
    ]);
}
```

## Authorization

### Implement Proper Authorization
- Use Policies and Gates
- Check permissions before actions
- Don't rely only on frontend checks
- Use middleware for route protection

**Example - Middleware Protection:**
```php
Route::middleware('auth:api')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
});
```

**Example - Policy:**
```php
// app/Policies/PostPolicy.php
public function update(User $user, Post $post): bool
{
    return $user->id === $post->user_id || $user->isAdmin();
}
```

## CSRF Protection

### CSRF Protection
- CSRF protection is for web routes only (Blade forms)
- API routes using JWT are NOT vulnerable to CSRF (no session cookies)
- For SPA/mobile apps, JWT in Authorization header is sufficient

**Note:** CSRF only affects state-changing requests that use cookies. JWT in Bearer token is inherently CSRF-safe.

## Sensitive Data Protection

### Environment Variables
- Never commit `.env` file
- Store sensitive data in `.env`
- Use `.env.example` as template
- Document required environment variables
- Add JWT_SECRET to .env (generated via `php artisan jwt:secret`)

### Secrets Management
- Never hardcode API keys or passwords
- Use `config()` to access environment variables
- Don't expose sensitive data in responses
- Don't log sensitive information
- JWT secret key should be at least 32 characters

**Example:**
```php
// Good
$apiKey = config('services.stripe.secret');
$jwtSecret = config('jwt.secret');

// Bad
$apiKey = 'sk_test_1234567890'; // Never hardcode!
```

## API Security

### Rate Limiting
- Implement rate limiting on API endpoints
- Use Laravel's throttle middleware with appropriate limits
- Different limits for different endpoints (login vs normal)

**Example:**
```php
// In routes/api.php
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1'); // 5 attempts per minute

Route::middleware(['auth:api', 'throttle:60,1'])->group(function () {
    // Protected routes - 60 requests per minute
});
```

### API Token Security (JWT)
- Token expiration: 60 minutes (configurable)
- Refresh token: 2 weeks (configurable)
- Implement token blacklisting for logout
- Allow token revocation

**User Model - JWT Interface:**
```php
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
```

### Sensitive Data in Responses
- Never return passwords in API responses
- Filter sensitive attributes using API Resources
- Use `$hidden` property in models

**Example:**
```php
class User extends Model
{
    protected $hidden = [
        'password',
        'remember_token',
    ];
}

// Or use API Resource
class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            // Never include password
        ];
    }
}
```

## Mass Assignment Protection

### Use Fillable or Guarded
- Define `$fillable` or `$guarded` on all models
- Protect against mass assignment vulnerabilities

**Example:**
```php
class User extends Model
{
    protected $fillable = [
        'name',
        'email',
    ];

    // Or use guarded (inverse approach)
    protected $guarded = [
        'id',
        'is_admin',
        'password',
    ];
}
```

### Use DTOs for Complex Data Transfer
```php
// app/DTOs/CreateUserDTO.php
readonly class CreateUserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->validated('name'),
            email: $request->validated('email'),
            password: $request->validated('password'),
        );
    }
}
```

## File Upload Security

### Validate File Uploads
- Validate file types and sizes
- Store uploaded files outside web root
- Use Laravel's file validation rules
- Generate unique filenames
- Scan for malware if possible

**Example:**
```php
$request->validate([
    'avatar' => [
        'required',
        'image',
        'mimes:jpeg,png,jpg,gif',
        'max:2048', // Max 2MB
        'dimensions:min_width=100,min_height=100',
    ],
]);

$path = $request->file('avatar')->store('avatars', 's3');
```

## Headers & Configuration

### Security Headers
- Set appropriate security headers
- Use HTTPS in production
- Implement Content Security Policy
- Set secure cookie flags (if using web routes)

**Example - Middleware:**
```php
// app/Http/Middleware/AddSecurityHeaders.php
public function handle($request, Closure $next)
{
    $response = $next($request);

    $response->headers->set('X-Content-Type-Options', 'nosniff');
    $response->headers->set('X-Frame-Options', 'DENY');
    $response->headers->set('X-XSS-Protection', '1; mode=block');

    return $response;
}
```

### Production Configuration
- Set `APP_DEBUG=false` in production
- Set `APP_ENV=production`
- Use secure session configuration
- Enable HTTPS
- Configure CORS properly for API

**Example - config/cors.php:**
```php
'paths' => ['api/*'],
'allowed_methods' => ['*'],
'allowed_origins' => env('CORS_ALLOWED_ORIGINS', ['https://yourdomain.com']),
'allowed_origins_patterns' => [],
'allowed_headers' => ['*'],
'exposed_headers' => [],
'max_age' => 0,
'supports_credentials' => false,
```

