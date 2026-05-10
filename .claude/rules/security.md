# Security Rules — Hospital Management System

## Threat Model

The HMS stores sensitive personal health information (PHI). The primary threats are:
- Unauthorised access to patient records (broken access control)
- Data manipulation by users acting outside their role (privilege escalation)
- Injection attacks (SQL injection, XSS, mass assignment)
- Session hijacking and CSRF
- Insecure file uploads

All controls below directly address these threats.

---

## Authentication

### Password Storage

- All passwords are hashed using Laravel's `Hash::make()` (Bcrypt, default cost = 10).
- Never store, log, or transmit plain-text passwords.
- Password fields must use `type="password"` in all forms.

### Login Rate Limiting

Apply Laravel's built-in rate limiter in `app/Http/Middleware/` or `RouteServiceProvider`:

```php
RateLimiter::for('login', function (Request $request) {
    return Limit::perMinutes(15, 5)->by($request->input('email') . '|' . $request->ip());
});
```

After 5 failed attempts on the same email within 15 minutes, return HTTP 429 with a retry-after message. Never reveal whether the email or the password was wrong ("Invalid credentials" only).

### Session Security

| Setting | Value |
|---------|-------|
| `SESSION_LIFETIME` | `120` (minutes) |
| `SESSION_SECURE_COOKIE` | `true` in production |
| `SESSION_HTTP_ONLY` | `true` (default) |
| `SESSION_SAME_SITE` | `lax` |

Call `session()->regenerate()` after a successful login to prevent session fixation.
Call `Auth::logout()` followed by `session()->invalidate()` and `session()->regenerateToken()` on logout.

### Password Reset

- Use Laravel's built-in `Password::sendResetLink()` and `Password::reset()`.
- Reset tokens expire after 60 minutes (`config/auth.php → passwords.users.expire = 60`).
- Invalidate the token immediately after a successful reset.
- The reset email must not include the token in the subject line or plaintext body beyond the signed link.

---

## Authorisation

### Middleware Stack

Every authenticated route must carry both `auth` and `check.role` middleware. Never rely on a single layer.

```php
// Correct
Route::middleware(['auth', 'check.role:admin'])->group(...);

// Wrong — auth alone is not enough
Route::middleware('auth')->group(...);
```

### CheckRole Middleware

```php
class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!in_array(auth()->user()->role, $roles, true)) {
            abort(403);
        }
        return $next($request);
    }
}
```

Register in `bootstrap/app.php` as `check.role`.

### Laravel Policies

Define a Policy for each major resource (`PatientPolicy`, `AppointmentPolicy`, `MedicalRecordPolicy`, `InvoicePolicy`). Register in `AppServiceProvider`.

Controllers must call `$this->authorize('action', $model)` before any operation:

```php
public function show(Patient $patient): View
{
    $this->authorize('view', $patient);
    // ...
}
```

Policy example for `MedicalRecord`:

```php
public function view(User $user, MedicalRecord $record): bool
{
    return match($user->role) {
        'admin'        => true,
        'doctor'       => $user->doctor?->id === $record->doctor_id,
        'receptionist' => true,   // read-only — controller enforces no edit
        'patient'      => $user->patient?->id === $record->patient_id,
        default        => false,
    };
}
```

### Direct Object Reference Protection

Never look up resources by raw ID without scoping to the authenticated user's context:

```php
// Wrong — any authenticated user can access any patient
$patient = Patient::findOrFail($id);

// Correct — scope first, then find
$patient = Patient::findOrFail($id);
$this->authorize('view', $patient);
```

For doctor-scoped resources, additionally scope the query:

```php
// Doctor can only see their own appointments
$appointment = auth()->user()->doctor->appointments()->findOrFail($id);
```

---

## CSRF Protection

- Laravel's `VerifyCsrfToken` middleware is active on all `web` routes by default — do not disable it.
- Every HTML form must include `@csrf`.
- AJAX requests (chart fetch calls) are GET-only and do not require CSRF tokens.
- The session cookie (`XSRF-TOKEN`) must not be exposed to JavaScript unless absolutely necessary.

---

## Input Validation and Mass Assignment

### Form Requests

Every POST, PUT, and PATCH request must be validated through a dedicated Form Request class:

