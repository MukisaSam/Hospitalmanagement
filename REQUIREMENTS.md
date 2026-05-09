# Software Requirements Specification (SRS)
# Hospital Management System (HMS)

**Version:** 1.0
**Date:** 2026-05-09
**Prepared by:** Group A
**Institution:** Makerere University
**Course:** Emerging Technologies
**Stack:** Laravel (PHP) | MySQL | Blade Templates

---

## Table of Contents

1. Introduction
2. Overall Description
3. System Actors and Roles
4. Functional Requirements
5. Non-Functional Requirements
6. Database Schema
7. Technology Stack
8. Use Case Summary
9. Constraints and Assumptions
10. Appendix — Status Enums and Field Reference

---

## 1. Introduction

### 1.1 Purpose

This Software Requirements Specification (SRS) document defines the complete requirements for the Hospital Management System (HMS). It is intended to serve as the authoritative reference for design, development, testing, and evaluation of the system. The document is structured to meet academic standards for a university final year project while remaining actionable for implementation.

### 1.2 Scope

The HMS is a web-based application that digitizes and centralises the core operations of a hospital. It replaces paper-based and fragmented manual processes with a unified, role-controlled platform covering patient registration, appointment scheduling, doctor and department management, medical records, billing and reporting.

The system does not cover:
- Laboratory equipment integration or DICOM imaging
- Pharmacy inventory management (out of scope for this version)
- Mobile native applications (web-responsive only)
- Insurance provider API integration

### 1.3 Definitions, Acronyms, and Abbreviations

| Term | Definition |
|------|-----------|
| HMS | Hospital Management System |
| SRS | Software Requirements Specification |
| CRUD | Create, Read, Update, Delete |
| OPD | Outpatient Department |
| IPD | Inpatient Department |
| EMR | Electronic Medical Record |
| MRN | Medical Record Number — unique patient identifier |
| FR | Functional Requirement |
| NFR | Non-Functional Requirement |
| UUID | Universally Unique Identifier |
| Blade | Laravel's built-in server-side templating engine |
| Eloquent | Laravel's Active Record ORM |
| Middleware | Laravel HTTP request pipeline filters |

### 1.4 References

- Laravel 11.x Official Documentation — https://laravel.com/docs
- MySQL 8.0 Reference Manual — https://dev.mysql.com/doc
- IEEE Std 830-1998 — Recommended Practice for SRS
- OWASP Top Ten Web Application Security Risks (2021)

### 1.5 Document Overview

Section 2 describes the system at a high level. Section 3 defines each actor. Section 4 details every functional requirement module by module. Section 5 covers non-functional requirements. Section 6 provides the database schema. Sections 7–9 address technology, use cases, constraints, and assumptions.

---

## 2. Overall Description

### 2.1 Product Perspective

The HMS is a standalone, self-hosted web application. It runs on a LAMP/LEMP-equivalent stack (PHP, MySQL, a web server). All actors interact through a browser. The system is session-based with role-based access control (RBAC) enforced at the middleware layer. There is no external dependency on third-party APIs beyond optional email (SMTP) for notifications.

```
+------------------+        HTTPS         +---------------------------+
|  Browser Client  | <------------------> |  Laravel Application      |
| (Admin/Doctor/   |                      |  - Routes                 |
|  Receptionist/   |                      |  - Controllers            |
|  Patient)        |                      |  - Eloquent Models        |
+------------------+                      |  - Blade Views            |
                                          +---------------------------+
                                                      |
                                          +---------------------------+
                                          |  MySQL 8.0 Database       |
                                          +---------------------------+
```

### 2.2 Product Functions (High-Level)

- User authentication with role-based routing and permission gates
- Patient registration, search, and profile management
- Doctor and staff profile management with specialty and schedule data
- Department and ward management
- Appointment booking, rescheduling, and cancellation
- Electronic Medical Records — visit notes, diagnoses, prescriptions
- Invoice generation, payment recording, and outstanding bill tracking
- Dashboard with real-time statistics and report exports

### 2.3 User Classes and Characteristics

| User Class | Technical Level | Access Frequency | Primary Concern |
|------------|----------------|-----------------|----------------|
| Administrator | Moderate | Daily | System configuration, reports |
| Doctor | Low-moderate | Every shift | Patient records, appointments |
| Receptionist | Low | Continuous (shift) | Bookings, patient check-in |
| Patient | Low | Occasional | View appointments, bills |

### 2.4 Operating Environment

- **Server OS:** Linux (Ubuntu 22.04 LTS recommended) or Windows Server
- **Web Server:** Apache 2.4+ or Nginx 1.24+
- **PHP:** 8.2 or higher
- **MySQL:** 8.0 or higher
- **Browser Support:** Chrome 120+, Firefox 120+, Edge 120+, Safari 17+
- **Minimum Server RAM:** 2 GB; Recommended: 4 GB
- **Network:** HTTPS required in production; self-signed certificate acceptable for development

### 2.5 Design and Implementation Constraints

- Must use Laravel's built-in authentication scaffolding (Laravel Breeze or custom Auth controllers)
- All database interactions must go through Eloquent ORM; raw queries are permitted only for complex report aggregations
- Blade templates must be used for all server-rendered views
- The application must follow Laravel's MVC conventions: controllers in `app/Http/Controllers`, models in `app/Models`, views in `resources/views`
- Bootstrap 5.x is recommended for rapid UI development

### 2.6 Assumptions and Dependencies

- A working SMTP server or Mailtrap configuration is available for email notifications
- The host institution provides a MySQL 8.0 database server
- All users have access to a modern desktop browser
- The database will be seeded with at least one administrator account during deployment

