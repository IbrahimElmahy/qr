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
            
            if ($_POST['action'] === 'add_user') {
                $username = sanitizeInput($_POST['username']);
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $role = sanitizeInput($_POST['role']);
                
                // إنشاء جدول المستخدمين إذا لم يكن موجوداً
                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS users (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        username VARCHAR(50) UNIQUE NOT NULL,
                        password VARCHAR(255) NOT NULL,
                        role ENUM('admin', 'manager', 'user') DEFAULT 'user',
                        is_active BOOLEAN DEFAULT TRUE,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )
                ");
                
                $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
                $stmt->execute([$username, $password, $role]);
                $success = "تم إضافة المستخدم بنجاح";
            }
            
            if ($_POST['action'] === 'toggle_user') {
                $id = (int)$_POST['user_id'];
                $stmt = $pdo->prepare("UPDATE users SET is_active = NOT is_active WHERE id = ?");
                $stmt->execute([$id]);
                $success = "تم تحديث حالة المستخدم";
            }
            
            if ($_POST['action'] === 'delete_user') {
                $id = (int)$_POST['user_id'];
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$id]);
                $success = "تم حذف المستخدم";
            }
            
        } catch (Exception $e) {
            $error = "خطأ: " . $e->getMessage();
        }
    }
}

// جلب المستخدمين
try {
    $pdo = getDatabaseConnection();
    
    // إنشاء جدول المستخدمين إذا لم يكن موجوداً
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'manager', 'user') DEFAULT 'user',
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // إدراج مستخدم افتراضي إذا لم يكن موجوداً
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users");
    $stmt->execute();
    $userCount = $stmt->fetchColumn();
    
    if ($userCount == 0) {
        $defaultPassword = password_hash('1234', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute(['admin', $defaultPassword, 'admin']);
    }
    
    $stmt = $pdo->prepare("SELECT * FROM users ORDER BY created_at DESC");
    $stmt->execute();
    $users = $stmt->fetchAll();
    
} catch (Exception $e) {
    $error = "خطأ في تحميل المستخدمين: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المستخدمين - نظام تتبع الشحنات</title>
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
        .role-badge {
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 0.8em;
        }
        .role-admin {
            background-color: #dc3545;
            color: white;
        }
        .role-manager {
            background-color: #ffc107;
            color: #212529;
        }
        .role-user {
            background-color: #6c757d;
            color: white;
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
                            <a class="nav-link active" href="users_management.php">
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
                            <i class="fas fa-users me-2 text-primary"></i>
                            إدارة المستخدمين
                        </h2>
                        <button class="btn btn-primary btn-custom" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="fas fa-user-plus me-2"></i>إضافة مستخدم جديد
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

                    <!-- Users Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i>
                                قائمة المستخدمين
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>اسم المستخدم</th>
                                            <th>الدور</th>
                                            <th>الحالة</th>
                                            <th>تاريخ الإنشاء</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($users) && !empty($users)): ?>
                                            <?php foreach ($users as $user): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                                    </td>
                                                    <td>
                                                        <span class="role-badge role-<?php echo $user['role']; ?>">
                                                            <?php 
                                                            $roles = [
                                                                'admin' => 'مدير',
                                                                'manager' => 'مشرف',
                                                                'user' => 'مستخدم'
                                                            ];
                                                            echo $roles[$user['role']] ?? $user['role'];
                                                            ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="status-badge <?php echo $user['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                                            <?php echo $user['is_active'] ? 'نشط' : 'معطل'; ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo date('Y-m-d H:i', strtotime($user['created_at'])); ?></td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <form method="POST" style="display: inline;">
                                                                <input type="hidden" name="action" value="toggle_user">
                                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                                                    <i class="fas fa-toggle-<?php echo $user['is_active'] ? 'on' : 'off'; ?>"></i>
                                                                    <?php echo $user['is_active'] ? 'تعطيل' : 'تفعيل'; ?>
                                                                </button>
                                                            </form>
                                                            <?php if ($user['id'] != 1): // لا يمكن حذف المستخدم الأول ?>
                                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')">
                                                                    <i class="fas fa-trash"></i>
                                                                    حذف
                                                                </button>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">
                                                    <i class="fas fa-users fa-2x mb-2"></i><br>
                                                    لا توجد مستخدمين
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

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus me-2"></i>إضافة مستخدم جديد
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_user">
                        <div class="mb-3">
                            <label for="username" class="form-label">اسم المستخدم</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">كلمة المرور</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">الدور</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="user">مستخدم</option>
                                <option value="manager">مشرف</option>
                                <option value="admin">مدير</option>
                            </select>
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

    <!-- Delete User Modal -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2 text-warning"></i>تأكيد الحذف
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>هل أنت متأكد من حذف المستخدم <strong id="userNameToDelete"></strong>؟</p>
                    <p class="text-danger"><small>هذا الإجراء لا يمكن التراجع عنه.</small></p>
                </div>
                <form method="POST" id="deleteUserForm">
                    <div class="modal-footer">
                        <input type="hidden" name="action" value="delete_user">
                        <input type="hidden" name="user_id" id="userIdToDelete">
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
        function deleteUser(id, name) {
            document.getElementById('userIdToDelete').value = id;
            document.getElementById('userNameToDelete').textContent = name;
            new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
        }
    </script>
</body>
</html>
