# Laravel Task Manager

A small task-management web application built for the coding exercise. It uses Laravel 11, PHP 8.3+, Blade, native browser drag-and-drop and MySQL.

## Features

- Create tasks with a name, project and automatic priority.
- Edit tasks and move them between projects.
- Delete tasks and automatically close priority gaps.
- Reorder tasks in the browser by drag-and-drop; priorities are saved immediately.
- Store tasks and projects in MySQL.
- Filter the task list by project.
- Create simple projects from the main screen.
- Feature tests for CRUD, reordering and project filtering.

## Requirements

- PHP 8.3 or newer
- Composer 2
- MySQL 8+ (or a compatible MySQL/MariaDB server)

No Node.js or frontend build step is required.

## Local setup

1. Extract the project and open a terminal in the project directory.
2. Install PHP dependencies:

   ```bash
   composer install
   ```

3. Create the environment file:

   ```bash
   cp .env.example .env
   ```

   On Windows PowerShell:

   ```powershell
   Copy-Item .env.example .env
   ```

4. Generate the application key:

   ```bash
   php artisan key:generate
   ```

5. Create a MySQL database named `laravel_task_manager` (or choose another name) and update these values in `.env`.

6. Run:

   ```bash
   php artisan migrate --seed
   php artisan serve
   ```

7. Open `http://127.0.0.1:8000`.

## Tests

```bash
php artisan test
```

## Deployment

Point the web server document root to the project's `public/` directory, configure production environment variables, install Composer dependencies with `--no-dev`, then run migrations and Laravel cache commands.

## Implementation notes

Priority is scoped to a project. A project's first task has priority `1`, the next `2`, and so on. Dragging a task sends the complete order to a dedicated reorder endpoint, which updates priorities inside a database transaction. When a task is deleted or moved to another project, priorities are normalised so there are no gaps.
