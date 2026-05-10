# Domain Rules — Hospital Management System

## Domain Overview

The HMS models the core operations of a single hospital facility. The central entity is the `Appointment`, which connects a `Patient` to a `Doctor` and acts as the trigger for both `MedicalRecord` creation and `Invoice` generation.

---

## Roles and Access Boundaries

Four roles exist. Each maps to exactly one value in `users.role`:

| Role | Value | Primary Domain Concern |
|------|-------|----------------------|
| Administrator | `admin` | System config, user management, all data |
| Doctor | `doctor` | Own schedule, assigned patient records |
| Receptionist | `receptionist` | Patient registration, appointment booking, payments |
| Patient | `patient` | Read-only view of own data |

**Enforcement:** The `CheckRole` middleware must be the second middleware on every authenticated route group. Laravel Policies and Gates provide method-level enforcement inside controllers.

Never perform role checks inside Blade templates as the sole security measure — always enforce at the routing/controller layer first.

---

## Patient Domain

### MRN Generation

- Format: `MRN-YYYYMMDD-XXXX` where XXXX is a zero-padded daily sequence starting at `0001`.
- `MrnGeneratorService::generate()` queries `MAX(mrn)` filtered to the current date prefix, extracts the sequence, increments by 1, and returns the new MRN.
- MRN is assigned at patient creation and is **immutable** — never update it after the fact.
- `date_of_birth` is also immutable after registration.

### Patient Deletion

- Only soft-delete (`deleted_at`). Hard deletion is not supported.
- Soft-deleted patients are excluded from all search results and dropdowns by default (Eloquent `SoftDeletes` handles this automatically).
- Only admins can restore soft-deleted patients.

---

## Doctor Domain

### Schedule

- Each doctor has up to 7 schedule rows in `doctor_schedules` (one per day of week).
- `is_available = false` means the doctor is not taking appointments on that day even if a row exists.
- `max_appointments` caps the number of `confirmed` appointments per doctor per day.

### Deactivation

- Setting `doctors.status = inactive` hides the doctor from the appointment booking dropdown.
- It does not cancel existing appointments — those must be manually handled by the receptionist.

---

## Appointment Domain

### Status Machine

The appointment status is a strict finite state machine. Only these transitions are valid:

```
pending    → confirmed | cancelled
confirmed  → checked_in | cancelled | no_show
checked_in → in_progress | no_show
in_progress → completed
completed  → (terminal)
cancelled  → (terminal)
no_show    → (terminal)
```

`AppointmentService::transition($appointment, $newStatus)` must:
1. Assert the transition is valid (throw `InvalidStatusTransitionException` if not).
2. Write a row to `appointment_logs` capturing old/new status, changed_by, and reason.
3. Trigger side effects:
   - `in_progress`: create an empty `MedicalRecord` stub for the doctor to complete.
   - `completed`: call `InvoiceService::createForAppointment($appointment)` inside a `DB::transaction`.
4. Persist the new status.

### Booking Validation

`AppointmentService::validateSlot($doctorId, $date, $time)` must check:
1. The date is today or in the future.
2. A `doctor_schedules` row exists for `$doctor_id` and the day-of-week of `$date` where `is_available = true`.
3. The `$time` falls within `[start_time, end_time]` of that schedule row.
4. The count of `confirmed` appointments for that doctor on `$date` is less than `max_appointments`.
5. The patient does not already have a `confirmed` appointment with the same doctor on `$date`.

Throw a `SlotUnavailableException` with a descriptive message for any failed check.

### Cancellation

- Allowed only when status is `pending` or `confirmed`.
- `cancellation_reason` is required (validated via Form Request, `required|string|min:10`).
- If `patients.email` is set, dispatch a `AppointmentCancelledNotification` (queued mail).

### Rescheduling

- Allowed only when status is `pending` or `confirmed`.
- Run the same `validateSlot` checks for the new date/time.
- Log the change in `appointment_logs` with `old_date`, `old_time`, `new_date`, `new_time`, and `reason`.

---

## Medical Record Domain

### Creation

- A `MedicalRecord` stub is created automatically when an appointment transitions to `in_progress`.
  ```php
  MedicalRecord::create([
      'patient_id'     => $appointment->patient_id,
      'doctor_id'      => $appointment->doctor_id,
      'appointment_id' => $appointment->id,
      'visit_date'     => today(),
  ]);
  ```
