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
$date = isset($_GET['date']) ? $_GET['date'] : null;
$barcode = isset($_GET['barcode']) ? sanitizeInput($_GET['barcode']) : null;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = isset($_GET['limit']) ? min(100, max(1, (int)$_GET['limit'])) : 50;
$offset = ($page - 1) * $limit;

try {
    $pdo = getDatabaseConnection();
    
    // بناء شروط البحث
    $whereConditions = [];
    $params = [];
    
    if ($companyId) {
        $whereConditions[] = "s.company_id = ?";
        $params[] = $companyId;
    }
    
    if ($date) {
        $whereConditions[] = "s.scan_date = ?";
        $params[] = $date;
    }
    
    if ($barcode) {
        $whereConditions[] = "s.barcode LIKE ?";
        $params[] = "%$barcode%";
    }
    
    $whereClause = $whereConditions ? "WHERE " . implode(" AND ", $whereConditions) : "";
    
    // عدد النتائج الإجمالي
    $countStmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM shipments s 
        JOIN companies c ON s.company_id = c.id 
        $whereClause
    ");
    $countStmt->execute($params);
    $totalCount = $countStmt->fetch()['total'];
    
    // جلب البيانات
    $stmt = $pdo->prepare("
        SELECT 
            s.id,
            s.barcode,
            s.company_id,
            c.name as company_name,
            s.scan_date,
            s.created_at
        FROM shipments s 
        JOIN companies c ON s.company_id = c.id 
        $whereClause
        ORDER BY s.created_at DESC
        LIMIT ? OFFSET ?
    ");
    
    $queryParams = array_merge($params, [$limit, $offset]);
    $stmt->execute($queryParams);
    $shipments = $stmt->fetchAll();
    
    // إضافة معلومات التكرار لكل شحنة
    foreach ($shipments as &$shipment) {
        $duplicateStmt = $pdo->prepare("
            SELECT COUNT(*) as duplicate_count 
            FROM shipments 
            WHERE barcode = ? AND company_id = ? AND scan_date = ?
        ");
        $duplicateStmt->execute([$shipment['barcode'], $shipment['company_id'], $shipment['scan_date']]);
        $shipment['duplicate_count'] = $duplicateStmt->fetch()['duplicate_count'];
    }
    
    sendResponse([
        'success' => true,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => (int)$totalCount,
            'pages' => ceil($totalCount / $limit)
        ],
        'filters' => [
            'company_id' => $companyId,
            'date' => $date,
            'barcode' => $barcode
        ],
        'shipments' => $shipments
    ]);
    
} catch (PDOException $e) {
    sendResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
?>