---

## 3. System Actors and Roles

### 3.1 Administrator

The Administrator has unrestricted access to all modules. This role is responsible for system configuration, user account management, and report generation.

**Capabilities:**
- Create, edit, suspend, and delete user accounts for all roles
- Manage departments and assign department heads
- Configure system-wide settings (hospital name, address, logo, timezone)
- Generate and export financial and operational reports
- View all records across all modules
- Manage doctor schedules and assign doctors to departments
- Override appointment statuses
- Access audit logs

### 3.2 Doctor

The Doctor interacts primarily with patient medical records and their own appointment schedule. Doctors cannot access billing details or administrative settings.

**Capabilities:**
- View their own appointment schedule (today, week, month views)
- Access assigned patient profiles and full EMR history
- Create and edit medical record entries (visit notes, diagnoses, prescriptions)
- Update appointment status (mark as completed, no-show)
- View their own profile and update personal/contact details

### 3.3 Receptionist

The Receptionist manages front-desk operations: patient registration, appointment booking, and basic billing tasks.

**Capabilities:**
- Register new patients and edit existing patient profiles
- Search patients by MRN, name, phone number, or date of birth
- Create, reschedule, and cancel appointments
- Check patients in and out for their appointments
- Record payments against invoices
- View invoice details (cannot create invoices — invoices are auto-generated on appointment completion)
- Print appointment slips and patient summary cards

### 3.4 Patient

The Patient has a read-only, self-service portal. Patients can view their own data only.

**Capabilities:**
- View their own profile information
- View their appointment history and upcoming appointments
- View their medical records (diagnoses and prescriptions)
- View their invoices and payment history
- Cannot book appointments directly (Receptionist books on their behalf in v1)

### 3.5 Role Hierarchy and Permission Matrix

| Feature | Admin | Doctor | Receptionist | Patient |
|---------|-------|--------|-------------|---------|
| Manage users | CRUD | — | — | — |
| Manage departments | CRUD | View | View | — |
| Manage doctors | CRUD | View self | View | — |
| Manage patients | CRUD | View assigned | CRUD | View self |
| Manage appointments | CRUD | View/Update status | CRUD | View own |
| Medical records | CRUD | CRUD (assigned) | View | View own |
| Billing/Invoices | CRUD | — | View/Pay | View own |
| Dashboard/Reports | Full | Limited (own stats) | Limited | — |
| System settings | CRUD | — | — | — |
| Audit log | View | — | — | — |

---

## 4. Functional Requirements

Each requirement is identified as FR-[MODULE]-[NUMBER].

---

### 4.1 Authentication Module

#### FR-AUTH-01 — User Login
- The system shall provide a login form accepting `email` and `password`.
- On successful authentication, the system shall redirect the user to a role-specific dashboard.
- On failure, the system shall display a generic error message ("Invalid credentials") without revealing whether the email or password was incorrect.
- Accounts with `status = suspended` shall be refused login with a descriptive message.

#### FR-AUTH-02 — Session Management
- Sessions shall expire after 120 minutes of inactivity.
- The system shall use Laravel's `session` driver with CSRF token validation on every POST, PUT, PATCH, and DELETE request.
- The system shall invalidate the session on logout and redirect to the login page.

#### FR-AUTH-03 — Password Management
- Passwords shall be hashed using Bcrypt (Laravel default via `Hash::make()`).
- The administrator can reset any user's password from the user management panel.
- A "Forgot Password" flow using signed email links shall be available.
- Password reset tokens expire after 60 minutes.

#### FR-AUTH-04 — Role-Based Access Control
- Each user record shall have a `role` field with one of four values: `admin`, `doctor`, `receptionist`, `patient`.
- Laravel middleware (`CheckRole`) shall be applied to all route groups, verifying the authenticated user's role.
- Unauthorized access attempts shall return an HTTP 403 response with a branded error view.

#### FR-AUTH-05 — Audit Logging
- All create, update, and delete operations on sensitive resources shall be logged to an `audit_logs` table capturing: `user_id`, `action`, `model_type`, `model_id`, `old_values` (JSON), `new_values` (JSON), `ip_address`, `created_at`.

---

### 4.2 Patient Management Module

#### FR-PAT-01 — Patient Registration
- Required fields: `first_name`, `last_name`, `date_of_birth`, `gender`, `phone_number`, `address`.
- Optional fields: `email`, `national_id`, `blood_group`, `marital_status`, `emergency_contact_name`, `emergency_contact_phone`, `allergies`, `profile_photo`.
- On successful registration, the system shall auto-generate a unique `mrn` in the format `MRN-YYYYMMDD-XXXX`.
- A patient user account shall be automatically created if an email is provided.

#### FR-PAT-02 — Patient Search
- Search by: `mrn`, `first_name`, `last_name`, `phone_number`, `date_of_birth`, `national_id`.
- Case-insensitive, partial matching on name fields using SQL `LIKE`.
- Results paginated at 20 records per page.

#### FR-PAT-03 — Patient Profile View
- Displays: personal details, contact information, appointment history (last 5), recent medical records (last 3 visits), and outstanding invoices.

#### FR-PAT-04 — Patient Update
- Administrators and receptionists may edit patient personal and contact information.
- `mrn` and `date_of_birth` are immutable after registration.

#### FR-PAT-05 — Patient Soft Delete
- Deletion sets `deleted_at` via Laravel's `SoftDeletes` trait.
- Administrators may view and restore soft-deleted records from an "Archived Patients" view.

---

### 4.3 Doctor Management Module

