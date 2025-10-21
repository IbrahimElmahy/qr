<?php
session_start();

// التحقق من تسجيل الدخول
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../api/config.php';

// جلب الإحصائيات المتقدمة
try {
    $pdo = getDatabaseConnection();
    
    // إجمالي الشحنات
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM shipments");
    $stmt->execute();
    $totalShipments = $stmt->fetch()['total'];
    
    // الشحنات اليوم
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM shipments WHERE scan_date = CURDATE()");
    $stmt->execute();
    $todayShipments = $stmt->fetch()['total'];
    
    // الشحنات هذا الأسبوع
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM shipments WHERE scan_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
    $stmt->execute();
    $weekShipments = $stmt->fetch()['total'];
    
    // الشحنات هذا الشهر
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM shipments WHERE scan_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
    $stmt->execute();
    $monthShipments = $stmt->fetch()['total'];
    
    // الشحنات الفريدة اليوم
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT barcode) as total FROM shipments WHERE scan_date = CURDATE()");
    $stmt->execute();
    $uniqueToday = $stmt->fetch()['total'];
    
    // المكررات اليوم
    $duplicatesToday = $todayShipments - $uniqueToday;
    
    // إحصائيات الشركات
    $stmt = $pdo->prepare("SELECT c.name, COUNT(s.id) as shipment_count FROM companies c LEFT JOIN shipments s ON c.id = s.company_id GROUP BY c.id, c.name ORDER BY shipment_count DESC");
    $stmt->execute();
    $companyStats = $stmt->fetchAll();
    
    // إحصائيات يومية (آخر 7 أيام)
    $stmt = $pdo->prepare("SELECT scan_date, COUNT(*) as count FROM shipments WHERE scan_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY scan_date ORDER BY scan_date ASC");
    $stmt->execute();
    $dailyStats = $stmt->fetchAll();
    
} catch (Exception $e) {
    $error = "خطأ في تحميل البيانات: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التقارير المتقدمة - نظام تتبع الشحنات</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                                <i class="fas fa-chart-bar me-2"></i>
                                التقارير المتقدمة
                            </h4>
                            <h5 class="text-white mb-0 d-md-none">
                                <i class="fas fa-chart-bar me-2"></i>
                                التقارير المتقدمة
                            </h5>
                            <button class="btn btn-sm btn-outline-light" onclick="toggleSidebar()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <nav class="nav flex-column">
                            <a class="nav-link" href="dashboard.php">
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
                            <a class="nav-link active" href="reports.php">
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
                            <i class="fas fa-chart-bar me-2 text-primary"></i>
                            التقارير المتقدمة
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

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-box fa-2x mb-2"></i>
                                    <h3 class="mb-1"><?php echo $totalShipments ?? 0; ?></h3>
                                    <p class="mb-0">إجمالي الشحنات</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card success">
                                <div class="card-body text-center">
                                    <i class="fas fa-calendar-day fa-2x mb-2"></i>
                                    <h3 class="mb-1"><?php echo $todayShipments ?? 0; ?></h3>
                                    <p class="mb-0">شحنات اليوم</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-calendar-week fa-2x mb-2"></i>
                                    <h3 class="mb-1"><?php echo $weekShipments ?? 0; ?></h3>
                                    <p class="mb-0">شحنات هذا الأسبوع</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card info">
                                <div class="card-body text-center">
                                    <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                                    <h3 class="mb-1"><?php echo $monthShipments ?? 0; ?></h3>
                                    <p class="mb-0">شحنات هذا الشهر</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-chart-line me-2"></i>
                                        الإحصائيات اليومية (آخر 7 أيام)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="dailyChart" height="100"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-info-circle me-2"></i>
                                        ملخص اليوم
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>إجمالي الشحنات:</strong> <?php echo $todayShipments ?? 0; ?>
                                    </div>
                                    <div class="mb-3">
                                        <strong>الشحنات الفريدة:</strong> <?php echo $uniqueToday ?? 0; ?>
                                    </div>
                                    <div class="mb-3">
                                        <strong>المكررات:</strong> <?php echo $duplicatesToday ?? 0; ?>
                                    </div>
                                    <div class="mb-3">
                                        <strong>نسبة المكررات:</strong> 
                                        <?php 
                                        if ($todayShipments > 0) {
                                            echo round(($duplicatesToday / $todayShipments) * 100, 1) . '%';
                                        } else {
                                            echo '0%';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Company Statistics -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-building me-2"></i>
                                        إحصائيات الشركات
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>#</th>
                                                    <th>اسم الشركة</th>
                                                    <th>عدد الشحنات</th>
                                                    <th>النسبة المئوية</th>
                                                    <th>شريط التقدم</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($companyStats as $index => $company): ?>
                                                    <tr>
                                                        <td><?php echo $index + 1; ?></td>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($company['name']); ?></strong>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-primary"><?php echo $company['shipment_count']; ?></span>
                                                        </td>
                                                        <td>
                                                            <?php 
                                                            if ($totalShipments > 0) {
                                                                $percentage = round(($company['shipment_count'] / $totalShipments) * 100, 1);
                                                                echo $percentage . '%';
                                                            } else {
                                                                echo '0%';
                                                            }
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <div class="progress" style="height: 20px;">
                                                                <div class="progress-bar" role="progressbar" 
                                                                     style="width: <?php echo $totalShipments > 0 ? ($company['shipment_count'] / $totalShipments) * 100 : 0; ?>%"
                                                                     aria-valuenow="<?php echo $company['shipment_count']; ?>" 
                                                                     aria-valuemin="0" 
                                                                     aria-valuemax="<?php echo $totalShipments; ?>">
                                                                </div>
                                                            </div>
                                                        </td>
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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Daily Chart
        const dailyData = <?php echo json_encode($dailyStats); ?>;
        const labels = dailyData.map(item => item.scan_date);
        const data = dailyData.map(item => item.count);
        
        const ctx = document.getElementById('dailyChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'عدد الشحنات',
                    data: data,
                    borderColor: 'rgb(102, 126, 234)',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
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