```php
// app/Http/Requests/StorePatientRequest.php
public function rules(): array
{
    return [
        'first_name'   => 'required|string|max:100',
        'last_name'    => 'required|string|max:100',
        'date_of_birth'=> 'required|date|before:today',
        'gender'       => 'required|in:male,female,other',
        'phone_number' => 'required|string|max:20',
        'address'      => 'nullable|string|max:500',
        'email'        => 'nullable|email|max:255|unique:patients,email',
        'blood_group'  => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
        // ...
    ];
}
```

Never call `$request->validate()` inside a controller method.

### Mass Assignment Protection

All models must declare `$fillable`. Never use `$guarded = []`:

```php
protected $fillable = ['first_name', 'last_name', 'date_of_birth', 'gender', 'phone_number'];
```

Never pass `$request->all()` to `create()` or `fill()` without filtering first. Use `$request->validated()`:

```php
Patient::create($request->validated());
```

---

## XSS Prevention

- Always use Blade's `{{ }}` double-curly syntax to echo user data. This HTML-encodes the output.
- Never use `{!! !!}` (unescaped) for any value that originates from user input or the database.
- The only acceptable use of `{!! !!}` is for system-generated HTML (e.g., Laravel's paginator links):
  ```blade
  {!! $patients->links() !!}
  ```

---

## SQL Injection Prevention

- All database interactions must use Eloquent's parameterised query builder.
- The only raw SQL permitted is in complex report aggregations (dashboard charts, CSV exports). Use `DB::select()` with PDO-style named bindings:
  ```php
  DB::select('SELECT DATE(appointment_date) as date, COUNT(*) as total
              FROM appointments
              WHERE appointment_date >= :start
              GROUP BY DATE(appointment_date)', ['start' => $start]);
  ```
- Never interpolate user input directly into a query string.

---

## File Upload Security

File uploads are allowed for profile photos (patients, doctors) and the hospital logo (settings).

Rules:
- Validate MIME type server-side using `mimes:jpeg,png,webp` (not just the file extension).
- Validate file size: `max:2048` (2 MB) for profile photos, `max:5120` (5 MB) for logo.
- Store uploaded files in `storage/app/private/uploads/` — not in `public/`. Serve via a controller:
  ```php
  Route::get('/uploads/{path}', [UploadController::class, 'serve'])
       ->middleware('auth')
       ->where('path', '.+');
  ```
- Rename files to a random UUID on storage: `$request->file('photo')->storeAs('profile-photos', Str::uuid() . '.jpg', 'private')`.
- Never trust the original filename from the client.

---

## Transport Security (Production)

Set the following in `.env` for production deployments:

```
APP_ENV=production
APP_DEBUG=false
SESSION_SECURE_COOKIE=true
```

Add HSTS header via middleware registered in `bootstrap/app.php`:

```php
$response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
```

`APP_DEBUG=false` ensures stack traces and environment details are never shown to users. All exceptions are logged to `storage/logs/laravel.log` instead.

---

## Data Privacy (PHI)

- Patient health data (`medical_records`, `prescriptions`, `vitals`, `diagnoses`) is classified as PHI.
- PHI fields must never appear in log files, error messages, or audit log `old_values`/`new_values` in plaintext beyond what is necessary.
- The `notes` field in `medical_records` (private clinical notes) is hidden from patients in views unless `settings.show_notes_to_patient = true`.
- The audit log is accessible only to `admin` role users.

---

## Audit Log Integrity

- `audit_logs` rows are write-only. There is no update or delete route for this table.
- The `audit_logs` table has no `updated_at` column to reinforce immutability.
- The admin view of audit logs is read-only — no editing controls are rendered.

---

## Error Pages

Create custom Blade error views at `resources/views/errors/`:

| File | HTTP Status | Description |
|------|------------|-------------|
| `403.blade.php` | 403 Forbidden | Role not permitted |
| `404.blade.php` | 404 Not Found | Resource not found |
| `419.blade.php` | 419 CSRF Expired | Session/token expired |
| `500.blade.php` | 500 Server Error | Unexpected exception |

All error views must extend `layouts.guest` and display a helpful, non-technical message. Never expose exception details, stack traces, or file paths in production error pages.
