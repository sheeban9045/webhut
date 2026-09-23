<?php   
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
     //echo $_SESSION["uid"];die;
    //  echo "<pre>";print_r($_SESSION);echo "</pre>";die;
?>

<!--header-->
<!DOCTYPE html>
<html lang="en">


<head>
    <title>WebHut Community Builder</title>    
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no, minimal-ui" />
    <link rel="stylesheet" href="../../cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="shortcut icon" href="https://webhut.net/images/logo/icon.png" />
    <link rel="stylesheet" href="https://webhut.net/css/css-library/bootstrap.min.css">
    <link rel="stylesheet" href="https://webhut.net/css/css-library/icon-font.css">
    <link rel="stylesheet" href="https://webhut.net/css/css-library/welcome.css">
    <link rel="stylesheet" href="https://webhut.net/css/style7e0c.css?v=0.1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
   
    
    
    
    
    <!-- Cookie Consent by FreePrivacyPolicy.com https://www.FreePrivacyPolicy.com -->
    <!-- https://www.freeprivacypolicy.com/free-cookie-consent/ -->
    <script type="text/javascript" src="//www.freeprivacypolicy.com/public/cookie-consent/4.0.0/cookie-consent.js" charset="UTF-8"></script>
    <script type="text/javascript" charset="UTF-8">
        document.addEventListener('DOMContentLoaded', function () {
        cookieconsent.run({"notice_banner_type":"simple","consent_type":"express","palette":"light","language":"en","page_load_consent_levels":["strictly-necessary"],"notice_banner_reject_button_hide":false,"preferences_center_close_button_hide":false,"page_refresh_confirmation_buttons":false,"website_name":"webhut.net","website_privacy_policy_url":"https://webhut.net/privacy-policy.php"});
        });
    </script>

    <noscript>Cookie Consent by <a href="https://www.freeprivacypolicy.com/" rel="noopener">Free Privacy Policy Generator</a></noscript>
    <!-- End Cookie Consent by FreePrivacyPolicy.com https://www.FreePrivacyPolicy.com -->

    <!-- Below is the link that users can use to open Preferences Center to change their preferences. Do not modify the ID parameter. Place it where appropriate, style it as needed. -->

    <style>
        .freeprivacypolicy-com---nb .cc-nb-main-container {
            padding: 2rem;
        }
        
        .freeprivacypolicy-com---palette-light.freeprivacypolicy-com---nb {
            border-radius: 10px;
        }
        
        .freeprivacypolicy-com---palette-light .cc-nb-okagree {
            background-color: #2d4156;
            width: 48%;
            border-radius: 5px;
        }
        
        .freeprivacypolicy-com---palette-light .cc-nb-reject {
            background-color: #eaeff2;
            width: 48%;
            border-radius: 5px;
            color: #2d4156;
        }
        
        .freeprivacypolicy-com---palette-light .cc-nb-changep {
            display: none;
        }
    </style>
    
    <!-- Bootstrap 4 dropdown support (jQuery + Popper included via bundle) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        .navbar, .navbar .container-fluid, .navbar-collapse { overflow: visible !important; }
        .navbar .nav-item.dropdown { position: relative; }
        .navbar .dropdown-toggle::after { display:none !important; }
        .navbar .dropdown-toggle .fa { font-size:10px; margin-left:5px; transition:transform .2s ease; }
        .navbar .dropdown.show .dropdown-toggle .fa { transform:rotate(180deg); }
        .navbar .dropdown-menu { min-width:225px; margin-top:8px; padding:8px 0; border:0; border-radius:10px; background:#fff; box-shadow:0 12px 35px rgba(0,0,0,.18); z-index:99999; }
        .navbar .dropdown-menu::before { content:''; position:absolute; top:-6px; left:24px; width:12px; height:12px; background:#fff; transform:rotate(45deg); }
        .navbar .dropdown-menu-right::before { left:auto; right:24px; }
        .navbar .dropdown-item { position:relative; z-index:1; padding:10px 18px; color:#30343b; font-size:14px; font-weight:500; white-space:nowrap; transition:all .18s ease; }
        .navbar .dropdown-item:hover, .navbar .dropdown-item:focus { color:#fff; background:#287dcc; }
        @media (min-width:992px) {
            .navbar .nav-item.dropdown:hover > .dropdown-menu { display:block; opacity:1; visibility:visible; transform:translateY(0); }
            .navbar .dropdown-menu { display:block; opacity:0; visibility:hidden; transform:translateY(6px); transition:opacity .18s ease, transform .18s ease, visibility .18s ease; }
        }
        @media (max-width:991.98px) {
            .navbar .dropdown-menu { margin-top:0; border-radius:8px; box-shadow:0 8px 25px rgba(0,0,0,.15); }
            .navbar .dropdown-menu::before { display:none; }
        }
    </style>
</head>

<body>
<div id="home">

        <section class="top-header">
            <div class="container-fluid">
                <div class="row">

                     <div class="col-md-6">
                        <div class="left-menu">
                            <ul class="text-left mb-0">
                                <li> 
                                    <a class="nav-link" href="emailto:friendsforlife28@gmail.com"><i class="fa fa-envelope-o"></i> friendsforlife28@gmail.com </a>
                                </li>
                                <li> 
                                    <a class="nav-link" href="tel:+99 9876543210"><i class="fa fa-phone"></i> +99 9876543210</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <div class="left-menu">
                            <!-- <ul class="text-right mb-0">
                                  <li> 
                                  <li> 
                                    <a class="nav-link" href="https://www.questwalktech.com/matt-subscription/store-admin/index.php/signin">Login</a>
                                </li>
                                <li> 
                                    <a class="nav-link" href="https://www.questwalktech.com/matt-subscription/store-admin/index.php/signup">Register</a>
                                </li>
                            </ul> -->
                        <?php 
                            
                            if(!isset($_SESSION["uid"]) || $_SESSION["uid"]=="")
                            {

                        ?>
                            <ul class="text-right mb-0">
                                  <li> 
                                  <li> 
                                    <!--<a class="nav-link" href="https://localhost/matt-subscription/store-admin/index.php/signin">Login</a>-->
                                    
                                     <a class="nav-link" href="/store-admin/index.php/signin">Login</a>
                                    
                                </li>
                                <li> 
                                    <!--<a class="nav-link" href="https://localhost/matt-subscription/store-admin/index.php/signup">Register</a>-->
                                    
                                    <a class="nav-link" href="/store-admin/index.php/signup">Register</a>
                                </li>
                            </ul>
                        <?php 
                            } else {
                        ?>
                            <ul class="text-right mb-0">
                                <li>                                     
                                    <a class="nav-link" href="/store-admin/index.php/dashboard">My Account</a>
                                </li>
                                <li>                                     
                                    <a class="nav-link" href="/store-admin/index.php/signin/sign_out">Logout</a>
                                </li>
                            </ul>
                        <?php 
                            }
                        ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <nav id="navbar" class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">
                    <img src="images/logo/logo.png" height="" width="180" alt="Matt Community">
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#site-nav" aria-controls="site-nav" aria-expanded="false" aria-label="Toggle navigation"> <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="site-nav">
                    <ul class="navbar-nav text-sm-left ml-auto">
                        <li class="nav-item"> 
                            <a class="nav-link" href="index.php">Home</a>
                        </li>
                        <li class="nav-item"> 
                            <a class="nav-link" href="about.php">About Us</a>
                        </li>
                        <li class="nav-item dropdown"> 
                            <a class="nav-link dropdown-toggle" href="#" id="featuresDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Features 
                                <i class="fa fa-chevron-down" aria-hidden="true"></i>  
                            </a>
                            <div class="dropdown-menu" aria-labelledby="featuresDropdown"> 
                                <a class="dropdown-item" href="comparison-table.php">Features Plan Comparison</a>
                                <!-- <a class="dropdown-item" href="#">Earn More Money</a>
                                <a class="dropdown-item" href="#">Instant Messaging</a>
                                <a class="dropdown-item" href="#">All payment methods are supported!</a>
                                <a class="dropdown-item" href="#">Powerful Live Streaming</a> -->
                            </div>
                        </li>
                        <li class="nav-item"> 
                            <a class="nav-link" href="pricing.php">Pricing</a>
                        </li>
                        <li class="nav-item"> 
                            <a class="nav-link" href="contact.php">Contact</a>
                        </li>
                        <li class="nav-item"> 
                            <!-- <a class="nav-link" href="plugins.php">Plugins</a> -->
                            <a class="nav-link" href="list-plugins.php">Plugins</a>
                        </li>
                        <li class="nav-item"> 
                            <a class="nav-link" href="faq.php">FAQ</a>
                        </li>
                        <li class="nav-item dropdown"> 
                            <a class="nav-link dropdown-toggle" href="#" id="forumDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Forum 
                                <i class="fa fa-chevron-down" aria-hidden="true"></i>  
                            </a>
                            <div class="dropdown-menu" aria-labelledby="forumDropdown">
                                <a class="dropdown-item" href="forum.php">Forum</a>
                                <?php if (!empty($_SESSION["uid"])) { ?>
                                <a class="dropdown-item" href="forum-new-topic.php">New Topic</a>
                                <a class="dropdown-item" href="forum-my-topics.php">My Topics</a>
                                <a class="dropdown-item" href="forum-my-replies.php">My Replies</a>
                                <?php } ?>
                            </div>
                        </li>
                        <li class="nav-item dropdown"> 
                            <a class="nav-link dropdown-toggle" href="#" id="moreDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More 
                                <i class="fa fa-chevron-down" aria-hidden="true"></i>  
                            </a>
                            <div class="dropdown-menu">                                
                                <a class="dropdown-item" href="#">Customization</a>
                                <a class="dropdown-item" href="#">Documentation</a>
                                <a class="dropdown-item" href="https://webhut.net/change-log.php">Change Log</a>
                            </div>
                        </li>
                        <li class="nav-item" style="position: relative;">
                            <button id="cartToggleBtn" style="background:none; border:none; cursor:pointer; position:relative; padding:6px 8px; color:#fff;">
                                <i class="fa fa-shopping-cart" style="font-size:20px;"></i>
                                <span id="cartBadge" style="
                                    position:absolute; top:1px; right:0;
                                    background:#e74c3c; color:#fff;
                                    border-radius:50%; font-size:10px; font-weight:600;
                                    width:16px; height:16px;
                                    display:flex; align-items:center; justify-content:center;">
                                    <?php
                                        $count = 0;
                                        if (!empty($_SESSION['cart'])) {
                                            $count = array_sum(array_column($_SESSION['cart'], 'qty'));
                                        }
                                        echo $count > 0 ? $count : 0;
                                    ?>
                                </span>
                            </button>

                            <!-- Dropdown -->
                            <div id="cartDropdown" style="
                                display:none; position:absolute; top:46px; right:0;
                                width:320px; background:#fff;
                                border:1px solid #ddd; border-radius:10px;
                                box-shadow:0 8px 24px rgba(0,0,0,0.12);
                                z-index:9999; overflow:hidden;">

                                <!-- Header -->
                                <div style="padding:12px 16px; border-bottom:1px solid #eee; display:flex; justify-content:space-between; align-items:center;">
                                    <strong style="font-size:14px;">🛒 My Cart</strong>
                                    <span id="cartItemCount" style="font-size:12px; color:#666; background:#f5f5f5; padding:2px 8px; border-radius:20px;">
                                        <?php echo $count; ?> items
                                    </span>
                                </div>

                                <!-- Items List -->
                                <div id="cartItemsList" style="max-height:250px; overflow-y:auto;">
                                    <?php
                                    if (!empty($_SESSION['cart'])) {
                                        foreach ($_SESSION['cart'] as $item) {
                                            echo '<div class="cart-drop-item" data-id="'.$item['id'].'" style="display:flex; align-items:center; gap:12px; padding:12px 16px; border-bottom:1px solid #f0f0f0;">
                                                <div style="flex:1;">
                                                    <div style="font-size:13px; font-weight:600; color:#333;">'.htmlspecialchars($item['name']).'</div>
                                                    <div style="font-size:12px; color:#888; margin-top:2px;">₹'.number_format((float)$item['price']).' × '.$item['qty'].'</div>
                                                </div>
                                                <button onclick="removeFromCart('.$item['id'].')" style="background:none; border:none; cursor:pointer; color:#aaa; font-size:18px; padding:4px;" title="Remove">✕</button>
                                            </div>';
                                        }
                                    } else {
                                        echo '<div style="padding:30px; text-align:center; color:#aaa; font-size:13px;">🛒 Cart is empty</div>';
                                    }
                                    ?>
                                </div>

                                <div id="cartFooter" style="padding:12px 16px; background:#f9f9f9; border-top:1px solid #eee; display:<?php echo !empty($_SESSION['cart']) ? 'block' : 'none'; ?>;">
                                    <div style="display:flex; justify-content:space-between; margin-bottom:12px;">
                                        <span style="font-size:13px; color:#666;">Total</span>
                                        <strong id="cartTotal" style="font-size:15px;">
                                            ₹<?php
                                                $total = 0;
                                                if (!empty($_SESSION['cart'])) {
                                                    foreach ($_SESSION['cart'] as $item) {
                                                        $total += (float)$item['price'] * $item['qty'];
                                                    }
                                                }
                                                echo number_format((float)$total);
                                            ?>
                                        </strong>
                                    </div>
                                    <div style="display:flex; gap:8px;">
                                        <a href="/store-admin/index.php/Webhut_plugins/checkout" style="flex:1; padding:9px; text-align:center; background:#12407a; border-radius:6px; color:#fff; font-size:13px; font-weight:600; text-decoration:none;">⚡ Checkout</a>
                                    </div>
                                </div>
                            </div>
                        </li>  
                        <li class="nav-item">
                            <a href="https://webhut.net/comparison-table.php" class="btn align-middle btn-primary my-2 my-lg-0">Demo</a>
                        </li>
                         <li class="nav-item">
                             <a href="pricing.php" class="btn align-middle btn-primary my-2 my-lg-0">Buy Now</a>
                         </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Keep Bootstrap's click/touch dropdown behavior and close other menus cleanly.
    if (window.jQuery && jQuery.fn.dropdown) {
        jQuery('.navbar .dropdown-toggle').on('click', function (e) {
            if (this.getAttribute('href') === '#') {
                e.preventDefault();
            }
        });
    }
});
</script>
