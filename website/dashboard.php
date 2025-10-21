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
            background: linear-gradient(135deg, #1e90ff 0%, #4682b4 100%);
            min-height: 100vh;
            box-shadow: 2px 0 15px rgba(30, 144, 255, 0.3);
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
            background: linear-gradient(135deg, #1e90ff 0%, #4682b4 100%);
            color: white;
            border: none;
            box-shadow: 0 8px 25px rgba(30, 144, 255, 0.3);
        }
        .stat-card.success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            box-shadow: 0 8px 25px rgba(17, 153, 142, 0.3);
        }
        .stat-card.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            box-shadow: 0 8px 25px rgba(240, 147, 251, 0.3);
        }
        .stat-card.info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            box-shadow: 0 8px 25px rgba(79, 172, 254, 0.3);
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .btn-custom {
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 500;
            background: linear-gradient(135deg, #1e90ff 0%, #4682b4 100%);
            border: none;
            color: white;
            box-shadow: 0 4px 15px rgba(30, 144, 255, 0.3);
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(30, 144, 255, 0.4);
        }
        
        /* Mobile Menu Button */
        .mobile-menu-btn {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1060;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        /* Responsive Design */
        @media (min-width: 769px) {
            .sidebar {
                position: fixed;
                top: 0;
                right: 0;
                width: 250px;
                height: 100vh;
                z-index: 1000;
                transform: translateX(0);
                transition: transform 0.3s ease;
            }
            
            .main-content {
                margin-right: 250px;
            }
            
            .mobile-menu-btn {
                display: none;
            }
        }
        
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: -100%;
                width: 80%;
                height: 100vh;
                z-index: 1050;
                transition: left 0.3s ease;
                overflow-y: auto;
            }
            
            .sidebar.show {
                left: 0;
            }
            
            .main-content {
                margin-left: 0;
            }
        }
        
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.875rem;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .stat-card .card-body {
                padding: 1rem 0.5rem;
            }
        }
        
        @media (max-width: 576px) {
            .container-fluid {
                padding: 0.5rem;
            }
            
            .stat-card .card-body {
                padding: 1rem 0.5rem;
            }
            
            .btn {
                font-size: 0.875rem;
                padding: 0.375rem 0.75rem;
            }
            
            .table th, .table td {
                padding: 0.5rem 0.25rem;
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Menu Button for All Devices -->
        <button class="btn btn-primary mobile-menu-btn" type="button" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        
        <div class="row">
            <!-- Sidebar -->
            <div class="col-12 col-md-3 col-lg-2 px-0">
                <div class="sidebar" id="sidebar">
                    <div class="p-3">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="text-white mb-0 d-none d-md-block">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                لوحة التحكم
                            </h4>
                            <h5 class="text-white mb-0 d-md-none">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                لوحة التحكم
                            </h5>
                            <button class="btn btn-sm btn-outline-light" onclick="toggleSidebar()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <nav class="nav flex-column">
                            <a class="nav-link active" href="dashboard.php">
                                <i class="fas fa-home me-3"></i> الرئيسية
                            </a>
                            <a class="nav-link" href="companies_management.php">
                                <i class="fas fa-building me-3"></i> إدارة الشركات
                            </a>
                            <a class="nav-link" href="shipments.php">
                                <i class="fas fa-box me-3"></i> استعراض الشحنات
                            </a>
                            <a class="nav-link" href="users_management.php">
                                <i class="fas fa-users me-3"></i> إدارة المستخدمين
                            </a>
                            <a class="nav-link" href="reports.php">
                                <i class="fas fa-chart-bar me-3"></i> التقارير
                            </a>
                            <hr class="text-white my-3">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt me-3"></i> تسجيل الخروج
                            </a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-12 col-md-9 col-lg-10">
                <div class="p-2 p-md-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="mb-0">
                            <i class="fas fa-tachometer-alt me-2 text-primary"></i>
                            لوحة التحكم
                        </h2>
                        <div class="d-flex align-items-center">
                            <span class="text-muted me-3">مرحباً، <?php echo $_SESSION['username'] ?? 'المدير'; ?></span>
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
                        <div class="col-6 col-md-4 mb-3">
                            <div class="card stat-card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-box fa-lg mb-2 d-none d-md-block"></i>
                                    <i class="fas fa-box mb-2 d-md-none"></i>
                                    <h3 class="mb-1"><?php echo $stats['total_unique_shipments'] ?? 0; ?></h3>
                                    <p class="mb-0 small">شحنات فريدة</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 mb-3">
                            <div class="card stat-card success h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-barcode fa-lg mb-2 d-none d-md-block"></i>
                                    <i class="fas fa-barcode mb-2 d-md-none"></i>
                                    <h3 class="mb-1"><?php echo $stats['total_scans'] ?? 0; ?></h3>
                                    <p class="mb-0 small">إجمالي المسحات</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 mb-3">
                            <div class="card stat-card warning h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-exclamation-triangle fa-lg mb-2 d-none d-md-block"></i>
                                    <i class="fas fa-exclamation-triangle mb-2 d-md-none"></i>
                                    <h3 class="mb-1"><?php echo $stats['duplicate_count'] ?? 0; ?></h3>
                                    <p class="mb-0 small">المكررات</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipments Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i>
                                <span class="d-none d-sm-inline">تفاصيل الشحنات</span>
                                <span class="d-sm-none">الشحنات</span>
                                (<?php echo count($shipments); ?>)
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th class="d-none d-md-table-cell">#</th>
                                            <th>الباركود</th>
                                            <th class="d-none d-sm-table-cell">الشركة</th>
                                            <th class="d-none d-md-table-cell">عدد المسحات</th>
                                            <th class="d-none d-lg-table-cell">أول مسح</th>
                                            <th class="d-none d-lg-table-cell">آخر مسح</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($shipments as $index => $shipment): ?>
                                            <tr>
                                                <td class="d-none d-md-table-cell"><?php echo $index + 1; ?></td>
                                                <td>
                                                    <code><?php echo htmlspecialchars($shipment['barcode']); ?></code>
                                                    <div class="d-sm-none small text-muted mt-1">
                                                        <?php echo htmlspecialchars($shipment['company_name']); ?>
                                                    </div>
                                                </td>
                                                <td class="d-none d-sm-table-cell">
                                                    <span class="badge bg-primary">
                                                        <?php echo htmlspecialchars($shipment['company_name']); ?>
                                                    </span>
                                                </td>
                                                <td class="d-none d-md-table-cell">
                                                    <span class="badge bg-<?php echo $shipment['scan_count'] > 1 ? 'warning' : 'success'; ?>">
                                                        <?php echo $shipment['scan_count']; ?>
                                                    </span>
                                                </td>
                                                <td class="d-none d-lg-table-cell"><?php echo date('H:i:s', strtotime($shipment['first_scan'])); ?></td>
                                                <td class="d-none d-lg-table-cell"><?php echo date('H:i:s', strtotime($shipment['last_scan'])); ?></td>
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
    <script>
        // Toggle sidebar function
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');
        }
        
        // Close sidebar when clicking outside
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const menuBtn = document.querySelector('.mobile-menu-btn');
            
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !menuBtn.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
        
        // Close sidebar when window is resized to desktop
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth > 768) {
                sidebar.classList.remove('show');
            }
        });
    </script>
</body>
</html>
