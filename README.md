# Freelance Time Tracker API

A Laravel 12-based RESTful API for freelancers to log and manage their work time across clients and projects.

## 🧰 Tech Stack

- Laravel 12+
- PHP 8.2+
- Sanctum (API authentication)
- Eloquent ORM
- MySQL
- Seeders & Factories
- Postman / Apidog for testing

---

## 🚀 Setup Instructions

Follow these steps to set up and run the project locally:

### 1. Clone the Repository

- `git clone https://github.com/Jobayer53/freelanceTracker.git
- `cd freelanceTracker
- `composer install
- `cp .env.example .env
- `php artisan key:generate
- `php artisan serve

## ⚙️ Features

### 🧑‍💼 Authentication (via Sanctum)
- Register
- Login
- Logout

### 👥 Clients
- Create, Read, Update, Delete clients
- Fields: `name`, `email`, `contact_person`

### 📁 Projects
- Belong to a client
- Fields: `client_id`,`user_id`,`title`, `description`, `status` (`active`, `completed`), `deadline`

### ⏱️ Time Logs
- Belong to a project
- Fields: `project_id`, `start_time`, `end_time`, `description`, `hours` (auto-calculated)
- Start/End time logs (real-time)
- Manual entry support
- View logs per day/week
- Filter logs by client/project/date

### 📊 Reports
- API: `GET /api/report?client_id=1&from=YYYY-MM-DD&to=YYYY-MM-DD`
- Returns total hours logged:
  - Per project
  - Per day
  - Per client

---

## 🔐 Authentication

All protected routes require an `Authorization` header:
Tokens are issued on successful login or registration.

---

## 📁 API Endpoints

### Auth
- `POST /api/register`
- `POST /api/login`
- `POST /api/logout`

### Clients
- `GET /api/clients`
- `POST /api/clients`
- `PUT /api/clients/{id}`
- `DELETE /api/clients/{id}`

### Projects
- `GET /api/projects`
- `POST /api/projects`
- `PUT /api/projects/{id}`
- `DELETE /api/projects/{id}`

### Time Logs
- `GET /api/timelogs`
- `POST /api/timelogs/start` – Start time log
- `POST /api/timelogs/end` – End active log
- `POST /api/timelogs/manual` – Add/edit manual entry
- `GET /api/report?client_id=&from=&to=` – Filter logs

---

## 🧪 Seeders

The database comes pre-seeded with:

- 1 User (freelancer)
- 2 Clients
- 2 Projects (linked to clients and user)
- 5+ Time Logs

To seed the database:
```bash
php artisan migrate:fresh --seed
