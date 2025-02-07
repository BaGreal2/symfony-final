# Symfony Project - Product and User Management

This Symfony project provides a backend to manage products and users. It includes CLI commands to add products and users to the database, as well as a Postman collection for easy API testing.

## Requirements

To run this project, you need to have the following installed:

- PHP 8.0 or higher
- Composer
- Symfony CLI
- Doctrine ORM (for database interaction)
- A database system (e.g., MySQL, PostgreSQL)

## Installation

Follow these steps to set up the project:

### 1. Clone the repository

### 2. Install Dependencies

Run the following command to install all required dependencies via Composer:

```bash
composer install
```

This will install the Symfony components, Doctrine ORM, and any other required PHP packages.

### 3. Set Up the Environment

Copy the `.env.example` file to .env to set up environment variables:

```bash
cp .env.example .env
```

### 4. Configure the Database

Make sure your .env file contains the correct database connection details. For example:

```bash
DATABASE_URL="postgresql://root:password@127.0.0.1:3306/product_api"
```

Update the connection details with your actual database credentials and database name.

### 5. Create the Database

Run the following command to create the database:

```bash
php bin/console doctrine:database:create
```

### 6. Run Migrations (if applicable)

If you have existing migrations to apply, run:

```bash
php bin/console doctrine:migrations:migrate
```

This will update the database schema.

### 7. Load Doctrine Fixtures

You can load sample data (if you have fixtures) using:

```bash
php bin/console doctrine:fixtures:load
```

### 8. Start the Symfony Server

You can start the Symfony development server to access the API locally:

```bash
symfony server:start
```

The API will be available at `http://localhost:8000`.

## CLI Commands

This project includes two custom Symfony CLI commands to add products and users to the database. You can run these commands from the command line.

### 1. Add Product

You can add a product to the database by running the following command:

```bash
php bin/console app:add-product --name="Product Name" --price="100.00" --created-at="2025-01-01"
```

- --`name`: The name of the product.
- --`price`: The price of the product.
- --`created-at`: The creation date (optional, defaults to the current date).

### 2. Add User

You can add a user to the database by running the following command:

```bash
php bin/console app:add-user --email="user@example.com" --password="password123" --role="ROLE_ADMIN"
```

- `--email`: The email address of the user.
- `--password`: The password for the user.
- `--role`: The role of the user (optional, defaults to ROLE_USER).

These commands will add products and users to the database and output a success message upon completion.
Postman Collection

For testing the API endpoints, a Postman collection is provided. You can import the `ProductAPI.postman_collection.json` file into Postman and use the pre-configured requests to interact with the API.

To use the collection:

1. Open Postman.
2. Click on Import in the top-left corner.
3. Select the `ProductAPI.postman_collection.json` file.
4. Once the collection is imported, you can use the predefined API requests to test adding products, users, and other API operations.

## Running Tests

If you have PHPUnit installed, you can run the tests with:

```bash
php bin/phpunit
```
