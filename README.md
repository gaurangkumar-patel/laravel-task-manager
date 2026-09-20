# Laravel Task Manager

A small Laravel task-management application with task CRUD, drag-and-drop priority ordering, MySQL persistence, and project-based filtering.

## Features

- Create tasks with a task name and project.
- Edit existing tasks.
- Delete tasks.
- Reorder tasks in the browser using drag and drop.
- Automatically update priority after reordering.
- Store tasks in MySQL.
- Create projects.
- Filter the task list by project so only tasks for the selected project are shown.
- Seed sample projects and tasks for quick testing.
- Feature tests for task CRUD, reordering, and project filtering.

## Tech stack

- PHP 8.3+
- Laravel 13
- MySQL 8+ or compatible MariaDB
- Blade
- Vanilla JavaScript
- CSS

No Node.js or frontend build step is required.

## Requirements

Install these before setting up the project:

- PHP 8.3 or newer
- Composer 2
- MySQL or MariaDB
- Git, if cloning from GitHub

On Windows, WAMP can be used to provide PHP, MySQL, Apache, and phpMyAdmin.

## Windows setup using WAMP

### 1. Start WAMP

Start WAMP and make sure the services are running.

For this application:

- MySQL must be running.
- Apache is useful if you want to use phpMyAdmin.
- Apache is not required when running the project with `php artisan serve`.

Open a terminal and verify that the command-line PHP version is at least PHP 8.3:

```bash
php -v
```

Also verify Composer:

```bash
composer --version
```

If `php -v` shows an older PHP version, update the PHP version used by your command line before continuing.

## Installation

### Option A: Clone from GitHub

```bash
git clone https://github.com/gaurangkumar-patel/laravel-task-manager.git
cd laravel-task-manager
```

### Option B: Use the submitted ZIP file

Extract the ZIP file, open a terminal, and change into the extracted project directory.

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Create the environment file

Windows Command Prompt:

```bat
copy .env.example .env
```

Git Bash, macOS, or Linux:

```bash
cp .env.example .env
```

PowerShell:

```powershell
Copy-Item .env.example .env
```

### 4. Generate the Laravel application key

```bash
php artisan key:generate
```

## Database setup

Create a MySQL database named:

```text
laravel_task_manager
```

### Option A: Create the database using phpMyAdmin

If using WAMP:

1. Start WAMP.
2. Make sure Apache and MySQL are running.
3. Open phpMyAdmin, normally from the WAMP menu or `http://localhost/phpmyadmin`.
4. Select **Databases**.
5. Create a database named `laravel_task_manager`.
6. Use `utf8mb4_unicode_ci` or another compatible utf8mb4 collation.

### Option B: Create the database from MySQL

Log in to MySQL and run:

```sql
CREATE DATABASE laravel_task_manager
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
```

## Configure the database connection

Open the `.env` file and set the database values.

Typical WAMP settings are:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_task_manager
DB_USERNAME=root
DB_PASSWORD=
```

If your local MySQL installation uses a different username, password, host, or port, update these values accordingly.

Do not commit the `.env` file to GitHub.

## Create database tables and sample data

Run:

```bash
php artisan migrate --seed
```

The seeder creates sample projects and tasks so the application can be tested immediately.

To completely reset the local database and recreate the sample data:

```bash
php artisan migrate:fresh --seed
```

Warning: `migrate:fresh` deletes the existing application tables and their data.

## Run the application locally

Start Laravel's local development server:

```bash
php artisan serve
```

The application will normally be available at:

```text
http://127.0.0.1:8000
```

Keep the terminal running while using the application.

## How to test the application manually

After opening the application:

1. Select a project from the project dropdown.
2. Create a new task.
3. Confirm the new task is added to the selected project.
4. Edit the task.
5. Add two or more tasks.
6. Drag tasks into a different order.
7. Confirm priority numbers update automatically.
8. Refresh the page and confirm the new order is still saved.
9. Delete a task and confirm the remaining priorities have no gaps.
10. Switch projects and confirm only tasks belonging to the selected project are displayed.

## Automated tests

Run:

```bash
php artisan test
```

The test environment uses an in-memory SQLite database, so running the automated tests does not modify the local MySQL database.

## Useful Laravel commands

Clear cached application data:

```bash
php artisan optimize:clear
```

View application routes:

```bash
php artisan route:list
```

Reset and reseed the database:

```bash
php artisan migrate:fresh --seed
```

## Production deployment

For a standard Linux/PHP deployment:

1. Upload or clone the project.
2. Point the web-server document root to the project's `public/` directory.
3. Create a production `.env` file.
4. Configure the production database.
5. Set at minimum:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.example
```

6. Install production dependencies:

```bash
composer install --no-dev --optimize-autoloader
```

7. Generate an application key if the deployment does not already have one:

```bash
php artisan key:generate
```

8. Run database migrations:

```bash
php artisan migrate --force
```

9. Cache Laravel configuration and views:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

10. Make sure the web-server user can write to:

```text
storage/
bootstrap/cache/
```

Never expose the `.env` file publicly and never point the web-server document root at the project root. It should point to the `public/` directory.

## Priority behaviour

Priority is scoped to a project.

A project's first task has priority `1`, the next has priority `2`, and so on. When tasks are reordered, the browser sends the complete task order to the reorder endpoint and the server updates priorities inside a database transaction.

When a task is deleted or moved to another project, priorities are normalised so there are no gaps.

## Author

**Gaurang Patel**

- LinkedIn: https://www.linkedin.com/in/gaurangpatel2326
- GitHub: https://github.com/gaurangkumar-patel
