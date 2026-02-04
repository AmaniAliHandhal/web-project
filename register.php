
<?php
require_once 'includes/config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    // التحقق من كلمات المرور
    if ($password !== $confirm_password) {
        $error = "كلمات المرور غير متطابقة";
    } elseif (strlen($password) < 6) {
        $error = "كلمة المرور يجب أن تكون 6 أحرف على الأقل";
    } else {
        // التحقق من وجود البريد الإلكتروني
        $check_sql = "SELECT id FROM users WHERE email = '$email'";
        $check_result = mysqli_query($conn, $check_sql);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = "البريد الإلكتروني مسجل بالفعل";
        } else {
            // تشفير كلمة المرور
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // إضافة المستخدم
            $sql = "INSERT INTO users (name, email, password, phone, address) 
                    VALUES ('$name', '$email', '$hashed_password', '$phone', '$address')";
            
            if (mysqli_query($conn, $sql)) {
                $success = "تم إنشاء الحساب بنجاح! يمكنك الآن تسجيل الدخول.";
            } else {
                $error = "حدث خطأ أثناء إنشاء الحساب: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <section class="form-section">
        <div class="container">
            <div class="form-container">
                <h2 style="text-align: center; margin-bottom: 2rem; color: var(--primary-color);">إنشاء حساب جديد</h2>
                
                <?php if($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label>الاسم الكامل *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>البريد الإلكتروني *</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>رقم الهاتف</label>
                        <input type="tel" name="phone" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label>العنوان</label>
                        <textarea name="address" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>كلمة المرور *</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    
                    <div class="form-group">
                        <label>تأكيد كلمة المرور *</label>
                        <input type="password" name="confirm_password" class="form-control" required minlength="6">
                    </div>
                    
                    <div class="form-group">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                            <input type="checkbox" id="terms" required>
                            <label for="terms" style="margin: 0;">أوافق على <a href="terms.php">الشروط والأحكام</a></label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">إنشاء الحساب</button>
                    </div>
                    
                    <div style="text-align: center; margin-top: 1rem;">
                        <p>لديك حساب بالفعل؟ <a href="login.php">سجل الدخول الآن</a></p>
                    </div>
                </form>
            </div>
        </div>
    </section>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/script.js"></script>
</body>
</html>