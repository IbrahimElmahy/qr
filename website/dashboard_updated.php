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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-2px);
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
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .btn-custom {
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 500;
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
                            <a class="nav-link active" href="dashboard_updated.php">
                                <i class="fas fa-home me-2"></i> الرئيسية
                            </a>
                            <a class="nav-link" href="companies_management.php">
                                <i class="fas fa-building me-2"></i> إدارة الشركات
                            </a>
                            <a class="nav-link" href="shipments_updated.php">
                                <i class="fas fa-box me-2"></i> استعراض الشحنات
                            </a>
                            <a class="nav-link" href="users_management.php">
                                <i class="fas fa-users me-2"></i> إدارة المستخدمين
                            </a>
                            <a class="nav-link" href="reports.php">
                                <i class="fas fa-chart-bar me-2"></i> التقارير
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
                            <i class="fas fa-tachometer-alt me-2 text-primary"></i>
                            لوحة التحكم
                        </h2>
                        <div class="d-flex align-items-center">
                            <span class="text-muted me-3">مرحباً، <?php echo $_SESSION['username'] ?? 'المدير'; ?></span>
                            <a href="logout.php" class="btn btn-outline-primary">
                                <i class="fas fa-sign-out-alt me-1"></i>
                                تسجيل الخروج
                            </a>
                        </div>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Filters -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-filter me-2"></i>
                                فلترة البيانات
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-4">
                                    <label for="date" class="form-label">التاريخ</label>
                                    <input type="date" class="form-control" id="date" name="date" 
                                           value="<?php echo $selectedDate; ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="company_id" class="form-label">الشركة</label>
                                    <select class="form-select" id="company_id" name="company_id">
                                        <option value="">جميع الشركات</option>
                                        <?php foreach ($companies as $company): ?>
                                            <option value="<?php echo $company['id']; ?>" 
                                                    <?php echo $selectedCompanyId == $company['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($company['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search me-1"></i>فلترة
                                        </button>
                                        <a href="dashboard.php" class="btn btn-outline-secondary">
                                            <i class="fas fa-refresh me-1"></i>إعادة تعيين
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-box fa-2x mb-2"></i>
                                    <h3 class="mb-1"><?php echo $stats['total_unique_shipments'] ?? 0; ?></h3>
                                    <p class="mb-0">شحنات فريدة</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card stat-card success">
                                <div class="card-body text-center">
                                    <i class="fas fa-barcode fa-2x mb-2"></i>
                                    <h3 class="mb-1"><?php echo $stats['total_scans'] ?? 0; ?></h3>
                                    <p class="mb-0">إجمالي المسحات</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card stat-card warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                    <h3 class="mb-1"><?php echo $stats['duplicate_count'] ?? 0; ?></h3>
                                    <p class="mb-0">المكررات</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipments Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i>
                                تفاصيل الشحنات (<?php echo count($shipments); ?>)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>الباركود</th>
                                            <th>الشركة</th>
                                            <th>عدد المسحات</th>
                                            <th>أول مسح</th>
                                            <th>آخر مسح</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($shipments as $index => $shipment): ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td>
                                                    <code><?php echo htmlspecialchars($shipment['barcode']); ?></code>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">
                                                        <?php echo htmlspecialchars($shipment['company_name']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo $shipment['scan_count'] > 1 ? 'warning' : 'success'; ?>">
                                                        <?php echo $shipment['scan_count']; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('H:i:s', strtotime($shipment['first_scan'])); ?></td>
                                                <td><?php echo date('H:i:s', strtotime($shipment['last_scan'])); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
