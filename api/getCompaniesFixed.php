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
    
    // جلب جميع الشركات
    $stmt = $pdo->prepare("
        SELECT 
            c.id,
            c.name,
            c.is_active,
            c.created_at,
            COUNT(s.id) as total_shipments,
            COUNT(DISTINCT s.scan_date) as active_days
        FROM companies c 
        LEFT JOIN shipments s ON c.id = s.company_id
        GROUP BY c.id, c.name, c.is_active, c.created_at
        ORDER BY c.name
    ");
    $stmt->execute();
    $companies = $stmt->fetchAll();
    
    sendResponse([
        'success' => true,
        'data' => $companies
    ]);
    
} catch (PDOException $e) {
    sendResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
?>
