# QuickStay Symfony Project

## Overview
QuickStay is an Airbnb-like property rental platform migrated from procedural PHP to Symfony 6.4 LTS framework. The application allows users to browse, book, and manage property rentals with comprehensive features including user authentication, property management, reservations, payments, reviews, and an admin dashboard.

## Current State
The Symfony migration is complete and validated. All core features have been successfully converted with proper best practices.

## Recent Changes (November 2025)

### Migration Completed
- Converted all procedural PHP code to Symfony 6.4 MVC architecture
- Implemented Doctrine ORM entities with proper relationships
- Created security system with dual firewalls (admin/main)
- Built comprehensive admin dashboard with statistics

### Critical Fixes Applied
1. **Repository Method Naming**: Aligned `getMostBookedProperties` with controller expectations
2. **PostgreSQL Compatibility**: Converted all MySQL-specific SQL to PostgreSQL syntax (TO_CHAR, MAKE_INTERVAL, DATE_TRUNC)
3. **Template Safety**: Added default filters and division-by-zero guards
4. **Data Contracts**: Ensured consistent array structures between repositories and templates
5. **Security**: Inline parameter substitution with int casting for SQL safety

## Project Architecture

### Directory Structure
```
quickstay22-1zip/symfony/
├── config/               # Symfony configuration
│   ├── packages/         # Package configs (doctrine, security, etc.)
│   └── routes/           # Route definitions
├── public/               # Web root
│   └── images/           # Static assets
├── src/
│   ├── Controller/       # Main and Admin controllers
│   ├── Entity/           # Doctrine entities
│   ├── Form/             # Symfony forms
│   ├── Repository/       # Doctrine repositories
│   ├── Security/         # Voters and authenticators
│   └── Service/          # Business logic services
├── templates/            # Twig templates
│   ├── admin/            # Admin panel views
│   ├── property/         # Property views
│   ├── reservation/      # Booking views
│   └── security/         # Login/register views
└── var/                  # Cache and logs
```

### Key Technologies
- **Framework**: Symfony 6.4 LTS
- **PHP Version**: 8.2
- **Database**: PostgreSQL (Neon-backed)
- **ORM**: Doctrine 2.x
- **Templates**: Twig 3.x
- **UI**: Tabler (Admin), Bootstrap 5 (Frontend)
- **Charts**: Chart.js
- **Pagination**: KnpPaginatorBundle

### Database Schema
- **users**: User accounts with roles (ROLE_USER, ROLE_HOST, ROLE_ADMIN)
- **properties**: Rental listings with categories
- **reservations**: Booking records with status tracking
- **payments**: Payment transactions
- **reviews**: Guest reviews for properties
- **categories**: Property type classifications

### Security Implementation
- Bcrypt password hashing (cost 13)
- CSRF protection on all forms
- Custom authenticators for admin/main firewalls
- Voters for fine-grained access control
- Proper SQL parameter binding

## Access Credentials

### Admin Panel
- URL: /admin
- Email: admin@quickstay.tn
- Password: admin123

### Test User
- Email: host@quickstay.tn
- Password: host123

## Running the Project

The application runs on port 5000:
```bash
cd quickstay22-1zip/symfony && php -S 0.0.0.0:5000 -t public
```

### Key Routes
- `/` - Home page with featured properties
- `/properties` - Property listings with search
- `/login` - User authentication
- `/register` - New user registration
- `/admin` - Admin dashboard (requires ROLE_ADMIN)

## Validation Report

### 1. Entities (All Complete)
- User.php: Roles, authentication, relationships
- Property.php: Full CRUD, image handling, status management
- Reservation.php: Date handling, status workflow
- Payment.php: Transaction tracking, status management
- Review.php: Rating validation, moderation support
- Category.php: Property classification

### 2. Repositories (All Functional)
- PropertyRepository: Search, filtering, statistics
- ReservationRepository: Date-based queries, status aggregation
- UserRepository: Registration stats, role queries
- PaymentRepository: Revenue calculations, monthly reports
- ReviewRepository: Rating aggregation, pending moderation

### 3. Controllers (All Operational)
- HomeController: Landing page, search
- PropertyController: CRUD, image upload
- ReservationController: Booking workflow
- SecurityController: Login, register, logout
- Admin controllers: Dashboard, CRUD operations

### 4. Templates (All Rendering)
- Base layouts with proper asset paths
- Admin panel with Tabler UI
- Frontend with Bootstrap 5
- Proper form rendering with CSRF

### 5. Security (All Configured)
- Dual firewall setup (admin/main)
- Role hierarchy implemented
- Custom authenticators working
- Voters for authorization

### 6. Database (PostgreSQL Compatible)
- All queries use PostgreSQL syntax
- Proper parameterized queries
- Migration-ready schema

## Known Limitations
- Asset management uses CDN (no Webpack Encore)
- Image storage is file-based (no cloud storage)
- No real-time notifications

## User Preferences
- Language: French/English
- Code style: PSR-12
- Database: PostgreSQL preferred
