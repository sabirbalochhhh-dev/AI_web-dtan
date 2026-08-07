# HealthCare Pharmacy Website

A responsive PHP and MySQL-based web application for a modern pharmacy business. The system includes a public-facing website for customers and a secure admin panel for managing medicines, categories, pharmacists, gallery content, and contact messages.

## Project Overview

HealthCare Pharmacy Website is a full-stack web project designed to provide an online presence for a pharmacy while also supporting internal management tasks. The website allows visitors to learn about the pharmacy, browse available medicines, view professional staff members, and contact the business. The admin panel enables authorized users to update the site content securely through CRUD operations.

## Features

- Responsive public website with pages for Home, About, Medicines, and Contact
- Modern and professional user interface using Bootstrap 5 and custom CSS
- Secure admin login and protected dashboard
- CRUD functionality for medicines, categories, pharmacists, and gallery items
- Image upload support for medicines, pharmacists, and gallery content
- Contact form with validation and database storage
- MySQL database with related tables for structured data management
- Search and filtering options for medicines

## Technologies Used

- HTML5
- CSS3
- Bootstrap 5
- JavaScript
- PHP
- MySQL

## Folder Structure

```text
HealthCare-Pharmacy/
├── admin/                 # Protected admin pages
├── assets/                # CSS, JavaScript, and styling files
├── config/                # Database connection and helper functions
├── database/              # SQL schema and database files
├── includes/              # Shared header and footer files
├── uploads/               # Uploaded images
├── about.php              # About page
├── contact.php            # Contact page
├── index.php              # Home page
├── medicines.php          # Medicines listing page
├── setup.php              # Database setup script
└── README.md              # Project documentation
```

## Installation Steps

1. Install XAMPP or WAMP on your system.
2. Place the project folder in the `htdocs` directory.
3. Start Apache and MySQL from XAMPP.
4. Open the following URL in your browser:
   - http://localhost/AI_web%20dtan/setup.php
5. The setup script will create the database and sample records.
6. Open the website at:
   - http://localhost/AI_web%20dtan/index.php
7. Open the admin panel at:
   - http://localhost/AI_web%20dtan/admin/login.php

## Database Import Instructions

If you want to import the database manually, use the SQL file available in the `database/` folder.

### Option 1: Using the setup script
- Run `setup.php` to create the database automatically.

### Option 2: Importing the SQL file manually
- Open phpMyAdmin.
- Create a database named `healthcare_pharmacy`.
- Import the SQL file from `database/healthcare_pharmacy.sql`.

## Admin Login Credentials

Default admin credentials for demonstration purposes:

- Username: `admin`
- Password: `admin123`

> Please change the password after setup for security purposes.

## Screenshots

Add screenshots of the following pages:

- Home Page
- Medicines Page
- Admin Dashboard
- Contact Page
- Gallery Management

## Future Improvements

- Online medicine ordering system
- Prescription upload feature
- Appointment booking for consultations
- Advanced analytics and reporting for admin
- Improved search and filtering options
- User account registration and login

## License

This project is for educational and academic purposes. You may use and modify it for learning and demonstration purposes.
