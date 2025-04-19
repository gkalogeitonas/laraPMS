# LARAPMS

LARAPMS is a web application designed to help you manage properties, tenants, and rooms with ease. It provides a user-friendly interface for property management tasks, making it simple to keep track of your real estate assets.

## Running the Development Environment

To set up and run the development environment, follow these steps:

1. **Clone the repository**:
    ```sh
    git clone git@github.com:gkalogeitonas/laraPMS.git
    cd laraPMS
    ```

2. **Install dependencies**:
    ```sh
    composer install
    npm install
    ```

3. **Copy the example environment file and set up your environment variables**:
    ```sh
    cp .env.example .env
    ```

4. **Generate an application key**:
    ```sh
    php artisan key:generate
    ```

5. **Run the database migrations and seed the database**:
    ```sh
    php artisan migrate:fresh --seed
    ```

6. **Start the development server**:
    ```sh
    php artisan serve
    ```

7. **Compile the front-end assets**:
    ```sh
    npm run dev
    ```

Now, you can access the application at `http://localhost:8000`.



Deploy with docker

```
# Upload your Laravel project with these files
# Then SSH into your VPS
cp .env.example .env
docker-compose up -d --build

# After containers are running:
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
```

## Multi-Tenant Property Management

LARAPMS supports multiple tenants, allowing each tenant to manage their own set of properties, rooms, and bookings independently. Key features include handling bookings, booking statuses, and booking sources, while enforcing strict access control to ensure each tenant can only view or modify their own data.

## Summary of Models and Relationships

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

6. **BookingStatus**  
   - Belongs to one `Tenant`.  
   - Used to delineate states like 'Confirmed', 'Cancelled', etc.

7. **BookingSource**  
   - Belongs to one `Tenant` and defines where a booking originated (website, external site, etc.).

8. **Customer**  
   - Belongs to one `Tenant`.  
   - Potentially linked to multiple `Booking`s.

## Testing

This project uses [Pest](https://pestphp.com/) for Laravel backend tests, offering a concise and readable approach to testing.

You can run the backend tests with:

```sh
php artisan test
```

If you have any frontend tests, you can run them with:

```sh
npm test
```

## Contributing

We welcome contributions! Please read our [contributing guidelines](CONTRIBUTING.md) for more information.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
