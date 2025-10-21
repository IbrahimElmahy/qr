<?php
session_start();

// التحقق من تسجيل الدخول
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../api/config.php';

// معاملات البحث والفلترة
$companyId = $_GET['company_id'] ?? '';
$date = $_GET['date'] ?? '';
$barcode = $_GET['barcode'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 50;
$offset = ($page - 1) * $limit;

try {
    $pdo = getDatabaseConnection();
    
    // جلب قائمة الشركات
    $companiesStmt = $pdo->prepare("SELECT id, name FROM companies ORDER BY name");
    $companiesStmt->execute();
    $companies = $companiesStmt->fetchAll();
    
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
    $totalPages = ceil($totalCount / $limit);
    
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
    
} catch (PDOException $e) {
    $error = "خطأ في قاعدة البيانات: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>استعراض الشحنات - نظام تتبع الشحنات</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            color: #333;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            font-size: 1.5rem;
        }
        
        .logout-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s;
        }
        
        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .nav-links {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .nav-links a {
            background: white;
            color: #667eea;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            text-decoration: none;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }
        
        .nav-links a:hover,
        .nav-links a.active {
            background: #667eea;
            color: white;
        }
        
        .filters {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .filters h2 {
            margin-bottom: 1rem;
            color: #333;
        }
        
        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            align-items: end;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            margin-bottom: 0.5rem;
            color: #555;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select {
            padding: 0.75rem;
            border: 2px solid #e1e5e9;
            border-radius: 5px;
            font-size: 1rem;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: transform 0.2s;
            height: fit-content;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .shipments-table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .shipments-table h2 {
            padding: 1.5rem;
            background: #f8f9fa;
            margin: 0;
            color: #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 1rem;
            text-align: right;
            border-bottom: 1px solid #e1e5e9;
        }
        
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
            position: sticky;
            top: 0;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .duplicate-badge {
            background: #ff6b6b;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 15px;
            font-size: 0.8rem;
        }
        
        .single-badge {
            background: #51cf66;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 15px;
            font-size: 0.8rem;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            padding: 1.5rem;
            background: #f8f9fa;
        }
        
        .pagination a,
        .pagination span {
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            color: #667eea;
            border: 1px solid #e1e5e9;
            background: white;
        }
        
        .pagination a:hover {
            background: #667eea;
            color: white;
        }
        
        .pagination .current {
            background: #667eea;
            color: white;
        }
        
        .pagination .disabled {
            color: #ccc;
            cursor: not-allowed;
        }
        
        .results-info {
            color: #666;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .filter-row {
                grid-template-columns: 1fr;
            }
            
            .shipments-table h2 {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>استعراض الشحنات - نظام تتبع الشحنات</h1>
        <a href="logout.php" class="logout-btn">تسجيل الخروج</a>
    </div>
    
    <div class="container">
        <div class="nav-links">
            <a href="dashboard.php">الصفحة الرئيسية</a>
            <a href="shipments.php" class="active">استعراض الشحنات</a>
        </div>
        
        <div class="filters">
            <h2>البحث والفلترة</h2>
            <form method="GET" class="filter-row">
                <div class="form-group">
                    <label for="company_id">الشركة:</label>
                    <select id="company_id" name="company_id">
                        <option value="">جميع الشركات</option>
                        <?php foreach ($companies as $company): ?>
                            <option value="<?php echo $company['id']; ?>" 
                                    <?php echo $companyId == $company['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($company['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="date">التاريخ:</label>
                    <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>">
                </div>
                
                <div class="form-group">
                    <label for="barcode">رقم الشحنة:</label>
                    <input type="text" id="barcode" name="barcode" value="<?php echo htmlspecialchars($barcode); ?>" 
                           placeholder="ابحث برقم الشحنة">
                </div>
                
                <button type="submit" class="btn">بحث</button>
                <a href="shipments.php" class="btn btn-secondary">مسح</a>
            </form>
        </div>
        
        <?php if (isset($error)): ?>
            <div style="background: #fee; color: #c33; padding: 1rem; border-radius: 5px; margin-bottom: 2rem;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <div class="shipments-table">
            <h2>
                <span>قائمة الشحنات</span>
                <span class="results-info">
                    عرض <?php echo number_format($offset + 1); ?> - <?php echo number_format(min($offset + $limit, $totalCount)); ?> 
                    من <?php echo number_format($totalCount); ?> نتيجة
                </span>
            </h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>رقم الشحنة</th>
                            <th>الشركة</th>
                            <th>تاريخ المسح</th>
                            <th>وقت المسح</th>
                            <th>عدد التكرار</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($shipments)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 2rem; color: #666;">
                                    لا توجد شحنات تطابق معايير البحث
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($shipments as $shipment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($shipment['barcode']); ?></td>
                                    <td><?php echo htmlspecialchars($shipment['company_name']); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($shipment['scan_date'])); ?></td>
                                    <td><?php echo date('H:i:s', strtotime($shipment['created_at'])); ?></td>
                                    <td><?php echo number_format($shipment['duplicate_count']); ?></td>
                                    <td>
                                        <?php if ($shipment['duplicate_count'] > 1): ?>
                                            <span class="duplicate-badge">مكرر</span>
                                        <?php else: ?>
                                            <span class="single-badge">فريد</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>">الأولى</a>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">السابقة</a>
                    <?php else: ?>
                        <span class="disabled">الأولى</span>
                        <span class="disabled">السابقة</span>
                    <?php endif; ?>
                    
                    <?php
                    $start = max(1, $page - 2);
                    $end = min($totalPages, $page + 2);
                    
                    for ($i = $start; $i <= $end; $i++):
                    ?>
                        <?php if ($i == $page): ?>
                            <span class="current"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">التالية</a>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $totalPages])); ?>">الأخيرة</a>
                    <?php else: ?>
                        <span class="disabled">التالية</span>
                        <span class="disabled">الأخيرة</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
