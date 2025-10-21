<?php
session_start();

// التحقق من تسجيل الدخول
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../shipment_tracking/api/config.php';

// معالجة الفلاتر
$companyId = isset($_GET['company_id']) ? (int)$_GET['company_id'] : null;
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$reportType = isset($_GET['report_type']) ? $_GET['report_type'] : 'daily';

try {
    $pdo = getDatabaseConnection();
    
    // جلب الشركات للفلتر
    $stmt = $pdo->prepare("SELECT id, name FROM companies ORDER BY name");
    $stmt->execute();
    $companies = $stmt->fetchAll();
    
    // بناء استعلام التقارير
    $whereClause = "WHERE s.scan_date BETWEEN :start_date AND :end_date";
    $params = ['start_date' => $startDate, 'end_date' => $endDate];
    
    if ($companyId) {
        $whereClause .= " AND s.company_id = :company_id";
        $params['company_id'] = $companyId;
    }
    
    // إحصائيات عامة
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_scans,
            COUNT(DISTINCT s.barcode) as unique_shipments,
            COUNT(*) - COUNT(DISTINCT s.barcode) as duplicates,
            COUNT(DISTINCT s.company_id) as active_companies
        FROM shipments s
        $whereClause
    ");
    $stmt->execute($params);
    $stats = $stmt->fetch();
    
    // تقرير يومي
    if ($reportType === 'daily') {
        $stmt = $pdo->prepare("
            SELECT 
                s.scan_date,
                COUNT(*) as total_scans,
                COUNT(DISTINCT s.barcode) as unique_shipments,
                COUNT(*) - COUNT(DISTINCT s.barcode) as duplicates
            FROM shipments s
            $whereClause
            GROUP BY s.scan_date
            ORDER BY s.scan_date DESC
        ");
        $stmt->execute($params);
        $dailyReport = $stmt->fetchAll();
    }
    
    // تقرير الشركات
    if ($reportType === 'companies') {
        $stmt = $pdo->prepare("
            SELECT 
                c.name as company_name,
                COUNT(s.id) as total_scans,
                COUNT(DISTINCT s.barcode) as unique_shipments,
                COUNT(*) - COUNT(DISTINCT s.barcode) as duplicates
            FROM companies c
            LEFT JOIN shipments s ON c.id = s.company_id AND s.scan_date BETWEEN :start_date AND :end_date
            " . ($companyId ? "WHERE c.id = :company_id" : "") . "
            GROUP BY c.id, c.name
            ORDER BY total_scans DESC
        ");
        $stmt->execute($params);
        $companiesReport = $stmt->fetchAll();
    }
    
    // تقرير الشحنات المكررة
    if ($reportType === 'duplicates') {
        $stmt = $pdo->prepare("
            SELECT 
                s.barcode,
                c.name as company_name,
                COUNT(*) as scan_count,
                MIN(s.scan_date) as first_scan,
                MAX(s.scan_date) as last_scan
            FROM shipments s
            JOIN companies c ON s.company_id = c.id
            $whereClause
            GROUP BY s.barcode, s.company_id
            HAVING COUNT(*) > 1
            ORDER BY scan_count DESC
        ");
        $stmt->execute($params);
        $duplicatesReport = $stmt->fetchAll();
    }
    
} catch (Exception $e) {
    $error = "خطأ في تحميل التقارير: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التقارير - نظام تتبع الشحنات</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 5px 0;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: rgba(255,255,255,0.1);
            color: white;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .btn-custom {
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 500;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .stat-card.success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        .stat-card.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .stat-card.info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar">
                    <div class="p-3">
                        <h4 class="text-white mb-4">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            لوحة التحكم
                        </h4>
                        <nav class="nav flex-column">
                            <a class="nav-link" href="admin_dashboard.php">
                                <i class="fas fa-home me-2"></i> الرئيسية
                            </a>
                            <a class="nav-link" href="companies_management.php">
                                <i class="fas fa-building me-2"></i> إدارة الشركات
                            </a>
                            <a class="nav-link" href="shipments_management.php">
                                <i class="fas fa-box me-2"></i> إدارة الشحنات
                            </a>
                            <a class="nav-link" href="users_management.php">
                                <i class="fas fa-users me-2"></i> إدارة المستخدمين
                            </a>
                            <a class="nav-link active" href="reports.php">
                                <i class="fas fa-chart-bar me-2"></i> التقارير
                            </a>
                            <a class="nav-link" href="settings.php">
                                <i class="fas fa-cog me-2"></i> الإعدادات
                            </a>
                            <hr class="text-white">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i> تسجيل الخروج
                            </a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="mb-0">
                            <i class="fas fa-chart-bar me-2 text-primary"></i>
                            التقارير والإحصائيات
                        </h2>
                        <div class="btn-group" role="group">
                            <button class="btn btn-outline-primary" onclick="exportReport()">
                                <i class="fas fa-download me-2"></i>تصدير التقرير
                            </button>
                            <button class="btn btn-outline-success" onclick="printReport()">
                                <i class="fas fa-print me-2"></i>طباعة
                            </button>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-filter me-2"></i>فلاتر التقرير
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-3">
                                    <label for="report_type" class="form-label">نوع التقرير</label>
                                    <select class="form-select" id="report_type" name="report_type">
                                        <option value="daily" <?php echo $reportType === 'daily' ? 'selected' : ''; ?>>تقرير يومي</option>
                                        <option value="companies" <?php echo $reportType === 'companies' ? 'selected' : ''; ?>>تقرير الشركات</option>
                                        <option value="duplicates" <?php echo $reportType === 'duplicates' ? 'selected' : ''; ?>>الشحنات المكررة</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="company_id" class="form-label">الشركة</label>
                                    <select class="form-select" id="company_id" name="company_id">
                                        <option value="">جميع الشركات</option>
                                        <?php if (isset($companies)): ?>
                                            <?php foreach ($companies as $company): ?>
                                                <option value="<?php echo $company['id']; ?>" <?php echo $companyId == $company['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($company['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="start_date" class="form-label">من تاريخ</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $startDate; ?>">
                                </div>
                                <div class="col-md-2">
                                    <label for="end_date" class="form-label">إلى تاريخ</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $endDate; ?>">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-search me-2"></i>عرض التقرير
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <?php if (isset($stats)): ?>
                        <div class="row mb-4">
                            <div class="col-md-3 mb-3">
                                <div class="card stat-card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-chart-line fa-2x mb-2"></i>
                                        <h3 class="mb-1"><?php echo $stats['total_scans']; ?></h3>
                                        <p class="mb-0">إجمالي المسحات</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card stat-card success">
                                    <div class="card-body text-center">
                                        <i class="fas fa-box fa-2x mb-2"></i>
                                        <h3 class="mb-1"><?php echo $stats['unique_shipments']; ?></h3>
                                        <p class="mb-0">شحنات فريدة</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card stat-card warning">
                                    <div class="card-body text-center">
                                        <i class="fas fa-copy fa-2x mb-2"></i>
                                        <h3 class="mb-1"><?php echo $stats['duplicates']; ?></h3>
                                        <p class="mb-0">مكررات</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card stat-card info">
                                    <div class="card-body text-center">
                                        <i class="fas fa-building fa-2x mb-2"></i>
                                        <h3 class="mb-1"><?php echo $stats['active_companies']; ?></h3>
                                        <p class="mb-0">شركات نشطة</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Report Content -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-file-alt me-2"></i>
                                <?php 
                                $reportTitles = [
                                    'daily' => 'التقرير اليومي',
                                    'companies' => 'تقرير الشركات',
                                    'duplicates' => 'الشحنات المكررة'
                                ];
                                echo $reportTitles[$reportType] ?? 'التقرير';
                                ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <?php echo $error; ?>
                                </div>
                            <?php else: ?>
                                <?php if ($reportType === 'daily' && isset($dailyReport)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>التاريخ</th>
                                                    <th>إجمالي المسحات</th>
                                                    <th>شحنات فريدة</th>
                                                    <th>مكررات</th>
                                                    <th>نسبة المكررات</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($dailyReport as $row): ?>
                                                    <tr>
                                                        <td><?php echo $row['scan_date']; ?></td>
                                                        <td><span class="badge bg-primary"><?php echo $row['total_scans']; ?></span></td>
                                                        <td><span class="badge bg-success"><?php echo $row['unique_shipments']; ?></span></td>
                                                        <td><span class="badge bg-warning"><?php echo $row['duplicates']; ?></span></td>
                                                        <td><?php echo $row['total_scans'] > 0 ? round(($row['duplicates'] / $row['total_scans']) * 100, 2) : 0; ?>%</td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php elseif ($reportType === 'companies' && isset($companiesReport)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>اسم الشركة</th>
                                                    <th>إجمالي المسحات</th>
                                                    <th>شحنات فريدة</th>
                                                    <th>مكررات</th>
                                                    <th>نسبة المكررات</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($companiesReport as $row): ?>
                                                    <tr>
                                                        <td><strong><?php echo htmlspecialchars($row['company_name']); ?></strong></td>
                                                        <td><span class="badge bg-primary"><?php echo $row['total_scans']; ?></span></td>
                                                        <td><span class="badge bg-success"><?php echo $row['unique_shipments']; ?></span></td>
                                                        <td><span class="badge bg-warning"><?php echo $row['duplicates']; ?></span></td>
                                                        <td><?php echo $row['total_scans'] > 0 ? round(($row['duplicates'] / $row['total_scans']) * 100, 2) : 0; ?>%</td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php elseif ($reportType === 'duplicates' && isset($duplicatesReport)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>رقم الباركود</th>
                                                    <th>الشركة</th>
                                                    <th>عدد المسحات</th>
                                                    <th>أول مسح</th>
                                                    <th>آخر مسح</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($duplicatesReport as $row): ?>
                                                    <tr>
                                                        <td><code><?php echo htmlspecialchars($row['barcode']); ?></code></td>
                                                        <td><?php echo htmlspecialchars($row['company_name']); ?></td>
                                                        <td><span class="badge bg-danger"><?php echo $row['scan_count']; ?></span></td>
                                                        <td><?php echo $row['first_scan']; ?></td>
                                                        <td><?php echo $row['last_scan']; ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center text-muted">
                                        <i class="fas fa-chart-bar fa-3x mb-3"></i>
                                        <p>لا توجد بيانات للعرض</p>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function exportReport() {
            // تصدير التقرير كـ CSV
            const table = document.querySelector('table');
            if (table) {
                let csv = '';
                const rows = table.querySelectorAll('tr');
                rows.forEach(row => {
                    const cells = row.querySelectorAll('th, td');
                    const rowData = Array.from(cells).map(cell => cell.textContent.trim());
                    csv += rowData.join(',') + '\n';
                });
                
                const blob = new Blob([csv], { type: 'text/csv' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'report_<?php echo date('Y-m-d'); ?>.csv';
                a.click();
                window.URL.revokeObjectURL(url);
            }
        }
        
        function printReport() {
            window.print();
        }
    </script>
</body>
</html>
