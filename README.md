# MV Electronics - E-Commerce Platform

MV Electronics is a dynamic, full-featured e-commerce platform built using HTML, CSS, JavaScript, PHP, and MySQL. It allows customers to browse, review, and purchase electronics, while admins can manage products, users, and orders through a secure backend dashboard.

---

## Live Demo

- **Project (Mandeep):** https://cosc360.ok.ubc.ca/msingh78/project360/project360/
- **Project (Varun):** https://cosc360.ok.ubc.ca/msingh78/project360/project360/
- **GitHub Repository:** https://github.com/mandeep3535/project360/

---

## Project Structure

- `images/` – Static images of the website  
- `mailer/` – Libraries required for email functionality  
- `uploads/` – Product images added by admin dynamically  
- `sql/msingh78.sql` – MySQL schema (for import)  
- `db_connection.php` – Edit this with your own DB credentials  

---

## Setup: How to Run the Project Locally

### Requirements

- XAMPP or similar (Apache + MySQL + PHP)
- A browser
- Git (optional)

### Setup Instructions

**Step 1:** Clone or download the repository
```bash
git clone https://github.com/mandeep3535/project360.git
```

**Step 2:** Place the project folder inside `htdocs/` of XAMPP  
Example: `C:/xampp/htdocs/project360`

**Step 3:** Start Apache and MySQL via XAMPP Control Panel

**Step 4:** Create and import the database:
1. Open http://localhost/phpmyadmin  
2. Create a new database (e.g., `mvsingh78`)  
3. Import `sql/msingh78.sql`

**Step 5:** Edit your database connection:  
Open `db_connection.php` and update:
```php
$host = "localhost";
$username = "root";       // default XAMPP user
$password = "";           // default is empty
$dbname = "msingh78";
```

**Step 6:** Run the project  
Visit: http://localhost/project360/home.php

---

##  Admin Access

To experience the admin panel:

- **Email:** Meritmandeep35@gmail.com  
- **Password:** Mandeep@123  
- Login via the **Admin Login** link in the footer

---

##  Features

###  User Side

- Homepage with promotional banner and product carousel  
- Dynamic product listings with category filters  
- Search bar for product lookup  
- Wishlist and cart (login required)  
- Checkout with delivery, coupon, and payment options  
- Product reviews using AJAX  
- Profile and password management  
- Order history with live status updates  
- Forgot password & reset system  
- ...and many more. Run the project for full experience.

###  Admin Side

- Dashboard with metrics: revenue, orders, products, users  
- Add/Edit/Delete Products with image upload  
- Manage customer orders (status, delivery, etc.)  
- Manage user accounts (edit, delete, activate)  
- Sales reports, category stats, top customers  
- ...and many more. Run the project for full experience.

---

## Email Functionality

- Users receive order confirmation emails  
- Admins are notified of new orders  
- Welcome email sent upon successful signup  
- Contact form messages are emailed to admin  
- Admin email edit triggers user notifications

---

##  Contact

For help, questions, or suggestions:

- **Email:** Meritmandeep35@gmail.com
- **Email:** GargVarun2000@gmail.com

---

##  Team Members

- **Mandeep Singh** (72040231)  
- **Varun Garg** (41082330)
