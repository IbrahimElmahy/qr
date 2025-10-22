<?php
require_once 'config.php';

// التحقق من نوع الطلب
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendResponse(['error' => 'Method not allowed'], 405);
}

// بدون مصادقة للاختبار
try {
    $pdo = getDatabaseConnection();
    
    // الحصول على المعاملات
    $companyId = isset($_GET['company_id']) ? (int)$_GET['company_id'] : null;
    $date = isset($_GET['date']) && $_GET['date'] !== '' ? $_GET['date'] : null;
    $startDate = isset($_GET['start_date']) && $_GET['start_date'] !== '' ? $_GET['start_date'] : null;
    $endDate = isset($_GET['end_date']) && $_GET['end_date'] !== '' ? $_GET['end_date'] : null;

    if ($date) {
        $startDate = null;
        $endDate = null;
    }

    $conditions = [];
    $params = [];

    if ($date) {
        $conditions[] = 's.scan_date = :date';
        $params['date'] = $date;
    } else {
        if ($startDate) {
            $conditions[] = 's.scan_date >= :start_date';
            $params['start_date'] = $startDate;
        }
        if ($endDate) {
            $conditions[] = 's.scan_date <= :end_date';
            $params['end_date'] = $endDate;
        }
    }

    if ($companyId) {
        $conditions[] = 's.company_id = :company_id';
        $params['company_id'] = $companyId;
    }

    $whereClause = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
    $whereSql = $whereClause ? ' ' . $whereClause : '';
    
    // إحصائيات عامة
    $stmt = $pdo->prepare("
        SELECT
            COUNT(DISTINCT s.barcode) as unique_shipments,
            COUNT(s.id) as total_scans,
            COUNT(s.id) - COUNT(DISTINCT s.barcode) as duplicate_count
        FROM shipments s
        $whereSql
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
        $whereSql
        GROUP BY s.barcode, c.name
        ORDER BY scan_count DESC, s.barcode
    ");
    $stmt->execute($params);
    $shipments = $stmt->fetchAll();
    
    sendResponse([
        'success' => true,
        'statistics' => $stats,
        'shipments' => $shipments
    ]);
    
} catch (PDOException $e) {
    sendResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
?>
