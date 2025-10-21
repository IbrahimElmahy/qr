<?php
session_start();

// التحقق من تسجيل الدخول
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../api/config.php';

// جلب الإحصائيات العامة
try {
    $pdo = getDatabaseConnection();
    
    // إجمالي الشركات
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM companies");
    $stmt->execute();
    $totalCompanies = $stmt->fetch()['total'];
    
    // إجمالي الشحنات
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM shipments");
    $stmt->execute();
    $totalShipments = $stmt->fetch()['total'];
    
    // الشحنات اليوم
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM shipments WHERE scan_date = CURDATE()");
    $stmt->execute();
    $todayShipments = $stmt->fetch()['total'];
    
    // الشحنات الفريدة اليوم
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT barcode) as total FROM shipments WHERE scan_date = CURDATE()");
    $stmt->execute();
    $uniqueToday = $stmt->fetch()['total'];
    
    // المكررات اليوم
    $duplicatesToday = $todayShipments - $uniqueToday;
    
    // الشركات النشطة
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM companies WHERE is_active = 1");
    $stmt->execute();
    $activeCompanies = $stmt->fetch()['total'];
    
} catch (Exception $e) {
    $error = "خطأ في تحميل البيانات: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم الإدارية - نظام تتبع الشحنات</title>
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
                            <a class="nav-link active" href="admin_dashboard.php">
                                <i class="fas fa-home me-2"></i> الرئيسية
                            </a>
                            <a class="nav-link" href="companies_management.php">
                                <i class="fas fa-building me-2"></i> إدارة الشركات
                            </a>
                            <a class="nav-link" href="shipments.php">
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
                            لوحة التحكم الإدارية
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

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-building fa-2x mb-2"></i>
                                    <h3 class="mb-1"><?php echo $totalCompanies ?? 0; ?></h3>
                                    <p class="mb-0">إجمالي الشركات</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card success">
                                <div class="card-body text-center">
                                    <i class="fas fa-box fa-2x mb-2"></i>
                                    <h3 class="mb-1"><?php echo $totalShipments ?? 0; ?></h3>
                                    <p class="mb-0">إجمالي الشحنات</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-calendar-day fa-2x mb-2"></i>
                                    <h3 class="mb-1"><?php echo $todayShipments ?? 0; ?></h3>
                                    <p class="mb-0">شحنات اليوم</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card info">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-line fa-2x mb-2"></i>
                                    <h3 class="mb-1"><?php echo $uniqueToday ?? 0; ?></h3>
                                    <p class="mb-0">شحنات فريدة اليوم</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-bolt me-2"></i>
                                        جميع الصفحات المتاحة
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-2">
                                            <a href="dashboard.php" class="btn btn-primary btn-custom w-100">
                                                <i class="fas fa-tachometer-alt me-2"></i>لوحة التحكم العادية
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <a href="companies_management.php" class="btn btn-success btn-custom w-100">
                                                <i class="fas fa-building me-2"></i>إدارة الشركات
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <a href="shipments.php" class="btn btn-info btn-custom w-100">
                                                <i class="fas fa-box me-2"></i>استعراض الشحنات
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <a href="reports.php" class="btn btn-warning btn-custom w-100">
                                                <i class="fas fa-chart-bar me-2"></i>التقارير المتقدمة
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <a href="users_management.php" class="btn btn-secondary btn-custom w-100">
                                                <i class="fas fa-users me-2"></i>إدارة المستخدمين
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <a href="admin_dashboard.php" class="btn btn-dark btn-custom w-100">
                                                <i class="fas fa-cog me-2"></i>لوحة التحكم الإدارية
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-history me-2"></i>
                                        النشاط الأخير
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>الوقت</th>
                                                    <th>النشاط</th>
                                                    <th>التفاصيل</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><?php echo date('H:i'); ?></td>
                                                    <td>تسجيل دخول</td>
                                                    <td>تم تسجيل دخول المدير</td>
                                                </tr>
                                                <tr>
                                                    <td><?php echo date('H:i', strtotime('-5 minutes')); ?></td>
                                                    <td>شحنة جديدة</td>
                                                    <td>تم إضافة شحنة جديدة</td>
                                                </tr>
                                                <tr>
                                                    <td><?php echo date('H:i', strtotime('-10 minutes')); ?></td>
                                                    <td>تحديث شركة</td>
                                                    <td>تم تحديث بيانات شركة</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-info-circle me-2"></i>
                                        معلومات النظام
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>إصدار النظام:</strong> 1.0.0
                                    </div>
                                    <div class="mb-3">
                                        <strong>آخر تحديث:</strong> <?php echo date('Y-m-d'); ?>
                                    </div>
                                    <div class="mb-3">
                                        <strong>الشركات النشطة:</strong> <?php echo $activeCompanies ?? 0; ?>
                                    </div>
                                    <div class="mb-3">
                                        <strong>المكررات اليوم:</strong> <?php echo $duplicatesToday ?? 0; ?>
                                    </div>
                                    <hr>
                                    <div class="text-center">
                                        <a href="dashboard.php" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-external-link-alt me-1"></i>
                                            لوحة التحكم العادية
                                        </a>
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
</body>
</html>
