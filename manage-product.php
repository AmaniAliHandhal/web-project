
<?php

require_once 'includes/config.php';
require_once 'includes/functions.php';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';
$product = null;

// ============ التحقق من وجود ID المنتج ============
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $error = "معرف المنتج غير صالح";
} else {
    $product_id = intval($_GET['id']);
    
    // جلب بيانات المنتج
    $sql = "SELECT * FROM products WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $product_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        $error = "المنتج غير موجود أو لا تملك صلاحية التعديل";
    } else {
        $product = mysqli_fetch_assoc($result);
    }
    mysqli_stmt_close($stmt);
}

// ============ معالجة الحذف ============
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_product'])) {
    if ($product) {
        $delete_sql = "DELETE FROM products WHERE id = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $delete_sql);
        mysqli_stmt_bind_param($stmt, "ii", $product_id, $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "تم حذف المنتج بنجاح";
            mysqli_stmt_close($stmt);
            header('Location: products.php');
            exit();
        } else {
            $error = "حدث خطأ أثناء حذف المنتج";
        }
        mysqli_stmt_close($stmt);
    }
}

// ============ معالجة التعديل ============
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_product']) && $product) {
    $title = mysqli_real_escape_string($conn, $_POST['title'] ?? '');
    $description = mysqli_real_escape_string($conn, $_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $category = mysqli_real_escape_string($conn, $_POST['category'] ?? '');
    $product_condition = mysqli_real_escape_string($conn, $_POST['product_condition'] ?? '');
    
    // التحقق من البيانات
    if (empty($title) || $price <= 0) {
        $error = "الرجاء إدخال عنوان صحيح وسعر أكبر من الصفر";
    } else {
        // تحديث المنتج
        $update_sql = "UPDATE products SET 
                      title = ?,
                      description = ?,
                      price = ?,
                      category = ?,
                      product_condition = ?,
                      updated_at = NOW()
                      WHERE id = ? AND user_id = ?";
        
        $stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($stmt, "ssdssii", $title, $description, $price, $category, $product_condition, $product_id, $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "تم تحديث المنتج بنجاح!";
            
            // إعادة جلب البيانات المحدثة
            $sql = "SELECT * FROM products WHERE id = ? AND user_id = ?";
            $stmt2 = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt2, "ii", $product_id, $user_id);
            mysqli_stmt_execute($stmt2);
            $result = mysqli_stmt_get_result($stmt2);
            $product = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt2);
        } else {
            $error = "حدث خطأ أثناء تحديث المنتج: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

// ============ معالجة رفع الصورة ============
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_image']) && $product) {
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (in_array($_FILES['image']['type'], $allowed_types) && $_FILES['image']['size'] <= $max_size) {
            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_filename = 'product_' . $product_id . '_' . time() . '.' . $file_extension;
            $upload_dir = 'uploads/products/';
            
            // إنشاء المجلد إذا لم يكن موجوداً
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $target_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                // تحديث مسار الصورة في قاعدة البيانات
                $image_url = $target_path;
                $sql = "UPDATE products SET image_url = ? WHERE id = ? AND user_id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "sii", $image_url, $product_id, $user_id);
                
                if (mysqli_stmt_execute($stmt)) {
                    $success = "تم تحديث صورة المنتج بنجاح!";
                    $product['image_url'] = $image_url;
                } else {
                    $error = "حدث خطأ أثناء تحديث الصورة في قاعدة البيانات";
                }
                mysqli_stmt_close($stmt);
            } else {
                $error = "حدث خطأ أثناء رفع الصورة";
            }
        } else {
            $error = "نوع الملف غير مدعوم أو الحجم كبير جداً (الحد الأقصى 5MB)";
        }
    } else {
        $error = "لم يتم اختيار صورة أو حدث خطأ في الرفع";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة المنتج</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
    /* ملف CSS رئيسي - تصميم عصري وجذاب */

/* Reset وإعدادات عامة */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

:root {
    --primary-color: #2c3e50;
    --secondary-color: #e74c3c;
    --accent-color: #3498db;
    --light-color: #ecf0f1;
    --dark-color: #2c3e50;
    --success-color: #27ae60;
    --warning-color: #f39c12;
    --text-color: #333;
    --border-radius: 8px;
    --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    --transition: all 0.3s ease;
}

body {
    background-color: #f9f9f9;
    color: var(--text-color);
    line-height: 1.6;
    direction: rtl;
}

.container {
    width: 90%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 15px;
}

/* الترويسة */
.header {
    background: linear-gradient(135deg, var(--primary-color), var(--dark-color));
    color: white;
    padding: 1rem 0;
    box-shadow: var(--box-shadow);
    position: sticky;
    top: 0;
    z-index: 1000;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    font-size: 1.8rem;
    font-weight: bold;
    color: white;
    text-decoration: none;
}

.logo span {
    color: var(--secondary-color);
}

.nav-links {
    display: flex;
    gap: 1.5rem;
    list-style: none;
}

.nav-links a {
    color: white;
    text-decoration: none;
    padding: 0.5rem 1rem;
    border-radius: var(--border-radius);
    transition: var(--transition);
}

.nav-links a:hover {
    background-color: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
}

.user-menu {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.user-menu a {
    color: white;
    text-decoration: none;
    padding: 0.5rem 1rem;
    border-radius: var(--border-radius);
}

.btn {
    display: inline-block;
    padding: 0.5rem 1.5rem;
    border-radius: var(--border-radius);
    text-decoration: none;
    font-weight: bold;
    transition: var(--transition);
    border: none;
    cursor: pointer;
}

.btn-primary {
    background-color: var(--secondary-color);
    color: white;
}

.btn-primary:hover {
    background-color: #c0392b;
    transform: translateY(-2px);
}

.btn-secondary {
    background-color: var(--accent-color);
    color: white;
}

.btn-secondary:hover {
    background-color: #2980b9;
}

/* القسم الرئيسي */
.hero {
    background: linear-gradient(rgba(44, 62, 80, 0.9), rgba(44, 62, 80, 0.9)), url('https://via.placeholder.com/1200x400');
    background-size: cover;
    background-position: center;
    color: white;
    padding: 4rem 0;
    text-align: center;
    border-radius: 0 0 var(--border-radius) var(--border-radius);
}

.hero h1 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.hero p {
    font-size: 1.2rem;
    margin-bottom: 2rem;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
}

/* قسم المنتجات */
.products-section {
    padding: 3rem 0;
}

.section-title {
    text-align: center;
    margin-bottom: 2rem;
    color: var(--primary-color);
    position: relative;
    padding-bottom: 1rem;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    right: 50%;
    transform: translateX(50%);
    width: 100px;
    height: 3px;
    background-color: var(--secondary-color);
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.product-card {
    background: white;
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--box-shadow);
    transition: var(--transition);
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

.product-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.product-info {
    padding: 1.5rem;
}

.product-title {
    font-size: 1.2rem;
    margin-bottom: 0.5rem;
    color: var(--primary-color);
}

.product-price {
    color: var(--secondary-color);
    font-weight: bold;
    font-size: 1.3rem;
    margin: 0.5rem 0;
}

.product-category {
    background-color: var(--light-color);
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.9rem;
    display: inline-block;
    margin-bottom: 1rem;
}

/* النموذج */
.form-container {
    max-width: 600px;
    margin: 2rem auto;
    background: white;
    padding: 2rem;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
    color: var(--primary-color);
}

.form-control {
    width: 100%;
    padding: 0.8rem;
    border: 1px solid #ddd;
    border-radius: var(--border-radius);
    font-size: 1rem;
    transition: var(--transition);
}

.form-control:focus {
    outline: none;
    border-color: var(--accent-color);
    box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
}

/* لوحة التحكم */
.dashboard {
    padding: 2rem 0;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 2rem;
}

.sidebar {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--box-shadow);
}

.sidebar-menu {
    list-style: none;
}

.sidebar-menu li {
    margin-bottom: 0.5rem;
}

.sidebar-menu a {
    display: block;
    padding: 0.8rem 1rem;
    color: var(--text-color);
    text-decoration: none;
    border-radius: var(--border-radius);
    transition: var(--transition);
}

.sidebar-menu a:hover,
.sidebar-menu a.active {
    background-color: var(--light-color);
    color: var(--primary-color);
}

.dashboard-content {
    background: white;
    border-radius: var(--border-radius);
    padding: 2rem;
    box-shadow: var(--box-shadow);
}

/* التذييل */
.footer {
    background-color: var(--primary-color);
    color: white;
    padding: 2rem 0;
    margin-top: 3rem;
}

.footer-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.footer-section h3 {
    margin-bottom: 1rem;
    color: white;
}

.footer-section ul {
    list-style: none;
}

.footer-section ul li {
    margin-bottom: 0.5rem;
}

.footer-section a {
    color: #ddd;
    text-decoration: none;
    transition: var(--transition);
}

.footer-section a:hover {
    color: white;
}

.copyright {
    text-align: center;
    padding-top: 2rem;
    margin-top: 2rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

/* التنبيهات */
.alert {
    padding: 1rem;
    border-radius: var(--border-radius);
    margin-bottom: 1rem;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert-warning {
    background-color: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

/* تحسينات للهواتف */
@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        gap: 1rem;
    }
    
    .nav-links {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
    
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    }
    
    .hero h1 {
        font-size: 2rem;
    }
}

/* ======== أنماط صفحة تعديل المنتج ======== */

/* خلفية صفحة التعديل */
.edit-product-page {
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    min-height: 100vh;
    padding: 20px;
    direction: rtl;
}

/* الحاوية الرئيسية */
.edit-product-container {
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    width: 100%;
    max-width: 800px;
    margin: 20px auto;
    overflow: hidden;
    animation: slideUp 0.5s ease;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* الهيدر الخاص بصفحة التعديل */
.edit-product-header {
    background: linear-gradient(135deg, var(--primary-color), var(--dark-color));
    color: white;
    padding: 30px;
    text-align: center;
    position: relative;
}

.edit-product-header h1 {
    font-size: 2.2rem;
    margin-bottom: 10px;
    color: white;
}

.edit-product-header p {
    font-size: 1.1rem;
    opacity: 0.9;
}

/* زر العودة */
.back-btn {
    position: absolute;
    right: 20px;
    top: 20px;
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    padding: 10px 20px;
    border-radius: 50px;
    cursor: pointer;
    text-decoration: none;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
}

.back-btn:hover {
    background: rgba(255,255,255,0.3);
    transform: translateX(-5px);
}

/* محتوى التعديل */
.edit-product-content {
    padding: 40px;
}

/* قسم الصورة */
.image-section {
    text-align: center;
    margin-bottom: 30px;
}

.image-section .section-title {
    color: var(--primary-color);
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--light-color);
    font-size: 1.5rem;
    text-align: center;
}

.current-image {
    max-width: 300px;
    max-height: 200px;
    border-radius: 15px;
    box-shadow: var(--box-shadow);
    margin-bottom: 15px;
    object-fit: cover;
}

.no-image {
    width: 300px;
    height: 200px;
    background: linear-gradient(135deg, var(--light-color), #c3cfe2);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    color: var(--text-color);
    font-size: 1.1rem;
}

/* أقسام النماذج */
.form-section {
    background: var(--light-color);
    border-radius: var(--border-radius);
    padding: 25px;
    margin-bottom: 25px;
    border: 2px solid #e9ecef;
}

.form-section .section-title {
    color: var(--primary-color);
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e9ecef;
    font-size: 1.5rem;
}

.form-section .form-group {
    margin-bottom: 20px;
}

.form-section .form-group label {
    display: block;
    margin-bottom: 8px;
    color: var(--primary-color);
    font-weight: 600;
}

.form-section .form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #dee2e6;
    border-radius: var(--border-radius);
    font-size: 1rem;
    transition: var(--transition);
    background: white;
}

.form-section .form-control:focus {
    outline: none;
    border-color: var(--accent-color);
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

textarea.form-control {
    min-height: 120px;
    resize: vertical;
}

/* مجموعة الأزرار */
.btn-group {
    display: flex;
    gap: 15px;
    margin-top: 25px;
    flex-wrap: wrap;
}

/* أزرار إضافية */
.btn-danger {
    background-color: #dc3545;
    color: white;
}

.btn-danger:hover {
    background-color: #c82333;
    transform: translateY(-2px);
}

.btn-warning {
    background-color: var(--warning-color);
    color: white;
}

.btn-warning:hover {
    background-color: #e67e22;
    transform: translateY(-2px);
}

/* قسم الحذف */
.delete-section {
    background: linear-gradient(135deg, #fff5f5 0%, #ffe3e3 100%);
    border: 2px solid #fecaca;
    border-radius: var(--border-radius);
    padding: 25px;
    text-align: center;
}

.delete-icon {
    font-size: 3rem;
    color: #dc3545;
    margin-bottom: 15px;
}

.delete-warning {
    color: #721c24;
    font-size: 1.1rem;
    margin-bottom: 15px;
    line-height: 1.6;
}

/* معلومات المنتج */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    background: white;
    border-radius: var(--border-radius);
    padding: 20px;
    border: 2px solid #e9ecef;
}

.info-item {
    padding: 15px;
    background: var(--light-color);
    border-radius: var(--border-radius);
}

.info-label {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 5px;
}

.info-value {
    color: var(--primary-color);
    font-weight: 600;
    font-size: 1.1rem;
}

/* تأثيرات الحركة */
.pulse {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.shake {
    animation: shake 0.5s;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}

.fade-in {
    animation: fadeIn 0.5s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* ======== تحسينات إضافية ======== */

/* صورة الملفات */
.file-input {
    display: block;
    width: 100%;
    padding: 0.8rem;
    border: 2px dashed var(--accent-color);
    border-radius: var(--border-radius);
    background: white;
    cursor: pointer;
    transition: var(--transition);
}

.file-input:hover {
    background: #f8f9fa;
    border-color: var(--primary-color);
}

/* رسالة إذا لم يتم العثور على المنتج */
.product-not-found {
    text-align: center;
    padding: 50px 20px;
}

.product-not-found-icon {
    font-size: 4rem;
    color: #6c757d;
    margin-bottom: 20px;
}

.product-not-found h3 {
    color: var(--primary-color);
    margin-bottom: 15px;
}

.product-not-found p {
    color: #666;
    margin-bottom: 25px;
}

/* ======== تحسينات للهواتف (صفحة التعديل) ======== */
@media (max-width: 768px) {
    .edit-product-page {
        padding: 10px;
    }
    
    .edit-product-container {
        margin: 10px auto;
        border-radius: 15px;
    }
    
    .edit-product-content {
        padding: 20px;
    }
    
    .edit-product-header {
        padding: 20px;
    }
    
    .edit-product-header h1 {
        font-size: 1.8rem;
        margin-top: 40px;
    }
    
    .back-btn {
        position: relative;
        top: 0;
        right: 0;
        margin-bottom: 15px;
        width: auto;
        display: inline-flex;
    }
    
    .btn-group {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        min-width: auto;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .current-image,
    .no-image {
        max-width: 100%;
        height: 200px;
    }
}

@media (max-width: 480px) {
    .edit-product-header h1 {
        font-size: 1.6rem;
    }
    
    .form-section {
        padding: 15px;
    }
}

/* ======== أنماط جانبية (Sidebar) ======== */
.sidebar {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--box-shadow);
    height: fit-content;
}

.user-profile {
    text-align: center;
    padding-bottom: 1.5rem;
    margin-bottom: 1.5rem;
    border-bottom: 1px solid #eee;
}

.profile-image {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    border-radius: 50%;
    margin: 0 auto 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2.5rem;
}

.user-profile h3 {
    margin-bottom: 0.5rem;
    color: var(--primary-color);
}

.user-role {
    color: #666;
    font-size: 0.9rem;
}

.sidebar-menu {
    list-style: none;
}

.sidebar-menu li {
    margin-bottom: 0.5rem;
}

.sidebar-menu a {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    padding: 0.8rem 1rem;
    color: var(--text-color);
    text-decoration: none;
    border-radius: var(--border-radius);
    transition: var(--transition);
}

.sidebar-menu a:hover,
.sidebar-menu a.active {
    background-color: var(--light-color);
    color: var(--primary-color);
}

.sidebar-menu a i {
    width: 20px;
    text-align: center;
}

.logout-btn {
    color: var(--secondary-color) !important;
}

.logout-btn:hover {
    background-color: #ffeaea !important;
}
    </style>
</head>
<body class="edit-product-page">
    <div class="edit-product-container">
          <!-- الهيدر -->
        <div class="edit-product-header">
            <a href="my-products.php" class="back-btn">
                <i class="fas fa-arrow-right"></i> العودة
            </a>
            <h1><i class="fas fa-edit"></i> إدارة المنتج</h1>
            <p>تعديل أو حذف المنتج الخاص بك</p>
        </div>
        
          <!-- المحتوى الرئيسي -->
        <div class="edit-product-content">
            <?php if($error): ?>
                <div class="alert alert-error shake">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert alert-success fade-in">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if($product): ?>
                   <!-- عرض الصورة -->
                <div class="image-section">
                    <h3 class="section-title"><i class="fas fa-image"></i> صورة المنتج</h3>
                    <?php if(!empty($product['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($product['title']); ?>" 
                             class="current-image">
                    <?php else: ?>
                        <div class="no-image">
                            <i class="fas fa-image"></i> لا توجد صورة
                        </div>
                    <?php endif; ?>
                    
                             <!-- نموذج تحديث الصورة -->
                    <form method="POST" enctype="multipart/form-data" class="form-container" style="max-width: 400px; margin: 20px auto;">
                        <div class="form-group">
                            <label><i class="fas fa-upload"></i> اختر صورة جديدة</label>
                            <input type="file" name="image" class="form-control file-input" accept="image/*" required>
                            <small style="display: block; margin-top: 5px; color: #666;">الصيغ المدعومة: JPG, PNG, GIF | الحد الأقصى: 5MB</small>
                        </div>
                        <button type="submit" name="update_image" class="btn btn-secondary">
                            <i class="fas fa-upload"></i> رفع صورة جديدة
                        </button>
                    </form>
                </div>
                <!-- نموذج تعديل البيانات -->
                <div class="form-section">
                    <h3 class="section-title">✏️ تعديل بيانات المنتج</h3>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label>📝 عنوان المنتج</label>
                            <input type="text" name="title" class="form-control" required 
                                   value="<?php echo htmlspecialchars($product['title']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>📄 وصف المنتج</label>
                            <textarea name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>💰 السعر (ريال سعودي)</label>
                            <input type="number" name="price" class="form-control" step="0.01" required 
                                   value="<?php echo $product['price']; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>🏷️ الفئة</label>
                            <input type="text" name="category" class="form-control" required 
                                   value="<?php echo htmlspecialchars($product['category']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>⭐ حالة المنتج</label>
                            <select name="product_condition" class="form-control" required>
                                <option value="جديد" <?php echo ($product['product_condition'] == 'جديد') ? 'selected' : ''; ?>>🆕 جديد</option>
                                <option value="مستعمل" <?php echo ($product['product_condition'] == 'مستعمل') ? 'selected' : ''; ?>>🔧 مستعمل</option>
                                <option value="ممتاز" <?php echo ($product['product_condition'] == 'ممتاز') ? 'selected' : ''; ?>>⭐ ممتاز</option>
                                <option value="جيد" <?php echo ($product['product_condition'] == 'جيد') ? 'selected' : ''; ?>>👍 جيد</option>
                            </select>
                        </div>
                        
                        <div class="btn-group">
                            <button type="submit" name="update_product" class="btn btn-primary">
                                💾 حفظ التعديلات
                            </button>
                            <a href="product-details.php?id=<?php echo $product['id']; ?>" class="btn btn-secondary">
                                👁️ عرض المنتج
                            </a>
                        </div>
                    </form>
                </div>
                
                <!-- معلومات المنتج -->
                <div class="form-section">
                    <h3 class="section-title">ℹ️ معلومات المنتج</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">🆔 رقم المنتج</div>
                            <div class="info-value">#<?php echo str_pad($product['id'], 6, '0', STR_PAD_LEFT); ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">📅 تاريخ الإضافة</div>
                            <div class="info-value"><?php echo date('Y/m/d', strtotime($product['created_at'])); ?></div>
                        </div>
                        
                        *
                        <div class="info-item">
                            <div class="info-label">👁️ المشاهدات</div>
                            <div class="info-value"><?php echo $product['views'] ?? 0; ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- قسم الحذف -->
                <div class="delete-section">
                    <div class="delete-icon">🗑️</div>
                    <h3 style="color: #dc3545; margin-bottom: 15px;">⚠️ منطقة الخطر</h3>
                    <p class="delete-warning">
                        سيتم حذف المنتج "<strong><?php echo htmlspecialchars($product['title']); ?></strong>" 
                        وجميع بياناته بشكل نهائي ولا يمكن التراجع عن هذا الإجراء.
                    </p>
                    
                    <form method="POST" onsubmit="return confirmDelete()">
                        <button type="submit" name="delete_product" class="btn btn-danger pulse">
                            🗑️ حذف المنتج نهائياً
                        </button>
                    </form>
                </div>
                
             <?php else: ?>
                <!-- إذا لم يتم العثور على المنتج -->
                <div class="product-not-found">
                    <div class="product-not-found-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <h3>المنتج غير موجود</h3>
                    <p>لا يمكن العثور على المنتج المطلوب أو لا تملك صلاحية الوصول إليه.</p>
                    <a href="my-products.php" class="btn btn-primary">
                        <i class="fas fa-arrow-right"></i> العودة إلى منتجاتي
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script>
        // تأكيد الحذف
        // تأكيد الحذف
        function confirmDelete() {
            const productName = "<?php echo addslashes($product['title'] ?? ''); ?>";
            return confirm(`⚠️ هل أنت متأكد من حذف المنتج:\n\n"${productName}"؟\n\n❌ هذا الإجراء نهائي ولا يمكن التراجع عنه!`);
        }
        
        // تأثيرات عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            // إضافة تأثير للعناصر
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    alert.style.transition = 'all 0.5s ease';
                    alert.style.opacity = '1';
                    alert.style.transform = 'translateY(0)';
                }, 100);
            });
            
            // التأكيد عند مغادرة الصفحة إذا كانت هناك تغييرات غير محفوظة
            const form = document.querySelector('form');
            let formChanged = false;
            
            if (form) {
                const inputs = form.querySelectorAll('input, textarea, select');
                inputs.forEach(input => {
                    input.addEventListener('input', () => {
                        formChanged = true;
                    });
                });
                
                window.addEventListener('beforeunload', (e) => {
                    if (formChanged) {
                        e.preventDefault();
                        e.returnValue = 'لديك تغييرات غير محفوظة. هل تريد المغادرة دون الحفظ؟';
                    }
                });
            }
        });
    </script>
</body>
</html>