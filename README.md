# Car Rental Management System

## Description

The Car Rental Management System is a web-based application designed to simplify the management of vehicle rental operations. Developed using PHP, MySQL, HTML, CSS, and JavaScript, the system provides an efficient platform for managing cars, customers, and rental transactions.

The application supports two types of users: Administrators and Standard Users. Administrators have full control over the system, including managing vehicles, rentals, and customer information, while users can browse available cars and make reservations.

The system aims to automate the rental process by reducing manual work, improving data organization, and ensuring better management of vehicle availability.

---

## Features

### User Authentication

* User registration.
* User login and logout.
* Session management.
* Role-based access control.
* Administrator and User accounts.

### Car Management

* Add new vehicles.
* Edit vehicle information.
* Delete vehicles.
* Upload and manage vehicle photos.
* View detailed car information.
* Manage vehicle availability and status.
* Store vehicle specifications such as:

  * Brand
  * Model
  * Year
  * License Plate
  * Price Per Day
  * Number of Seats
  * Transmission Type
  * Fuel Type
  * Description

### Rental Management

* Create new rentals.
* Select an existing customer or create a new customer.
* Calculate rental cost based on rental duration.
* Assign vehicles to customers.
* View all rental records.
* Update rental status.
* Cancel rentals.
* Track active rentals.

### Customer Management

* Register new customers.
* Store customer information:

  * First Name
  * Last Name
  * Email
  * Phone Number
  * Address
  * Driver License Number
* Reuse existing customer information during future rentals.

### Dashboard

* Overview of system activity.
* Quick access to management functions.
* Statistics and summaries for administrators.

### User Interface

* Responsive navigation bar.
* Modern and intuitive design.
* Organized forms and tables.
* User-friendly experience.

---

## Technologies Used

### Frontend

* HTML5
* CSS3
* JavaScript

### Backend

* PHP

### Database

* MySQL

### Development Environment

* XAMPP

### Version Control

* Git
* GitHub

---

## Database

The application uses MySQL to store and manage data.

Main tables include:

* Users
* Customers
* Cars
* Rentals
* Brands

The database handles relationships between customers, vehicles, and rental transactions.

---

## Project Structure

```text
Car-Rent-project/
│
├── addcar.php
├── addcar_action.php
├── addrental.php
├── allcars.php
├── allrentals.php
├── authForm.php
├── cancel_rental.php
├── checkAuth.php
├── checksession.php
├── dashboard.php
├── dbconnection.php
├── deletecar.php
├── editcar.php
├── editstatusrentals.php
├── icon_helper.php
├── index.php
├── logout.php
├── navbar.php
├── saverental.php
├── showcar.php
├── signupForm.php
├── updatecar.php
├── photos/
├── cssfiles/
└── Database.sql
```

---

## Installation

### Prerequisites

* XAMPP
* PHP 8.x
* MySQL
* Web Browser

### Setup Instructions

1. Clone the repository:

```bash
git clone <repository-url>
```

2. Move the project folder to:

```text
C:\xampp\htdocs\
```

3. Start Apache and MySQL using XAMPP.

4. Open phpMyAdmin.

5. Create a new database.

6. Import the file:

```text
Database.sql
```

7. Configure database credentials in:

```php
dbconnection.php
```

8. Open the application in your browser:

```text
http://localhost/Car-Rent-project
```

---

## User Roles

### Administrator

Administrators can:

* Access the dashboard.
* Add, edit, and delete cars.
* Upload vehicle images.
* Manage rentals.
* View all customers.
* Update rental status.
* Monitor system activity.

### User

Users can:

* Register and log in.
* Browse available cars.
* View vehicle details.
* Make rental reservations.
* Manage their rental information.

---

## Future Improvements

Possible enhancements include:

* Online payment integration.
* PDF invoice generation.
* Advanced search and filtering.
* Email notifications.
* Rental history reports.
* Data analytics dashboard.
* Multi-language support.
* Vehicle availability calendar.

---

## Author

**Mohammed Bouaroua**

Information Systems Engineering Student

Academic Project – Car Rental Management System
