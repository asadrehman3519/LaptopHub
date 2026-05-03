-- LaptopHub Database Update - Advanced Features
-- Run this to add new tables and features

USE laptophub;

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Categories
INSERT INTO categories (name, slug, description, icon) VALUES
('Business Laptops', 'business', 'Light weight, long battery life, security focused', 'briefcase'),
('Student Laptops', 'student', 'Budget friendly, good for study and browsing', 'graduation-cap'),
('Gaming Laptops', 'gaming', 'High graphics GPU, RGB keyboard, cooling system', 'gamepad'),
('Premium Laptops', 'premium', 'MacBook/ultra-thin, high performance + design', 'gem'),
('Budget Laptops', 'budget', 'Under 100,000 PKR, basic use', 'tag');

-- Update Products Table to include category and more specs
ALTER TABLE products ADD COLUMN category_id INT NULL AFTER brand;
ALTER TABLE products ADD COLUMN ram VARCHAR(50) NULL AFTER specs;
ALTER TABLE products ADD COLUMN storage VARCHAR(50) NULL AFTER ram;
ALTER TABLE products ADD COLUMN processor VARCHAR(100) NULL AFTER storage;
ALTER TABLE products ADD COLUMN graphics VARCHAR(100) NULL AFTER processor;
ALTER TABLE products ADD COLUMN display VARCHAR(100) NULL AFTER graphics;
ALTER TABLE products ADD COLUMN battery VARCHAR(50) NULL AFTER display;
ALTER TABLE products ADD COLUMN weight VARCHAR(50) NULL AFTER battery;
ALTER TABLE products ADD COLUMN warranty VARCHAR(50) NULL AFTER weight;
ALTER TABLE products ADD COLUMN rating DECIMAL(3,2) DEFAULT 0.00 AFTER image;
ALTER TABLE products ADD COLUMN reviews_count INT DEFAULT 0 AFTER rating;
ALTER TABLE products ADD COLUMN is_featured BOOLEAN DEFAULT FALSE AFTER reviews_count;
ALTER TABLE products ADD COLUMN is_deal BOOLEAN DEFAULT FALSE AFTER is_featured;
ALTER TABLE products ADD COLUMN deal_price DECIMAL(10,2) NULL AFTER is_deal;
ALTER TABLE products ADD FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL;

-- Accessories Table
CREATE TABLE IF NOT EXISTS accessories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    category VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    stock INT DEFAULT 10,
    rating DECIMAL(3,2) DEFAULT 0.00,
    reviews_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Sample Accessories
INSERT INTO accessories (name, category, price, description, image, stock) VALUES
('Wireless Gaming Mouse', 'Input Devices', 49.99, 'High precision wireless mouse with RGB lighting', 'mouse.jpg', 20),
('Mechanical Keyboard', 'Input Devices', 89.99, 'RGB mechanical keyboard with blue switches', 'keyboard.jpg', 15),
('Laptop Cooling Pad', 'Cooling & Maintenance', 39.99, 'Dual fan cooling pad for laptops', 'cooling_pad.jpg', 25),
('Laptop Sleeve 15.6"', 'Laptop Protection', 24.99, 'Protective sleeve for 15.6 inch laptops', 'sleeve.jpg', 30),
('USB-C Hub', 'Power Accessories', 34.99, '7-in-1 USB-C hub with HDMI and USB ports', 'usb_hub.jpg', 18);

-- Wishlist Table
CREATE TABLE IF NOT EXISTS wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_wishlist (user_id, product_id)
);

-- Product Reviews Table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    review_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Compare Table (for session-based compare)
CREATE TABLE IF NOT EXISTS compare (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_compare (user_id, product_id)
);

-- Update Orders Table for payment info
ALTER TABLE orders ADD COLUMN payment_method VARCHAR(50) DEFAULT 'cod' AFTER status;
ALTER TABLE orders ADD COLUMN payment_status VARCHAR(50) DEFAULT 'pending' AFTER payment_method;
ALTER TABLE orders ADD COLUMN transaction_id VARCHAR(100) NULL AFTER payment_status;
ALTER TABLE orders ADD COLUMN tracking_number VARCHAR(100) NULL AFTER phone;
ALTER TABLE orders ADD COLUMN estimated_delivery DATE NULL AFTER tracking_number;

-- Price Alerts Table
CREATE TABLE IF NOT EXISTS price_alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    target_price DECIMAL(10, 2) NOT NULL,
    is_notified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- EMI/Installment Options Table
CREATE TABLE IF NOT EXISTS emi_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    months INT NOT NULL,
    interest_rate DECIMAL(5,2) NOT NULL,
    description VARCHAR(255)
);

INSERT INTO emi_plans (months, interest_rate, description) VALUES
(3, 0.00, '3 Months - 0% Interest'),
(6, 5.00, '6 Months - 5% Interest'),
(12, 10.00, '12 Months - 10% Interest'),
(18, 15.00, '18 Months - 15% Interest'),
(24, 20.00, '24 Months - 20% Interest');

-- Update existing products with categories and specs
UPDATE products SET category_id = 4, ram = '18GB', storage = '512GB SSD', processor = 'M3 Pro', graphics = 'Integrated', display = '14.2" Liquid Retina XDR', battery = 'Up to 22 hours', weight = '1.6 kg', warranty = '1 Year', rating = 4.8, reviews_count = 125 WHERE name = 'MacBook Pro 14"';

UPDATE products SET category_id = 1, ram = '16GB', storage = '512GB SSD', processor = 'Intel Core i7', graphics = 'Intel Iris Xe', display = '15.6" OLED', battery = 'Up to 12 hours', weight = '1.8 kg', warranty = '1 Year', rating = 4.6, reviews_count = 89 WHERE name = 'Dell XPS 15';

UPDATE products SET category_id = 4, ram = '16GB', storage = '512GB SSD', processor = 'Intel Core i7', graphics = 'Intel Iris Xe', display = '13.5" Touchscreen', battery = 'Up to 16 hours', weight = '1.3 kg', warranty = '1 Year', rating = 4.5, reviews_count = 67 WHERE name = 'HP Spectre x360';

UPDATE products SET category_id = 1, ram = '16GB', storage = '1TB SSD', processor = 'Intel Core i7', graphics = 'Intel Iris Xe', display = '14" IPS', battery = 'Up to 18 hours', weight = '1.1 kg', warranty = '3 Year', rating = 4.7, reviews_count = 156 WHERE name = 'Lenovo ThinkPad X1';

UPDATE products SET category_id = 3, ram = '32GB', storage = '1TB SSD', processor = 'AMD Ryzen 9', graphics = 'NVIDIA RTX 4060', display = '15.6" 165Hz', battery = 'Up to 6 hours', weight = '2.1 kg', warranty = '2 Year', rating = 4.9, reviews_count = 203, is_featured = TRUE WHERE name = 'ASUS ROG Zephyrus';

UPDATE products SET category_id = 5, ram = '8GB', storage = '256GB SSD', processor = 'Intel Core i5', graphics = 'Intel Iris Xe', display = '14" IPS', battery = 'Up to 10 hours', weight = '1.2 kg', warranty = '1 Year', rating = 4.3, reviews_count = 45 WHERE name = 'Acer Swift 5';

-- Set some products as deals
UPDATE products SET is_deal = TRUE, deal_price = 1799.99 WHERE name = 'MacBook Pro 14"';
UPDATE products SET is_deal = TRUE, deal_price = 1299.99 WHERE name = 'Dell XPS 15';
