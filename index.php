
<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// جلب آخر المنتجات
$products = getLatestProducts(6);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - الرئيسية</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <section class="hero">
        <div class="container">
            <h1>مرحباً بكم في أثاثي المستعمل</h1>
            <p>منصة متخصصة لبيع وشراء الأثاث المستعمل بجودة عالية وأسعار مناسبة</p>
            <a href="products.php" class="btn btn-primary">تصفح المنتجات</a>
            <?php if(!isset($_SESSION['user_id'])): ?>
                <a href="register.php" class="btn btn-secondary">انضم إلينا</a>
            <?php endif; ?>
        </div>
    </section>
    
    <section class="products-section">
        <div class="container">
            <h2 class="section-title">أحدث المنتجات</h2>
            
            <div class="products-grid">
                <?php foreach($products as $product): ?>
                <div class="product-card">
                    <img src="<?php echo $product['image_url'] ?: 'https://via.placeholder.com/300x200'; ?>" 
                         alt="<?php echo $product['title']; ?>" class="product-image">
                    <div class="product-info">
                        <h3 class="product-title"><?php echo $product['title']; ?></h3>
                        <div class="product-category"><?php echo $product['category']; ?></div>
                        <p class="product-description"><?php echo substr($product['description'], 0, 100) . '...'; ?></p>
                        <div class="product-price"><?php echo $product['price']; ?> ريال</div>
                        <a href="product-details.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">عرض التفاصيل</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div style="text-align: center; margin-top: 2rem;">
                <a href="products.php" class="btn btn-secondary">عرض جميع المنتجات</a>
            </div>
        </div>
    </section>
    
    <section class="features-section" style="padding: 3rem 0; background-color: var(--light-color);">
        <div class="container">
            <h2 class="section-title">لماذا تختار أثاثي المستعمل؟</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-top: 2rem;">
                <div style="text-align: center; padding: 2rem; background: white; border-radius: var(--border-radius);">
                    <h3>جودة مضمونة</h3>
                    <p>جميع المنتجات مفحوصة ومضمونة الجودة</p>
                </div>
                <div style="text-align: center; padding: 2rem; background: white; border-radius: var(--border-radius);">
                    <h3>أسعار مناسبة</h3>
                    <p>عروض وأسعار تنافسية للميزانيات المختلفة</p>
                </div>
                <div style="text-align: center; padding: 2rem; background: white; border-radius: var(--border-radius);">
                    <h3>توصيل سريع</h3>
                    <p>خدمة توصيل لجميع أنحاء المدينة</p>
                </div>
            </div>
        </div>
    </section>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/script.js"></script>
</body>
</html>