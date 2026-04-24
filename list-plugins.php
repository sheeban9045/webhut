<?php require './Config.php'; ?>
<!--header-->
<?php require './header.php'; ?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet">
<style>
.products-wrapper{
    display:flex;
    flex-wrap:wrap;
    gap:20px;
    margin-top:50px;
    margin-bottom:50px;
    justify-content:center;
}

.product-grid-custom{
    width:220px;
    border:1px solid #ddd;
    padding:10px;
    font-family:arial;
    background:#fff;
}

.product-grid-custom img{
    width:100%;
    height:150px;
    object-fit:contain;
}

.product-title-custom{
    font-size:16px;
    color:#1a73e8;
    margin-top:8px;
}

.product-category-custom{
    font-size:13px;
    color:#777;
}

.price-custom{
    font-size:18px;
    color:#e65100;
    font-weight:bold;
}

.old-price-custom{
    text-decoration:line-through;
    color:#777;
    font-size:14px;
    margin-left:5px;
}

.discount-custom{
    color:green;
    font-size:13px;
}

.cart-btn-custom {
    color: #fff;
    background: url(https://webhut.net/webhut-parent-community/application/modules/Sitestoreproduct/externals/images/icons/cart-add24.png?c=54) no-repeat center;
    background-color: #5cb85c;
    padding: 6px 10px;
    display: block;
    width: 50%;
    height: 36px;
    border-radius: 4px;
    text-decoration: none;
}
</style>
<?php 

$se_host = 'localhost';
// $se_user = 'webhut96_parent_community';
// $se_pass = '@#$Deepak25';
$se_user = 'root';
$se_pass = '';
$se_db   = 'webhut96_parent_community';

$se_conn = mysqli_connect($se_host, $se_user, $se_pass, $se_db);

if (!$se_conn) die("Connection failed: " . mysqli_connect_error());

$query = "SELECT p.*, c.*, f.*, o.*
FROM engine4_sitestoreproduct_products p
LEFT JOIN engine4_storage_files f 
ON p.photo_id = f.file_id
LEFT JOIN engine4_sitestoreproduct_categories c
ON p.category_id = c.category_id
LEFT JOIN engine4_sitestoreproduct_otherinfo o
ON p.product_id = o.product_id
WHERE p.approved = 1";

$result = mysqli_query($se_conn, $query);

echo '<div class="products-wrapper">';

    while($row = mysqli_fetch_assoc($result)){
        $image = "https://webhut.net/webhut-parent-community/".$row['storage_path'];
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $row['title'])));
        $old_price = $row['price'];
        $price = $old_price - $row['discount_amount'];

        ?>

        <div class="product-grid-custom">
            <img src="<?php echo $image; ?>">
            <div class="product-title-custom">
                <?php echo $row['title']; ?>
            </div>
            <!-- <div class="product-category-custom">
                <?php // echo $row['category_name']; ?>
            </div> -->
            <div class="price-custom">
                $<?php echo $price; ?>
                <?php if($row['discount_amount']>0){ ?>
                    <span class="old-price-custom">$<?php echo $old_price; ?></span>
                    <span class="discount-custom">(<?php echo $row['discount_percentage']; ?>% off)</span>
                <?php } ?>
            </div>
            <br>
            <a href="/webhut-parent-community/stores/product/<?php echo $row['product_id']; ?>/<?php echo $slug; ?>" class="cart-btn-custom"></a>
        </div>
        <?php } ?>
</div>