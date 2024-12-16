# Workspace Management System

The **Workspace Management System** is a web-based application built using **PHP**, **MySQL**, and **HTML/CSS** with a focus on simplicity, security, and a clean user experience. It enables users to manage team members with intuitive functionality, modern UI design, and secure user authentication.

---

## Features

### 1. **User Authentication**
   - Secure login system with password hashing (using `bcrypt`).
   - Session-based authentication to ensure user security.
   - "Remember Me" functionality for seamless access.

### 2. **Team Member Management**
   - Add, edit, and delete team members.
   - Profile picture upload with automatic file handling.
   - Input fields include:
     - Name
     - Gmail Link
     - Drive Link
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
   git clone https://github.com/yourusername/workspace-management-system.git
   cd workspace-management-system
   ```

2. **Setup Database:**
   - Import the provided SQL file into your MySQL server.
   - Ensure two tables: `users` (for authentication) and `workspace` (for team members).

   **SQL Structure:**
   - `users`:
     - `id` (AUTO_INCREMENT, PRIMARY KEY)
     - `user_id` (VARCHAR, UNIQUE)
     - `password` (VARCHAR, hashed password)
   - `workspace`:
     - `id` (AUTO_INCREMENT, PRIMARY KEY)
     - `user_id` (VARCHAR, FOREIGN KEY linked to `users.user_id`)
     - `acc_name`, `gmail`, `drive`, `pfp` (profile picture path)

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

3. **Database:**
   - SQL file for setting up `users` and `workspace` tables.

4. **Assets:**
   - Background images and uploaded profile pictures stored in the `uploads` folder.

---

## Screenshots

1. **Login Page**
   ![Login Page](path-to-your-screenshot)

2. **Dashboard**
   ![Dashboard](path-to-your-screenshot)

3. **Add Member Form**
   ![Add Member](path-to-your-screenshot)

---

## Future Improvements

- Add role-based access control for admins.
- Integrate search and filter options for team members.
- Implement email notifications on adding or editing members.
- Enhance the UI with animations and micro-interactions.

---

## Contributing

Feel free to fork the repository, submit pull requests, or raise issues. Contributions are welcome!

---
