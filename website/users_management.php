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
    if ($_POST['action'] === 'add_user') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'user';
        
        if (!empty($username) && !empty($password)) {
            try {
                $pdo = getDatabaseConnection();
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, password, role, is_active) VALUES (?, ?, ?, 1)");
                $stmt->execute([$username, $hashedPassword, $role]);
                $success_message = "تم إضافة المستخدم بنجاح";
            } catch (Exception $e) {
                $error_message = "خطأ في إضافة المستخدم: " . $e->getMessage();
            }
        }
    } elseif ($_POST['action'] === 'edit_user') {
        $userId = $_POST['user_id'] ?? '';
        $username = $_POST['username'] ?? '';
        $role = $_POST['role'] ?? 'user';
        
        if (!empty($userId) && !empty($username)) {
            try {
                $pdo = getDatabaseConnection();
                $stmt = $pdo->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
                $stmt->execute([$username, $role, $userId]);
                $success_message = "تم تحديث المستخدم بنجاح";
            } catch (Exception $e) {
                $error_message = "خطأ في تحديث المستخدم: " . $e->getMessage();
            }
        }
    } elseif ($_POST['action'] === 'change_password') {
        $userId = $_POST['user_id'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        
        if (!empty($userId) && !empty($newPassword)) {
            try {
                $pdo = getDatabaseConnection();
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashedPassword, $userId]);
                $success_message = "تم تغيير كلمة المرور بنجاح";
            } catch (Exception $e) {
                $error_message = "خطأ في تغيير كلمة المرور: " . $e->getMessage();
            }
        }
    } elseif ($_POST['action'] === 'delete_user') {
        $userId = $_POST['user_id'] ?? '';
        if (!empty($userId)) {
            try {
                $pdo = getDatabaseConnection();
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$userId]);
                $success_message = "تم حذف المستخدم بنجاح";
            } catch (Exception $e) {
                $error_message = "خطأ في حذف المستخدم: " . $e->getMessage();
            }
        }
    } elseif ($_POST['action'] === 'toggle_user') {
        $userId = $_POST['user_id'] ?? '';
        if (!empty($userId)) {
            try {
                $pdo = getDatabaseConnection();
                $stmt = $pdo->prepare("UPDATE users SET is_active = NOT is_active WHERE id = ?");
                $stmt->execute([$userId]);
                $success_message = "تم تحديث حالة المستخدم";
            } catch (Exception $e) {
                $error_message = "خطأ في تحديث المستخدم: " . $e->getMessage();
            }
        }
    }
}

