# LaptopHub - Online Laptop Store

A complete full-stack e-commerce website for buying laptops online, built with PHP, MySQL, HTML, CSS, and JavaScript.

## 🚀 Features

### User Features
- **User Authentication**: Register and login system
- **Product Browsing**: View all laptops with search and filter by brand
- **Product Details**: Detailed view of each laptop with specifications
- **Shopping Cart**: Add products to cart, update quantities, remove items
- **Checkout System**: Complete order placement with shipping information
- **Order History**: View past orders and their status

### Admin Features
- **Dashboard**: Overview of total products, orders, users, and revenue
- **Product Management**: Add, edit, and delete products
- **Order Management**: View all orders, update order status
- **Order Details**: View detailed information about each order

## 📋 Requirements

- XAMPP (Apache + MySQL + PHP)
- Modern web browser

## 🛠️ Installation Steps

### 1. Setup XAMPP
- Download and install XAMPP from https://www.apachefriends.org/
- Start Apache and MySQL services from XAMPP Control Panel

### 2. Create Database
- Open phpMyAdmin: http://localhost/phpmyadmin
- Click on "New" to create a new database
- Name the database: `laptophub`
- Click "Create"

### 3. Import Database Schema
- Select the `laptophub` database
- Click on "Import" tab
- Choose the `database.sql` file from the project folder
- Click "Go" to import

### 4. Place Project Files
- Copy the entire `laptopHub` folder to: `C:\xampp\htdocs\`
- The project should now be accessible at: http://localhost/laptopHub

### 5. Configure Database Connection (if needed)
- Open `includes/config.php`
- Verify database credentials:
  - DB_HOST: localhost
  - DB_USER: root
  - DB_PASS: (leave empty for default XAMPP)
  - DB_NAME: laptophub

## 👤 Default Admin Account

- **Email**: admin@laptophub.com
- **Password**: admin123

**Important**: Change the default admin password after first login for security.

## 📁 Project Structure

```
laptopHub/
├── admin/                  # Admin panel pages
│   ├── index.php          # Admin dashboard
│   ├── products.php       # Manage products
│   ├── add_product.php    # Add new product
│   ├── edit_product.php   # Edit existing product
│   ├── orders.php         # Manage orders
│   └── view_order.php     # View order details
├── assets/                 # Static assets
│   ├── css/
│   │   └── style.css      # Main stylesheet
│   ├── js/
│   │   └── script.js      # JavaScript functionality
│   └── images/            # Product images
├── includes/               # PHP includes
│   ├── config.php         # Database configuration
│   ├── auth.php           # Authentication functions
│   ├── header.php         # Common header
│   └── footer.php         # Common footer
├── api/                    # API endpoints (for future use)
├── database.sql           # Database schema
├── index.php              # Home page
├── products.php           # Product listing page
├── product.php            # Product detail page
├── cart.php               # Shopping cart
├── checkout.php           # Checkout page
├── orders.php             # User order history
├── login.php              # Login page
├── register.php           # Registration page
└── logout.php             # Logout handler
```

## 🎨 Database Schema

### Tables:
- **users**: Stores user information (name, email, password, role)
- **products**: Stores laptop details (name, brand, price, specs, image, stock)
- **cart**: Stores shopping cart items
- **orders**: Stores order information
- **order_items**: Stores individual items in each order

## 🌐 Accessing the Website

- **Frontend**: http://localhost/laptopHub
- **Admin Panel**: http://localhost/laptopHub/admin/
- **phpMyAdmin**: http://localhost/phpmyadmin

## 📱 Usage Guide

### For Users:
1. Register a new account or login
2. Browse laptops on the home page or products page
3. Click on a laptop to view details
4. Add to cart and set quantity
5. View cart and proceed to checkout
6. Enter shipping information and place order
7. View order history in "My Orders"

### For Admin:
1. Login with admin credentials
2. Access admin panel from navigation
3. Add new products with images
4. Edit or delete existing products
5. View and manage customer orders
6. Update order status (pending → processing → completed)

## 🔒 Security Notes

- Passwords are hashed using PHP's password_hash()
- SQL injection prevention using prepared statements
- Session-based authentication
- Role-based access control (user/admin)

## 🎯 Sample Data

The database includes:
- 1 admin user
- 6 sample laptops from various brands
- Ready to use immediately after setup

## 🐛 Troubleshooting

### Database Connection Error
- Ensure MySQL is running in XAMPP
- Check database credentials in `includes/config.php`
- Verify database name is `laptophub`

### Images Not Displaying
- Ensure `assets/images/` folder exists and has write permissions
- Check image upload path in admin panel

### Session Issues
- Ensure PHP session is properly configured
- Check browser cookies are enabled

## 📝 License

This project is for educational purposes.

## 👨‍💻 Author

Created as a full-stack e-commerce project using PHP and MySQL.

## 🎓 Project Goal

"The goal of LaptopHub is to provide an online platform where users can easily browse and purchase laptops with a smooth and user-friendly experience."
