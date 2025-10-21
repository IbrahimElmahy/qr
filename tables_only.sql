-- ملف إنشاء الجداول فقط (بدون إنشاء قاعدة البيانات)
-- استخدم هذا الملف مع قاعدة البيانات: ztjmal_shipmen

-- جدول الشركات
CREATE TABLE IF NOT EXISTS companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- جدول الشحنات
CREATE TABLE IF NOT EXISTS shipments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    barcode VARCHAR(255) NOT NULL,
    company_id INT NOT NULL,
    scan_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

-- إدراج بيانات تجريبية للشركات
INSERT IGNORE INTO companies (id, name, is_active) VALUES 
(1, 'شركة أرامكس', TRUE),
(2, 'شركة DHL', TRUE),
(3, 'شركة فيديكس', TRUE),
(4, 'شركة UPS', FALSE);

-- إدراج شحنات تجريبية
INSERT IGNORE INTO shipments (barcode, company_id, scan_date) VALUES 
('TEST001', 1, CURDATE()),
('TEST002', 1, CURDATE()),
('TEST001', 1, CURDATE()), -- مكرر
('TEST003', 2, CURDATE()),
('TEST004', 2, CURDATE());

-- إنشاء فهارس لتحسين الأداء
CREATE INDEX IF NOT EXISTS idx_shipments_barcode ON shipments(barcode);
CREATE INDEX IF NOT EXISTS idx_shipments_company_id ON shipments(company_id);
CREATE INDEX IF NOT EXISTS idx_shipments_scan_date ON shipments(scan_date);
CREATE INDEX IF NOT EXISTS idx_shipments_company_date ON shipments(company_id, scan_date);