#### FR-DOC-01 — Doctor Registration
- Required fields: `first_name`, `last_name`, `email`, `phone_number`, `specialization`, `department_id`, `qualification`, `experience_years`.
- Optional fields: `bio`, `consultation_fee`, `profile_photo`.
- A user account with `role = doctor` shall be created automatically.

#### FR-DOC-02 — Doctor Listing
- Displays all active doctors with: photo (or avatar), full name, specialization, and department.

#### FR-DOC-03 — Doctor Schedule Management
- Each doctor has a schedule defined in `doctor_schedules`: `day_of_week`, `start_time`, `end_time`, `max_appointments`, `is_available`.
- Appointment booking validates against this schedule.

#### FR-DOC-04 — Doctor Profile Update
- Administrators can update any doctor's profile.
- Doctors can update their own `bio`, `phone_number`, and `profile_photo`.

#### FR-DOC-05 — Doctor Deactivation
- Setting `status = inactive` hides the doctor from appointment booking dropdowns.
- Existing appointments for inactive doctors remain unchanged.

---

### 4.4 Department Management Module

#### FR-DEPT-01 — Department Creation
- Required fields: `name` (unique), `description`, `head_doctor_id` (nullable FK to doctors).
- Optional fields: `location`, `phone_extension`.

#### FR-DEPT-02 — Department Listing and View
- All authenticated users can view the list of departments.
- Department detail page shows the department head, assigned doctors, and description.

#### FR-DEPT-03 — Department Update and Delete
- A department cannot be deleted if it has active doctors assigned.
- Deletion is soft-delete only.

---

### 4.5 Appointment Management Module

#### FR-APPT-01 — Appointment Booking
- Required fields: `patient_id`, `doctor_id`, `appointment_date`, `appointment_time`, `type` (opd, ipd, emergency, follow_up), `notes` (optional).
- Validation:
  - `appointment_date` is not in the past
  - Selected time falls within the doctor's schedule for that day
  - Slot does not exceed `max_appointments`
  - Patient does not already have a confirmed appointment with the same doctor on the same date

#### FR-APPT-02 — Appointment Status Lifecycle
- Statuses: `pending`, `confirmed`, `checked_in`, `in_progress`, `completed`, `cancelled`, `no_show`
- Valid transition matrix:

| From | Permitted Next States |
|------|---------------------|
| pending | confirmed, cancelled |
| confirmed | checked_in, cancelled, no_show |
| checked_in | in_progress, no_show |
| in_progress | completed |
| completed | — (terminal) |
| cancelled | — (terminal) |
| no_show | — (terminal) |

#### FR-APPT-03 — Appointment Cancellation
- Any `pending` or `confirmed` appointment may be cancelled.
- `cancellation_reason` (text, required) must be recorded.
- Patient receives email notification if they have a valid email.

#### FR-APPT-04 — Appointment Rescheduling
- Rescheduling creates a log entry in `appointment_logs` with old/new date, time, reason.
- Same slot validation rules apply.

#### FR-APPT-05 — Appointment Calendar View
- Administrators and receptionists see all appointments; doctors see only their own.
- Calendar colour-codes appointments by status.

#### FR-APPT-06 — Today's Appointment Queue
- Receptionist dashboard displays a live queue of today's appointments sorted by time.
- Status can be updated inline from the queue view.

---

### 4.6 Medical Records Module

#### FR-MED-01 — Medical Record Creation
- Created automatically when an appointment reaches `in_progress` status.
- Doctor completes: `chief_complaint`, `symptoms`, `diagnosis`, `diagnosis_code` (ICD-10, optional), `treatment_plan`, `notes`.

#### FR-MED-02 — Prescription Sub-Record
- Each medical record can have multiple prescriptions: `medicine_name`, `dosage`, `frequency`, `duration`, `instructions`.

#### FR-MED-03 — Vital Signs Sub-Record
- Each medical record can have one vitals record: `blood_pressure`, `pulse_rate`, `temperature`, `weight`, `height`, `bmi` (computed), `oxygen_saturation`.

#### FR-MED-04 — Medical Record Access Control
- Doctors: read/write for assigned patients.
- Receptionists: read-only summary.
- Patients: read-only simplified view (clinical notes hidden unless admin enables `show_notes_to_patient`).

#### FR-MED-05 — Medical History Timeline
- Patient profile shows a chronological timeline of all visits: date, doctor, diagnosis, link to full record.

---

### 4.7 Billing and Invoicing Module

#### FR-BILL-01 — Automatic Invoice Generation
- Invoice auto-created when appointment transitions to `completed`.
- Default line item: consultation fee from `doctors.consultation_fee`.
- Invoice fields include: `invoice_number` (INV-YYYYMMDD-XXXX), `subtotal`, `tax_rate`, `tax_amount`, `discount_amount`, `total_amount`, `status`, `due_date`.

#### FR-BILL-02 — Invoice Line Items
- Multiple `invoice_items` per invoice: `description`, `quantity`, `unit_price`, `total_price`.
- Receptionists/admins can add, edit, remove items on `unpaid` or `partial` invoices.

#### FR-BILL-03 — Invoice Status Lifecycle
- Statuses: `unpaid`, `partial`, `paid`, `cancelled`, `refunded`.

#### FR-BILL-04 — Payment Recording
- Payments table: `invoice_id`, `amount_paid`, `payment_method` (cash, card, mobile_money, bank_transfer, insurance), `reference_number`, `payment_date`, `notes`.
- After each payment, the system recomputes `amount_paid` and updates invoice `status`.

