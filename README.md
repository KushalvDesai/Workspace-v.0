# Workspace Management System

The **Workspace Management System** is a web-based application built using **PHP**, **MySQL**, and **HTML/CSS** with a focus on simplicity, security, and a clean user experience. It enables users to manage accounts with intuitive functionality, modern UI design, and secure user authentication.

---

## Features

### 1. **User Authentication**
   - Secure login system with password hashing (using `bcrypt`).
   - Session-based authentication to ensure user security.

### 2. **Account Management**
   - Add, edit, and delete accounts.
   - Profile picture upload with automatic file handling.
   - Input fields include:
     - Name
     - Gmail Link
     - Drive Link
     - Several other google linked apps
     - Profile Picture
   - User-specific visibility: Each user can only view and manage members added by them.

### 3. **Modern Dark-Mode UI**
   - Built-in dark mode with **tinted glass effects** for containers.
   - Consistent theme across all pages for improved user experience.
   - Elegant backgrounds with gradients and images for a professional look.

### 4. **Error Handling**
   - Graceful handling of database errors (foreign key constraints, invalid inputs, etc.).
   - Proper session validation to prevent unauthorized access.

### 5. **Responsive Design**
   - Fully responsive layout using pure **CSS**.
   - Optimized for both desktop and mobile devices.

---

## Technology Stack

- **Frontend:** HTML, CSS (Poppins font, responsive layout)
- **Backend:** PHP 8.x
- **Database:** MySQL
- **Deployment:** XAMPP/WAMP for local development, Apache server for production

---

## Installation Guide

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/KushalvDesai/Workspace-v.0.git
   cd workspace-management-system
   ```

2. **Setup Database:**
   - Import the provided SQL file (`DB Setup.sql`) into your MySQL server.
   - Ensure three tables: `users` (for authentication), `workspace` (for basic accounts) and `workspace_apps` (for additional linked apps).

   **SQL Structure:**
   - `users`:
     - `id` (AUTO_INCREMENT, PRIMARY KEY)
     - `user_id` (VARCHAR, UNIQUE)
     - `password` (VARCHAR, hashed password)
   - `workspace`:
     - `id` (AUTO_INCREMENT, PRIMARY KEY)
     - `user_id` (VARCHAR, FOREIGN KEY linked to `users.user_id`)
     - `acc_name`, `gmail`, `drive`, `pfp` (profile picture path)
   - `workspace_apps`:
     - `id` (AUTO_INCREMENT, PRIMARY KEY)
     - `workspace_id` (VARCHAR, FOREIGN KEY linked to `users.user_id`)
     - `app_name` and `applink`  

3. **Configure Database Connection:**
   Update the database credentials in PHP files (e.g., `login.php`, `add_member.php`, etc.):
   ```php
   $conn = new mysqli('127.0.0.1', 'username', 'password', 'database_name');
   ```

4. **Run the Application:**
   - Place the project folder in your Apache server (e.g., `htdocs` for XAMPP).
   - Access the application via `http://localhost/your_project_folder`.

---

## Project Files

1. **Authentication:**
   - `login.php`: User login page.
   - `logout.php`: Ends user session.
   - `validate_login.php`: Validates user credentials.

2. **Core Pages:**
   - `index.php`: Displays team members added by the logged-in user.
   - `add_member.php`: Form to add a new team member.
   - `edit_member.php`: Form to edit an existing member's details.
   - `delete_member.php`: Deletes a team member.

4. **Assets:**
   - Background images and uploaded profile pictures stored in the `uploads` folder.

---

