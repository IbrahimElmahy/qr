-- إنشاء قاعدة البيانات
CREATE DATABASE IF NOT EXISTS shipmen CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE shipmen;

-- جدول الشركات
CREATE TABLE companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- جدول الشحنات
CREATE TABLE shipments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    barcode VARCHAR(255) NOT NULL,
    company_id INT NOT NULL,
    scan_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

-- إدراج بيانات تجريبية للشركات
INSERT INTO companies (name, is_active) VALUES 
('شركة أرامكس', TRUE),
('شركة DHL', TRUE),
('شركة فيديكس', TRUE),
('شركة UPS', FALSE);

-- إنشاء فهرس لتحسين الأداء
CREATE INDEX idx_shipments_barcode ON shipments(barcode);
CREATE INDEX idx_shipments_company_id ON shipments(company_id);
CREATE INDEX idx_shipments_scan_date ON shipments(scan_date);
CREATE INDEX idx_shipments_company_date ON shipments(company_id, scan_date);