#### FR-BILL-05 — Invoice PDF Generation
- Printable PDF via `barryvdh/laravel-dompdf`.
- Includes: hospital details, patient details, invoice number, itemised charges, payment history, outstanding balance.

#### FR-BILL-06 — Outstanding Bills Report
- Admins view unpaid/partial invoices filtered by date range, doctor, or department.

---

### 4.8 Dashboard and Reports Module

#### FR-DASH-01 — Administrator Dashboard
- Summary cards: total patients, today's appointments by status, active doctors, this month's revenue, pending invoices count.
- Bar chart: appointments per day (last 30 days).
- Pie chart: appointments by type.
- Charts rendered client-side via Chart.js fed by JSON API endpoint.

#### FR-DASH-02 — Doctor Dashboard
- Today's appointment count, next upcoming appointment, total patients treated, recent records authored.

#### FR-DASH-03 — Receptionist Dashboard
- Today's appointment queue, new patients registered today, pending payments.

#### FR-DASH-04 — Reports — Appointments
- Filterable by: date range, doctor, department, status, type. Exportable as CSV.

#### FR-DASH-05 — Reports — Revenue
- Monthly revenue grouped by department and doctor. Total collected vs. outstanding. Exportable as CSV.

#### FR-DASH-06 — Reports — Patient Statistics
- New patient registrations per month (last 12 months). Age and gender distribution charts.

---

### 4.9 System Settings Module

#### FR-SET-01 — Hospital Profile Configuration
- Settings stored as key-value pairs in a `settings` table.
- Keys: `hospital_name`, `hospital_address`, `hospital_phone`, `hospital_email`, `hospital_logo`, `timezone`, `currency`, `tax_rate`, `show_notes_to_patient`.

#### FR-SET-02 — User Management
- Administrators can: list all users, create users of any role, edit user details, reset passwords, suspend/reactivate accounts.

---

## 5. Non-Functional Requirements

### 5.1 Security

#### NFR-SEC-01 — Authentication Security
- Passwords hashed using Bcrypt (cost factor ≥ 10).
- CSRF protection enabled on all state-changing requests.
- Rate limiting: max 5 failed login attempts per email per 15 minutes.

#### NFR-SEC-02 — Authorization
- Every controller action verifies the authenticated user's role using Laravel Policies and Gates.
- All queries for user-specific resources must be scoped to the authenticated user.

#### NFR-SEC-03 — Input Validation and Sanitisation
- All user inputs validated server-side using Laravel Form Request classes.
- Eloquent parameterised queries used exclusively (no SQL injection vectors).
- File uploads validated for MIME type and max size (2 MB photos, 5 MB logo). Files stored outside public web root.

#### NFR-SEC-04 — Transport Security
- In production, all traffic served over HTTPS (TLS 1.2 minimum).
- `SESSION_SECURE_COOKIE` and `APP_ENV=production` set in `.env`.

#### NFR-SEC-05 — Data Privacy
- Patient data is classified as sensitive personal health information (PHI).
- Access strictly scoped by role as defined in Section 3.5.
- Audit logs ensure traceable record of all data modifications.

### 5.2 Performance

#### NFR-PERF-01 — Response Time
- Page loads shall complete within 3 seconds under normal load (up to 50 concurrent users) on reference server (2 vCPU, 4 GB RAM).
- Search queries return results within 1 second for datasets up to 100,000 records.

#### NFR-PERF-02 — Database Indexing
Required indexes:
- `patients.mrn` (unique index)
- `patients.phone_number` (index)
- `patients.national_id` (index)
- `appointments.appointment_date` (index)
- `appointments.doctor_id` (index)
- `appointments.patient_id` (index)
- `appointments.status` (index)
- `invoices.invoice_number` (unique index)
- `invoices.status` (index)
- `medical_records.patient_id` (index)
- `audit_logs.model_type`, `audit_logs.model_id` (composite index)

#### NFR-PERF-03 — Pagination
- All listing views paginated at a maximum of 25 records per page using Laravel's `paginate()`.

#### NFR-PERF-04 — Query Optimisation
- Eager loading (`with()`) used on all Eloquent queries accessing related models.
- Dashboard statistics use aggregation queries (COUNT, SUM), not PHP-memory collections.

### 5.3 Usability

#### NFR-USE-01 — Responsive Design
- All Blade views responsive on screens ≥ 768px wide (Bootstrap 5.x grid).

#### NFR-USE-02 — Navigation
- Persistent sidebar navigation on all authenticated pages with role-based menu items.
- Breadcrumb navigation on all detail and edit pages.

#### NFR-USE-03 — Feedback and Validation Messages
- Inline validation errors adjacent to offending fields.
- Flash notification (green alert) on successful operations.
- Confirmation dialog before destructive operations.

#### NFR-USE-04 — Accessibility
- All form inputs have associated `<label>` elements.
- All images have descriptive `alt` attributes.
- Status badges include text labels (not colour alone).

### 5.4 Reliability

#### NFR-REL-01 — Error Handling
- Custom exception handler for branded 404, 403, 500, and 419 error pages.
- All exceptions logged to `storage/logs/laravel.log`.

#### NFR-REL-02 — Database Transactions
- Multi-step operations (appointment completion + invoice creation; payment + invoice status update) wrapped in `DB::transaction()`.

#### NFR-REL-03 — Data Integrity
- Foreign key constraints defined in all migrations.
- Nullable FKs use `ON DELETE SET NULL`; required FKs use `ON DELETE RESTRICT`.

### 5.5 Maintainability

