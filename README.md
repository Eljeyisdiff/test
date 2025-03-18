# NU Queuest

NU Queuest is a queue management system designed to streamline the process of managing queues in various offices. This system includes functionalities for both administrators and users.

## Table of Contents

- [Project Structure](#project-structure)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [API Endpoints](#api-endpoints)
- [Contributing](#contributing)
- [License](#license)

## Project Structure

The project is organized into the following directories:

### Directories

- **admin/**: Contains the admin dashboard and views for managing offices and employees.
- **api/**: Contains API endpoints for various functionalities such as adding employees, offices, and handling queue operations.
- **assets/**: Contains static assets like images and icons.
- **auth/**: Contains authentication-related files and session management.
- **config/**: Contains configuration files, including database connection settings.
- **css/**: Contains CSS files for styling the application.
- **employee/**: Contains views and functionalities for employee-specific operations.
- **js/**: Contains JavaScript files for client-side functionalities.
- **public/**: Contains public-facing views.
- **user/**: Contains user-specific views and functionalities.
- **windows/**: Contains files related to window management in offices.

### Key Files

- **index.php**: The main entry point of the application.
- **README.md**: This file.
- **sqldump.sql**: SQL dump file for setting up the database.
- **latest_sql.sql**: Latest SQL dump file for setting up the database.
- **notes.txt**: Contains miscellaneous notes.

## Installation

To set up the project locally, follow these steps:

1. Clone the repository:
    ```sh
    git clone https://github.com/yourusername/nu_queuest.git
    cd nu_queuest
    ```

2. Set up the database:
    - Import the `nu_queuest.sql` file into your MySQL database.

3. Configure the database connection:
    - Update the database connection settings in `config/connection.php`.

4. Start the server:
    - Use a local server environment like XAMPP or WAMP to serve the project.

## Configuration

Ensure that the database connection settings in `config/connection.php` are correctly configured to match your local database setup.

## Usage

### Admin

- **Login**: Admins can log in via the `auth/loginadmin.php` page.
- **Dashboard**: Admins can view the dashboard at `admin/admin_dashboard.php`.
- **Manage Offices**: Admins can manage offices via `admin/admin_officesview.php`.
- **Manage Employees**: Admins can manage employees via `admin/admin_usersview.php`.

### User

- **Queue Status**: Users can view their queue status at `user/queue_status.php`.
- **Join Queue**: Users can join a queue via `user/join_queue.php`.

## API Endpoints

### Admin API

- **Login**: `api/admin_login_api.php`
- **Add Office**: `api/add_office.php`
- **Add Employee**: `api/add_employee.php`

### User API

- **Check Ticket Status**: `api/check_ticket_status.php`
- **Cancel Ticket**: `api/cancel_ticket.php`

## Contributing

Contributions are welcome! Please fork the repository and create a pull request with your changes.
