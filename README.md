# ✈️ Airline Management System

## 📌 Project Overview

The **Airline Management System** is a web-based application developed to simplify airline operations by providing an efficient platform for managing flights, passenger bookings, and airline records. The system enables users to search available flights, book tickets, and manage reservations, while administrators can manage flight information and monitor booking records.

This project was developed using **PHP** for backend development, **MySQL** for database management, and **HTML/CSS** for creating a responsive and user-friendly interface.

---

## 🚀 Features

### 👤 User Features

* User Registration and Login
* Search Available Flights
* Book Flight Tickets
* View Booking Details
* Cancel Bookings (if implemented)
* Responsive User Interface

### 🔑 Admin Features

* Admin Login
* Add New Flights
* Update Flight Details
* Delete Flights
* Manage Passenger Records
* View All Bookings
* Manage Airline Information

---

## 🛠️ Technologies Used

| Technology              | Purpose                  |
| ----------------------- | ------------------------ |
| PHP 8+                  | Backend Development      |
| MySQL / MariaDB         | Database Management      |
| HTML5                   | Frontend Markup          |
| CSS                     | Styling and Layout       |
| Database Administration | phpMyAdmin               |
| XAMPP/WAMP              | Local Server Environment |

---

## 📂 Project Structure

```text
Airline-Management-System/
├── config/db.php                
├── database/Airline_data.sql    
├── admin_setup.php              
├── includes/                    
├── css/style.css                
├── index.php, about.php         
├── register.php, login.php, logout.php    
├── book_tickets.php → search_results.php → book_ticket.php → payment.php → ticket.php
├── check_pnr.php, cancel_ticket.php        
├── dashboard.php                
├── screenshots/                 
└── admin/                       
    ├── login.php, logout.php    
    ├── dashboard.php            
    ├── manage_flights.php, flight_form.php, delete_flight.php
    ├── manage_users.php, delete_user.php
    └── view_tickets.php

```

---

## 💾 Database

* Database: **MySQL**
* Database file: `Airline_data.sql`

The database stores information such as:

* Users
* Flights
* Bookings
* Passengers
* Admin Details

---

## ⚙️ Installation Guide

### Step 1: Clone the Repository

```bash
git clone https://github.com/yourusername/Airline-Management-System.git
```

### Step 2: Move the Project

Copy the project folder into your web server directory.

For XAMPP:

```text
C:\xampp\htdocs\
```

### Step 3: Start Services

Open XAMPP Control Panel and start:

* Apache
* MySQL

### Step 4: Import the Database

1. Open phpMyAdmin.
2. Create a new database (for example, getair_db).
3. Import the `Airline_data.sql` file into the newly created database.

### Step 5: Configure Database Connection

Update your database connection file with your local credentials.

Example:

```php
$host = "localhost";
$user = "root";
$password = "";
$database = "getair_db";
```

### Step 6: Run the Project

Open your browser and visit:

```text
https://localhost/Airline-Management-System/ ```

Fix the admin login (no terminal needed)

The schema seeds a placeholder password hash for admin, so log in fails until you run this one-time browser fix:

Start Apache + MySQL in the XAMPP Control Panel.
Go to http://localhost/getair/admin_setup.php.
Click Fix Admin Login — you'll see a green success message.
Delete admin_setup.php afterward so it can't be used again.

This sets the admin login to:

username: admin
password: admin123
```

---

## 📸 Screenshots

* Home Page
![image alt](


* Login Page
![image alt](
  
* Registration Page
![image alt](
  
* Flight Search
![image alt](
  
* Flight Tickets
![image alt](
  
* Admin Dashboard
![image alt](

## 🎯 Learning Outcomes

This project helped in understanding:

* PHP Programming
* CRUD Operations
* MySQL Database Design
* Session Management
* Authentication and Authorization
* Form Validation
* Responsive Web Design
* Database Connectivity using PHP

---

## 🔮 Future Enhancements

* Online Payment Gateway Integration
* Email Notifications
* Flight Seat Selection
* Passenger Profile Management
* Password Recovery
* Search Filters
* Admin Analytics Dashboard

---

##