#### NFR-MAIN-01 — Code Standards
- PHP code follows PSR-12 coding standards.
- Business logic separated from controllers using Service classes in `app/Services/`.
- Form validation in dedicated Request classes in `app/Http/Requests/`.

#### NFR-MAIN-02 — Database Migrations
- All schema changes managed through Laravel migrations.
- Seeders provided for: default admin, sample departments, sample doctors, sample patients.

---

## 6. Database Schema

All tables use `id` (BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY). `created_at` and `updated_at` present on all tables. `deleted_at` added on tables with SoftDeletes.

---

### 6.1 `users`

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | bigint unsigned | PK, AI | |
| name | varchar(255) | NOT NULL | Full name |
| email | varchar(255) | UNIQUE, NOT NULL | |
| email_verified_at | timestamp | nullable | |
| password | varchar(255) | NOT NULL | Bcrypt hash |
| role | enum | NOT NULL | admin, doctor, receptionist, patient |
| status | enum | NOT NULL, default: active | active, suspended |
| profile_photo | varchar(255) | nullable | Storage path |
| last_login_at | timestamp | nullable | |
| remember_token | varchar(100) | nullable | |

---

### 6.2 `patients`

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | bigint unsigned | PK, AI | |
| user_id | bigint unsigned | FK users.id, nullable | Linked login account |
| mrn | varchar(30) | UNIQUE, NOT NULL | MRN-YYYYMMDD-XXXX |
| first_name | varchar(100) | NOT NULL | |
| last_name | varchar(100) | NOT NULL | |
| date_of_birth | date | NOT NULL | |
| gender | enum | NOT NULL | male, female, other |
| phone_number | varchar(20) | NOT NULL | |
| email | varchar(255) | nullable | |
| national_id | varchar(50) | nullable, indexed | |
| blood_group | enum | nullable | A+, A-, B+, B-, AB+, AB-, O+, O- |
| marital_status | enum | nullable | single, married, divorced, widowed |
| address | text | nullable | |
| allergies | text | nullable | Free text |
| emergency_contact_name | varchar(100) | nullable | |
| emergency_contact_phone | varchar(20) | nullable | |
| profile_photo | varchar(255) | nullable | |
| deleted_at | timestamp | nullable | SoftDeletes |

---

### 6.3 `departments`

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | bigint unsigned | PK, AI | |
| name | varchar(100) | UNIQUE, NOT NULL | |
| description | text | nullable | |
| head_doctor_id | bigint unsigned | FK doctors.id, nullable, ON DELETE SET NULL | |
| location | varchar(100) | nullable | |
| phone_extension | varchar(20) | nullable | |
| deleted_at | timestamp | nullable | |

---

### 6.4 `doctors`

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | bigint unsigned | PK, AI | |
| user_id | bigint unsigned | FK users.id, NOT NULL, ON DELETE RESTRICT | |
| department_id | bigint unsigned | FK departments.id, nullable, ON DELETE SET NULL | |
| first_name | varchar(100) | NOT NULL | |
| last_name | varchar(100) | NOT NULL | |
| specialization | varchar(100) | NOT NULL | |
| qualification | varchar(255) | NOT NULL | e.g., "MBChB, MMed" |
| experience_years | tinyint unsigned | NOT NULL, default: 0 | |
| consultation_fee | decimal(10,2) | NOT NULL, default: 0.00 | |
| bio | text | nullable | |
| phone_number | varchar(20) | nullable | |
| profile_photo | varchar(255) | nullable | |
| status | enum | NOT NULL, default: active | active, inactive |
| deleted_at | timestamp | nullable | |

---

### 6.5 `doctor_schedules`

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | bigint unsigned | PK, AI | |
| doctor_id | bigint unsigned | FK doctors.id, ON DELETE CASCADE | |
| day_of_week | enum | NOT NULL | monday–sunday |
| start_time | time | NOT NULL | |
| end_time | time | NOT NULL | |
| max_appointments | tinyint unsigned | NOT NULL, default: 20 | |
| is_available | tinyint(1) | NOT NULL, default: 1 | |

Unique constraint: `(doctor_id, day_of_week)`.

---

### 6.6 `appointments`

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | bigint unsigned | PK, AI | |
| patient_id | bigint unsigned | FK patients.id, ON DELETE RESTRICT | |
| doctor_id | bigint unsigned | FK doctors.id, ON DELETE RESTRICT | |
| appointment_date | date | NOT NULL | |
| appointment_time | time | NOT NULL | |
| type | enum | NOT NULL | opd, ipd, emergency, follow_up |
| status | enum | NOT NULL, default: pending | pending, confirmed, checked_in, in_progress, completed, cancelled, no_show |
| notes | text | nullable | Booking notes |
| cancellation_reason | text | nullable | Required if cancelled |
| booked_by | bigint unsigned | FK users.id, nullable | Who created the booking |

---

### 6.7 `appointment_logs`

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | bigint unsigned | PK, AI | |
| appointment_id | bigint unsigned | FK appointments.id, ON DELETE CASCADE | |
| old_status | varchar(50) | nullable | |
| new_status | varchar(50) | nullable | |
| old_date | date | nullable | |
| new_date | date | nullable | |
| old_time | time | nullable | |
| new_time | time | nullable | |
| changed_by | bigint unsigned | FK users.id, nullable | |
| reason | text | nullable | |

---

