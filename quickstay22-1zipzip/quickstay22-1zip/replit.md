# Overview

QuickStay is a property rental platform built with **Symfony 6.4** for vacation rentals in Tunisia. The platform features property listings, reservations, payments, reviews, user management, and an admin dashboard. The application is fully functional and running on a PostgreSQL database.

**Current Status**: Application is running and operational with sample data loaded.

# Recent Changes (November 2025)

- Fixed deprecated Symfony 6.2+ Security class imports
- Created all missing Twig templates (home, property, reservation, admin, security)
- Configured PostgreSQL database using Replit environment variables
- Replaced Webpack Encore with Bootstrap 5 CDN for simplicity
- Ran database migrations and loaded fixtures with sample data
- Fixed route name consistency across admin templates

# Test Accounts

- **Admin**: admin@quickstay.tn / admin123
- **Users**: user1@quickstay.tn to user5@quickstay.tn / user123

# User Preferences

Preferred communication style: Simple, everyday language.

# System Architecture

## Frontend Architecture (Next.js)

The frontend is built with **Next.js 16.0.3** using the App Router architecture and React Server Components. Key architectural decisions include:

**UI Component System**: Extensive use of **Radix UI** primitives for accessible, unstyled components that are customized with Tailwind CSS. This approach provides:
- Consistent accessibility patterns across the application
- Flexible styling through class-variance-authority for component variants
- Modular, reusable UI components (accordions, dialogs, dropdowns, forms, etc.)

**Form Management**: Integration of `react-hook-form` with `@hookform/resolvers` for robust form validation and state management, essential for property listings and reservation forms.

**Styling Strategy**: Utility-first CSS using Tailwind via `autoprefixer` and PostCSS, with `clsx` and `class-variance-authority` for dynamic class composition.

**Asset Building**: Webpack Encore is configured for the Next.js application to handle modern JavaScript transpilation and asset optimization.

**Analytics**: Vercel Analytics integration for tracking user behavior and performance metrics.

## Backend Architecture (Symfony)

The backend uses **Symfony 6.4** framework with a traditional MVC architecture:

**ORM Layer**: **Doctrine ORM 2.x** for database abstraction with entities representing core business objects:
- User (authentication and user profiles)
- Property (rental listings)
- Reservation (booking management)
- Review (user feedback)
- Payment (transaction handling)
- Category (property classification)

**Database Relationships**:
- User has many Reservations (one-to-many)
- Property has many Reservations (one-to-many)
- User owns many Properties (one-to-many via Property.owner)
- Reservation belongs to User and Property (many-to-one)
- Review belongs to User and Property (many-to-one)
- Payment has one Reservation (one-to-one)

**Controller Organization**:
- AdminController: CRUD operations for administrative tasks
- PropertyController: Property listing, search, and display
- ReservationController: Booking creation and management
- SecurityController: Authentication and authorization

**Form System**: Symfony Form component with dedicated FormType classes (PropertyType, ReservationType, UserType) for handling complex form validation and rendering.

**File Upload Management**: **VichUploaderBundle** for handling property images and user avatar uploads with automatic storage management.

**Migration Strategy**: Doctrine Migrations Bundle for version-controlled database schema changes.

**Asset Pipeline**: Symfony Webpack Encore for frontend asset compilation, configured to output to `/public/build/` directory.

**Development Tools**:
- Doctrine Fixtures Bundle for seeding test data
- Symfony Profiler and Debug Bundle for development debugging
- Maker Bundle for code generation scaffolding

## Data Storage

**Primary Database**: The application is configured to use a relational database through Doctrine DBAL. While the specific database system isn't explicitly defined in the configuration, Doctrine supports multiple RDBMS options (MySQL/MariaDB, PostgreSQL, SQLite).

**File Storage**: Uploaded files (property images, user avatars) are stored in the `/public/uploads` directory, managed by VichUploaderBundle.

**Metadata Storage**: Doctrine migrations table tracks applied database migrations for version control.

## Authentication and Authorization

**Security Bundle**: Symfony Security component handles:
- User authentication via SecurityController
- Session management
- Password hashing and validation
- Access control rules

**Voters**: Custom Security Voters (if implemented) for fine-grained authorization decisions on entities like Property and Reservation.

## Design Patterns

**Repository Pattern**: Each entity has a corresponding Repository class for encapsulating database query logic, promoting separation between business logic and data access.

**Event System**: Doctrine Event Manager for lifecycle hooks and custom event handling during entity persistence operations.

**Pagination**: KnpPaginatorBundle provides standardized pagination for property listings and search results, with customizable templates.

**Templating**: Twig templating engine for server-side rendering with:
- Base layout template (base.html.twig)
- Modular template inheritance
- Component-based template organization

# External Dependencies

## Third-Party Services

**Email**: Symfony Mailer component configured for transactional emails (reservation confirmations, notifications).

**HTTP Client**: Symfony HTTP Client for potential external API integrations.

**Logging**: Monolog Bundle for application logging and error tracking.

## Frontend Libraries

- **Radix UI**: Complete set of accessible UI primitives (30+ components)
- **Lucide React**: Icon library (v0.454.0)
- **date-fns**: Date manipulation and formatting
- **cmdk**: Command palette component
- **embla-carousel-react**: Carousel/slider functionality for property images
- **next-themes**: Dark/light theme support

## Backend Libraries

- **Doctrine**: Complete ORM stack (DBAL, ORM, Migrations, Fixtures)
- **KnpPaginatorBundle**: Pagination abstraction layer
- **VichUploaderBundle**: File upload handling
- **Twig**: Templating engine with internationalization extensions
- **JMS Metadata**: Metadata management for object mapping
- **Egulias Email Validator**: Email validation library

## Development Dependencies

- **Babel**: JavaScript transpilation
- **PHPStan**: Static analysis for PHP code
- **PHPUnit**: Testing framework for backend
- **Doctrine Coding Standard**: Code style enforcement
- **Symfony Debug Tools**: Web Profiler, Debug Bundle, Stopwatch