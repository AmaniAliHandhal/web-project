
<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// البحث والتصفية
$category = $_GET['category'] ?? 'all';
$search = $_GET['search'] ?? '';

// جلب المنتجات مع التصفية
$products = getProducts($category, $search);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المنتجات - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <section class="products-section">
        <div class="container">
            <h1 class="section-title">منتجاتنا</h1>
            
            <!-- أداة البحث والتصفية -->
            <div style="background: white; padding: 2rem; border-radius: var(--border-radius); margin-bottom: 2rem; box-shadow: var(--box-shadow);">
                <form action="products.php" method="GET" style="display: grid; grid-template-columns: 1fr auto auto; gap: 1rem; align-items: end;">
                    <div class="form-group">
                        <label>بحث في المنتجات:</label>
                        <input type="text" name="search" class="form-control" placeholder="ابحث عن منتج..." value="<?php echo $search; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>تصفية حسب الفئة:</label>
                        <select name="category" class="form-control">
                            <option value="all">جميع الفئات</option>
                            <option value="غرف نوم" <?php echo $category == 'غرف نوم' ? 'selected' : ''; ?>>غرف نوم</option>
                            <option value="صالات" <?php echo $category == 'صالات' ? 'selected' : ''; ?>>صالات</option>
                            <option value="مطابخ" <?php echo $category == 'مطابخ' ? 'selected' : ''; ?>>مطابخ</option>
                            <option value="مكاتب" <?php echo $category == 'مكاتب' ? 'selected' : ''; ?>>مكاتب</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">بحث</button>
                </form>
            </div>
            
            <?php if(empty($products)): ?>
                <div class="alert alert-warning" style="text-align: center;">
                    <p>لا توجد منتجات مطابقة لبحثك</p>
                </div>
            <?php else: ?>
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
            <?php endif; ?>
        </div>
    </section>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/script.js"></script>
</body>
</html>