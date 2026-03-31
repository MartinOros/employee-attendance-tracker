# Employee Attendance Tracker

QR Code-based Employee Attendance Tracker — open-source web app built with PHP and MySQL.

## Description

Employees check in and out by scanning their unique QR codes. Supervisors have a dedicated admin dashboard to monitor and manage attendance records in real-time.

## Key Features

- **QR Code Check-In/Check-Out** — employees scan their personalized QR codes via any device camera
- **Supervisor Dashboard** — admin area to view and manage all attendance records
- **Attendance Editing** — supervisors can manually adjust check-in/check-out times
- **Excel Export** — download attendance data as `.xlsx` for payroll and HR reporting
- **QR Code Generator** — generate employee QR codes directly from the admin area

## Requirements

- PHP 8.0+
- MySQL 5.7+ / MariaDB
- Web server (Apache, Nginx, or PHP built-in server)
- Composer (for dependencies)

## Installation

**1. Clone the repository**

```bash
git clone https://github.com/mansuroguslu/employee-attendance-tracker.git
cd employee-attendance-tracker
```

**2. Install PHP dependencies**

```bash
composer install
```

**3. Start MySQL**

```bash
mysql.server start
```

**4. Start the PHP development server**

```bash
php -S localhost:8080
```

**5. Run the installer**

Open [http://localhost:8080/install/install.php](http://localhost:8080/install/install.php) and fill in:

| Field | Default value |
|-------|--------------|
| Database Host | `localhost` |
| Database Username | `root` |
| Database Password | *(leave empty for local)* |
| Database Name | `mewdev_eat` |
| Admin Username | `admin` |
| Admin Password | *(your choice)* |
| Employee Username | `employee` |
| Employee Password | *(your choice)* |

This creates the database, tables, users, and `db_connection.php` automatically.

## Usage

| Area | URL | Credentials |
|------|-----|-------------|
| Employee App | [http://localhost:8080](http://localhost:8080) | employee login |
| Admin Login | [http://localhost:8080/admin/login.php](http://localhost:8080/admin/login.php) | admin login |
| Admin Dashboard | [http://localhost:8080/admin](http://localhost:8080/admin) | *(after admin login)* |

> **Note:** The employee app and admin area use separate login systems. Use `/admin/login.php` to access the admin dashboard.

## Video Tutorial

[Watch on YouTube](https://www.youtube.com/watch?v=jQKrtat3JKs)

## Documentation

[Full Documentation](https://oguslu.com/oss-employee-attendance-tracker-0)

## Changelog

- **v0.1.1** (2023-08-02) — QR Code Generator added to Admin Area

## Contributing

Contributions are welcome! See [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

## Issues

Report bugs in the [Issue Tracker](https://github.com/mansuroguslu/employee_attendance_tracker/issues).

## License

GNU General Public License — see [LICENSE](LICENSE) for details.

## Developer

Developed and maintained by [Mansur Oguslu](https://github.com/mansuroguslu).
