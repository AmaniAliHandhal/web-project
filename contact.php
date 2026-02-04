
<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message_text = mysqli_real_escape_string($conn, $_POST['message']);
    
    if (addContactMessage($name, $email, $subject, $message_text)) {
        $success = true;
        $message = "تم إرسال رسالتك بنجاح. سنتواصل معك قريباً.";
    } else {
        $message = "حدث خطأ أثناء إرسال الرسالة. يرجى المحاولة مرة أخرى.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اتصل بنا - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <section class="contact-section">
        <div class="container">
            <h1 class="section-title">اتصل بنا</h1>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin-top: 2rem;">
                <!-- نموذج الاتصال -->
                <div style="background: white; padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--box-shadow);">
                    <h2 style="color: var(--primary-color); margin-bottom: 1.5rem;">أرسل رسالة</h2>
                    
                    <?php if($message): ?>
                        <div class="<?php echo $success ? 'alert alert-success' : 'alert alert-error'; ?>">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="form-group">
                            <label>الاسم الكامل</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label>البريد الإلكتروني</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label>الموضوع</label>
                            <input type="text" name="subject" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label>الرسالة</label>
                            <textarea name="message" class="form-control" rows="5" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" style="width: 100%;">إرسال الرسالة</button>
                        </div>
                    </form>
                </div>
                
                <!-- معلومات الاتصال -->
                <div style="background: white; padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--box-shadow);">
                    <h2 style="color: var(--primary-color); margin-bottom: 1.5rem;">معلومات التواصل</h2>
                    
                    <div style="margin-bottom: 2rem;">
                        <h3 style="color: var(--secondary-color); margin-bottom: 1rem;">📍 العنوان</h3>
                        <p>صنعاء \ حي الصافية<br>الجمهورية اليمنية</p>
                    </div>
                    
                    <div style="margin-bottom: 2rem;">
                        <h3 style="color: var(--secondary-color); margin-bottom: 1rem;">📞 الهواتف</h3>
                        <p>+967 775399993</p>
                        <p>+967 779908080</p>
                    </div>
                    
                    <div style="margin-bottom: 2rem;">
                        <h3 style="color: var(--secondary-color); margin-bottom: 1rem;">📧 البريد الإلكتروني</h3>
                        <p>info@furniture.com</p>
                        <p>support@furniture.com</p>
                    </div>
                    
                    <div>
                        <h3 style="color: var(--secondary-color); margin-bottom: 1rem;">⏰ ساعات العمل</h3>
                        <p>الأحد - الخميس: 9:00 ص - 6:00 م</p>
                        <p>الجمعة: 4:00 م - 9:00 م</p>
                        <p>السبت: إجازة</p>
                    </div>
                    
                  
                </div>
            </div>
            
        
        </div>
    </section>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/script.js"></script>
</body>
</html>