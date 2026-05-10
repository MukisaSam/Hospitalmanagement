# Database Rules — Hospital Management System

## Engine and Version

- MySQL 8.0+
- Default charset: `utf8mb4`, collation: `utf8mb4_unicode_ci` — set in `config/database.php`.
- Storage engine: InnoDB on all tables (enforces foreign keys and transactions).

---

## Migration Rules

- Every schema change goes through a Laravel migration. Never alter tables manually.
- Migration filenames follow the automatic timestamp convention: `php artisan make:migration`.
- One logical change per migration file (e.g., create a table, add a column, add an index).
- Always provide a `down()` method that is the exact inverse of `up()`.
- Foreign key constraints must be declared in the same migration as the table that holds the FK column — never in a separate migration that runs later (order matters).

### Column Defaults

| Scenario | Rule |
|----------|------|
| String PKs | Do not use — use BIGINT UNSIGNED AI |
| Timestamps | All tables get `$table->timestamps()` |
| Soft deletes | Add `$table->softDeletes()` to: patients, doctors, departments |
| Monetary values | `decimal(12, 2)` — never `float` or `double` |
| Boolean flags | `tinyInteger(1)` (MySQL) via `$table->boolean()` |
| Enum columns | Use `$table->enum()` with the exact values from the SRS appendix |
| JSON columns | `$table->json()` — used only for `audit_logs.old_values` and `new_values` |

---

## Tables and Expected Indexes

### Required Unique Indexes

```
patients.mrn
users.email
invoices.invoice_number
doctor_schedules.(doctor_id, day_of_week)   ← composite unique
vitals.(medical_record_id)                  ← unique (one per record)
settings.key
```

### Required Non-Unique Indexes

```
patients.phone_number
patients.national_id
appointments.appointment_date
appointments.doctor_id
appointments.patient_id
appointments.status
invoices.status
invoices.patient_id
medical_records.patient_id
audit_logs.(model_type, model_id)           ← composite
```

Add all indexes in the migration's `up()` method using `$table->index()` or `$table->unique()`.

---

## Foreign Key Rules

| Relationship | ON DELETE behaviour |
|-------------|-------------------|
| `doctors.user_id → users.id` | RESTRICT |
| `patients.user_id → users.id` | SET NULL |
| `departments.head_doctor_id → doctors.id` | SET NULL |
| `doctors.department_id → departments.id` | SET NULL |
| `doctor_schedules.doctor_id → doctors.id` | CASCADE |
| `appointments.patient_id → patients.id` | RESTRICT |
| `appointments.doctor_id → doctors.id` | RESTRICT |
| `appointments.booked_by → users.id` | SET NULL |
| `appointment_logs.appointment_id → appointments.id` | CASCADE |
| `appointment_logs.changed_by → users.id` | SET NULL |
| `medical_records.patient_id → patients.id` | RESTRICT |
| `medical_records.doctor_id → doctors.id` | RESTRICT |
| `medical_records.appointment_id → appointments.id` | SET NULL |
| `prescriptions.medical_record_id → medical_records.id` | CASCADE |
| `vitals.medical_record_id → medical_records.id` | CASCADE |
| `vitals.recorded_by → users.id` | SET NULL |
| `invoices.patient_id → patients.id` | RESTRICT |
| `invoices.appointment_id → appointments.id` | SET NULL |
| `invoices.doctor_id → doctors.id` | SET NULL |
| `invoices.created_by → users.id` | SET NULL |
| `invoice_items.invoice_id → invoices.id` | CASCADE |
| `payments.invoice_id → invoices.id` | RESTRICT |
| `payments.paid_by → users.id` | SET NULL |
| `audit_logs.user_id → users.id` | SET NULL |

---

## Eloquent Model Rules

- Place all models in `app/Models/`.
- Declare `$fillable` explicitly on every model. Never use `$guarded = []`.
- Declare `$casts` for all non-string columns:
  - `date` columns → `'date'`
  - `decimal` columns → `'decimal:2'`
  - `boolean` columns → `'boolean'`
  - `json` columns → `'array'`
  - `enum` columns → use PHP-backed Enum classes in `app/Enums/`
- Use `SoftDeletes` trait on: `Patient`, `Doctor`, `Department`.
- Audit log observers must be registered in `app/Providers/AppServiceProvider.php` via `Model::observe(ModelObserver::class)`.

### Relationship Definitions

```php
// User
public function patient(): HasOne    // → Patient
public function doctor(): HasOne     // → Doctor

// Patient
public function user(): BelongsTo           // → User
public function appointments(): HasMany     // → Appointment
public function medicalRecords(): HasMany   // → MedicalRecord
public function invoices(): HasMany         // → Invoice

// Doctor
public function user(): BelongsTo           // → User
public function department(): BelongsTo     // → Department
public function schedules(): HasMany        // → DoctorSchedule
public function appointments(): HasMany     // → Appointment

// Appointment
public function patient(): BelongsTo        // → Patient
public function doctor(): BelongsTo         // → Doctor
public function medicalRecord(): HasOne     // → MedicalRecord
public function invoice(): HasOne           // → Invoice
public function logs(): HasMany             // → AppointmentLog

// MedicalRecord
public function prescriptions(): HasMany    // → Prescription
public function vitals(): HasOne            // → Vital

// Invoice
public function items(): HasMany            // → InvoiceItem
public function payments(): HasMany         // → Payment
```

---

## Query Rules

- **Always** eager-load relationships accessed in a view or loop to prevent N+1 queries.
  ```php
  Appointment::with(['patient', 'doctor.department'])->paginate(25);
  ```
- Use `->paginate(25)` on all listing queries. Never `->get()` on unbounded tables.
- Dashboard aggregates must use DB-level aggregation, not PHP:
  ```php
  Payment::whereMonth('payment_date', now()->month)->sum('amount_paid');
  ```
- Scope patient/doctor/appointment queries to the authenticated user's context:
  ```php
  // Doctor sees only their own appointments
  auth()->user()->doctor->appointments()->with('patient')->paginate(25);
  ```
- Use `->chunk(500, callback)` for CSV exports and bulk operations.

---

## Transaction Rules

Wrap every multi-step write in `DB::transaction()`:

```php
DB::transaction(function () use ($appointment) {
    $appointment->update(['status' => 'completed']);
    InvoiceService::createForAppointment($appointment);
});
```

Required transaction boundaries:
- Appointment status → `completed` (triggers invoice creation)
- Payment recording (updates `payments` + recomputes `invoices.amount_paid` + updates `invoices.status`)
- Patient registration (creates `patients` + optionally creates `users`)
- Doctor registration (creates `doctors` + creates `users`)

---

## Seeders

Seeders live in `database/seeders/`. Each seeder must be idempotent (use `firstOrCreate` or `updateOrCreate`, not `create`).

Required seeders:
| Seeder | Purpose |
|--------|---------|
| `AdminSeeder` | Creates one admin user (credentials in `.env` or documented) |
| `DepartmentSeeder` | 5 sample departments |
| `DoctorSeeder` | 5 sample doctors (one per department) |
| `PatientSeeder` | 20 sample patients with MRNs |
| `AppointmentSeeder` | 30 sample appointments across statuses |
| `SettingSeeder` | Inserts default key-value rows into `settings` |

`DatabaseSeeder::run()` must call all seeders in dependency order (departments before doctors, etc.).
