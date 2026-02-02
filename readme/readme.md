# Student Record Management System (SRMS)

## Login Credentials
- **Admin Username:** admin  
- **Admin Password:** admin123  

## Setup Instructions
1. Clone or download the repository into your server directory (`htdocs` or `public_html`).
2. Import the provided SQL schema into MySQL using phpMyAdmin or CLI.
3. Update `config/db.php` with your database connection details.
4. Place the project folder on the school’s student server.
5. Access the application via browser at `https://student.heraldcollege.edu.np/~np03cs4a240123/SRMS/public/login.php` or the hosted server link.

## Features Implemented
- Session-based authentication (login/logout)
- Role-based access (Admin vs User)
- CRUD operations for student records (Create, Read, Update, Delete)
- Autocomplete search using Ajax
- CSRF protection on forms
- SQL injection prevention with prepared statements
- XSS prevention with `htmlspecialchars`
- Client-side validation (required fields, email format, numeric ranges)
- Server-side validation (username uniqueness, password strength, roll number uniqueness)

## Known Issues
- Email uniqueness currently enforced only by database constraint (PDO error if duplicate).
- Autocomplete dropdown styling may overlap input field depending on browser.
- Advanced search (multi-criteria) not yet implemented.
- Template engine integration (Twig/Blade) not included.
