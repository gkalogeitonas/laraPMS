# Project Documentation

## Overview

This document provides an overview of the project, including its requirements, architecture, and implementation details.

## Project Requirements

| Requirement                | Description                                                                                               |
|----------------------------|-----------------------------------------------------------------------------------------------------------|
| **Multi-Tenant**           | Each tenant has isolated data and cannot view or modify other tenants’ properties or bookings.           |
| **User Management**        | Secure registration and authentication, role-based/permission-based user access.                         |
| **Property & Room**        | CRUD (Create, Read, Update, Delete) for properties and rooms, with listing and filtering capabilities.    |
| **Bookings**               | Create, update, and cancel bookings, track booking statuses, and maintain a calendar view of availability.|
| **Booking Sources**        | Record the origin of bookings (website, third-party) and allow reporting by source.                       |
| **Notifications**          | Optional email or in-app alerts for booking changes or tenant invites.                                    |
| **Testing**                | Automated tests (using Pest for Laravel) covering core CRUD, authentication, and multi-tenant constraints.|

## Architecture

The project follows a modular architecture, ensuring each domain remains separate but still collaborates through well-defined interfaces.

## Implementation Details

- **Backend**: Laravel (with Laravel Breeze and Inertia)  
- **Frontend**: Vue.js  
- **Database**: MySQL  
- **Authentication**: Managed by Laravel Breeze and Inertia  
- **Testing**: Pest (backend), plus a frontend test framework of your choice  

## Entities & Relationships

1. **User**  
   - Belongs to many `Tenant`s (through `tenant_user` pivot).  
   - Each user can set an “active tenant” stored in session.

2. **Tenant**  
   - Has many `Property`s, `Room`s, `Booking`s, `BookingStatus`es, `BookingSource`s, `Customer`s.  

3. **Property**  
   - Belongs to one `Tenant`.  
   - Has many `Room`s.  

4. **Room**  
   - Belongs to one `Tenant` and one `Property`.  
   - Has many `Booking`s.  

5. **Booking**  
   - Belongs to one `Tenant`, one `Room`, one `BookingStatus`, and one `BookingSource`.  
   - (Optionally) has many `Payment`s (if implemented).  

6. **BookingStatus**  
   - Belongs to one `Tenant`.  
   - Used to define states like ‘Confirmed’, ‘Cancelled’, etc.  

7. **BookingSource**  
   - Belongs to one `Tenant`.  
   - Defines where a booking originated (website, external site, etc.).

8. **Customer**  
   - Belongs to one `Tenant`.  
   - Can have many `Booking`s if desired (currently commented in code).

9. **Payment** (optional or future expansion)  
   - Belongs to a `Booking`.  

## Setup Instructions

1. Clone the repository.  
2. Install dependencies:  
   ```sh
   composer install
   npm install
   ```
3. Set up the environment variables (copy `.env.example` to `.env` and configure as needed).
4. Run database migrations (e.g., `php artisan migrate:fresh --seed`).
5. Start the development server:
   ```sh
   php artisan serve
   ```
6. Compile front-end assets:
   ```sh
   npm run dev
   ```

## Contributing

1. Fork the repository.
2. Create a new branch for your feature or bug fix.
3. Make your changes and commit them.
4. Push your changes to your fork.
5. Create a pull request to the main repository.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.
