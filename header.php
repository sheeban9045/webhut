<?php   
    session_start();
     //echo $_SESSION["uid"];die;
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
                            
                            if($_SESSION["uid"]=="")
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
                            }
                            //else Name pending !
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
                            <a class="nav-link" href="#" data-toggle="dropdown">Features 
                                <span class="pe-2x pe-7s-angle-down" style="color: #ffffff;"></span>  
                            </a>
                            <div class="dropdown-menu"> 
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
                            <a class="nav-link" href="#" data-toggle="dropdown">More 
                                <span class="pe-2x pe-7s-angle-down" style="color: #ffffff;"></span>  
                            </a>
                            <div class="dropdown-menu">                                
                                <a class="dropdown-item" href="#">Customization</a>
                                <a class="dropdown-item" href="#">Documentation</a>
                                <a class="dropdown-item" href="https://webhut.net/change-log.php">Change Log</a>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a href="https://webhut.net/comparison-table.php" class="btn align-middle btn-primary my-2 my-lg-0">Demo</a>
                        </li>
                         <a href="pricing.php" class="btn align-middle btn-primary my-2 my-lg-0">Buy Now</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>