
<?php
require_once 'includes/config.php';
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>من نحن - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <section class="about-section">
        <div class="container">
            <h1 class="section-title">من نحن</h1>
            
            <div style="background: white; padding: 2rem; border-radius: var(--border-radius); margin-bottom: 2rem; box-shadow: var(--box-shadow);">
                <h2 style="color: var(--primary-color); margin-bottom: 1rem;">مهمتنا ورؤيتنا</h2>
                <p style="margin-bottom: 1rem; line-height: 1.8;">
                    <strong>أثاثي المستعمل</strong> هي منصة رائدة في مجال بيع وشراء الأثاث المستعمل في الجمهورية اليمنية 
                    نهدف إلى توفير حل عملي ومستدام لتحويل الأثاث المستعمل من عبء إلى فرصة.
                </p>
                
                <p style="margin-bottom: 1rem; line-height: 1.8;">
                    بدأنا رحلتنا في عام 2026 برؤية واضحة: خلق سوق موثوق وشفاف للأثاث المستعمل، 
                    حيث يمكن للجميع بيع أثاثهم بسرعة وسهولة، وشراء أثاث بجودة عالية وبأسعار مناسبة.
                </p>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-top: 2rem;">
                <div style="background: white; padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--box-shadow);">
                    <h3 style="color: var(--secondary-color); margin-bottom: 1rem;">🎯 مهمتنا</h3>
                    <p>توفير منصة آمنة وموثوقة لتبادل الأثاث المستعمل، مع الحفاظ على جودة المنتجات ورضا العملاء.</p>
                </div>
                
                <div style="background: white; padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--box-shadow);">
                    <h3 style="color: var(--secondary-color); margin-bottom: 1rem;">👁️ رؤيتنا</h3>
                    <p>أن نصبح المنصة الأولى في الشرق الأوسط لبيع وشراء الأثاث المستعمل بحلول عام2028 .</p>
                </div>
                
                <div style="background: white; padding: 2rem; border-radius: var(--border-radius); box-shadow: var(--box-shadow);">
                    <h3 style="color: var(--secondary-color); margin-bottom: 1rem;">💎 قيمنا</h3>
                    <ul style="list-style: none; padding-right: 1rem;">
                        <li>✅ الشفافية والموثوقية</li>
                        <li>✅ الجودة والتميز</li>
                        <li>✅ رضا العملاء</li>
                        <li>✅ الاستدامة البيئية</li>
                    </ul>
                </div>
            </div>
            
            <div style="background: var(--light-color); padding: 2rem; border-radius: var(--border-radius); margin-top: 2rem;">
                <h2 style="color: var(--primary-color); margin-bottom: 1rem;">إحصائياتنا</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; text-align: center;">
                    <div>
                        <h3 style="color: var(--secondary-color); font-size: 2.5rem;">+500</h3>
                        <p>منتج مباع</p>
                    </div>
                    <div>
                        <h3 style="color: var(--secondary-color); font-size: 2.5rem;">+200</h3>
                        <p>مستخدم نشط</p>
                    </div>
                    <div>
                        <h3 style="color: var(--secondary-color); font-size: 2.5rem;">+50</h3>
                        <p>بائع موثوق</p>
                    </div>
                    <div>
                        <h3 style="color: var(--secondary-color); font-size: 2.5rem;">95%</h3>
                        <p>رضا العملاء</p>
                    </div>
                </div>
            </div>
            

        </div>
    </section>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/script.js"></script>
</body>
</html>