### 6.8 `medical_records`

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | bigint unsigned | PK, AI | |
| patient_id | bigint unsigned | FK patients.id, ON DELETE RESTRICT | |
| doctor_id | bigint unsigned | FK doctors.id, ON DELETE RESTRICT | |
| appointment_id | bigint unsigned | FK appointments.id, nullable, ON DELETE SET NULL | |
| visit_date | date | NOT NULL | |
| chief_complaint | text | NOT NULL | |
| symptoms | text | nullable | |
| diagnosis | text | NOT NULL | |
| diagnosis_code | varchar(20) | nullable | ICD-10 code |
| treatment_plan | text | nullable | |
| notes | text | nullable | Private clinical notes |
| follow_up_date | date | nullable | |

---

### 6.9 `prescriptions`

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | bigint unsigned | PK, AI | |
| medical_record_id | bigint unsigned | FK medical_records.id, ON DELETE CASCADE | |
| medicine_name | varchar(255) | NOT NULL | |
| dosage | varchar(100) | NOT NULL | e.g., "500mg" |
| frequency | varchar(100) | NOT NULL | e.g., "twice daily" |
| duration | varchar(100) | NOT NULL | e.g., "7 days" |
| instructions | text | nullable | |

---

### 6.10 `vitals`

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | bigint unsigned | PK, AI | |
| medical_record_id | bigint unsigned | FK medical_records.id, ON DELETE CASCADE | |
| blood_pressure | varchar(20) | nullable | e.g., "120/80 mmHg" |
| pulse_rate | smallint unsigned | nullable | bpm |
| temperature | decimal(4,1) | nullable | Celsius |
| weight | decimal(5,2) | nullable | kg |
| height | decimal(5,2) | nullable | cm |
| bmi | decimal(4,2) | nullable | Stored computed value |
| oxygen_saturation | decimal(4,1) | nullable | % SpO2 |
| recorded_by | bigint unsigned | FK users.id, nullable, ON DELETE SET NULL | |
| recorded_at | timestamp | NOT NULL | |

Unique constraint: `(medical_record_id)` — one vitals record per visit.

---

### 6.11 `invoices`

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | bigint unsigned | PK, AI | |
| invoice_number | varchar(30) | UNIQUE, NOT NULL | INV-YYYYMMDD-XXXX |
| patient_id | bigint unsigned | FK patients.id, ON DELETE RESTRICT | |
| appointment_id | bigint unsigned | FK appointments.id, nullable, ON DELETE SET NULL | |
| doctor_id | bigint unsigned | FK doctors.id, nullable, ON DELETE SET NULL | |
| subtotal | decimal(12,2) | NOT NULL, default: 0.00 | |
| tax_rate | decimal(5,2) | NOT NULL, default: 0.00 | % |
| tax_amount | decimal(12,2) | NOT NULL, default: 0.00 | Computed |
| discount_amount | decimal(12,2) | NOT NULL, default: 0.00 | |
| total_amount | decimal(12,2) | NOT NULL, default: 0.00 | subtotal + tax - discount |
| amount_paid | decimal(12,2) | NOT NULL, default: 0.00 | Sum of payments |
| status | enum | NOT NULL, default: unpaid | unpaid, partial, paid, cancelled, refunded |
| due_date | date | nullable | |
| notes | text | nullable | |
| created_by | bigint unsigned | FK users.id, nullable | |

---

### 6.12 `invoice_items`

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | bigint unsigned | PK, AI | |
| invoice_id | bigint unsigned | FK invoices.id, ON DELETE CASCADE | |
| description | varchar(255) | NOT NULL | |
| quantity | decimal(8,2) | NOT NULL, default: 1 | |
| unit_price | decimal(12,2) | NOT NULL, default: 0.00 | |
| total_price | decimal(12,2) | NOT NULL, default: 0.00 | quantity × unit_price |

---

### 6.13 `payments`

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | bigint unsigned | PK, AI | |
| invoice_id | bigint unsigned | FK invoices.id, ON DELETE RESTRICT | |
| amount_paid | decimal(12,2) | NOT NULL | |
| payment_method | enum | NOT NULL | cash, card, mobile_money, bank_transfer, insurance |
| reference_number | varchar(100) | nullable | Transaction/receipt ref |
| payment_date | date | NOT NULL | |
| paid_by | bigint unsigned | FK users.id, nullable, ON DELETE SET NULL | Staff who recorded payment |
| notes | text | nullable | |

---

### 6.14 `settings`

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | bigint unsigned | PK, AI | |
| key | varchar(100) | UNIQUE, NOT NULL | |
| value | text | nullable | |
| type | enum | NOT NULL, default: string | string, boolean, integer, file |

Default keys: `hospital_name`, `hospital_address`, `hospital_phone`, `hospital_email`, `hospital_logo`, `timezone`, `currency`, `tax_rate`, `show_notes_to_patient`.

---

### 6.15 `audit_logs`

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | bigint unsigned | PK, AI | |
| user_id | bigint unsigned | FK users.id, nullable, ON DELETE SET NULL | Who performed the action |
| action | enum | NOT NULL | created, updated, deleted, restored |
| model_type | varchar(100) | NOT NULL | Fully qualified model class |
| model_id | bigint unsigned | NOT NULL | |
| old_values | json | nullable | State before change |
| new_values | json | nullable | State after change |
| ip_address | varchar(45) | nullable | Supports IPv6 |
| user_agent | varchar(255) | nullable | |
| created_at | timestamp | | Audit logs have no updated_at — immutable |

---

### 6.16 Entity-Relationship Summary

```
users (1) ────────────── (0..1) patients
users (1) ────────────── (0..1) doctors
departments (1) ─────── (0..N) doctors
doctors (1) ─────────── (0..N) doctor_schedules
patients (1) ────────── (0..N) appointments
doctors (1) ─────────── (0..N) appointments
appointments (1) ─────── (0..1) medical_records
appointments (1) ─────── (0..1) invoices
medical_records (1) ──── (0..N) prescriptions
medical_records (1) ──── (0..1) vitals
invoices (1) ────────── (0..N) invoice_items
invoices (1) ────────── (0..N) payments
appointments (1) ─────── (0..N) appointment_logs
```

