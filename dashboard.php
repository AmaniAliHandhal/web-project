
<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// التحقق من تسجيل الدخول
checkAuth();

// جلب إحصائيات المستخدم
$user_id = $_SESSION['user_id'];
$products_count = getUserProductsCount($user_id);
$sold_products = getSoldProductsCount($user_id);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <section class="dashboard">
        <div class="container">
            <h1 class="section-title">لوحة التحكم</h1>
            
            <div class="dashboard-grid">
                <!-- القائمة الجانبية -->
                <aside class="sidebar">
                    <h3 style="margin-bottom: 1rem; color: var(--primary-color);">القائمة</h3>
                    <ul class="sidebar-menu">
                        <li><a href="dashboard.php" class="active">الرئيسية</a></li>
                        <li><a href="add-product.php">إضافة منتج جديد</a></li>
                        <li><a href="products.php?my=1">منتجاتي</a></li>
                        <li><a href="edit-profile.php">تعديل الملف الشخصي</a></li>
                        <?php if($_SESSION['user_role'] === 'admin'): ?>
                            <li><a href="admin/">لوحة الإدارة</a></li>
                        <?php endif; ?>
                        <li><a href="logout.php">تسجيل الخروج</a></li>
                    </ul>
                </aside>
                
                <!-- المحتوى الرئيسي -->
                <main class="dashboard-content">
                    <h2 style="color: var(--primary-color); margin-bottom: 2rem;">مرحباً <?php echo $_SESSION['user_name']; ?>!</h2>
                    
                    <!-- الإحصائيات -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                        <div style="background: linear-gradient(135deg, var(--accent-color), #2980b9); color: white; padding: 1.5rem; border-radius: var(--border-radius); text-align: center;">
                            <h3 style="margin-bottom: 0.5rem;">المنتجات</h3>
                            <p style="font-size: 2rem; font-weight: bold;"><?php echo $products_count; ?></p>
                        </div>
                        
                        <div style="background: linear-gradient(135deg, var(--success-color), #229954); color: white; padding: 1.5rem; border-radius: var(--border-radius); text-align: center;">
                            <h3 style="margin-bottom: 0.5rem;">المنتجات المباعة</h3>
                            <p style="font-size: 2rem; font-weight: bold;"><?php echo $sold_products; ?></p>
                        </div>
                        
                        <div style="background: linear-gradient(135deg, var(--warning-color), #d68910); color: white; padding: 1.5rem; border-radius: var(--border-radius); text-align: center;">
                            <h3 style="margin-bottom: 0.5rem;">المنتجات النشطة</h3>
                            <p style="font-size: 2rem; font-weight: bold;"><?php echo $products_count - $sold_products; ?></p>
                        </div>
                    </div>
                    
                    <!-- إجراءات سريعة -->
                    <div style="margin-top: 2rem;">
                        <h3 style="margin-bottom: 1rem; color: var(--primary-color);">إجراءات سريعة</h3>
                        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                            <a href="add-product.php" class="btn btn-primary">إضافة منتج جديد</a>
                            <a href="products.php?my=1" class="btn btn-secondary">عرض منتجاتي</a>
                            <a href="edit-profile.php" class="btn" style="background-color: var(--light-color); color: var(--text-color);">تعديل الملف الشخصي</a>
                        </div>
                    </div>
                    
                    <!-- المنتجات الأخيرة -->
                    <?php 
                    $recent_products = getUserRecentProducts($user_id, 3);
                    if(!empty($recent_products)): 
                    ?>
                    <div style="margin-top: 3rem;">
                        <h3 style="margin-bottom: 1rem; color: var(--primary-color);">آخر منتجاتك</h3>
                        <div class="products-grid">
                            <?php foreach($recent_products as $product): ?>
                            <div class="product-card">
                                <img src="<?php echo $product['image_url'] ?: 'https://via.placeholder.com/300x200'; ?>" 
                                     alt="<?php echo $product['title']; ?>" class="product-image">
                                <div class="product-info">
                                    <h3 class="product-title"><?php echo $product['title']; ?></h3>
                                    <div class="product-price"><?php echo $product['price']; ?> ريال</div>
                                    <div style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                                        <a href="product-details.php?id=<?php echo $product['id']; ?>" class="btn" style="padding: 0.3rem 0.8rem; font-size: 0.9rem;">عرض</a>
                                        <a href="manage-product.php?id=<?php echo $product['id']; ?>" class="btn" style="padding: 0.3rem 0.8rem; font-size: 0.9rem; background-color: var(--warning-color);">تعديل</a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </main>
            </div>
        </div>
    </section>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/script.js"></script>
</body>
</html>