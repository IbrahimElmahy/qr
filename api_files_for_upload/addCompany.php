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

if (!$input || !isset($input['name'])) {
    sendResponse(['error' => 'Company name is required'], 400);
}

$companyName = sanitizeInput($input['name']);

if (empty($companyName)) {
    sendResponse(['error' => 'Company name cannot be empty'], 400);
}

try {
    $pdo = getDatabaseConnection();
    
    // التحقق من وجود الشركة
    $stmt = $pdo->prepare("SELECT id FROM companies WHERE name = ?");
    $stmt->execute([$companyName]);
    
    if ($stmt->fetch()) {
        sendResponse(['error' => 'Company already exists'], 409);
    }
    
    // إضافة الشركة الجديدة
    $stmt = $pdo->prepare("INSERT INTO companies (name, is_active) VALUES (?, 1)");
    $stmt->execute([$companyName]);
    
    $companyId = $pdo->lastInsertId();
    
    sendResponse([
        'success' => true,
        'message' => 'Company added successfully',
        'company' => [
            'id' => $companyId,
            'name' => $companyName,
            'is_active' => true
        ]
    ]);
    
} catch (PDOException $e) {
    sendResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
}
?>
