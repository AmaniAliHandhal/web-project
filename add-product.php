
<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// التحقق من تسجيل الدخول
checkAuth();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $product_condition = isset($_POST['product_condition' ]) ? mysqli_real_escape_string($conn, $_POST['product_condition'] ) : '';
    $user_id = $_SESSION['user_id'];
    
    // معالجة رفع الصورة
    $image_url = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_result = uploadImage($_FILES['image']);
        if ($upload_result) {
            $image_url = $upload_result;
        } else {
            $error = "حدث خطأ أثناء رفع الصورة. يرجى التأكد من أن الملف صورة وحجمها أقل من 5MB.";
        }
    }
    
    if (!$error) {
        $sql = "INSERT INTO products (title, description, price, category, product_condition, image_url, user_id) 
                VALUES ('$title', '$description', '$price', '$category', '$product_condition', '$image_url', '$user_id')";
        
        if (mysqli_query($conn, $sql)) {
            $success = "تم إضافة المنتج بنجاح!";
            
            // إعادة تعيين النموذج
            $_POST = array();
        } else {
            $error = "حدث خطأ أثناء إضافة المنتج: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة منتج جديد - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <section class="dashboard">
        <div class="container">
            <div class="dashboard-grid">
                <?php include 'includes/sidebar.php'; ?>
                
                <main class="dashboard-content">
                    <h1 style="color: var(--primary-color); margin-bottom: 2rem;">إضافة منتج جديد</h1>
                    
                    <?php if($error): ?>
                        <div class="alert alert-error"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>عنوان المنتج *</label>
                            <input type="text" name="title" class="form-control" required 
                                   value="<?php echo $_POST['title'] ?? ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>وصف المنتج *</label>
                            <textarea name="description" class="form-control" rows="6" required><?php echo $_POST['description'] ?? ''; ?></textarea>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label>السعر (ريال) *</label>
                                <input type="number" name="price" class="form-control" step="0.01" min="1" required 
                                       value="<?php echo $_POST['price'] ?? ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label>الفئة *</label>
                                <select name="category" class="form-control" required>
                                    <option value="">اختر الفئة</option>
                                    <option value="غرف نوم" <?php echo ($_POST['category'] ?? '') == 'غرف نوم' ? 'selected' : ''; ?>>غرف نوم</option>
                                    <option value="صالات" <?php echo ($_POST['category'] ?? '') == 'صالات' ? 'selected' : ''; ?>>صالات</option>
                                    <option value="مطابخ" <?php echo ($_POST['category'] ?? '') == 'مطابخ' ? 'selected' : ''; ?>>مطابخ</option>
                                    <option value="مكاتب" <?php echo ($_POST['category'] ?? '') == 'مكاتب' ? 'selected' : ''; ?>>مكاتب</option>
                                   
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>حالة المنتج *</label>
                            <select name="product_condition" class="form-control" required>
                                <option value="">اختر الحالة</option>
                                <option value="جديد" <?php echo ($_POST['product_condition'] ?? '') == 'جديد' ? 'selected' : ''; ?>>جديد</option>
                                <option value="جيد جدا" <?php echo ($_POST['product_condition '] ?? '') == 'جيد جدا' ? 'selected' : ''; ?>>جيد جداً</option>
                                <option value="جيد" <?php echo ($_POST['product_condition '] ?? '') == 'جيد' ? 'selected' : ''; ?>>جيد</option>
                                <option value="متوسط" <?php echo ($_POST['product_condition '] ?? '') == 'متوسط' ? 'selected' : ''; ?>>متوسط</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>صورة المنتج</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small style="color: #666;">الحد الأقصى لحجم الصورة: 5MB (JPEG, PNG, GIF)</small>
                        </div>
                        
                        <div class="form-group">
                            <div style="display: flex; gap: 1rem;">
                                <button type="submit" class="btn btn-primary">إضافة المنتج</button>
                                <a href="dashboard.php" class="btn" style="background-color: var(--light-color); color: var(--text-color);">إلغاء</a>
                            </div>
                        </div>
                    </form>
                    
                    <!-- نصائح لإضافة منتج -->
                    <div style="margin-top: 3rem; padding: 1.5rem; background-color: var(--light-color); border-radius: var(--border-radius);">
                        <h3 style="color: var(--primary-color); margin-bottom: 1rem;">💡 نصائح لإضافة منتج ناجح:</h3>
                        <ul style="list-style: none; padding-right: 1rem;">
                            <li>✅ استخدم صور واضحة وجيدة الإضاءة</li>
                            <li>✅ اكتب وصفاً مفصلاً للمنتج</li>
                            <li>✅ حدد السعر المناسب حسب حالة المنتج</li>
                            <li>✅ اختر الفئة المناسبة للمنتج</li>
                            <li>✅ كن صادقاً في وصف حالة المنتج</li>
                        </ul>
                    </div>
                </main>
            </div>
        </div>
    </section>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/script.js"></script>
</body>
</html>