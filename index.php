<?php
    require './Config.php';
 ?>
<?php require './header.php'; ?>
<!--banner background-->
<div class="wave_scene">
    <svg width="100%" height="100%" version="1.1" xmlns="http://www.w3.org/2000/svg" class="wave"><defs></defs><path id="feel-the-wave" d=""/></svg>
    <svg width="100%" height="100%" version="1.1" xmlns="http://www.w3.org/2000/svg" class="wave"><defs></defs><path id="feel-the-wave-two" d=""/></svg>
    <svg width="100%" height="100%" version="1.1" xmlns="http://www.w3.org/2000/svg" class="wave"><defs></defs><path id="feel-the-wave-three" d=""/></svg>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet" />
</div>
<?php
    error_reporting(0);
    include("Database.php");
    // CODE FOR DISPALY THE ANNOUNCEMENT
    $result = (mysqli_query($conn, "SELECT `setting_value` from `crm_settings` WHERE `setting_name`='timezone'"));
    $row = mysqli_fetch_assoc($result);
    $timezoneSetting = $row["setting_value"];
    
    $d = DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));
    $d->setTimeZone(new DateTimeZone("UTC"));
    $d = $d->format("Y-m-d H:i:s");

    $timeZone = new DateTimeZone($timezoneSetting);
    $dateTime = new DateTime("now", $timeZone);
    $timeZone = $timeZone->getOffset($dateTime);

    $now = date("Y-m-d H:i:s", strtotime($d) + $timeZone); 
    
    $announcements = (mysqli_query($conn, "SELECT * from crm_announcements WHERE start_date<='$now' AND end_date>='$now'AND deleted = 0"));
    $annouce = array();
    while ($row = mysqli_fetch_assoc($announcements)) {
        $annouce[] = $row;
    }
    // CODE FOR DISPALY THE ANNOUNCEMENT
    
    $itemsResult = (mysqli_query($conn, "SELECT * from crm_items where `type` = 'monthly' AND `deleted` = 0 ORDER BY `order` ASC LIMIT 5"));    
    // Fetch the data and store it in an array
    $items = array();
    $hasSix = false;

    while ($row = mysqli_fetch_assoc($itemsResult)) {
        if ($row['id'] == 6) $hasSix = true;
        $items[] = $row;
    }

    $items = $hasSix ? array_slice($items, 0, 5) : array_slice($items, 0, 4);

    $resultOneTime = (mysqli_query($conn, "SELECT * from crm_items WHERE `type` = 'onetime' AND `deleted` = 0 ORDER BY `order` ASC"));    
    // Fetch the data and store it in an array
    $itemsOneTime = array();

    while ($row = mysqli_fetch_assoc($resultOneTime)) {
        $itemsOneTime[] = $row;
    }
