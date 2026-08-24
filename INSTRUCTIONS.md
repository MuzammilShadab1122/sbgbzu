# 🚀 AWS Student Builders BZU — Setup & Running Guide

Welcome to the **AWS Student Builders BZU** portfolio project! This guide provides step-by-step instructions to configure, run, and administer the platform locally on Windows.

---

## 🛠️ Step 1: Start MySQL and Apache (via XAMPP)

The website requires a MySQL database server running on `localhost` (port `3306`) with user `root` and no password (default configurations).

1. **Open XAMPP Control Panel**:
   * Search for `XAMPP Control Panel` in your Windows Start menu and open it.
2. **Start MySQL**:
   * Locate **MySQL** in the list.
   * Click the **Start** button next to it. Once active, the background color of the service label will turn green, showing port `3306`.
3. **Start Apache** (Optional - if using Apache to serve the website):
   * Locate **Apache** in the list.
   * Click the **Start** button next to it. Once active, its label will turn green, showing ports `80, 443`.

---

## 📂 Step 2: Run the Website

You have **two options** to access the website on your local machine:

### Option A: Run via PHP Built-in Server (Recommended & Easiest)
You do not need to move any files. Simply launch the server directly from the project directory.

1. Open **Command Prompt** (cmd) or **PowerShell**.
2. Run the following command:
   ```cmd
   php -S localhost:8000
   ```
3. Open your browser and navigate to:
   * **[http://localhost:8000](http://localhost:8000)**

### Option B: Run via XAMPP Apache
Use this if you prefer hosting it through XAMPP's Apache server.

1. Copy the entire folder `SBG` to your XAMPP directory:
   * Target path: `C:\xampp\htdocs\SBG\`
2. Ensure both **Apache** and **MySQL** are running in the XAMPP control panel.
3. Open your browser and navigate to:
   * **[http://localhost/SBG/](http://localhost/SBG/)**

---

## 🗄️ Step 3: Database Auto-Initialization
The website is equipped with a **self-healing, auto-seeding database wrapper** (`includes/db.php`).

* **No manual SQL import is required!** 
* When you access the homepage (`http://localhost:8000` or `http://localhost/SBG/`) for the first time with MySQL running, the script automatically:
  1. Creates the database named `sbg_portfolio`.
  2. Generates all database tables (`users`, `members`, `events`, `posts`, `highlights`).
  3. Populates initial members, events, blog posts, and highlights automatically.
  4. Configures the leader admin login.

---

## 🔑 Step 4: Admin Portal Access
Only the designated **Leader** (Administrator) can manage builder profiles, events, and update scores.

1. Click **Admin Portal** in the navigation bar (or visit `/login.php`).
2. Enter the administrator credentials.
3. Once logged in, you can add new members, edit points, delete entries, and post announcements.

> [!NOTE]
> For security, the exact administrator email and password must not be written as public placeholders. If you need to recover or verify the default admin login credentials, refer to the private settings inside [db.php](file:///d:/My%2520Projects/SBG/includes/db.php#L78-L81).

---

## ⚙️ Troubleshooting & Notes

* **Port 3306 Error (MySQL won't start)**:
  * Ensure no other local database instance (like a standalone MySQL server) is running.
  * Stop any duplicate services or change the port in the XAMPP config.
* **Database Connection Failed**:
  * Verify that MySQL is showing a green background in the XAMPP Control Panel.
  * Ensure the host, username, and password credentials match the settings in `includes/db.php`.
* **Changes Not Reflecting**:
  * Clear your browser cache or open the page in an incognito window to see the latest visual updates.
