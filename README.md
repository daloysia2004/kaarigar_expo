How to Run the Project Locally
**Prerequisites:**
* **XAMPP** (Apache + MySQL with PHP 8.x)
* Modern Web Browser

### Setup Instructions

1. **Clone & Place Files:**
   Clone this repository directly into your XAMPP `htdocs` directory

2. Import Database:

Open XAMPP Control Panel and start Apache and MySQL.

Go to http://localhost/phpmyadmin in your browser.

Create a new database named kaarigar_expo.

Click Import and select the schema.sql (or database.sql) file included in this repository.

Verify Configuration:
Ensure db.php matches your local database settings:

PHP
$host = "localhost";
$user = "root";
$password = "";
$dbname = "kaarigar_expo";

Launch Application:
Open http://localhost/kaarigar_expo/index.php in your browser.

Built vs. Skipped Scope
✅ What Was Built
Role-Based Authentication: Dynamic login & registration (visitor, kaarigar, admin).

Artisan Profiles: Kaarigar portfolio creation with craft metadata and file upload handling (uploads/).

Admin Dashboard: Multi-tab management panel for creating events, tracking stall applications (approve/reject), viewing Kaarigar profiles, and listing Visitor RSVPs using grouped SQL aggregation queries.

Database Integration: Pre-configured MySQL schema (schema.sql) for seamless local setup.

⏳ Skipped (Time Constraints)
Real-Time Notifications: Email/SMS alerts for stall approval/rejection.

Payment Processing: Gateways for stall booking fees or visitor tickets.

Full CRUD Management: Admin options to edit or delete existing melas and user accounts.



