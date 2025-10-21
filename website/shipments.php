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
    $totalRecords = $countStmt->fetch()['total'];
    $totalPages = ceil($totalRecords / $limit);
    
    // جلب الشحنات
    $shipmentsStmt = $pdo->prepare("
        SELECT 
            s.id,
            s.barcode,
            c.name as company_name,
            s.scan_date,
            s.scan_time,
            s.created_at
        FROM shipments s 
        JOIN companies c ON s.company_id = c.id 
        $whereClause
        ORDER BY s.created_at DESC
        LIMIT $limit OFFSET $offset
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
    <title>استعراض الشحنات - نظام تتبع الشحنات</title>
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
        .pagination {
            justify-content: center;
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
                                <i class="fas fa-box me-2"></i>
                                استعراض الشحنات
                            </h4>
                            <h5 class="text-white mb-0 d-md-none">
                                <i class="fas fa-box me-2"></i>
                                استعراض الشحنات
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
                            <a class="nav-link active" href="shipments.php">
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
                            <i class="fas fa-box me-2 text-primary"></i>
                            استعراض الشحنات
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

                    <!-- Search Filters -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-search me-2"></i>
                                البحث والفلترة
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-3">
                                    <label for="barcode" class="form-label">البحث بالباركود</label>
                                    <input type="text" class="form-control" id="barcode" name="barcode" 
                                           value="<?php echo htmlspecialchars($barcode); ?>" 
                                           placeholder="أدخل الباركود">
                                </div>
                                <div class="col-md-3">
                                    <label for="company_id" class="form-label">الشركة</label>
                                    <select class="form-select" id="company_id" name="company_id">
                                        <option value="">جميع الشركات</option>
                                        <?php foreach ($companies as $company): ?>
                                            <option value="<?php echo $company['id']; ?>" 
                                                    <?php echo $companyId == $company['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($company['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="date" class="form-label">التاريخ</label>
                                    <input type="date" class="form-control" id="date" name="date" 
                                           value="<?php echo $date; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search me-1"></i>بحث
                                        </button>
                                        <a href="shipments.php" class="btn btn-outline-secondary">
                                            <i class="fas fa-refresh me-1"></i>إعادة تعيين
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Results Info -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h6 class="mb-0">
                                        <i class="fas fa-info-circle me-2"></i>
                                        عرض <?php echo count($shipments); ?> من أصل <?php echo $totalRecords; ?> شحنة
                                    </h6>
                                </div>
                                <div class="col-md-6 text-end">
                                    <span class="badge bg-primary">الصفحة <?php echo $page; ?> من <?php echo $totalPages; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipments Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i>
                                قائمة الشحنات
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
                                            <th>التاريخ</th>
                                            <th>الوقت</th>
                                            <th>تاريخ الإنشاء</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($shipments as $index => $shipment): ?>
                                            <tr>
                                                <td><?php echo $offset + $index + 1; ?></td>
                                                <td>
                                                    <code class="bg-light p-2 rounded">
                                                        <?php echo htmlspecialchars($shipment['barcode']); ?>
                                                    </code>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">
                                                        <?php echo htmlspecialchars($shipment['company_name']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <i class="fas fa-calendar me-1"></i>
                                                    <?php echo date('Y-m-d', strtotime($shipment['scan_date'])); ?>
                                                </td>
                                                <td>
                                                    <i class="fas fa-clock me-1"></i>
                                                    <?php echo $shipment['scan_time']; ?>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?php echo date('Y-m-d H:i:s', strtotime($shipment['created_at'])); ?>
                                                    </small>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Page navigation" class="mt-4">
                            <ul class="pagination">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
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
