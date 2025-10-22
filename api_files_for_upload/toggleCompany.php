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

if (!$input || !isset($input['company_id'])) {
    sendResponse(['error' => 'Company ID is required'], 400);
}

$companyId = (int)$input['company_id'];

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
    
    // تبديل حالة الشركة
    $newStatus = $company['is_active'] ? 0 : 1;
    $stmt = $pdo->prepare("UPDATE companies SET is_active = ? WHERE id = ?");
    $stmt->execute([$newStatus, $companyId]);
    
    sendResponse([
        'success' => true,
        'message' => 'Company status updated successfully',
        'company' => [
            'id' => $company['id'],
            'name' => $company['name'],
            'is_active' => (bool)$newStatus
        ]
    ]);
    
} catch (PDOException $e) {
    sendResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
?>
