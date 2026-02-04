
<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// التحقق من تسجيل الدخول
checkAuth();

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// جلب بيانات المستخدم الحالية
$sql = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    // التحقق من البريد الإلكتروني إذا تم تغييره
    if ($email != $user['email']) {
        $check_sql = "SELECT id FROM users WHERE email = '$email' AND id != '$user_id'";
        $check_result = mysqli_query($conn, $check_sql);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = "البريد الإلكتروني مسجل بالفعل لحساب آخر";
        }
    }
    
    if (!$error) {
        // تحديث البيانات
        $sql = "UPDATE users SET 
                name = '$name',
                email = '$email',
                phone = '$phone',
                address = '$address'
                WHERE id = '$user_id'";
        
        if (mysqli_query($conn, $sql)) {
            $success = "تم تحديث الملف الشخصي بنجاح!";
            
            // تحديث بيانات الجلسة
            $_SESSION['user_name'] = $name;
            
            // إعادة جلب البيانات المحدثة
            $result = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
            $user = mysqli_fetch_assoc($result);
        } else {
            $error = "حدث خطأ أثناء تحديث الملف الشخصي: " . mysqli_error($conn);
        }
    }
}

// معالجة تغيير كلمة المرور
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (password_verify($current_password, $user['password'])) {
        if ($new_password === $confirm_password) {
            if (strlen($new_password) >= 6) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET password = '$hashed_password' WHERE id = '$user_id'";
                
                if (mysqli_query($conn, $sql)) {
                    $success = "تم تغيير كلمة المرور بنجاح!";
                } else {
                    $error = "حدث خطأ أثناء تغيير كلمة المرور";
                }
            } else {
                $error = "كلمة المرور الجديدة يجب أن تكون 6 أحرف على الأقل";
            }
        } else {
            $error = "كلمات المرور الجديدة غير متطابقة";
        }
    } else {
        $error = "كلمة المرور الحالية غير صحيحة";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل الملف الشخصي - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <section class="dashboard">
        <div class="container">
            <div class="dashboard-grid">
                <?php include 'includes/sidebar.php'; ?>
                
                <main class="dashboard-content">
                    <h1 style="color: var(--primary-color); margin-bottom: 2rem;">تعديل الملف الشخصي</h1>
                    
                    <?php if($error): ?>
                        <div class="alert alert-error"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="form-group">
                            <label>الاسم الكامل *</label>
                            <input type="text" name="name" class="form-control" required 
                                   value="<?php echo htmlspecialchars($user['name']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>البريد الإلكتروني *</label>
                            <input type="email" name="email" class="form-control" required 
                                   value="<?php echo htmlspecialchars($user['email']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>رقم الهاتف</label>
                            <input type="tel" name="phone" class="form-control" 
                                   value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>العنوان</label>
                            <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                        </div>
                    </form>
                    
                    <!-- قسم تغيير كلمة المرور -->
                    <div style="margin-top: 3rem; padding-top: 2rem; border-top: 2px solid var(--light-color);">
                        <h2 style="color: var(--primary-color); margin-bottom: 1.5rem;">تغيير كلمة المرور</h2>
                        
                        <form method="POST" action="">
                            <div class="form-group">
                                <label>كلمة المرور الحالية *</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label>كلمة المرور الجديدة *</label>
                                <input type="password" name="new_password" class="form-control" required minlength="6">
                            </div>
                            
                            <div class="form-group">
                                <label>تأكيد كلمة المرور الجديدة *</label>
                                <input type="password" name="confirm_password" class="form-control" required minlength="6">
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" name="change_password" class="btn btn-secondary">تغيير كلمة المرور</button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- معلومات الحساب -->
                    <div style="margin-top: 3rem; padding: 1.5rem; background-color: var(--light-color); border-radius: var(--border-radius);">
                        <h3 style="color: var(--primary-color); margin-bottom: 1rem;">معلومات الحساب</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div>
                                <p><strong>رقم العضوية:</strong> #<?php echo str_pad($user['id'], 6, '0', STR_PAD_LEFT); ?></p>
                                <p><strong>تاريخ الانضمام:</strong> <?php echo date('Y/m/d', strtotime($user['created_at'])); ?></p>
                            </div>
                            <div>
                                <p><strong>نوع الحساب:</strong> <?php echo $user['role'] == 'admin' ? 'مدير' : 'مستخدم'; ?></p>
                                <p><strong>حالة الحساب:</strong> <span style="color: var(--success-color);">نشط</span></p>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </section>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/script.js"></script>
</body>
</html>