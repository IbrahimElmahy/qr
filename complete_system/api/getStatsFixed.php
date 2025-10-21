<?php
require_once 'config.php';

// التحقق من نوع الطلب
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendResponse(['error' => 'Method not allowed'], 405);
}

// التحقق من المصادقة مع رسالة خطأ واضحة
try {
    authenticate();
} catch (Exception $e) {
    sendResponse(['error' => 'Authentication failed: ' . $e->getMessage()], 401);
}

try {
    $pdo = getDatabaseConnection();
    
    // الحصول على المعاملات
    $companyId = isset($_GET['company_id']) ? (int)$_GET['company_id'] : null;
    $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
    
    // بناء الاستعلام
    $whereClause = "WHERE scan_date = :date";
    $params = ['date' => $date];
    
    if ($companyId) {
        $whereClause .= " AND company_id = :company_id";
        $params['company_id'] = $companyId;
    }
    
    // إحصائيات عامة
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(DISTINCT barcode) as unique_shipments,
            COUNT(*) as total_scans,
            COUNT(*) - COUNT(DISTINCT barcode) as duplicate_count
        FROM shipments 
        $whereClause
    ");
    $stmt->execute($params);
    $stats = $stmt->fetch();
    
    // تفاصيل الشحنات
    $stmt = $pdo->prepare("
        SELECT 
            s.barcode,
            c.name as company_name,
            COUNT(*) as scan_count,
            MIN(s.scan_date) as first_scan,
            MAX(s.scan_date) as last_scan
        FROM shipments s
        JOIN companies c ON s.company_id = c.id
        $whereClause
        GROUP BY s.barcode, c.name
        ORDER BY scan_count DESC, s.barcode
    ");
    $stmt->execute($params);
    $shipments = $stmt->fetchAll();
    
    sendResponse([
        'success' => true,
        'data' => [
            'statistics' => $stats,
            'shipments' => $shipments
        ]
    ]);
    
} catch (PDOException $e) {
    sendResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
?>