---

## 7. Technology Stack

### 7.1 Backend

| Component | Technology | Version | Purpose |
|-----------|-----------|---------|---------|
| Language | PHP | 8.2+ | Application language |
| Framework | Laravel | 11.x | MVC framework, routing, ORM, auth |
| ORM | Eloquent | (Laravel built-in) | Database abstraction |
| Auth | Laravel Breeze | 2.x | Authentication scaffolding |
| PDF | barryvdh/laravel-dompdf | 3.x | Invoice PDF generation |
| Validation | Laravel Form Requests | (built-in) | Server-side input validation |
| Mail | Laravel Mail + SMTP | (built-in) | Email notifications |

### 7.2 Frontend

| Component | Technology | Version | Purpose |
|-----------|-----------|---------|---------|
| Templates | Blade | (Laravel built-in) | Server-side rendering |
| CSS Framework | Bootstrap | 5.3 | Responsive layout, components |
| Charts | Chart.js | 4.x | Dashboard visualisations |
| Icons | Font Awesome | 6.x | UI icons |
| Date Picker | Flatpickr | 4.x | Appointment date/time selection |
| DataTables | DataTables.js | 1.13 | Sortable, searchable tables |
| Confirmation Dialogs | SweetAlert2 | 11.x | Destructive action confirmations |

### 7.3 Database and Infrastructure

| Component | Technology | Version | Purpose |
|-----------|-----------|---------|---------|
| Database | MySQL | 8.0+ | Primary data store |
| Web Server | Apache / Nginx | 2.4 / 1.24 | HTTP server |
| Session Driver | File / Database | — | Session storage |
| Cache Driver | File | — | Application cache |
| Queue Driver | Sync / Database | — | Background jobs (emails) |

### 7.4 Development Tools

| Tool | Purpose |
|------|---------|
| Composer | PHP dependency management |
| npm / Vite | Asset compilation |
| Laravel Artisan | CLI — migrations, seeders, make commands |
| Git | Version control |

### 7.5 Directory Structure (Key Directories)

```
app/
  Http/
    Controllers/
      Admin/
      Auth/
      AppointmentController.php
      PatientController.php
      DoctorController.php
      DepartmentController.php
      MedicalRecordController.php
      InvoiceController.php
      PaymentController.php
      DashboardController.php
    Middleware/
      CheckRole.php
    Requests/
  Models/
    User.php
    Patient.php
    Doctor.php
    Department.php
    DoctorSchedule.php
    Appointment.php
    AppointmentLog.php
    MedicalRecord.php
    Prescription.php
    Vital.php
    Invoice.php
    InvoiceItem.php
    Payment.php
    Setting.php
    AuditLog.php
  Services/
    AppointmentService.php
    InvoiceService.php
    AuditLogService.php
    MrnGeneratorService.php
    InvoiceNumberService.php
resources/
  views/
    layouts/
      app.blade.php
      guest.blade.php
    auth/
    dashboard/
    patients/
    doctors/
    departments/
    appointments/
    medical-records/
    invoices/
    payments/
    settings/
    reports/
database/
  migrations/
  seeders/
    DatabaseSeeder.php
    AdminSeeder.php
    DepartmentSeeder.php
    DoctorSeeder.php
routes/
  web.php
```

---

## 8. Use Case Summary

### 8.1 Administrator Use Cases

| Use Case ID | Use Case Name | Description |
|-------------|--------------|-------------|
| UC-ADM-01 | Manage User Accounts | Create, edit, suspend, and delete all system users |
| UC-ADM-02 | Configure Hospital Profile | Set hospital name, logo, address, tax rate |
| UC-ADM-03 | Manage Departments | Add, edit, and soft-delete departments |
| UC-ADM-04 | Assign Department Head | Link a doctor as department head |
| UC-ADM-05 | Generate Financial Report | View and export monthly revenue |
| UC-ADM-06 | Generate Appointment Report | View appointment statistics |
| UC-ADM-07 | View Audit Log | Review all data modification events |
| UC-ADM-08 | Reset User Password | Force-reset any user's password |
| UC-ADM-09 | Manage Doctor Schedules | Add and edit weekly availability slots |
| UC-ADM-10 | Override Appointment Status | Change any appointment's status |

### 8.2 Doctor Use Cases

| Use Case ID | Use Case Name | Description |
|-------------|--------------|-------------|
| UC-DOC-01 | View Own Schedule | See booked appointments for today/week/month |
| UC-DOC-02 | View Patient Record | Access full EMR of an assigned patient |
| UC-DOC-03 | Create Medical Record | Fill in visit notes, diagnosis, treatment plan |
| UC-DOC-04 | Add Prescription | Attach medicine entries to a medical record |
| UC-DOC-05 | Record Vitals | Enter vital signs for a patient visit |
| UC-DOC-06 | Mark Appointment Complete | Transition appointment to `completed` |
| UC-DOC-07 | Mark Patient No-Show | Transition appointment to `no_show` |
| UC-DOC-08 | Update Own Profile | Edit bio, phone number, and profile photo |

### 8.3 Receptionist Use Cases

