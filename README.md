# Employee Management System

## Overview
This is a web-based Employee Management System that allows employees to manage their profiles and administrators to oversee the entire system. The system is built using PHP and XML for data storage, featuring a clean and user-friendly interface.

## Features
- Employee Profile Management
  - View personal information
  - Edit profile details
  - Update profile picture
  - Delete account
- Employee List View
- Secure Login System
- Admin Dashboard
- Session Tracking
- Responsive Design

## System Access

### Employee Access
1. Visit `index.php` to start. 
2. Login with your Employee ID and password
3. New employees can sign up through the "Sign Up" link

### Admin Access
1. Go to the login page (`index.php`)
2. Click on the "Employee Login" header 5 times
3. This will redirect you to the admin login page
4. Login with admin credentials

## File Structure
- `index.php` - Landing page
- `dashboard.php` - Employee dashboard
- `admin_login.php` - Admin login page
- `admin_dashboard.php` - Admin control panel
- `add.php` - Employee registration
- `edit.php` - Profile editing
- `delete.php` - Account deletion
- `list.php` - Employee list view
- `verify_login.php` - Login authentication
- `verify_admin_login.php` - Admin authentication
- `logout.php` - Session termination
- `cict.xml` - Data storage file

## Technical Requirements
- PHP 7.0 or higher
- Web server (Apache recommended)
- XML support
- Write permissions for file uploads

## Security Features
- Session-based authentication
- Password protection
- Secure file handling
- Input validation
- XSS prevention

## Directory Structure
```
├── index2.php
├── dashboard.php
├── admin_login.php
├── admin_dashboard.php
├── add.php
├── edit.php
├── delete.php
├── list.php
├── verify_login.php
├── verify_admin_login.php
├── logout.php
├── cict.xml
├── covers/
│   └── (profile pictures)
└── img/
    └── default-avatar.png
```

## Setup Instructions
1. Place all files in your web server directory
2. Ensure the `covers` directory has write permissions
3. Create an `img` directory with a `default-avatar.png`
4. Access the system through your web browser

## Admin Access Instructions
To access the admin panel:
1. Go to the index page (login page) 
2. Click on the "Employee Login" header exactly 5 times
3. You will be redirected to the admin login page
4. Enter admin credentials to access the admin dashboard

## Support
For any issues or questions, please contact the system administrator.

## Note
This system uses XML for data storage. For production environments, consider migrating to a more robust database solution like MySQL or PostgreSQL. 
