<?php
session_start();

// التحقق من تسجيل الدخول
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../api/config.php';

// معالجة العمليات المختلفة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_company') {
        $companyName = $_POST['company_name'] ?? '';
        if (!empty($companyName)) {
            try {
                $pdo = getDatabaseConnection();
                $stmt = $pdo->prepare("INSERT INTO companies (name, is_active) VALUES (?, 1)");
                $stmt->execute([$companyName]);
                $success_message = "تم إضافة الشركة بنجاح";
            } catch (Exception $e) {
                $error_message = "خطأ في إضافة الشركة: " . $e->getMessage();
            }
        }
    } elseif ($_POST['action'] === 'edit_company') {
        $companyId = $_POST['company_id'] ?? '';
        $companyName = $_POST['company_name'] ?? '';
        if (!empty($companyId) && !empty($companyName)) {
            try {
                $pdo = getDatabaseConnection();
                $stmt = $pdo->prepare("UPDATE companies SET name = ? WHERE id = ?");
                $stmt->execute([$companyName, $companyId]);
                $success_message = "تم تحديث الشركة بنجاح";
            } catch (Exception $e) {
                $error_message = "خطأ في تحديث الشركة: " . $e->getMessage();
            }
        }
    } elseif ($_POST['action'] === 'delete_company') {
        $companyId = $_POST['company_id'] ?? '';
        if (!empty($companyId)) {
            try {
                $pdo = getDatabaseConnection();
                // حذف الشحنات المرتبطة أولاً
                $stmt = $pdo->prepare("DELETE FROM shipments WHERE company_id = ?");
                $stmt->execute([$companyId]);
                // حذف الشركة
                $stmt = $pdo->prepare("DELETE FROM companies WHERE id = ?");
                $stmt->execute([$companyId]);
                $success_message = "تم حذف الشركة وجميع شحناتها بنجاح";
            } catch (Exception $e) {
                $error_message = "خطأ في حذف الشركة: " . $e->getMessage();
            }
        }
    } elseif ($_POST['action'] === 'toggle_company') {
        $companyId = $_POST['company_id'] ?? '';
        if (!empty($companyId)) {
            try {
                $pdo = getDatabaseConnection();
                $stmt = $pdo->prepare("UPDATE companies SET is_active = NOT is_active WHERE id = ?");
                $stmt->execute([$companyId]);
                $success_message = "تم تحديث حالة الشركة";
            } catch (Exception $e) {
                $error_message = "خطأ في تحديث الشركة: " . $e->getMessage();
            }
        }
    }
}

// جلب قائمة الشركات
try {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare("SELECT * FROM companies ORDER BY name ASC");
    $stmt->execute();
    $companies = $stmt->fetchAll();
} catch (Exception $e) {
    $error_message = "خطأ في تحميل الشركات: " . $e->getMessage();
    $companies = [];
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
                                <i class="fas fa-building me-2"></i>
                                إدارة الشركات
                            </h4>
                            <h5 class="text-white mb-0 d-md-none">
                                <i class="fas fa-building me-2"></i>
                                إدارة الشركات
                            </h5>
                            <button class="btn btn-sm btn-outline-light" onclick="toggleSidebar()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <nav class="nav flex-column">
                            <a class="nav-link" href="dashboard.php">
                                <i class="fas fa-home me-3"></i> الرئيسية
                            </a>
                            <a class="nav-link active" href="companies_management.php">
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
                            <i class="fas fa-building me-2 text-primary"></i>
                            إدارة الشركات
                        </h2>
                        <div class="d-flex align-items-center">
                            <span class="text-muted me-3">مرحباً، <?php echo $_SESSION['username'] ?? 'المدير'; ?></span>
                        </div>
                    </div>

                    <?php if (isset($success_message)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php echo $success_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo $error_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Add Company Form -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-plus me-2"></i>
                                إضافة شركة جديدة
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="action" value="add_company">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="company_name" class="form-label">اسم الشركة</label>
                                            <input type="text" class="form-control" id="company_name" name="company_name" 
                                                   placeholder="أدخل اسم الشركة" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">&nbsp;</label>
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="fas fa-plus me-2"></i>
                                                إضافة الشركة
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Companies List -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i>
                                قائمة الشركات (<?php echo count($companies); ?>)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>اسم الشركة</th>
                                            <th>الحالة</th>
                                            <th>تاريخ الإنشاء</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($companies as $company): ?>
                                            <tr>
                                                <td><?php echo $company['id']; ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($company['name']); ?></strong>
                                                </td>
                                                <td>
                                                    <?php if ($company['is_active']): ?>
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check me-1"></i>نشطة
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-times me-1"></i>غير نشطة
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo date('Y-m-d H:i', strtotime($company['created_at'])); ?></td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <!-- Edit Button -->
                                                        <button type="button" class="btn btn-sm btn-primary" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#editModal<?php echo $company['id']; ?>">
                                                            <i class="fas fa-edit me-1"></i>تعديل
                                                        </button>
                                                        
                                                        <!-- Toggle Button -->
                                                        <form method="POST" style="display: inline;">
                                                            <input type="hidden" name="action" value="toggle_company">
                                                            <input type="hidden" name="company_id" value="<?php echo $company['id']; ?>">
                                                            <button type="submit" class="btn btn-sm <?php echo $company['is_active'] ? 'btn-warning' : 'btn-success'; ?>">
                                                                <i class="fas fa-<?php echo $company['is_active'] ? 'pause' : 'play'; ?> me-1"></i>
                                                                <?php echo $company['is_active'] ? 'إيقاف' : 'تفعيل'; ?>
                                                            </button>
                                                        </form>
                                                        
                                                        <!-- Delete Button -->
                                                        <button type="button" class="btn btn-sm btn-danger" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#deleteModal<?php echo $company['id']; ?>">
                                                            <i class="fas fa-trash me-1"></i>حذف
                                                        </button>
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

    <!-- Edit and Delete Modals -->
    <?php foreach ($companies as $company): ?>
        <!-- Edit Modal -->
        <div class="modal fade" id="editModal<?php echo $company['id']; ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">تعديل الشركة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <div class="modal-body">
                            <input type="hidden" name="action" value="edit_company">
                            <input type="hidden" name="company_id" value="<?php echo $company['id']; ?>">
                            <div class="mb-3">
                                <label for="edit_company_name_<?php echo $company['id']; ?>" class="form-label">اسم الشركة</label>
                                <input type="text" class="form-control" id="edit_company_name_<?php echo $company['id']; ?>" 
                                       name="company_name" value="<?php echo htmlspecialchars($company['name']); ?>" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div class="modal fade" id="deleteModal<?php echo $company['id']; ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">تأكيد الحذف</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <div class="modal-body">
                            <input type="hidden" name="action" value="delete_company">
                            <input type="hidden" name="company_id" value="<?php echo $company['id']; ?>">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                هل أنت متأكد من حذف الشركة "<?php echo htmlspecialchars($company['name']); ?>"؟
                                <br><strong>سيتم حذف جميع الشحنات المرتبطة بهذه الشركة!</strong>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-danger">حذف الشركة</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

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