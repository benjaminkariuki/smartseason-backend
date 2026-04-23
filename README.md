# Smart Seasons - Backend API

The backend core for the Smart Seasons platform. This is a headless-first, RESTful API built to power modern frontend applications (like React SPAs) and future mobile clients. It manages agricultural field tracking, rigorous automated audit logs, and role-based workforce administration.

Built with an emphasis on "Integrity & Engineering," this backend prioritizes data accuracy, secure architectural patterns, and maintainable business logic.

## 🛠 Tech Stack
* **Framework:** Laravel 10
* **Database:** MySQL
* **Authentication:** Laravel Sanctum (Cookie-based SPA Authentication)
* **Architecture:** Headless REST API

---

## 🏗 Architecture & Design Decisions

### 1. Cookie-Based Sanctum Authentication
Instead of relying on standard bearer tokens stored in `localStorage` (which are vulnerable to XSS attacks), this API is configured to use Laravel Sanctum's SPA authentication pattern. It utilizes secure, HTTP-only cookies. This ensures seamless and highly secure session management between the backend and the React frontend.

### 2. Role-Based Access Control (RBAC) via Middleware
Security and data scoping are enforced at the route level using custom middleware. The system defines strict boundaries between:
* **Admin Role:** Granted global access to user management, system-wide metrics, and all field histories.
* **Field Agent Role:** Scoped strictly to read/write access for agricultural fields explicitly assigned to their `user_id`.
Routes are protected using group middleware chains (e.g., `['auth:sanctum', 'role:admin']`), ensuring intent and permissions are validated before a controller is ever reached.

### 3. Dynamic Health Status Calculation (The "At Risk" Logic)
To maintain an accurate, real-time assessment of field health without running heavy, scheduled cron jobs to update database rows, the `status` of a field is not a physical database column. Instead, it is calculated dynamically on-the-fly using a Laravel Eloquent Accessor based on business rules:
* **Completed:** The field's `current_stage` is explicitly marked as `harvested`.
* **At Risk:** The field has not been harvested, AND the `updated_at` timestamp is older than 14 days. This acts as an automated warning that an agent has not logged any observations, treatments, or notes within the acceptable operational window.
* **Active:** The field is not harvested, and an agent has actively logged an update within the last 14 days.

### 4. Automated Audit Logging (Observer Pattern)
To guarantee a 100% accurate system of record, history tracking is decoupled from the controllers. A global `FieldObserver` listens for `created` and `updated` lifecycle events. It utilizes Laravel's `isDirty()` methods to compare original attributes against new requests, automatically writing precise "old value vs. new value" logs to the `field_histories` table anytime a critical field (like `agent_id`, `current_stage`, or `notes`) is modified.

---

## 🚀 Setup Instructions

Follow these steps to configure and run the Laravel API locally.

### 1. Clone & Install
```bash
git clone https://github.com/benjaminkariuki/smartseason-backend.git
cd smart-seasons-backend

# Install PHP dependencies
composer install

cp .env.example .env

##include in env

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smart_seasons
DB_USERNAME=root
DB_PASSWORD=your_password

SANCTUM_STATEFUL_DOMAINS=localhost:5173,127.0.0.1:5173
SESSION_DOMAIN=localhost

ADMIN_NAME="System Administrator"
ADMIN_EMAIL="admin@openrails.com"
ADMIN_PASSWORD="securePassword123"
ADMIN_ROLE="admin"
FRONTEND_URL=http://localhost:5173

# Staging Agent Credentials 
AGENT_1_NAME="Sarah Jenkins"
AGENT_1_EMAIL="sarah@smartseason.com"
AGENT_1_PASSWORD="secureAgentPassword1!"

AGENT_2_NAME="David Ochieng"
AGENT_2_EMAIL="david@smartseason.com"
AGENT_2_PASSWORD="secureAgentPassword2!"

AGENT_3_NAME="Grace Mutuku"
AGENT_3_EMAIL="grace@smartseason.com"
AGENT_3_PASSWORD="secureAgentPassword3!"

php artisan key:generate
php artisan migrate --seed
php artisan serve
