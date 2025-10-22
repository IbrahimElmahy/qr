<?php
require_once 'config.php';

// التحقق من نوع الطلب
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(['error' => 'Method not allowed'], 405);
}

// التحقق من المصادقة
authenticate();

// قراءة البيانات المرسلة
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['barcode']) || !isset($input['company_id'])) {
    sendResponse(['error' => 'Barcode and company ID are required'], 400);
}

$barcode = sanitizeInput($input['barcode']);
$companyId = (int)$input['company_id'];
$scanDate = isset($input['scan_date']) ? $input['scan_date'] : date('Y-m-d');

if (empty($barcode)) {
    sendResponse(['error' => 'Barcode cannot be empty'], 400);
}

if ($companyId <= 0) {
    sendResponse(['error' => 'Invalid company ID'], 400);
}

try {
    $pdo = getDatabaseConnection();
    
    // التحقق من وجود الشركة
    $stmt = $pdo->prepare("SELECT id, name, is_active FROM companies WHERE id = ?");
    $stmt->execute([$companyId]);
    $company = $stmt->fetch();
    
    if (!$company) {
        sendResponse(['error' => 'Company not found'], 404);
    }
    
    if (!$company['is_active']) {
        sendResponse(['error' => 'Company is inactive'], 400);
    }
    
    // إضافة الشحنة
    $stmt = $pdo->prepare("INSERT INTO shipments (barcode, company_id, scan_date) VALUES (?, ?, ?)");
    $stmt->execute([$barcode, $companyId, $scanDate]);
    
    $shipmentId = $pdo->lastInsertId();
    
    sendResponse([
        'success' => true,
        'message' => 'Shipment added successfully',
        'shipment' => [
            'id' => $shipmentId,
            'barcode' => $barcode,
            'company_id' => $companyId,
            'company_name' => $company['name'],
            'scan_date' => $scanDate
        ]
    ]);
    
} catch (PDOException $e) {
    sendResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
?>
