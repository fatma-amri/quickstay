# QuickStay - Symfony Rental Property Platform

## Overview
QuickStay is a complete rental property booking platform built with Symfony 6.4, similar to Airbnb. It allows users to browse properties, make reservations, and leave reviews.

## Project Structure
```
quickstay22-1zipzip/quickstay22-1zip/symfony/
  src/
    Controller/     - Route controllers (HomeController, PropertyController, etc.)
    Entity/         - Doctrine entities (User, Property, Reservation, Review, Payment, Category)
    Form/           - Form types
    Repository/     - Doctrine repositories
    Security/       - Authentication handlers
    DataFixtures/   - Test data fixtures
  templates/        - Twig templates
  config/           - Symfony configuration
  public/           - Web root
```

## Entities
- **User**: Users with authentication (admin/user roles)
- **Category**: Property categories (Apartments, Houses, Villas, Studios)
- **Property**: Rental properties with details, pricing, amenities
- **Reservation**: Bookings with dates, guests, pricing
- **Review**: User reviews for properties (with detailed ratings)
- **Payment**: Payment records for reservations

## Test Accounts
- **Admin**: admin@quickstay.tn / admin123
- **Users**: user1@quickstay.tn to user5@quickstay.tn / user123

## Key URLs
- Homepage: /
- Properties: /properties
- Login: /login
- Register: /register
- Admin: /admin/login

## Database
Using SQLite database (var/data.db) for development.

## Running the Application
The development server runs on port 5000:
```bash
cd quickstay22-1zipzip/quickstay22-1zip/symfony
php -S 0.0.0.0:5000 -t public
```

## Recent Changes
- 2025-12-04: Blocked admin users from making reservations (admins manage, don't book)
- 2025-12-03: Initial setup with SQLite database, fixtures loaded with test data

## Access Restrictions
- **Admins**: Can manage properties, users, and view all reservations in admin panel, but CANNOT make reservations
- **Regular Users**: Can browse properties, make reservations, and leave reviews
