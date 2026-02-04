<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$product_id = $_GET['id'] ?? 0;
$product = getProductById($product_id);

if (!$product) {
    header("Location: products.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['title']; ?> - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <section class="product-details-section">
        <div class="container">
            <div class="breadcrumb" style="margin-bottom: 1rem;">
                <a href="index.php"><i class="fas fa-home"></i> الرئيسية</a> 
                <i class="fas fa-chevron-left"></i>
                <a href="products.php">المنتجات</a> 
                <i class="fas fa-chevron-left"></i>
                <span><?php echo $product['title']; ?></span>
            </div>
            
            <div style="background: white; padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--box-shadow);">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin-bottom: 2rem;">
                    <!-- صورة المنتج -->
                    <div>
                        <div class="product-image-container">
                            <img src="<?php echo $product['image_url'] ?: 'https://via.placeholder.com/500x400'; ?>" 
                                 alt="<?php echo $product['title']; ?>" 
                                 class="product-main-image"
                                 style="width: 100%; border-radius: var(--border-radius);">
                        </div>
                    </div>
                    
                    <!-- معلومات المنتج -->
                    <div>
                        <h1 style="color: var(--primary-color); margin-bottom: 1rem;"><?php echo $product['title']; ?></h1>
                        
                        <div style="display: flex; gap: 1rem; margin-bottom: 1rem; flex-wrap: wrap;">
                            <span class="product-category">
                                <i class="fas fa-tag"></i> <?php echo $product['category']; ?>
                            </span>
                            <span style="background-color: var(--light-color); padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.9rem;">
                                <i class="fas fa-star"></i> الحالة: <?php echo $product['product_condition']; // التصحيح هنا ?>
                            </span>
                            <span style="background-color: <?php echo $product['status'] == 'متاح' ? '#d4edda' : '#f8d7da'; ?>; 
                                  color: <?php echo $product['status'] == 'متاح' ? '#155724' : '#721c24'; ?>;
                                  padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.9rem;">
                                <?php echo $product['status']; ?>
                            </span>
                        </div>
                        
                        <div style="font-size: 2rem; color: var(--secondary-color); font-weight: bold; margin: 1rem 0;">
                            <i class="fas fa-money-bill-wave"></i> <?php echo $product['price']; ?> ريال
                        </div>
                        
                        <div style="margin: 2rem 0;">
                            <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;">
                                <i class="fas fa-info-circle"></i> وصف المنتج
                            </h3>
                            <p style="line-height: 1.8; padding: 1rem; background-color: var(--light-color); border-radius: var(--border-radius);">
                                <?php echo nl2br($product['description']); ?>
                            </p>
                        </div>
                        
                        <!-- معلومات البائع -->
                        <div style="background-color: var(--light-color); padding: 1.5rem; border-radius: var(--border-radius); margin-top: 2rem;">
                            <h3 style="color: var(--primary-color); margin-bottom: 1rem;">
                                <i class="fas fa-user-tie"></i> معلومات البائع
                            </h3>
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <div style="width: 60px; height: 60px; background: #ddd; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div>
                                    <h4 style="margin-bottom: 0.3rem;"><?php echo $product['seller_name']; ?></h4>
                                    <?php if($product['seller_phone']): ?>
                                    <p style="margin-bottom: 0.3rem;">
                                        <i class="fas fa-phone"></i> <?php echo $product['seller_phone']; ?>
                                    </p>
                                    <?php endif; ?>
                                    <small style="color: #666;">
                                        <i class="fas fa-clock"></i> عضو منذ 
                                        <?php 
                                        $date = new DateTime($product['created_at']);
                                        echo $date->format('Y/m/d');
                                        ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- أزرار الإجراء -->
                        <div style="display: flex; gap: 1rem; margin-top: 2rem; flex-wrap: wrap;">
                            <button onclick="contactSeller()" class="btn btn-primary">
                                <i class="fas fa-phone"></i> اتصل بالبائع
                            </button>
                            
                            <?php if(isset($_SESSION['user_id'])): ?>
                                <button onclick="addToFavorites(<?php echo $product['id']; ?>)" class="btn btn-secondary">
                                    <i class="fas fa-heart"></i> إضافة إلى المفضلة
                                </button>
                                
                                <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $product['user_id']): ?>
                                    <a href="edit-product.php?id=<?php echo $product['id']; ?>" class="btn" style="background-color: var(--warning-color);">
                                        <i class="fas fa-edit"></i> تعديل المنتج
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-secondary">
                                    <i class="fas fa-sign-in-alt"></i> سجل الدخول لإضافة للمفضلة
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- منتجات مشابهة -->
                <div style="margin-top: 3rem;">
                    <h2 style="color: var(--primary-color); margin-bottom: 1rem;">
                        <i class="fas fa-th-large"></i> منتجات مشابهة
                    </h2>
                    <?php
                    $similar_products = getSimilarProducts($product['category'], $product['id']);
                    if(!empty($similar_products)):
                    ?>
                    <div class="products-grid">
                        <?php foreach($similar_products as $similar): ?>
                        <div class="product-card">
                            <img src="<?php echo $similar['image_url'] ?: 'https://via.placeholder.com/300x200'; ?>" 
                                 alt="<?php echo $similar['title']; ?>" class="product-image">
                            <div class="product-info">
                                <h3 class="product-title"><?php echo $similar['title']; ?></h3>
                                <div class="product-category"><?php echo $similar['category']; ?></div>
                                <div class="product-price"><?php echo $similar['price']; ?> ريال</div>
                                <a href="product-details.php?id=<?php echo $similar['id']; ?>" class="btn btn-primary">
                                    <i class="fas fa-eye"></i> عرض التفاصيل
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <p style="text-align: center; padding: 2rem; background: var(--light-color); border-radius: var(--border-radius);">
                        <i class="fas fa-info-circle"></i> لا توجد منتجات مشابهة حالياً
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    
    <?php include 'includes/footer.php'; ?>
    
    <script>
    function contactSeller() {
        const phone = "<?php echo $product['seller_phone'] ?? ''; ?>";
        if(phone) {
            if(confirm("هل تريد الاتصال بالرقم " + phone + "؟")) {
                window.location.href = "tel:" + phone;
            }
        } else {
            alert("رقم الهاتف غير متاح. يرجى التواصل من خلال الموقع.");
        }
    }
    
    function addToFavorites(productId) {
        fetch('add-favorite.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({product_id: productId})
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert("تم إضافة المنتج إلى المفضلة");
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    </script>
    
    <script src="js/script.js"></script>
</body>
</html>