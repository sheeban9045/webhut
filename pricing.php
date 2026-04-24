<?php require './Config.php'; ?>
<!--header-->
<?php require './header.php'; ?>

<?php 
    error_reporting(0);
    include("Database.php");

    //Select all plugins for user section

    $result = mysqli_query($conn, "SELECT * from crm_headings");
    
    $options = array();
    $headings = array();
    //Getting options 
    while($heading = mysqli_fetch_assoc($result)){
        
        $data = mysqli_query($conn, "SELECT * from crm_options where heading_id=".$heading['heading_id']);
        
        $temp = array();
        while($a = mysqli_fetch_assoc($data)){
            $temp[] = $a;
        }
        $options[$heading['heading_id']] = $temp;
        $headings[] = $heading;
    }

    //$result = (mysqli_query($conn, "SELECT * from crm_items Order By rate DESC"));    
    $result = (mysqli_query($conn, "SELECT * from crm_items WHERE `type` = 'monthly' AND `deleted` = 0 ORDER BY `order` ASC"));    
    // Fetch the data and store it in an array
    $items = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }

    $planFeatures = [];

    foreach ($items as $item) {
        $planFeatures[$item['id']] = explode(',', $item['feature_ids'] ?? '');
    }
    

    $resultOneTime = (mysqli_query($conn, "SELECT * from crm_items WHERE `type` = 'onetime' AND `deleted` = 0 ORDER BY `order` ASC"));    
    // Fetch the data and store it in an array
    $itemsOneTime = array();

    while ($row = mysqli_fetch_assoc($resultOneTime)) {
        $itemsOneTime[] = $row;
    }

    foreach ($itemsOneTime as $item) {
        $planFeatures[$item['id']] = explode(',', $item['feature_ids'] ?? '');
    }


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
    
    $announcements = (mysqli_query($conn, "SELECT * from crm_announcements WHERE start_date<='$now' AND end_date>='$now' AND deleted = 0"));
    $annouce = array();
    while ($row = mysqli_fetch_assoc($announcements)) {
        $annouce[] = $row;
    }
    // CODE FOR DISPALY THE ANNOUNCEMENT

    $typeResult = mysqli_query($conn, "SELECT * FROM crm_feature_types WHERE deleted = 0 ORDER BY sort_order ASC");

    $featureTypes = [];
    while ($row = mysqli_fetch_assoc($typeResult)) {
        $featureTypes[$row['id']] = $row;
    }

    $featureResult = mysqli_query($conn, "
        SELECT * FROM crm_features 
        WHERE deleted = 0 
        ORDER BY sort_order ASC
    ");

    $features = [];
    while ($row = mysqli_fetch_assoc($featureResult)) {
        $features[] = $row;
    }

    $groupedFeatures = [];

    foreach ($features as $f) {
        $groupedFeatures[$f['type']][] = $f;
    }

 ?>

<!--<div class="py-1">
    <div class="container">
        <div class="text-center">
            <h1 class="mb-0">Pricing</h1>
        </div>
    </div>
</div>-->
<style>
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

<div id="pricing" class="bg_light co_stats pricing-section">
    <div class="announcement-div">
            <?php
            foreach ($annouce as $announcement) {
                ?>
                <div id="<?php echo "announcement-$announcement->id"; ?>" class="alert alert-danger">
                <h5 style="color:red;margin-top:0px;padding-top:0px">Announcement</h5>
                <i data-feather="volume-2" class="icon-18 mr10"></i>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-volume-2 icon-18 mr10"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg> 
                    <a style = "color: black;font-weight: bold;" href="store-admin/index.php/announcements" style="color:Red"><?php echo $announcement['title'];?></a>
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
    <div class="container-fluid pb-5">
        <div class="text-center">
            <h1 class="display_7 pure_white">Choose your plan</h1>
            <div class="price-btn" style="display: block;"> 
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

                <!-- card 1 -->
                <?php foreach( $items as $item){ 
                    if($item['id'] != 6){
                ?>

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


            <div class="comparison-table-section ">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead style="background-color: #054a85;">
                            <tr>
                                <th colspan="1" class="first-price-bg"></th>
                                <?php foreach($items as $item){ 
                                    if($item['id'] != 6){
                                ?>
                                <th><?php echo $item['title'] ?>
                                    <br>$<?php echo number_format($item['rate'], 0, ".", ",") ?><br>
                                    <span class="span-month">A Month</span>
                                </th>
                                <?php }} ?>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <?php foreach($items as $item){ 
                                    if($item['id'] != 6){
                                ?>
                                    <td class="text-center"><a href="store-admin/index.php/items/pricing_add_to_cart/<?php echo $item['id']; ?>" class="btn align-middle btn-primary my-2 my-lg-0"><i class="fa fa-shopping-cart" aria-hidden="true"></i>&nbsp;&nbsp;Buy Now</a></td>
                                <?php }} ?>
                            </tr>
                            <tr>
                                <td></td>
                                <?php foreach($items as $item){ 
                                    if($item['id'] != 6){
                                ?>
                                    <td class="text-center">
                                        <a class="btn align-middle btn-outline-primary my-2 my-lg-0" href="<?php echo $item['demo_url']; ?>" target="_blank">▶ Demo</a>
                                    </td>
                                <?php }} ?>
                            </tr>

                            <?php foreach ($featureTypes as $typeId => $typeData): ?>

                                <?php if (!empty($groupedFeatures[$typeId])): ?>

                                    <!-- TYPE HEADING -->
                                    <tr style="background:#c7c7c7;">
                                        <td><strong style="color:#000"><?= $typeData['title'] ?></strong></td>
                                        <?php foreach($items as $item){ if($item['id'] != 6){ ?>
                                            <td></td>
                                        <?php }} ?>
                                    </tr>

                                    <!-- FEATURES -->
                                    <?php foreach ($groupedFeatures[$typeId] as $feature): ?>
                                        <tr>
                                            <td><?= $feature['title'] ?></td>

                                            <?php foreach($items as $item){ 
                                                if($item['id'] != 6){

                                                    $hasFeature = in_array($feature['id'], $planFeatures[$item['id']]);
                                            ?>
                                                <td class="text-center">
                                                    <?= $hasFeature ? '<i class="fa fa-check"></i>' : '<i class="fa fa-close"></i>' ?>
                                                </td>
                                            <?php }} ?>

                                        </tr>
                                    <?php endforeach; ?>

                                <?php endif; ?>

                            <?php endforeach; ?>
                           
                        </tbody>

                    </table>
                </div>
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

            <div class="comparison-table-section ">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead style="background-color: #054a85;">
                            <tr>
                                <th colspan="1" class="first-price-bg"></th>
                                <?php foreach($itemsOneTime as $item){  ?>
                                    <th><?php echo $item['title'] ?>
                                        <br>$<?php echo number_format($item['rate'], 0, ".", ",") ?><br>
                                        <span class="span-month">One Time Fee</span>
                                    </th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <?php foreach($itemsOneTime as $item){ 
                                ?>
                                    <td class="text-center"><a href="store-admin/index.php/items/pricing_add_to_cart/<?php echo $item['id']; ?>" class="btn align-middle btn-primary my-2 my-lg-0"><i class="fa fa-shopping-cart" aria-hidden="true"></i>&nbsp;&nbsp;Buy Now</a></td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td></td>
                                <?php foreach($itemsOneTime as $item){ 
                                ?>
                                    <td class="text-center">
                                        <a class="btn align-middle btn-outline-primary my-2 my-lg-0" href="<?php echo $item['demo_url']; ?>" target="_blank">▶ Demo</a>
                                    </td>
                                <?php } ?>
                            </tr>

                            <?php foreach ($featureTypes as $typeId => $typeData): ?>

                                <?php if (!empty($groupedFeatures[$typeId])): ?>

                                    <!-- TYPE HEADING -->
                                    <tr style="background:#c7c7c7;">
                                        <td><strong style="color:#000"><?= $typeData['title'] ?></strong></td>
                                        <?php foreach($itemsOneTime as $item){ if($item['id'] != 6){ ?>
                                            <td></td>
                                        <?php }} ?>
                                    </tr>

                                    <!-- FEATURES -->
                                    <?php foreach ($groupedFeatures[$typeId] as $feature): ?>
                                        <tr>
                                            <td><?= $feature['title'] ?></td>

                                            <?php foreach($itemsOneTime as $item){ 
                                                if($item['id'] != 6){

                                                    $hasFeature = in_array($feature['id'], $planFeatures[$item['id']]);
                                            ?>
                                                <td class="text-center">
                                                    <?= $hasFeature ? '<i class="fa fa-check"></i>' : '<i class="fa fa-close"></i>' ?>
                                                </td>
                                            <?php }} ?>

                                        </tr>
                                    <?php endforeach; ?>

                                <?php endif; ?>

                            <?php endforeach; ?>
                           
                        </tbody>

                    </table>
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
    function showFullDescription2(item) {
        // Toggle the display of the short and full descriptions
        var shortDescription = item.querySelector('.item-description-short-2');
        var fullDescription = item.querySelector('.item-description-full-2');
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
    var readMoreLinks2 = document.querySelectorAll('.read-more-link-2');
    readMoreLinks2.forEach(function(link) {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            var item = event.target.parentElement;
            showFullDescription2(item);
        });
    });
</script>
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
</style>





<?php require './footer.php' ?>