| Use Case ID | Use Case Name | Description |
|-------------|--------------|-------------|
| UC-REC-01 | Register New Patient | Create patient profile and generate MRN |
| UC-REC-02 | Search Patient | Locate patient by MRN, name, or phone |
| UC-REC-03 | Book Appointment | Create a new appointment |
| UC-REC-04 | Check In Patient | Transition appointment to `checked_in` |
| UC-REC-05 | Reschedule Appointment | Change date/time of existing appointment |
| UC-REC-06 | Cancel Appointment | Cancel with mandatory reason |
| UC-REC-07 | View Today's Queue | Monitor live appointment queue |
| UC-REC-08 | Record Payment | Enter payment against an invoice |
| UC-REC-09 | Print Invoice PDF | Generate and download PDF invoice |
| UC-REC-10 | View Patient Profile | Read-only access to patient profile |

### 8.4 Patient Use Cases

| Use Case ID | Use Case Name | Description |
|-------------|--------------|-------------|
| UC-PAT-01 | View Own Profile | See personal and contact information |
| UC-PAT-02 | View Appointment History | See past and upcoming appointments |
| UC-PAT-03 | View Medical Records | Read-only diagnoses and prescriptions |
| UC-PAT-04 | View Invoices and Payments | See outstanding and paid invoices |
| UC-PAT-05 | Update Contact Details | Edit own phone number and address |

---

## 9. Constraints and Assumptions

### 9.1 Technical Constraints

- **C-01:** Must run on PHP 8.2, MySQL 8.0, and Composer.
- **C-02:** All business logic implemented in PHP/Laravel. No Node.js back-end.
- **C-03:** Must function without Redis. File driver used for cache, queue, and sessions.
- **C-04:** PDF generation uses DomPDF only (no wkhtmltopdf binary required).
- **C-05:** No external payment gateway integration in v1. All payment recording is manual.

### 9.2 Business Constraints

- **C-06:** Patients cannot self-book. Appointments booked by receptionist/admin only.
- **C-07:** A doctor belongs to only one department at a time.
- **C-08:** Invoice generation is automatic and triggered only by appointment completion.
- **C-09:** Single-facility deployment only. Multi-branch out of scope.

### 9.3 Regulatory Assumptions

- **A-01:** Deploying institution is responsible for compliance with Uganda's Data Protection and Privacy Act, 2019. The HMS provides access controls and audit logs to support compliance.
- **A-02:** ICD-10 codes entered as free text. No automated code lookup in v1.

### 9.4 Project Assumptions

- **A-03:** At least one administrator account seeded at deployment with documented credentials.
- **A-04:** All users access the system from devices on a stable network. Offline mode not required.
- **A-05:** Email notifications are best-effort. SMTP failures are logged but do not surface to users.
- **A-06:** System evaluated with at least 20 patients, 5 doctors, 3 departments, 30 appointments as seeded demo data.
- **A-07:** All monetary values in a single configured currency. Multi-currency not supported.
- **A-08:** UI language is English only. Internationalisation not in scope.

---

## 10. Appendix — Status Enums and Field Reference

### 10.1 Complete Enum Reference

| Table | Column | Allowed Values |
|-------|--------|---------------|
| users | role | admin, doctor, receptionist, patient |
| users | status | active, suspended |
| patients | gender | male, female, other |
| patients | blood_group | A+, A-, B+, B-, AB+, AB-, O+, O- |
| patients | marital_status | single, married, divorced, widowed |
| doctors | status | active, inactive |
| doctor_schedules | day_of_week | monday, tuesday, wednesday, thursday, friday, saturday, sunday |
| appointments | type | opd, ipd, emergency, follow_up |
| appointments | status | pending, confirmed, checked_in, in_progress, completed, cancelled, no_show |
| invoices | status | unpaid, partial, paid, cancelled, refunded |
| payments | payment_method | cash, card, mobile_money, bank_transfer, insurance |
| audit_logs | action | created, updated, deleted, restored |
| settings | type | string, boolean, integer, file |

### 10.2 Auto-Generated Identifier Formats

| Identifier | Format | Example |
|------------|--------|---------|
| MRN | MRN-YYYYMMDD-XXXX | MRN-20260509-0001 |
| Invoice Number | INV-YYYYMMDD-XXXX | INV-20260509-0001 |

XXXX is a zero-padded sequential counter that resets daily.

### 10.3 Key Relationships Summary

| Relationship | Type | Notes |
|-------------|------|-------|
| users → patients | One-to-One | Optional; patient may not have a login |
| users → doctors | One-to-One | Required; each doctor has exactly one login |
| departments → doctors | One-to-Many | A doctor belongs to one department |
| doctors → doctor_schedules | One-to-Many | One schedule row per day of week |
| patients → appointments | One-to-Many | |
| doctors → appointments | One-to-Many | |
| appointments → medical_records | One-to-One | Created on in_progress transition |
| appointments → invoices | One-to-One | Created on completed transition |
| medical_records → prescriptions | One-to-Many | |
| medical_records → vitals | One-to-One | |
| invoices → invoice_items | One-to-Many | |
| invoices → payments | One-to-Many | |

### 10.4 Service Class Responsibilities

| Service | Responsibility |
|---------|---------------|
| `MrnGeneratorService` | Generates unique MRN values with date-prefix and sequential suffix |
| `InvoiceNumberService` | Generates unique invoice numbers on appointment completion |
| `AppointmentService` | Validates slot availability, enforces status transitions, logs changes |
| `InvoiceService` | Creates invoices on completion, recomputes totals on payment, updates status |
| `AuditLogService` | Writes immutable audit entries; called from Eloquent model observers |

---

*End of Software Requirements Specification*

*Document version 1.0 — Makerere University Final Year Project — HMS — 2026-05-09*
