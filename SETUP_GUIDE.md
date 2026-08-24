# 🏁 AWS Student Builders BZU — Beginner's Setup Guide

This guide is designed for a **new person** setting up this website for the first time. Follow these step-by-step instructions to install XAMPP, configure the database, and launch the site on your computer.

---

## 📋 Table of Contents
1. [Step 1: Download & Install XAMPP](#step-1-download--install-xampp)
2. [Step 2: Extract the Project ZIP File](#step-2-extract-the-project-zip-file)
3. [Step 3: Start Apache & MySQL in XAMPP](#step-3-start-apache--mysql-in-xampp)
4. [Step 4: Launch and Run the Project](#step-4-launch-and-run-the-project)
5. [Step 5: Logging In as Admin (Leader)](#step-5-logging-in-as-admin-leader)

---

## 📥 Step 1: Download & Install XAMPP

XAMPP provides the local environment we need: PHP (to execute web logic) and MySQL (to store user profiles, events, and leaderboard points).

1. Go to the official XAMPP download page: **[https://www.apachefriends.org/download.html](https://www.apachefriends.org/download.html)**
2. Under the **XAMPP for Windows** section, download the latest version (preferably PHP 8.x).
3. Double-click the downloaded file (e.g., `xampp-windows-x64-...-installer.exe`) to start the installation.
4. **Follow the Setup Wizard**:
   * If you see a warning about User Account Control (UAC), click **OK** to proceed.
   * On the **Select Components** screen, ensure **Apache**, **MySQL**, **PHP**, and **phpMyAdmin** are checked (keeping everything default is recommended).
   * Choose the installation folder (the default is `C:\xampp`).
5. Click **Next** until the installation completes, then click **Finish** to open the **XAMPP Control Panel**.

---

## 📦 Step 2: Extract the Project ZIP File

Once you have downloaded the project ZIP file (e.g. `SBG.zip`), you need to extract it to a directory of your choice.

### Option A: Running from any folder (Easiest)
* Extract the ZIP file anywhere on your computer (e.g. `D:\Projects\SBG` or your Desktop).
* You will run the site using a command shell in Step 4.

### Option B: Running via XAMPP Web Root (Standard)
* Open your file explorer and go to `C:\xampp\htdocs\`.
* Extract your ZIP file directly here so that the files are in `C:\xampp\htdocs\SBG\`.

---

## ⚡ Step 3: Start Apache & MySQL in XAMPP

For the website to fetch leaderboard scores and member profiles, you must activate the local servers.

1. Open the **XAMPP Control Panel** application (you can find it in your Windows Start Menu or at `C:\xampp\xampp-control.exe`).
2. Locate the row labeled **Apache** and click the **Start** button.
3. Locate the row labeled **MySQL** and click the **Start** button.
4. **Verify success**: Both labels should turn **green**, showing active ports (usually port `80` for Apache and `3306` for MySQL).

> [!IMPORTANT]
> If the MySQL button turns red or stops immediately:
> * Ensure no other SQL databases (like MySQL Workbench or PostgreSQL) are already running on your computer.
> * If you have a standalone MySQL installation running, stop its service in Windows Services (`services.msc`) and try starting XAMPP MySQL again.

---

## 🚀 Step 4: Launch and Run the Project

Now that MySQL is running, you can launch the website. Choose the method matching your extraction location in Step 2:

### Method A: Run via command line (If you extracted anywhere)
1. Open **Command Prompt** (cmd) or **PowerShell**.
2. Navigate to your project folder using `cd` (e.g. `cd /d D:\Projects\SBG`).
3. Start PHP's built-in server by running:
   ```cmd
   php -S localhost:8000
   ```
4. Keep the command prompt window open, launch your browser, and go to:
   * **[http://localhost:8000](http://localhost:8000)**

### Method B: Run via XAMPP Localhost (If you extracted to `htdocs`)
1. Ensure Apache and MySQL are running in XAMPP.
2. Open your web browser and go to:
   * **[http://localhost/SBG/](http://localhost/SBG/)**

---

## 🗄️ Step 5: Database Activation & Admin Access

### Automatic Activation (No SQL Import Required)
This project has an **auto-install feature**. When you open the website in your browser for the first time:
1. It automatically detects if a database exists.
2. If not, it creates a database named `sbg_portfolio`.
3. It sets up all required tables (`users`, `members`, `events`, `posts`, `highlights`) and populates them with seed data.

*To verify this database*, you can open your browser and visit **[http://localhost/phpmyadmin/](http://localhost/phpmyadmin/)**. You will see `sbg_portfolio` listed on the left panel.

---

## 🔑 Step 6: Log In as Administrator (Leader)

Only the Chapter Leader has administrative rights to add members, post announcements, or edit points.

1. Go to the home page of the site and click **Admin Portal** in the navigation bar (or go directly to `http://localhost:8000/login.php` or `http://localhost/SBG/login.php`).
2. Log in with the default administrator credentials.
3. Once logged in, you will be redirected to the Admin Panel dashboard where you can manage data.

> [!NOTE]
> For security, the exact administrator email and password must not be written as public placeholders. If you need to recover or verify the default admin login credentials, refer to the private settings inside [db.php](file:///d:/My%2520Projects/SBG/includes/db.php#L78-L81).

---

## 🛠️ Common Questions & Troubleshooting

* **Can I run this without installing XAMPP?**
  Yes, but you still need PHP and a MySQL server installed on your machine. XAMPP is simply the easiest way to install both at once.
* **How do I stop the servers?**
  Click the **Stop** buttons in the XAMPP Control Panel, and close your Command Prompt window (if running Method A).
* **Where are member pictures stored?**
  They are located in the `public/images/AWS-MembersPics/` folder.