- The doctor then fills in `chief_complaint`, `diagnosis`, `treatment_plan`, etc.

### Prescriptions

- Multiple `Prescription` rows can be attached to one `MedicalRecord`.
- A prescription cannot exist without a parent `MedicalRecord`.

### Vitals

- Exactly one `Vital` row per `MedicalRecord` (unique constraint on `medical_record_id`).
- BMI is computed server-side: `weight (kg) / (height (m))²`, stored to 2 decimal places.

### Access Control

- Doctors may only read/write records where `doctor_id = auth()->user()->doctor->id`.
- Receptionists see a read-only summary (no `notes` field).
- Patients see `visit_date`, `diagnosis`, `diagnosis_code`, `treatment_plan`, and `prescriptions`. The `notes` field is hidden unless `settings.show_notes_to_patient = true`.

---

## Invoice Domain

### Auto-Generation

`InvoiceService::createForAppointment(Appointment $appointment)` runs inside a `DB::transaction` and:
1. Generates an `invoice_number` via `InvoiceNumberService::generate()` (format: `INV-YYYYMMDD-XXXX`, same daily-sequence logic as MRN).
2. Creates the `Invoice` record with `status = unpaid`.
3. Creates one `InvoiceItem` for the doctor's consultation fee (`doctors.consultation_fee`).
4. Computes `subtotal`, `tax_amount` (using `settings.tax_rate`), and `total_amount`.

### Payment Recording

`InvoiceService::recordPayment(Invoice $invoice, array $data)`:
1. Creates a `Payment` row.
2. Recomputes `invoices.amount_paid = SUM(payments.amount_paid)` for this invoice.
3. Updates `invoices.status`:
   - `amount_paid == 0` → `unpaid`
   - `0 < amount_paid < total_amount` → `partial`
   - `amount_paid >= total_amount` → `paid`

### Invoice Immutability

- Line items (`invoice_items`) can only be added/edited when `invoices.status` is `unpaid` or `partial`.
- An invoice with `status = paid` or `cancelled` is locked — no edits allowed.

---

## Audit Log Domain

### What to Log

Log every create, update, and delete on: `patients`, `doctors`, `users`, `appointments`, `medical_records`, `invoices`, `payments`, `settings`.

### How to Log

Use Eloquent model observers. Register observers in `AppServiceProvider::boot()`:

```php
Patient::observe(PatientObserver::class);
// etc.
```

Each observer method calls `AuditLogService::log(string $action, Model $model, array $oldValues, array $newValues)`.

`AuditLogService::log` writes to `audit_logs`:
```php
AuditLog::create([
    'user_id'    => auth()->id(),
    'action'     => $action,          // 'created' | 'updated' | 'deleted' | 'restored'
    'model_type' => get_class($model),
    'model_id'   => $model->getKey(),
    'old_values' => $oldValues,       // cast to JSON
    'new_values' => $newValues,
    'ip_address' => request()->ip(),
    'user_agent' => request()->userAgent(),
]);
```

### What NOT to Log

- `audit_logs` rows themselves (no recursive logging).
- `sessions`, `password_reset_tokens`, `personal_access_tokens` table changes.
- Read-only operations (SELECT queries).

---

## Settings Domain

- `SettingService::get(string $key, mixed $default = null)` retrieves a value from the `settings` table and casts it according to `settings.type`.
- `SettingService::set(string $key, mixed $value)` updates the value.
- Cache settings for the duration of the request using a simple static array in `SettingService` — do not hit the DB on every `get()` call within a single request.
- The `tax_rate` setting is read by `InvoiceService` at invoice creation time.

---

## Service Classes

All business logic lives in `app/Services/`. Controllers call services; services call models.

| Service | Key Methods |
|---------|------------|
| `MrnGeneratorService` | `generate(): string` |
| `InvoiceNumberService` | `generate(): string` |
| `AppointmentService` | `validateSlot(...)`, `transition(...)`, `cancel(...)`, `reschedule(...)` |
| `InvoiceService` | `createForAppointment(Appointment)`, `recordPayment(Invoice, array)` |
| `AuditLogService` | `log(string, Model, array, array)` |
| `SettingService` | `get(string, mixed)`, `set(string, mixed)`, `all(): array` |

Services must not depend on each other circularly. Inject dependencies via constructor.
