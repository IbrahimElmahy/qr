<?php
require_once 'config.php';

// تعطيل المصادقة مؤقتاً للاختبار
// authenticate();

// قراءة المعاملات
$companyId = isset($_GET['company_id']) ? (int)$_GET['company_id'] : null;
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

try {
    $pdo = getDatabaseConnection();
    
    // بناء الاستعلام
    $whereClause = "WHERE s.scan_date = ?";
    $params = [$date];
    
    if ($companyId) {
        $whereClause .= " AND s.company_id = ?";
        $params[] = $companyId;
    }
    
    // إحصائيات عامة
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(DISTINCT s.barcode) as total_unique_shipments,
            COUNT(s.id) as total_scans,
            (COUNT(s.id) - COUNT(DISTINCT s.barcode)) as duplicate_count
        FROM shipments s 
        $whereClause
    ");
    $stmt->execute($params);
    $stats = $stmt->fetch();
    
    // قائمة الشحنات مع عدد التكرار
    $stmt = $pdo->prepare("
        SELECT 
            s.barcode,
            c.name as company_name,
            COUNT(s.id) as scan_count,
            MIN(s.created_at) as first_scan,
            MAX(s.created_at) as last_scan
        FROM shipments s 
        JOIN companies c ON s.company_id = c.id 
        $whereClause
        GROUP BY s.barcode, c.name
        ORDER BY scan_count DESC, s.barcode
    ");
    $stmt->execute($params);
    $shipments = $stmt->fetchAll();
    
    // إحصائيات حسب الشركة
    $stmt = $pdo->prepare("
        SELECT 
            c.id,
            c.name,
            COUNT(DISTINCT s.barcode) as unique_shipments,
            COUNT(s.id) as total_scans
        FROM companies c 
        LEFT JOIN shipments s ON c.id = s.company_id AND s.scan_date = ?
        " . ($companyId ? "WHERE c.id = ?" : "") . "
        GROUP BY c.id, c.name
        ORDER BY c.name
    ");
    
    $companyParams = [$date];
    if ($companyId) {
        $companyParams[] = $companyId;
    }
    $stmt->execute($companyParams);
    $companyStats = $stmt->fetchAll();
    
    sendResponse([
        'success' => true,
        'date' => $date,
        'company_id' => $companyId,
        'statistics' => [
            'total_unique_shipments' => (int)$stats['total_unique_shipments'],
            'total_scans' => (int)$stats['total_scans'],
            'duplicate_count' => (int)$stats['duplicate_count']
        ],
        'shipments' => $shipments,
        'company_statistics' => $companyStats
    ]);
    
} catch (PDOException $e) {
    sendResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
?>
