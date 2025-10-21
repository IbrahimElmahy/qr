<?php
session_start();

// إذا كان المستخدم مسجل دخول بالفعل، توجيهه للوحة التحكم
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // التحقق من بيانات الدخول
    if ($username === 'admin' && $password === '1234') {
        $_SESSION['logged_in'] = true;
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['admin_username'] = $username;
        // تأكد من حفظ الجلسة
        session_write_close();
        header('Location: dashboard.php');
        exit;
    } else {
        $error_message = 'اسم المستخدم أو كلمة المرور غير صحيحة';
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - نظام تتبع الشحنات</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(180deg, #66b2e6 0%, #3399cc 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .login-container {
            background: white;
            border-radius: 25px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            position: relative;
        }
        .login-header {
            background: #1e90ff;
            color: white;
            padding: 40px 30px;
            text-align: center;
            border-radius: 25px 25px 0 0;
        }
        .login-header .logo {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 50%;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #1e90ff;
        }
        .login-header h1 {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            letter-spacing: 1px;
        }
        .login-body {
            padding: 40px 30px;
        }
        .welcome-message {
            text-align: center;
            margin-bottom: 30px;
        }
        .welcome-message h2 {
            font-size: 28px;
            font-weight: bold;
            color: #000;
            margin-bottom: 10px;
        }
        .welcome-message h2 .highlight {
            color: #1e90ff;
        }
        .welcome-message p {
            color: #808080;
            font-size: 16px;
            margin: 0;
        }
        .form-control {
            border-radius: 12px;
            border: 2px solid #4682b4;
            padding: 15px 20px;
            transition: all 0.3s;
            font-size: 16px;
        }
        .form-control:focus {
            border-color: #1e90ff;
            box-shadow: 0 0 0 0.2rem rgba(30, 144, 255, 0.25);
        }
        .btn-login {
            background: linear-gradient(90deg, #1e90ff 0%, #4682b4 100%);
            border: none;
            border-radius: 12px;
            padding: 15px 30px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s;
            box-shadow: 0 6px 20px rgba(30, 144, 255, 0.3);
            width: 100%;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(30, 144, 255, 0.4);
        }
        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 10px 0 0 10px;
        }
        .input-group .form-control {
            border-radius: 0 10px 10px 0;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="logo">
                <i class="fas fa-shipping-fast"></i>
            </div>
            <h1>SHIPMENT TRACKER</h1>
        </div>
        
        <div class="login-body">
            <div class="welcome-message">
                <h2>مرحباً بك في <span class="highlight">Shipment Tracker</span></h2>
                <p>سجل دخولك للوصول إلى لوحة التحكم</p>
            </div>
            
            <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="mb-4">
                    <label for="username" class="form-label">
                        <i class="fas fa-user me-2"></i>اسم المستخدم
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                               placeholder="أدخل اسم المستخدم" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock me-2"></i>كلمة المرور
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="أدخل كلمة المرور" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                            <i class="fas fa-eye" id="password-eye"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-login w-100">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    تسجيل الدخول
                </button>
            </form>
            
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const eye = document.getElementById(inputId + '-eye');
            
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
    </script>
</body>
</html>
