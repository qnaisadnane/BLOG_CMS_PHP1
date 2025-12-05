# BlogCMS - Simple & Secure Blog Management System

A lightweight, procedural PHP-based Content Management System (CMS) built for blogs with a clean admin dashboard, role-based access control, and all essential blogging features.

## Features

### For All Users
- Secure login page
- Role-based access control (Admin, Editor/Author, Visitor)

### For Administrators
- Dashboard with key statistics (articles, users, comments, categories)
- Full CRUD operations for Categories
- Comment moderation (approve/reject/delete)
- User management (create, edit, delete, change role)

### For Authors (Editors)
- View list of published articles
- Create, edit, and delete their own articles
- Post comments

### For Visitors (Public)
- View published articles
- Post comments (pending moderation)

### Bonus Features Implemented
- Image upload for articles (stored in `/uploads/`)
- Article search functionality
- Pagination on article lists and admin panels

## Technology Stack

### Backend
- **PHP 8+** (procedural style – no frameworks)
- **MySQL** (or PostgreSQL) with **PDO** and prepared statements
- Secure session management
- Password hashing with **bcrypt**
- CSRF protection on forms
- XSS protection using `htmlspecialchars()`

### Frontend
- HTML5 + CSS3
- **Bootstrap 5** (responsive design)
- Vanilla JavaScript (minimal, no heavy libraries)
