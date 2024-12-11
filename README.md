# Laravel Monolithic Authentication and Authorization Application

This Laravel application provides a simple authentication and authorization application. The API is structured using versioning (`v1`) for easy scalability.

---

## Features
- **Authentication**:
  - User Registration
  - Login
  - Forgot Password
  - Reset Password
  - Logout
- **Role and Permission Management**:
  - Create, Read, Update, and Delete Roles
  - Assign and Remove Permissions from Roles
  - Manage Role Permissions
  - Add, View, Update, and Delete Permissions
- **Middleware**:
  - Secures routes using `auth:api` and role-based access control (RBAC)
- **Database Seeders**:
  - `PermissionsSeeder`, `RolesSeeder`, and `RolePermissionSeeder` for initial setup.
  - initial roles are [`admin`, `user`]
  - Role and Permission Management is been performed by the `admin` role and also has the priviledge to assign permissions to other roles.

---

## Setup Instructions

### Prerequisites
- PHP >= 8.1
- Composer
- Laravel >= 10.x
- MySQL

### 1. Clone the Repository
```bash
git clone https://github.com/pc-ezema/Software-Developer-Assessment-Promise-Ezema.git <project-directory>
cd <project-directory>

### 2. Install Dependencies
composer install

### 3. Copy .env Example
cp .env.example .env

### 4. Configure Environment Settings
Open the .env file and configure the following:
Set up your mail server settings (e.g., MAIL_MAILER, MAIL_HOST, etc.).
Configure the database connection (e.g., DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD).

### 5. Run Migrations and Seeders
php artisan migrate --seed

### 6. Generate JWT Secret
php artisan jwt:secret

### 7. Start the Application
php artisan serve

### 8. Postman Documentation
A Postman collection for the project is included inside the project directory. You can import this collection into Postman to easily test the API endpoints. Please find the Postman collection file in the project and import it into your Postman for API testing.
