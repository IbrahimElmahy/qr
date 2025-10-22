<?php
require_once 'config.php';

// التحقق من نوع الطلب
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendResponse(['error' => 'Method not allowed'], 405);
}

// التحقق من المصادقة
authenticate();

// قراءة المعاملات
$companyId = isset($_GET['company_id']) ? (int)$_GET['company_id'] : null;
$date = isset($_GET['date']) && $_GET['date'] !== '' ? $_GET['date'] : null;
$startDate = isset($_GET['start_date']) && $_GET['start_date'] !== '' ? $_GET['start_date'] : null;
$endDate = isset($_GET['end_date']) && $_GET['end_date'] !== '' ? $_GET['end_date'] : null;

// في حال تم تحديد تاريخ محدد، يتم تجاهل النطاق الزمني
if ($date) {
    $startDate = null;
    $endDate = null;
}

// بناء شروط الاستعلام المشتركة
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

try {
    $pdo = getDatabaseConnection();

    $whereSql = $whereClause ? ' ' . $whereClause : '';

    // إحصائيات عامة
    $stmt = $pdo->prepare("
        SELECT
            COUNT(DISTINCT s.barcode) as total_unique_shipments,
            COUNT(s.id) as total_scans,
            (COUNT(s.id) - COUNT(DISTINCT s.barcode)) as duplicate_count
        FROM shipments s
        $whereSql
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
        $whereSql
        GROUP BY s.barcode, c.name
        ORDER BY scan_count DESC, s.barcode
    ");
    $stmt->execute($params);
    $shipments = $stmt->fetchAll();
    
    // إحصائيات حسب الشركة
    $companyJoinConditions = ['c.id = s.company_id'];
    $companyParams = [];

    if ($date) {
        $companyJoinConditions[] = 's.scan_date = :date';
        $companyParams['date'] = $date;
    } else {
        if ($startDate) {
            $companyJoinConditions[] = 's.scan_date >= :start_date';
            $companyParams['start_date'] = $startDate;
        }
        if ($endDate) {
            $companyJoinConditions[] = 's.scan_date <= :end_date';
            $companyParams['end_date'] = $endDate;
        }
    }

    $companyWhere = '';
    if ($companyId) {
        $companyWhere = 'WHERE c.id = :company_id';
        $companyParams['company_id'] = $companyId;
    }

    $joinClause = 'LEFT JOIN shipments s ON ' . implode(' AND ', $companyJoinConditions);

    $stmt = $pdo->prepare("
        SELECT
            c.id,
            c.name,
            COUNT(DISTINCT s.barcode) as unique_shipments,
            COUNT(s.id) as total_scans
        FROM companies c
        $joinClause
        $companyWhere
        GROUP BY c.id, c.name
        ORDER BY c.name
    ");

    $stmt->execute($companyParams);
    $companyStats = $stmt->fetchAll();
    
    sendResponse([
        'success' => true,
        'filters' => [
            'date' => $date,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'company_id' => $companyId,
        ],
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
