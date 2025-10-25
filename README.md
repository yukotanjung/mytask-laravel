# Laravel My Task

This project is a RESTful API built using the Laravel framework, with a MongoDB database and a token-based authentication system powered by Laravel Sanctum.

---

## Installation

Follow these steps to set up the project on your local machine for development.

### Prerequisites
- [PHP](https://www.php.net/downloads.php) (version 8.2 or higher)
- [Composer](https://getcomposer.org/download/)
- [MongoDB](https://www.mongodb.com/try/download/community) installed and running locally.
- PHP MongoDB Driver (`pecl install mongodb`)

### Installation Steps

1.  **Clone the Repository**
    ```bash
    git clone https://github.com/yukotanjung/mytask-laravel
    cd mytask-laravel
    ```

2.  **Create Environment File**
    Copy the example environment file. This file will hold your local configuration.
    ```bash
    cp .env.example .env
    ```

3.  **Configure `.env` File**
    Open the `.env` file and update the database connection details to point to your local MongoDB instance.
    ```env
    DB_CONNECTION=mongodb
    DB_HOST=127.0.0.1
    DB_PORT=27017
    DB_DATABASE=mileapp
    DB_USERNAME=
    DB_PASSWORD=
    ```

4.  **Install Composer Dependencies**
    This command will download and install all the required PHP packages for the project.
    ```bash
    composer install
    ```

5.  **Generate Application Key**
    Laravel requires an application key for securing session data and other encrypted values.
    ```bash
    php artisan key:generate
    ```

6.  **Run the Development Server**
    Start the local development server.
    ```bash
    php artisan serve
    ```

7.  **Done!**
    Your API is now running and accessible at `http://127.0.0.1:8000`.

---

## Project Overview & Design Decisions



*   **What was built**: A scalable REST API foundation. It handles user authentication and provides a starting point for building out more complex features, such as the included Task management functionality.

*   **Design Decisions**:
    *   **Laravel Framework**: Chosen for its mature ecosystem, elegant syntax, and comprehensive features (Eloquent ORM, routing, middleware). 
    *   **MongoDB Database**: As a NoSQL database, MongoDB offers high schema flexibility. 
    *   **Laravel Sanctum for Authentication**: Sanctum provides a lightweight, token-based authentication system perfect for SPAs (Single Page Applications), mobile apps, and other token-based APIs. It is simpler to configure than Laravel Passport (OAuth2) and is ideal when a full OAuth2 server is not required.

---

## Strengths of the Project

*   **Performance**: By leveraging appropriate database indexes, queries to MongoDB remain fast and efficient, even with large datasets. This ensures a responsive API.
*   **Rapid Development**: The combination of Laravel's powerful features and a flexible database schema allows developers to build and iterate on features quickly.
*   **Modern & Secure**: The API is secured with Laravel Sanctum, a modern standard for stateless API authentication, protecting endpoints from unauthorized access.

---

## Database Indexes

1.  **`users` collection (on `email` field)**
    -   **Type**: Unique Index.
    -   **Reasoning**:
        -   **Performance**: speeds up user lookups by email.

2.  **`personal_access_tokens` collection (on `tokenable_type` and `tokenable_id`)**
    -   **Type**: Compound Index.
    -   **Reasoning**: This index is used directly by Laravel Sanctum. When an incoming request includes an API token, Sanctum needs to efficiently find which user the token belongs to.

3.  **`tasks` collection (on `title` field)**
    -   **Type**: Single Index.
    -   **Reasoning**: This index was created to accelerate any queries that involve searching, filtering, or sorting tasks by their title. If the application has a feature allowing users to search for a task by its name, this index will ensure the search is performant.
