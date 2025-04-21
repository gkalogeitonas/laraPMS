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

## Database Structure

### Tables & Schema

1. **tenants**
   - `id` - Primary key
   - `name` - Tenant name
   - `timestamps` - Created/updated timestamps

2. **users**
   - `id` - Primary key
   - `name` - User's name
   - `email` - Unique email address
   - `email_verified_at` - Nullable timestamp
   - `password` - Hashed password
   - `remember_token` - Remember me token
   - `timestamps` - Created/updated timestamps

3. **tenant_user** (pivot)
   - `id` - Primary key
   - `tenant_id` - Foreign key to tenants
   - `user_id` - Foreign key to users
   - `timestamps` - Created/updated timestamps

4. **properties**
   - `id` - Primary key
   - `tenant_id` - Foreign key to tenants (with cascade delete)
   - `name` - Property name
   - `address` - Property address
   - `description` - Nullable property description
   - `type` - Property type
   - `timestamps` - Created/updated timestamps

5. **rooms**
   - `id` - Primary key
   - `property_id` - Foreign key to properties (with cascade delete)
   - `tenant_id` - Foreign key to tenants (with cascade delete)
   - `name` - Room name
   - `description` - Nullable room description
   - `type` - Room type
   - `timestamps` - Created/updated timestamps

6. **customers**
   - `id` - Primary key
   - `tenant_id` - Foreign key to tenants (with cascade delete)
   - `name` - Customer name
   - `email` - Nullable, unique email address
   - `phone` - Nullable phone number
   - `address` - Nullable address
   - `timestamps` - Created/updated timestamps

7. **booking_statuses**
   - `id` - Primary key
   - `tenant_id` - Foreign key to tenants
   - `name` - Status name
   - `color` - Nullable color code
   - `timestamps` - Created/updated timestamps

8. **booking_sources**
   - `id` - Primary key
   - `tenant_id` - Foreign key to tenants
   - `name` - Source name
   - `timestamps` - Created/updated timestamps

9. **bookings**
   - `id` - Primary key
   - `room_id` - Foreign key to rooms (with cascade delete)
   - `tenant_id` - Foreign key to tenants (with cascade delete)
   - `customer_id` - Nullable foreign key to customers
   - `name` - Guest name
   - `email` - Nullable guest email
   - `phone` - Nullable guest phone
   - `booking_source_id` - Nullable foreign key to booking sources
   - `check_in` - Arrival date
   - `check_out` - Departure date
   - `total_guests` - Number of guests
   - `price` - Booking price (decimal)
   - `booking_status_id` - Nullable foreign key to booking statuses
   - `timestamps` - Created/updated timestamps

10. **password_reset_tokens** (Laravel default)
    - `email` - Primary key
    - `token` - Reset token
    - `created_at` - Nullable creation timestamp

11. **personal_access_tokens** (Laravel Sanctum)
    - `id` - Primary key
    - `tokenable_type`, `tokenable_id` - Polymorphic relation
    - `name` - Token name
    - `token` - Unique hashed token
    - `abilities` - Nullable JSON permissions
    - `last_used_at` - Nullable timestamp
    - `expires_at` - Nullable expiration timestamp
    - `timestamps` - Created/updated timestamps

12. **failed_jobs** (Laravel default)
    - `id` - Primary key
    - `uuid` - Unique identifier
    - `connection` - Queue connection
    - `queue` - Queue name
    - `payload` - Job payload
    - `exception` - Exception information
    - `failed_at` - Failure timestamp

### Key Relationships

- **Multi-tenant isolation** is enforced through the `tenant_id` foreign key on most tables
- **User-tenant** relationship is a many-to-many through the `tenant_user` pivot table
- **Property-room** is a one-to-many relationship 
- **Room-booking** is a one-to-many relationship
- **All core entities** (properties, rooms, bookings, etc.) belong to a tenant

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
