# QuickStay - Airbnb-like Property Rental Platform

A complete Symfony 6.4 LTS application for managing property rentals with user authentication, reservations, payments, reviews, and a comprehensive admin dashboard.

## 📋 Table of Contents
- [Prerequisites](#prerequisites)
- [Database Setup](#database-setup)
- [Installation](#installation)
- [Configuration](#configuration)
- [Running the Application](#running-the-application)
- [Admin Credentials](#admin-credentials)
- [Project Structure](#project-structure)
- [Features](#features)

## Prerequisites

Before you start, ensure you have the following installed on your laptop:

- **PHP 8.2 or higher** - [Download](https://www.php.net/downloads)
- **Composer** - [Download](https://getcomposer.org/download/)
- **MySQL 5.7+ or MariaDB** - [Download](https://www.mysql.com/downloads/)
- **PhpMyAdmin** - [Installation Guide](https://www.phpmyadmin.net/downloads/)
- **Git** - [Download](https://git-scm.com/downloads)

### Optional but Recommended
- **Visual Studio Code** - For code editing
- **PHP 8.2 via XAMPP/WAMP** - All-in-one PHP development environment

## Database Setup

### Step 1: Install and Start MySQL

**On Windows (XAMPP):**
1. Download XAMPP from [apachefriends.org](https://www.apachefriends.org/)
2. Install and run XAMPP Control Panel
3. Click "Start" next to MySQL and Apache

**On macOS:**
```bash
brew install mysql
brew services start mysql
```

**On Linux:**
```bash
sudo apt-get install mysql-server
sudo service mysql start
```

### Step 2: Start PhpMyAdmin

**Option A: XAMPP (Windows/Mac)**
- Run XAMPP Control Panel
- Click "Admin" button next to MySQL
- PhpMyAdmin opens automatically at `http://localhost/phpmyadmin`

**Option B: Standalone Installation**
- Access PhpMyAdmin at `http://localhost:8080/phpmyadmin` (default port may vary)

### Step 3: Create the Database

1. Open PhpMyAdmin: `http://localhost/phpmyadmin`
2. Log in (default: username `root`, no password)
3. Click the **SQL** tab
4. Paste this command:

```sql
CREATE DATABASE quickstay CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

5. Click **Go**

### Step 4: Verify Database Creation

1. In the left sidebar, refresh and verify `quickstay` database appears
2. Click on `quickstay` to select it

## Installation

### Step 1: Clone or Extract the Project

```bash
# If you have a git repository
git clone <your-repository-url>
cd quickstay22-1zip/symfony

# Or if you extracted a ZIP file
cd /path/to/quickstay22-1zip/symfony
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

This downloads all required PHP packages including Symfony framework.

### Step 3: Create Environment File

```bash
# Copy the example environment file
cp .env.example .env
# Or create it manually
```

### Step 4: Configure Database Connection

Open `.env` file and update the database URL:

**Find this line:**
```
DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=8.0.32&charset=utf8mb4"
```

**Replace with:**
```
DATABASE_URL="mysql://root@127.0.0.1:3306/quickstay?serverVersion=5.7&charset=utf8mb4"
```

**Note:** If your MySQL has a password, use:
```
DATABASE_URL="mysql://root:yourpassword@127.0.0.1:3306/quickstay?serverVersion=5.7&charset=utf8mb4"
```

### Step 5: Set Application Secret

Generate a unique secret key:

```bash
# On Windows (PowerShell)
php -r "echo bin2hex(random_bytes(32));"

# On Mac/Linux
php -r 'echo bin2hex(random_bytes(32));'
```

Copy the output and update `.env`:

```
APP_SECRET=<paste-your-generated-key-here>
```

## Configuration

### Step 1: Create Database Schema

Run migrations to create all tables:

```bash
php bin/console doctrine:migrations:migrate
```

When prompted, type `yes` and press Enter.

### Step 2: Verify Tables in PhpMyAdmin

1. Open PhpMyAdmin
2. Select the `quickstay` database
3. Verify these tables exist:
   - users
   - properties
   - categories
   - reservations
   - payments
   - reviews

### Step 3: Load Sample Data (Optional)

```bash
php bin/console doctrine:fixtures:load
```

This creates test users and properties. When prompted, confirm with `yes`.

## Running the Application

### Start the Development Server

```bash
# From the symfony directory
php -S localhost:8000 -t public
```

The application runs at: **http://localhost:8000**

### Keeping the Server Running

Keep this terminal window open and don't close it while developing.

### Open in Browser

1. Go to: `http://localhost:8000`
2. You should see the QuickStay homepage

## Admin Credentials

### Admin Panel Access

**URL:** `http://localhost:8000/admin`

**Username (Email):** `admin@quickstay.tn`
**Password:** `admin123`

### Test User Credentials

**URL:** `http://localhost:8000/login`

**Email:** `host@quickstay.tn`
**Password:** `host123`

## Database Management

### PhpMyAdmin Operations

**View Data:**
1. Open PhpMyAdmin: `http://localhost/phpmyadmin`
2. Select `quickstay` database
3. Click table names to view/edit data

**Export Database:**
1. Right-click `quickstay` database
2. Click "Export"
3. Click "Go" to download SQL file

**Reset Database:**
```bash
# Drop all tables and recreate
php bin/console doctrine:schema:drop --force
php bin/console doctrine:migrations:migrate
```

### Backup Your Database

**Using PhpMyAdmin:**
1. Select `quickstay` database
2. Go to "Export" tab
3. Choose "Quick" mode
4. Click "Go"
5. Save the `.sql` file

**Using Command Line:**
```bash
mysqldump -u root quickstay > quickstay_backup.sql
```

## Project Structure

```
symfony/
├── public/                  # Web root
│   ├── index.php           # Main entry point
│   └── images/             # Static images
├── src/
│   ├── Controller/         # Application controllers
│   ├── Entity/             # Database entities
│   ├── Form/               # Symfony forms
│   ├── Repository/         # Database queries
│   ├── Security/           # Authentication
│   └── Service/            # Business logic
├── templates/              # Twig templates
│   ├── admin/              # Admin panel views
│   ├── property/           # Property pages
│   ├── reservation/        # Booking pages
│   ├── profile/            # User profile
│   └── security/           # Login/Register
├── config/                 # Configuration files
├── migrations/             # Database migrations
└── .env                    # Environment variables
```

## Features

### User Features
- ✅ User registration and authentication
- ✅ User profile management
- ✅ View available properties
- ✅ Book properties with date selection
- ✅ View reservation history
- ✅ Leave reviews and ratings
- ✅ Manage profile avatar

### Host Features
- ✅ List properties for rent
- ✅ Manage property details
- ✅ View reservations
- ✅ Respond to reviews

### Admin Features
- ✅ Dashboard with statistics
- ✅ Manage all users
- ✅ Manage all properties
- ✅ Manage categories
- ✅ Review and approve reservations
- ✅ View and moderate reviews
- ✅ System statistics and charts

## Troubleshooting

### "Connection refused" Error
**Problem:** Cannot connect to database
**Solution:**
1. Ensure MySQL is running: `mysql -u root -p`
2. Check `.env` DATABASE_URL is correct
3. Verify port 3306 is not blocked by firewall

### "Table doesn't exist" Error
**Problem:** Database tables not created
**Solution:**
```bash
php bin/console doctrine:migrations:migrate
```

### Port Already in Use
**Problem:** Port 8000 is already in use
**Solution:**
```bash
# Use a different port
php -S localhost:8001 -t public
```

### PhpMyAdmin Access Denied
**Problem:** Cannot login to PhpMyAdmin
**Solution:**
1. XAMPP: Use username `root` with no password
2. Standalone: Check your MySQL user credentials
3. Reset MySQL password if forgotten

## Security Notes

⚠️ **Important for Production:**
- Change `.env` APP_SECRET to a unique value
- Use strong admin passwords
- Enable HTTPS
- Keep Symfony and dependencies updated
- Never commit `.env` file to version control

## Support & Documentation

- **Symfony Docs:** https://symfony.com/doc/current/index.html
- **Doctrine ORM:** https://www.doctrine-project.org/
- **MySQL Guide:** https://dev.mysql.com/doc/
- **PhpMyAdmin:** https://www.phpmyadmin.net/docs/

## License

This project is part of QuickStay platform.
