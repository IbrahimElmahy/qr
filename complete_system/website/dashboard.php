<?php
session_start();

// التحقق من تسجيل الدخول
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../api/config.php';

// جلب البيانات
$selectedDate = $_GET['date'] ?? date('Y-m-d');
$selectedCompanyId = $_GET['company_id'] ?? '';

try {
    $pdo = getDatabaseConnection();
    
    // جلب قائمة الشركات
    $companiesStmt = $pdo->prepare("SELECT id, name FROM companies ORDER BY name");
    $companiesStmt->execute();
    $companies = $companiesStmt->fetchAll();
    
    // جلب الإحصائيات
    $whereClause = "WHERE s.scan_date = ?";
    $params = [$selectedDate];
    
    if ($selectedCompanyId) {
        $whereClause .= " AND s.company_id = ?";
        $params[] = $selectedCompanyId;
    }
    
    $statsStmt = $pdo->prepare("
        SELECT 
            COUNT(DISTINCT s.barcode) as total_unique_shipments,
            COUNT(s.id) as total_scans,
            (COUNT(s.id) - COUNT(DISTINCT s.barcode)) as duplicate_count
        FROM shipments s 
        $whereClause
    ");
    $statsStmt->execute($params);
    $stats = $statsStmt->fetch();
    
    // جلب تفاصيل الشحنات
    $shipmentsStmt = $pdo->prepare("
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
    $shipmentsStmt->execute($params);
    $shipments = $shipmentsStmt->fetchAll();
    
} catch (PDOException $e) {
    $error = "خطأ في قاعدة البيانات: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - نظام تتبع الشحنات</title>
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
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
            display: flex;
            gap: 1rem;
            align-items: end;
            flex-wrap: wrap;
        }
        
        .form-group {
            flex: 1;
            min-width: 200px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
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
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .stat-card h3 {
            color: #667eea;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .stat-card p {
            color: #666;
            font-size: 1rem;
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
        
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .filter-row {
                flex-direction: column;
            }
            
            .form-group {
                min-width: 100%;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>لوحة التحكم - نظام تتبع الشحنات</h1>
        <a href="logout.php" class="logout-btn">تسجيل الخروج</a>
    </div>
    
    <div class="container">
        <div class="nav-links">
            <a href="dashboard.php" class="active">الصفحة الرئيسية</a>
            <a href="shipments.php">استعراض الشحنات</a>
        </div>
        
        <div class="filters">
            <h2>فلترة البيانات</h2>
            <form method="GET" class="filter-row">
                <div class="form-group">
                    <label for="date">التاريخ:</label>
                    <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($selectedDate); ?>">
                </div>
                
                <div class="form-group">
                    <label for="company_id">الشركة:</label>
                    <select id="company_id" name="company_id">
                        <option value="">جميع الشركات</option>
                        <?php foreach ($companies as $company): ?>
                            <option value="<?php echo $company['id']; ?>" 
                                    <?php echo $selectedCompanyId == $company['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($company['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="btn">تطبيق الفلتر</button>
            </form>
        </div>
        
        <?php if (isset($error)): ?>
            <div style="background: #fee; color: #c33; padding: 1rem; border-radius: 5px; margin-bottom: 2rem;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?php echo number_format($stats['total_unique_shipments']); ?></h3>
                <p>إجمالي الشحنات</p>
            </div>
            
            <div class="stat-card">
                <h3><?php echo number_format($stats['duplicate_count']); ?></h3>
                <p>الشحنات المكررة</p>
            </div>
            
            <div class="stat-card">
                <h3><?php echo number_format($stats['total_scans']); ?></h3>
                <p>إجمالي المسحات</p>
            </div>
        </div>
        
        <div class="shipments-table">
            <h2>تفاصيل الشحنات</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>رقم الشحنة</th>
                            <th>الشركة</th>
                            <th>عدد المسحات</th>
                            <th>أول مسح</th>
                            <th>آخر مسح</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($shipments)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 2rem; color: #666;">
                                    لا توجد شحنات في هذا التاريخ
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($shipments as $shipment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($shipment['barcode']); ?></td>
                                    <td><?php echo htmlspecialchars($shipment['company_name']); ?></td>
                                    <td><?php echo number_format($shipment['scan_count']); ?></td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($shipment['first_scan'])); ?></td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($shipment['last_scan'])); ?></td>
                                    <td>
                                        <?php if ($shipment['scan_count'] > 1): ?>
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
        </div>
    </div>
</body>
</html>
