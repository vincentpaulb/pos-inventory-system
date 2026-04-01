R'B HEAVY EQUIPMENT PARTS TRADING
Inventory Management System with POS
===================================

REQUIREMENTS
- XAMPP or WAMP
- PHP 8.0+
- MySQL / MariaDB

SETUP STEPS
1. Copy the project folder "rb_heavy_inventory_pos" into:
   - XAMPP: htdocs/
   - WAMP: www/

2. Create the database:
   - Open phpMyAdmin
   - Import the file: database.sql

3. Update database config if needed:
   File: /config/database.php
   Default values:
   - DB_HOST = 127.0.0.1
   - DB_NAME = rb_heavy_inventory
   - DB_USER = root
   - DB_PASS =

4. Open in browser:
   http://localhost/rb_heavy_inventory_pos/

DEFAULT LOGIN
- Username: admin
- Password: admin123

FEATURES INCLUDED
- Authentication with role-based access
- Admin and Cashier roles
- Product inventory management
- Stock in / stock out tracking
- Category and supplier management
- User management (Admin only)
- POS checkout with printable receipt
- Dashboard statistics
- Sales reports + CSV export
- Activity logs
- Dark mode toggle
- Basic brute-force login protection
- CSRF protection and prepared statements

NOTES
- Delete actions are restricted to Admin in major areas.
- Bootstrap is loaded via CDN.
- This is built in pure PHP with an MVC-like folder organization.
- Make sure mod_rewrite is enabled in Apache for clean routing.

OPTIONAL APACHE NOTE
If your Apache blocks .htaccess, enable mod_rewrite and AllowOverride All.

RECOMMENDED NEXT IMPROVEMENTS
- Add image upload for products
- Add barcode scanner integration
- Add printer-size receipt styling for thermal printers
- Add pagination and audit filters
- Add backup/restore tools
