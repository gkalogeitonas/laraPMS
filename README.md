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

## Contributing

We welcome contributions! Please read our [contributing guidelines](CONTRIBUTING.md) for more information.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