?>
<style>
    .announcement-div::-webkit-scrollbar {
        display: none;
    }
    .announcement-div {
        width: 100%;
        overflow-x: hidden;
        overflow-y: scroll;
        max-height: 170px;
        text-align: left;
        position: relative;
        bottom: 64px;
    }

    svg:not(.gantt) {
        margin-top: -3px;
        pointer-events: none;
    }

    .icon-18 {
        width: 18px;
        height: 18px; 
    }

    .mr10 {
        margin-right: 10px; 
    }   

    img,
    svg {
        vertical-align: middle;
    }

    .card-layout {
        background: #fff;
        border-radius: 20px;
        width: 320px;
        padding: 32px 28px 28px;
        box-shadow: 0 8px 40px rgba(30, 120, 220, 0.15);
        text-align: center;
        position: relative;
        overflow: hidden;
        font-family: "Nunito", sans-serif;
        min-height: 564px;
        margin: 5px auto;
    }

    .card-layout::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #1a7fe8, #4eb8ff);
        border-radius: 20px 20px 0 0;
    }

    .card-layout .card-title {
        font-size: 17px;
        font-weight: 700;
        color: #555;
        margin-bottom: 10px;
        letter-spacing: 0.3px;
    }

    .card-layout .price {
        font-size: 64px;
        font-weight: 900;
        color: #1a1a2e;
        line-height: 1;
        margin-bottom: 4px;
        }

    .card-layout .price-period {
        font-size: 22px;
        font-weight: 800;
        color: #1a7fe8;
        margin-bottom: 4px;
    }

    .card-layout .domain-tag {
        display: inline-block;
        background: #f0f7ff;
        color: #888;
        font-size: 13px;
        font-weight: 600;
        padding: 3px 14px;
        border-radius: 20px;
        margin-bottom: 20px;
    }

    .card-layout .divider {
        border: none;
        border-top: 1px solid #eef2f7;
        margin: 0 0 18px;
    }

    .card-layout .description {
        font-size: 14px;
        color: #666;
        line-height: 1.65;
        height: 139px;
        margin-bottom: 6px;
    }

    .card-layout .read-more {
        color: #1a7fe8;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        display: inline-block;
        margin-bottom: 24px;
        transition: color 0.2s;
    }

    .card-layout .read-more:hover {
        color: #0f5bb5;
    }

    .card-layout .btn-row {
        display: flex;
        gap: 10px;
    }

    .card-layout .btn {
        flex: 1;
        padding: 13px 10px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 800;
        cursor: pointer;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        letter-spacing: 0.3px;
        transition:
            transform 0.15s,
            box-shadow 0.15s,
            opacity 0.15s;
    }

    .card-layout .btn:hover {
        transform: translateY(-2px);
        opacity: 0.92;
    }

    .card-layout .btn:active {
        transform: translateY(0);
    }

    .card-layout .btn-buy {
        background: linear-gradient(135deg, #1a7fe8, #3faeff);
        color: #fff;
        box-shadow: 0 4px 16px rgba(26, 127, 232, 0.35);
    }

    .card-layout .btn-demo {
        background: #fff;
        color: #1a7fe8;
        border: 2px solid #1a7fe8;
        box-shadow: 0 4px 12px rgba(26, 127, 232, 0.1);
    }

    .card-layout .btn-demo:hover {
        background: #f0f7ff;
    }

    .card-layout .icon {
        font-size: 15px;
    }
</style>

<section class="home-pge jumbotron text-center">
    <div class="announcement-div">
            <?php
            foreach ($annouce as $announcement) {
                ?>
                <div  id="<?php echo "announcement-$announcement->id"; ?>" class="alert alert-danger">
                    <h5 style="color:red;margin-top:0px;padding-top:0px">Announcement</h5>
                    <i data-feather="volume-2" class="icon-18 mr10"></i> 
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-volume-2 icon-18 mr10"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
                    <a style= "font-weight: bold;color: black;" href="store-admin/index.php/announcements" style="color:Red"><?php echo $announcement['title'];?></a>
                    <div style="color: black;
                        padding-left: 55px;
                        font-size: 14px;
                    " >
                        <?php echo $announcement['description'];?>
                    </div>
                </div>
                <?php
            }
            ?>
    </div>
    <div>&nbsp;</div>
    <div class="container">
        <h1 class="display_4 pure_white">Build Your Own Community</h1>
        <p class="lead my-4 white_text">WebHut is the leading PHP Social Network Script which allows you to start your own social network website, anytime, anywhere!</p>
        <p>
            <a href="pricing.php" class="btn btn-lg btn-light">Get WebHut</a>
            <a href="https://webhut.net/comparison-table.php" class="btn btn-lg btn-outline-light">See Demo</a>
        </p>
    </div>
</section>

<!--banner slider with image-->
<div class="split_bg">
    <div class="macbook_hero">
        <img src="images/macbook.png" alt="macbook" class="img-fluid" />
        <div class="macbook-screen">
            <div id="carouselCommunity" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                    <li data-target="#carouselCommunity" data-slide-to="0" class="active"></li>
                    <li data-target="#carouselCommunity" data-slide-to="1"></li>
                    <li data-target="#carouselCommunity" data-slide-to="2"></li>
                </ol>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img class="d-block w-100" src="images/banner/banner-1.png" alt="First screen">
                    </div>
                    <div class="carousel-item">
                        <img class="d-block w-100" src="images/banner/banner-2.png" alt="Second screen">
                    </div>
                    <div class="carousel-item">
                        <img class="d-block w-100" src="images/banner/banner-3.png" alt="Third screen">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="section" id="intro">
    <div class="container">
        <div class="lang_logos negative-margin text-center">
            <h1>Made with latest Technology</h1>
            <div class="language-icon mt-3">
                <img src="images/tech-icon/bootstrap.png">
                    <img src="images/tech-icon/html.png">
                        <img src="images/tech-icon/css.png">
                            <img src="images/tech-icon/jquery.png">
                                <img src="images/tech-icon/php.png">
                                    </div>
                                    </div>
                                    </div>
                                    </div>

                                    <!--Why WebHut-->

                                    <div class="section bg_dark pt_lg co_features why-choose-section">
                                        <div class="container text-center prel">
                                            <h1 class="display_7 pure_white">Why Choose WebHut?</h1>
                                            <div class="row">
                                                <div class="col-md-6 col-lg-4">
                                                    <div class="media">
                                                        <div class="media-body text-center">
                                                            <div class="color-icon mb-3"> <i class="fa fa-cog pe-3x"></i> 
                                                            </div>
                                                            <h5 class="mb-4 pure_white">Highly Customization</h5>  
                                                            <span class="white_text">Change any setting or customize any color through Admin Panel.</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-lg-4">
                                                    <div class="media">
                                                        <div class="media-body text-center">
                                                            <div class="color-icon mb-3"> <i class="fa fa-rocket pe-3x"></i> 
                                                            </div>
                                                            <h5 class="mb-4 pure_white">Super Fast</h5>  
                                                            <span class="white_text">Enable the Powerful Cache system and make your website super fast.</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-lg-4">
                                                    <div class="media">
                                                        <div class="media-body text-center">
                                                            <div class="color-icon mb-3"> <i class="fa fa-tachometer pe-3x"></i> 
                                                            </div>
                                                            <h5 class="mb-4 pure_white">High Performance</h5>  
                                                            <span class="white_text">Due to secure system, website can handle more than 1 Million users.</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-lg-4 order-first order-md-last">
                                                    <div class="media">
                                                        <div class="media-body text-center">
                                                            <div class="color-icon mb-3"> <i class="fa fa-cloud-upload pe-3x"></i> 
                                                            </div>
                                                            <h5 class="mb-4 pure_white">Cloud Upload</h5>  
                                                            <span class="white_text">Use Amazon S3 to remotely upload images and videos to the Cloud.</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-lg-4">
                                                    <div class="media">
                                                        <div class="media-body text-center">
                                                            <div class="color-icon mb-3"> <i class="pe-7s-science pe-3x"></i> 
                                                            </div>
                                                            <h5 class="mb-4 pure_white">Latest Technology</h5>  
                                                            <span class="white_text">We always belive in to use best technologies, which provide high security.</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-lg-4">
                                                    <div class="media">
                                                        <div class="media-body text-center">
                                                            <div class="color-icon mb-3"> <i class="fa fa-mobile pe-3x"></i> 
                                                            </div>
                                                            <h5 class="mb-4 pure_white">Fully Responsive</h5>  
                                                            <span class="white_text">Built on popular Bootstrap framework, WebHut is fully mobile responsive.</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!--Features-->
                                    <section id="features">
                                        <div class="section">
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-sm-8">
                                                        <div class="browser-window limit-height mr-0 mr-sm-5">
                                                            <div class="content">
                                                                <img src="images/admin-side/admin-panel.jpg" alt="image">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="media mt-4">
                                                            <div class="media-body">
                                                                <div class="media-icon mb-3"> 
                                                                    <i class="pe-7s-tools pe-3x"></i> 
                                                                </div>
                                                                <h3 class="mt-0">Powerful Admin Panel</h3>
                                                                <div class="feature-border"></div>
                                                                <p>Manage your site content, users, posts, pages, groups, settings, and style your website also from our full managment control panel.</p> 
                                                                <a href="#">Get Started Now →</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mt-md-5">
                                                    <div class="col-sm-4">
                                                        <div class="media mt-4">
                                                            <div class="media-body">
                                                                <div class="media-icon mb-3"> 
                                                                    <i class="pe-7s-graph1 pe-3x"></i> 
                                                                </div>
                                                                <h3 class="mt-0">Earn More Money</h3>
                                                                <div class="feature-border"></div>
                                                                <p>Monetize your site with various Subscription plans or display Ads in your site to earn money.</p> 
                                                                <a href="#">Get Started Now →</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <div class="browser-window limit-height mr-0 mr-sm-5">
                                                            <div class="content">
                                                                <img src="images/admin-side/earn.jpg" alt="image">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mt-5">
                                                    <div class="col-sm-8">
                                                        <div class="browser-window limit-height">
                                                            <div class="content">
                                                                <img src="images/admin-side/chat.jpg" alt="image">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="media mt-4">
                                                            <div class="media-body">
                                                                <div class="media-icon mb-3"> 
                                                                    <i class="pe-7s-comment pe-3x"></i> 
                                                                </div>
                                                                <h3 class="mt-0">Instant Messaging</h3>
                                                                <div class="feature-border"></div>
                                                                <p>Instantly chat with your friends with the great messaging system, which supports Audio, Video calls, emojis, chat groups and much more. Our chat system is powered by NodeJs for fast real-time messaging.</p> 
                                                                <a href="#">Get Started Now →</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mt-5">
                                                    <div class="col-sm-4">
                                                        <div class="media mt-4">
                                                            <div class="media-body">
                                                                <div class="media-icon mb-3"> 
                                                                    <i class="pe-7s-diamond pe-3x"></i> 
                                                                </div>
                                                                <h3 class="mt-0">All payment methods are supported!</h3>
                                                                <div class="feature-border"></div>
                                                                <p>Your users can pay using Bitcoin, PayPal, Local Bank, Credit Cards, and by Mobile.</p> 
                                                                <a href="#">Get Started Now →</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <div class="browser-window limit-height mr-0 mr-sm-5">
                                                            <div class="content">
                                                                <img src="images/admin-side/payment.jpg" alt="image">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mt-5">
                                                    <div class="col-sm-8">
                                                        <div class="browser-window limit-height mr-0 mr-sm-5">
                                                            <div class="content">
                                                                <img src="images/admin-side/live.jpg" alt="image">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="media mt-4">
                                                            <div class="media-body">
                                                                <div class="media-icon mb-3"> 
                                                                    <i class="pe-7s-comment pe-3x"></i> 
                                                                </div>
                                                                <h3 class="mt-0">Powerful Live Streaming</h3>
                                                                <div class="feature-border"></div>
                                                                <p>People engage longer when they see, hear, and interact with each other. With WebHut, your users can go live anytime, anywhere, from any device.</p> 
                                                                <a href="#">Get Started Now →</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-center and-much"><a href="comparison-table.html" class="btn btn-xl btn-outline-primary">And Much More!</a></div>
                                            </div>
                                        </div>
                                    </section>

                                    <!--Stats-->
                                    <div class="section bg_dark py_lg co_stats counter-section">
                                        <div class="container">
                                            <div class="section_title text-center mb-5">
                                                <h1 class="display_7 pure_white">Some Cool Stats</h1>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6 col-lg-3">
                                                    <div class="media">
                                                        <div class="media-body text-center">
                                                            <div class="color-icon mb-3"> 
                                                                <i class="pe-7s-smile pe-3x"></i> 
                                                            </div>
                                                            <h5 class="mt-0 pure_white">6,500+</h5>
                                                            <span class="white_text">HAPPY CUSTOMERS</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 col-lg-3">
                                                    <div class="media">
                                                        <div class="media-body text-center">
                                                            <div class="color-icon mb-3"> 
                                                                <i class="fa fa-briefcase pe-3x"></i> 
                                                            </div>
                                                            <h5 class="mt-0 pure_white">20</h5>
                                                            <span class="white_text">Projects Done</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 col-lg-3">
                                                    <div class="media">
                                                        <div class="media-body text-center">
                                                            <div class="color-icon mb-3"> 
                                                                <i class="fa fa-code pe-3x"></i> 
                                                            </div>
                                                            <h5 class="mt-0 pure_white">9,314+</h5>
                                                            <span class="white_text">Lines of Code</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 col-lg-3">
                                                    <div class="media">
                                                        <div class="media-body text-center">
                                                            <div class="color-icon mb-3"> 
                                                                <i class="pe-7s-star pe-3x"></i> 
                                                            </div>
                                                            <h5 class="mt-0 pure_white">800+</h5><span class="white_text">5 STAR REVIEWS</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="section">
                                        <div class="container-fluid text-center">
                                            <div class="section_title">
                                                <h1 class="display_7 ">WebHut Cool Features</h1>
                                            </div>
                                            <div class="row co_cool_features">
                                                <div class="single-feature">
                                                    <div class="feature-icon">
                                                        <img src="images/features/1.png" alt="">
                                                    </div>
                                                    <h4 class="title">RTL Support</h4>
                                                </div>
                                                <div class="single-feature">
                                                    <div class="feature-icon">
                                                        <img src="images/features/2.png" alt="">
                                                    </div>
                                                    <h4 class="title">Social Login</h4>
                                                </div>
                                                <div class="single-feature">
                                                    <div class="feature-icon">
                                                        <img src="images/features/3.png" alt="">
                                                    </div>
                                                    <h4 class="title">Friends & Follow</h4>
                                                </div>
                                                <div class="single-feature">
                                                    <div class="feature-icon">
                                                        <img src="images/features/4.png" alt="">
                                                    </div>
                                                    <h4 class="title">Photo Albums</h4>
                                                </div>
                                                <div class="single-feature">
                                                    <div class="feature-icon">
                                                        <img src="images/features/5.png" alt="">
                                                    </div>
                                                    <h4 class="title">User Privacy</h4>
                                                </div>
                                                <div class="single-feature">
                                                    <div class="feature-icon">
                                                        <img src="images/features/6.png" alt="">
                                                    </div>
                                                    <h4 class="title">Save Posts</h4>
                                                </div>
                                                <div class="single-feature">
                                                    <div class="feature-icon">
                                                        <img src="images/features/7.png" alt="">
                                                    </div>
                                                    <h4 class="title">Live Chat</h4>
                                                </div>
                                                <div class="single-feature">
                                                    <div class="feature-icon">
                                                        <img src="images/features/8.png" alt="">
                                                    </div>
                                                    <h4 class="title">Emoticons</h4>
                                                </div>
                                            </div>
                                            <br>
                                                <br> <a href="comparison-table.html" class="btn btn-xl btn-outline-primary">View More Features</a>
                                                    </div>
                                                    </div>


                                                    <!-- <div class="section bg_light py_lg co_stats" id="pricing">
                                                        <div class="container prel">
                                                            <div class="section_title text-center mt-0 mb-5">
                                                                <h1 class="display_7 pure_white">Choose your plan</h1>
                                                                <p class=" white_text">Simple pricing. No hidden charges.</p>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-lg-4">
                                                                    <div class="card pricing">
                                                                        <div class="card-body"> <small>ADVANCED</small>
                                                                            <h5 class="card-title">$135</h5>
                                                                            <div class="card-text">
                                                                                <ul class="list-unstyled">
                                                                                    <li>Life-time updates</li>
                                                                                    <li>1 year support package</li>
                                                                                    <li>All features included</li>
                                                                                    <li>Installation service</li>
                                                                                    <li>Modify source code.</li>
                                                                                    <li>Party user</li>
                                                                                </ul>
                                                                               <divs">
                                                                                    <p>$135.00 / Month For 1 Month and 2 Days free trial</p>
                                                                               </div>
                                                                            </div> 
                                                                             <a href="#" class="btn btn-xl btn-outline-light mt-1">Choose this plan</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4">
                                                                    <div class="card pricing">
                                                                        <div class="card-body"> <small>BASIC (Recommended)</small>
                                                                            <h5 class="card-title">$99</h5>
                                                                            <div class="card-text">
                                                                                <ul class="list-unstyled">
                                                                                    <li>Life-time updates</li>
                                                                                    <li>6 months support package</li>
                                                                                    <li>All features included</li>
                                                                                    <li>Installation service</li>
                                                                                    <li>Modify source code</li>
                                                                                    <li>Party user</li>
                                                                                </ul>
                                                                                 <divs">
                                                                                    <p>$99.00 / Month For 1 Month and 2 Days free trial</p>
                                                                               </div>
                                                                            </div> 
                                                                           <a href="#" class="btn btn-xl btn-outline-light mt-1">Choose this plan</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4">
                                                                    <div class="card pricing">
                                                                        <div class="card-body"> <small>EXTENDED</small>
                                                                            <h5 class="card-title">$299</h5>
                                                                            <div class="card-text">
                                                                                <ul class="list-unstyled">
                                                                                    <li>Life-time updates</li>
                                                                                    <li>6 months support package</li>
                                                                                    <li>All features included</li>
                                                                                    <li>Installation service</li>
                                                                                    <li>Modify source code</li>
                                                                                    <li>Party user</li>
                                                                                </ul>
                                                                                 <divs">
                                                                                    <p>$299.00 / Month For 1 Month and 2 Days free trial</p>
                                                                               </div>
                                                                            </div> 
                                                                           <a href="#" class="btn btn-xl btn-outline-light mt-1">Choose this plan</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> -->

                                                    <div class="section bg_light py_lg co_stats pricing-section">
                                                        <div class="container-fluid">
                                                            <div class="text-center">
                                                                <h1 class="display_7 pure_white">Choose your plan</h1>
                                                                <div class="price-btn"> 
                                                                    <a href="javascript:void(0);" id="monthly" class="price-btn-bg btn btn-xl btn-outline-light my-2 mx-2">Monthly Fee</a>
                                                                    <a href="javascript:void(0);" id="one-time" class="btn btn-xl btn-outline-light my-2 mx-2">One Time Fee</a>
                                                                </div>
                                                            </div>


                                                            <div id="monthly-section">
                                                                <div class="row text-center mb-5">
                                                                    <div class="col-lg-12 col-md-12">
                                                                        <div class="price-btn"> 
                                                                            <p class="white_text mt-3 mb-0">Host and Manage your Website on our Servers for a Monthly Fee!</p>
                                                                            <p class="white_text ">Your Website will Automatically Install and Set Up in Seconds!</p>

                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <?php foreach($items as $item) { 
                                                                        if($item['id'] != 6) { ?>
                                                                        
                                                                        <div class="col-lg-3 col-md-6 col-sm-6">
                                                                            <div class="card-layout">

                                                                                <div class="card-title"><?php echo $item['title']; ?></div>

                                                                                <div class="price">$<?php echo $item['rate']; ?></div>
                                                                                <div class="price-period">A Month</div>

                                                                                <div class="domain-tag">1 domain</div>

                                                                                <hr class="divider" />

                                                                                <div class="description">
                                                                                    <?php echo substr(strip_tags($item['description']), 0, 200) . '...'; ?>
                                                                                </div>

                                                                                <div class="full-description" style="display: none;">
                                                                                    <?php echo $item['description']; ?>
                                                                                </div>

                                                                                <a href="#" class="read-more read-more-link">Read More</a>

                                                                                <div class="btn-row">
                                                                                    <a class="btn btn-demo" href="<?php echo $item['demo_url']; ?>" target="_blank">
                                                                                        ▶ Demo
                                                                                    </a>

                                                                                    <a class="btn btn-buy" href="store-admin/index.php/items/pricing_add_to_cart/<?php echo $item['id']; ?>">
                                                                                        🛒 Buy Now
                                                                                    </a>
                                                                                </div>

                                                                                <div class="price-bottom my-3">
                                                                                    <label>
                                                                                        <input type="checkbox">
                                                                                        Installation Service (+$<?php echo $item['installation_charges'] ?? 0; ?>)
                                                                                    </label>
                                                                                </div>

                                                                            </div>
                                                                        </div>

                                                                    <?php }} ?>
                                                                </div>
                                                            </div>


                                                            <div id="one-time-section">
                                                                <div class="row text-center mb-5">
                                                                    <div class="col-lg-12 col-md-12">
                                                                        <div class="price-btn"> 
                                                                            <p class="white_text mt-3 mb-0">Host, Manage, setup, and install your website on your own server for a one time fee</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="row">
                                                                        <?php foreach($itemsOneTime as $item) { ?>                                                                         
                                                                            <div class="col-lg-3 col-md-6 col-sm-6">
                                                                                <div class="card-layout">

                                                                                    <div class="card-title"><?php echo $item['title']; ?></div>

                                                                                    <div class="price">$<?php echo $item['rate']; ?></div>
                                                                                    <div class="price-period">One Time Fee</div>

                                                                                    <div class="domain-tag">1 domain</div>

                                                                                    <hr class="divider" />

                                                                                    <div class="description">
                                                                                        <?php echo substr(strip_tags($item['description']), 0, 200) . '...'; ?>
                                                                                    </div>

                                                                                    <div class="full-description" style="display: none;">
                                                                                        <?php echo $item['description']; ?>
                                                                                    </div>

                                                                                    <a href="#" class="read-more read-more-link">Read More</a>

                                                                                    <div class="btn-row">
                                                                                        <a class="btn btn-demo" href="<?php echo $item['demo_url']; ?>" target="_blank">
                                                                                            ▶ Demo
                                                                                        </a>

                                                                                        <a class="btn btn-buy" href="store-admin/index.php/items/pricing_add_to_cart/<?php echo $item['id']; ?>">
                                                                                            🛒 Buy Now
                                                                                        </a>
                                                                                    </div>

                                                                                    <div class="price-bottom my-3">
                                                                                        <label>
                                                                                            <input type="checkbox">
                                                                                            Installation Service (+$<?php echo $item['installation_charges'] ?? 0; ?>)
                                                                                        </label>
                                                                                    </div>

                                                                                </div>
                                                                            </div>
                                                                        <?php } ?>
                                                                    </div>
                                                                </div>

                                                            </div>

                                                        </div>
                                                    </div>


                                                    <div class="section">
                                                        <div class="container">
                                                            <div class="row align-items-center">
                                                                <div class="col-sm-6">
                                                                    <div class="media">
                                                                        <div class="media-body">
                                                                            <h2 class="mt-0">Full API included</h2>
                                                                            <p>Build amazing application using our full API system. So you can provide users the ability to Login with your Wbesite on other sites.</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div>
                                                                        <div class="content">
                                                                            <img src="images/api.jpg" class="img-fluid">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="section bg_dark">
                                                        <div class="container">
                                                            <div class="row align-items-center">
                                                                <div class="col-sm-6">
                                                                    <div>
                                                                        <div class="content">
                                                                            <img src="images/security.jpg" class="img-fluid">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="media">
                                                                        <div class="media-body">
                                                                            <h2 class="mt-0 pure_white">Privacy and Security</h2>
                                                                            <p class="white_text">Providing best security solution is our top priority. You can fully control who can follow you, send you message, post on your timeline, etc.</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="section subscribe-section">
                                                        <div class="container">
                                                            <div class="section_title text-center">
                                                                <h1 class="display_7">Subscribe to Newsletter</h1>
                                                                <p>We'll never Spam your Inbox!</p>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-lg-3"></div>
                                                                <form class="col col-md-12 col-lg-6" method="post" action="#">
                                                                    <div class="col-sm-8 pull-left">
                                                                        <input class="form-control" name="email" type="email" placeholder="Your Email" required="">
                                                                    </div>
                                                                    <div class="col-sm-4 pull-left">
                                                                        <button type="submit" class="btn btn-xl btn-block btn-primary">Subscribe</button>
                                                                    </div>
                                                                </form>
                                                                <div class="col-lg-3"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="section bg_light py_lg testimonial-section">
                                                        <div class="container prel">
                                                            <div class="section_title text-center">
                                                                <h1 class="display_7 pure_white">What our Customers say</h1>
                                                                <p class="white_text">They love it. Read what our users had to say!</p>
                                                            </div>
                                                            <div class="row text-center">
                                                                <div class="col-md-2"></div>
                                                                <div class="col-md-8 co_reviews">
                                                                    <div class="co_reviews_item">
                                                                        <img src="images/testimonial/user1.jpg" width="100" height="100" alt="Amanda Smith">
                                                                            <figcaption>
                                                                                <a href="#">
                                                                                    <cite class="name">Amanda Smith</cite></a>
                                                                                <span class="designation light_text d-block mb-2">Developer</span>
                                                                            </figcaption>
                                                                            <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem ipsum dolor sit amet,<br> temp consectetur adipisicing elit.</p>
                                                                    </div>
                                                                    <div class="co_reviews_item">
                                                                        <img src="images/testimonial/user2.jpg" width="100" height="100" alt="Amanda Smith">
                                                                            <figcaption>
                                                                                <a href="#">
                                                                                    <cite class="name">Travis Cullan</cite></a>
                                                                                <span class="designation light_text d-block mb-2">Team Leader</span>
                                                                            </figcaption>
                                                                            <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem ipsum dolor sit amet,<br> temp consectetur adipisicing elit.</p>
                                                                    </div>

                                                                    <div class="co_reviews_item">
                                                                        <img src="images/testimonial/user3.jpg" width="100" height="100" alt="Amanda Smith">
                                                                            <figcaption>
                                                                                <a href="#">
                                                                                    <cite class="name">Victoria Wills</cite></a>
                                                                                <span class="designation light_text d-block mb-2">Volunteer</span>
                                                                            </figcaption>
                                                                            <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem ipsum dolor sit amet,<br> temp consectetur adipisicing elit.</p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="section" id="contact">
                                                        <div class="container row mauto">
                                                            <div class="col-md-5">
                                                                <div class="section_title">
                                                                    <h3>WebHut Requirements</h3>
                                                                    <p>Check below if your host match these requirements.</p>
                                                                </div>
                                                                <ul class="co_reqment">
                                                                    <li>PHP 5.4 or Higher</li>
                                                                    <li>MySQLi</li>
                                                                    <li>GD Library</li>
                                                                    <li>mbstring</li>
                                                                    <li>cURL</li>
                                                                    <li>allow_url_fopen</li>
                                                                    <li>SSL certificate </li>
                                                                </ul>
                                                            </div>
                                                            <div class="col-md-1"></div>
                                                            <div class="col-md-6">
                                                                <div class="section_title">
                                                                    <h3>Drop us a line</h3>
                                                                    <p>We always love to hear from you, let us know what you need !</p>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col col-md-12">
                                                                        <form method="post" class="co_contact" action="#">
                                                                            <div class="row">
                                                                                <div class="form-group col-sm-6">
                                                                                    <input type="text" name="name" class="form-control" placeholder="Your Name">
                                                                                </div>
                                                                                <div class="form-group col-sm-6">
                                                                                    <input type="email" name="email" class="form-control" placeholder="Your Email">
                                                                                </div>
                                                                            </div>
                                                                            <textarea class="form-control" name="message" placeholder="Your Message"></textarea>
                                                                            <br>
                                                                                <div class="form-group">
                                                                                    <button type="submit" class="btn btn-xl btn-block btn-primary">Send Message</button>
                                                                                </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 
                                                    <!-- Include SweetAlert library if not already included -->
                                                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

                                                    <script>
                                                        // Function to show the full description and trigger SWAL
                                                        function showFullDescription(item) {
                                                            // Toggle the display of the short and full descriptions
                                                            var shortDescription = item.querySelector('.item-description-short');
                                                            var fullDescription = item.querySelector('.full-description');
                                                            // shortDescription.style.display = 'none';
                                                            // fullDescription.style.display = 'inline';

                                                            // Trigger SWAL popup
                                                            Swal.fire({
                                                                title: 'Full Description',
                                                                text: fullDescription.innerText,
                                                                icon: 'info',
                                                                confirmButtonText: 'Close'
                                                            });
                                                        }

                                                        // Attach click event to all "Read More" links
                                                        var readMoreLinks = document.querySelectorAll('.read-more-link');
                                                        readMoreLinks.forEach(function(link) {
                                                            link.addEventListener('click', function(event) {
                                                                event.preventDefault();
                                                                var item = event.target.parentElement;
                                                                showFullDescription(item);
                                                            });
                                                        });

                                                        document.querySelectorAll('.read-more').forEach(function(btn) {
                                                            btn.addEventListener('click', function(e) {
                                                                e.preventDefault();

                                                                let card = this.closest('.card-layout');
                                                                let desc = card.querySelector('.description');

                                                                desc.classList.toggle('expanded');

                                                                this.textContent = desc.classList.contains('expanded')
                                                                    ? 'Read Less'
                                                                    : 'Read More';
                                                            });
                                                        });
                                                    </script>
                                                    <?php require './footer.php' ?>