// جلب قائمة المستخدمين
try {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->prepare("SELECT * FROM users ORDER BY username ASC");
    $stmt->execute();
    $users = $stmt->fetchAll();
} catch (Exception $e) {
    $error_message = "خطأ في تحميل المستخدمين: " . $e->getMessage();
    $users = [];
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
                                <i class="fas fa-users me-2"></i>
                                إدارة المستخدمين
                            </h4>
                            <h5 class="text-white mb-0 d-md-none">
                                <i class="fas fa-users me-2"></i>
                                إدارة المستخدمين
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
                            <a class="nav-link active" href="users_management.php">
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
                            <i class="fas fa-users me-2 text-primary"></i>
                            إدارة المستخدمين
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

                    <!-- Add User Form -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-user-plus me-2"></i>
                                إضافة مستخدم جديد
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="action" value="add_user">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">اسم المستخدم</label>
                                            <input type="text" class="form-control" id="username" name="username" 
                                                   placeholder="أدخل اسم المستخدم" required>
                                        </div>
                                    </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="password" class="form-label">كلمة المرور</label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" id="password" name="password" 
                                                           placeholder="أدخل كلمة المرور" required>
                                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                                        <i class="fas fa-eye" id="password-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="role" class="form-label">الدور</label>
                                            <select class="form-select" id="role" name="role">
                                                <option value="user">مستخدم</option>
                                                <option value="admin">مدير</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">&nbsp;</label>
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="fas fa-user-plus me-2"></i>
                                                إضافة المستخدم
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Users List -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i>
                                قائمة المستخدمين (<?php echo count($users); ?>)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>اسم المستخدم</th>
                                            <th>الدور</th>
                                            <th>الحالة</th>
                                            <th>تاريخ الإنشاء</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td><?php echo $user['id']; ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                                </td>
                                                <td>
                                                    <?php if ($user['role'] === 'admin'): ?>
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-crown me-1"></i>مدير
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-info">
                                                            <i class="fas fa-user me-1"></i>مستخدم
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($user['is_active']): ?>
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check me-1"></i>نشط
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-times me-1"></i>غير نشط
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo date('Y-m-d H:i', strtotime($user['created_at'])); ?></td>
                                                <td>
                                                    <div class="d-flex gap-2 flex-wrap">
                                                        <!-- Edit Button -->
                                                        <button type="button" class="btn btn-sm btn-primary" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#editModal<?php echo $user['id']; ?>">
                                                            <i class="fas fa-edit me-1"></i>تعديل
                                                        </button>
                                                        
                                                        <!-- Change Password Button -->
                                                        <button type="button" class="btn btn-sm btn-info" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#passwordModal<?php echo $user['id']; ?>">
                                                            <i class="fas fa-key me-1"></i>كلمة مرور
                                                        </button>
                                                        
                                                        <!-- Toggle Button -->
                                                        <form method="POST" style="display: inline;">
                                                            <input type="hidden" name="action" value="toggle_user">
                                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                            <button type="submit" class="btn btn-sm <?php echo $user['is_active'] ? 'btn-warning' : 'btn-success'; ?>">
                                                                <i class="fas fa-<?php echo $user['is_active'] ? 'pause' : 'play'; ?> me-1"></i>
                                                                <?php echo $user['is_active'] ? 'إيقاف' : 'تفعيل'; ?>
                                                            </button>
                                                        </form>
                                                        
                                                        <!-- Delete Button -->
                                                        <button type="button" class="btn btn-sm btn-danger" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#deleteModal<?php echo $user['id']; ?>">
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

    <!-- Edit, Password, and Delete Modals -->
    <?php foreach ($users as $user): ?>
        <!-- Edit Modal -->
        <div class="modal fade" id="editModal<?php echo $user['id']; ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">تعديل المستخدم</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <div class="modal-body">
                            <input type="hidden" name="action" value="edit_user">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <div class="mb-3">
                                <label for="edit_username_<?php echo $user['id']; ?>" class="form-label">اسم المستخدم</label>
                                <input type="text" class="form-control" id="edit_username_<?php echo $user['id']; ?>" 
                                       name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_role_<?php echo $user['id']; ?>" class="form-label">الدور</label>
                                <select class="form-select" id="edit_role_<?php echo $user['id']; ?>" name="role">
                                    <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>مستخدم</option>
                                    <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>مدير</option>
                                </select>
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

        <!-- Change Password Modal -->
        <div class="modal fade" id="passwordModal<?php echo $user['id']; ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">تغيير كلمة المرور</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <div class="modal-body">
                            <input type="hidden" name="action" value="change_password">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <div class="mb-3">
                                <label for="new_password_<?php echo $user['id']; ?>" class="form-label">كلمة المرور الجديدة</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="new_password_<?php echo $user['id']; ?>" 
                                           name="new_password" required minlength="4">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password_<?php echo $user['id']; ?>')">
                                        <i class="fas fa-eye" id="new_password_<?php echo $user['id']; ?>_eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password_<?php echo $user['id']; ?>" class="form-label">تأكيد كلمة المرور</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirm_password_<?php echo $user['id']; ?>" 
                                           name="confirm_password" required minlength="4">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password_<?php echo $user['id']; ?>')">
                                        <i class="fas fa-eye" id="confirm_password_<?php echo $user['id']; ?>_eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-info">تغيير كلمة المرور</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div class="modal fade" id="deleteModal<?php echo $user['id']; ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">تأكيد الحذف</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <div class="modal-body">
                            <input type="hidden" name="action" value="delete_user">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                هل أنت متأكد من حذف المستخدم "<?php echo htmlspecialchars($user['username']); ?>"؟
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-danger">حذف المستخدم</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password confirmation validation
        document.addEventListener('DOMContentLoaded', function() {
            const passwordModals = document.querySelectorAll('[id^="passwordModal"]');
            passwordModals.forEach(modal => {
                const form = modal.querySelector('form');
                const newPassword = modal.querySelector('input[name="new_password"]');
                const confirmPassword = modal.querySelector('input[name="confirm_password"]');
                
                form.addEventListener('submit', function(e) {
                    if (newPassword.value !== confirmPassword.value) {
                        e.preventDefault();
                        alert('كلمة المرور وتأكيدها غير متطابقين!');
                        return false;
                    }
                });
            });
        });

        // Toggle password visibility
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const eye = document.getElementById(inputId + '_eye');
            
            if (input.type === 'password') {
                input.type = 'text';
                eye.classList.remove('fa-eye');
                eye.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                eye.classList.remove('fa-eye-slash');
                eye.classList.add('fa-eye');
            }
        }
        
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