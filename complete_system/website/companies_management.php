<?php
session_start();

// التحقق من تسجيل الدخول
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../shipment_tracking/api/config.php';

// معالجة الطلبات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            $pdo = getDatabaseConnection();
            
            if ($_POST['action'] === 'add_company') {
                $name = sanitizeInput($_POST['name']);
                $stmt = $pdo->prepare("INSERT INTO companies (name, is_active) VALUES (?, 1)");
                $stmt->execute([$name]);
                $success = "تم إضافة الشركة بنجاح";
            }
            
            if ($_POST['action'] === 'toggle_company') {
                $id = (int)$_POST['company_id'];
                $stmt = $pdo->prepare("UPDATE companies SET is_active = NOT is_active WHERE id = ?");
                $stmt->execute([$id]);
                $success = "تم تحديث حالة الشركة";
            }
            
            if ($_POST['action'] === 'delete_company') {
                $id = (int)$_POST['company_id'];
                $stmt = $pdo->prepare("DELETE FROM companies WHERE id = ?");
                $stmt->execute([$id]);
                $success = "تم حذف الشركة";
            }
            
        } catch (Exception $e) {
            $error = "خطأ: " . $e->getMessage();
        }
    }
}

// جلب الشركات
try {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare("
        SELECT 
            c.*,
            COUNT(s.id) as total_shipments,
            COUNT(CASE WHEN s.scan_date = CURDATE() THEN 1 END) as today_shipments
        FROM companies c 
        LEFT JOIN shipments s ON c.id = s.company_id
        GROUP BY c.id
        ORDER BY c.name
    ");
    $stmt->execute();
    $companies = $stmt->fetchAll();
} catch (Exception $e) {
    $error = "خطأ في تحميل الشركات: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الشركات - نظام تتبع الشحنات</title>
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
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85em;
        }
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
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
                            <a class="nav-link active" href="companies_management.php">
                                <i class="fas fa-building me-2"></i> إدارة الشركات
                            </a>
                            <a class="nav-link" href="shipments_management.php">
                                <i class="fas fa-box me-2"></i> إدارة الشحنات
                            </a>
                            <a class="nav-link" href="users_management.php">
                                <i class="fas fa-users me-2"></i> إدارة المستخدمين
                            </a>
                            <a class="nav-link" href="reports.php">
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
                            <i class="fas fa-building me-2 text-primary"></i>
                            إدارة الشركات
                        </h2>
                        <button class="btn btn-primary btn-custom" data-bs-toggle="modal" data-bs-target="#addCompanyModal">
                            <i class="fas fa-plus me-2"></i>إضافة شركة جديدة
                        </button>
                    </div>

                    <!-- Alerts -->
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Companies Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i>
                                قائمة الشركات
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>اسم الشركة</th>
                                            <th>الحالة</th>
                                            <th>إجمالي الشحنات</th>
                                            <th>شحنات اليوم</th>
                                            <th>تاريخ الإنشاء</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($companies) && !empty($companies)): ?>
                                            <?php foreach ($companies as $company): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($company['name']); ?></strong>
                                                    </td>
                                                    <td>
                                                        <span class="status-badge <?php echo $company['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                                            <?php echo $company['is_active'] ? 'فعالة' : 'غير فعالة'; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info"><?php echo $company['total_shipments']; ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-success"><?php echo $company['today_shipments']; ?></span>
                                                    </td>
                                                    <td><?php echo date('Y-m-d', strtotime($company['created_at'])); ?></td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <form method="POST" style="display: inline;">
                                                                <input type="hidden" name="action" value="toggle_company">
                                                                <input type="hidden" name="company_id" value="<?php echo $company['id']; ?>">
                                                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                                                    <i class="fas fa-toggle-<?php echo $company['is_active'] ? 'on' : 'off'; ?>"></i>
                                                                    <?php echo $company['is_active'] ? 'إلغاء التفعيل' : 'تفعيل'; ?>
                                                                </button>
                                                            </form>
                                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteCompany(<?php echo $company['id']; ?>, '<?php echo htmlspecialchars($company['name']); ?>')">
                                                                <i class="fas fa-trash"></i>
                                                                حذف
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">
                                                    <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                                    لا توجد شركات
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Company Modal -->
    <div class="modal fade" id="addCompanyModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>إضافة شركة جديدة
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_company">
                        <div class="mb-3">
                            <label for="companyName" class="form-label">اسم الشركة</label>
                            <input type="text" class="form-control" id="companyName" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>حفظ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Company Modal -->
    <div class="modal fade" id="deleteCompanyModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2 text-warning"></i>تأكيد الحذف
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>هل أنت متأكد من حذف الشركة <strong id="companyNameToDelete"></strong>؟</p>
                    <p class="text-danger"><small>هذا الإجراء لا يمكن التراجع عنه.</small></p>
                </div>
                <form method="POST" id="deleteCompanyForm">
                    <div class="modal-footer">
                        <input type="hidden" name="action" value="delete_company">
                        <input type="hidden" name="company_id" id="companyIdToDelete">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>حذف
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteCompany(id, name) {
            document.getElementById('companyIdToDelete').value = id;
            document.getElementById('companyNameToDelete').textContent = name;
            new bootstrap.Modal(document.getElementById('deleteCompanyModal')).show();
        }
    </script>
</body>
</html>
