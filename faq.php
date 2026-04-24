<?php require './Config.php'; ?>
<?php require './Database.php'; ?>
<!--header-->
<?php require './header.php'; ?>

<?php

$sql = "SELECT * FROM crm_faqs where deleted=0";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Get All Categories
$sql = "SELECT * FROM crm_faq_categories where deleted=0";
$result_cat = $conn->query($sql);
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
$conn->close();

?>
<style>
    .list-group-item .active{
color: black;
    }
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
<!-- Content -->
<div class="announcement-div">
        <?php
        foreach ($annouce as $announcement) {
            ?>
            <div id="<?php echo "announcement-$announcement->id"; ?>" class="alert alert-danger">
                <h5 style="color:red;margin-top:0px;padding-top:0px">Announcement</h5><i data-feather="volume-2" class="icon-18 mr10"></i> 
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
<div class="section contact-bg">
    <div class="container">
        <div class="text-left text-white">
            <h1 class=" wow fadeInUp">Frequently Asked Question (FAQ)</h1>
        </div>
    </div>
</div>

<section class="faq-section">
    <div class="container">
        <div class="row mt-5">

            <div class="col-md-3">
                <ul class="list-group">
                    <?php

                    if ($result_cat->num_rows > 0) {
                        while ($row = $result_cat->fetch_assoc()) { ?>
                            <li class="list-group-item"><a id="cat_<?php echo $row['id']?>"  href="javascript:void()" onclick="getFaqs(<?php echo $row['id'] ?>)"><?php echo $row['title'] ?></a></li>
                    <?php

                        }
                    }
                    ?>
                </ul>
            </div>

            <div class="col-md-9">
                <div id="accordion">
                    <h4>Click on cetgory to view your questions.</h4>
                </div>
            </div>
        </div>
    </div>
</section>


<?php require './footer.php' ?>
<!-- Footer -->
<script type="text/javascript">
    var url = '<?php echo  $baseURL?>';
    function getFaqs(id){
        url = url + '/ajax_faq.php'
        
            $('.list-group-item a').removeClass('active');
        $('.list-group-item #cat_'+id).addClass('active');

        $.ajax({
        url: url,
        type: 'POST',
        dataType: 'html',
        data: {id: id},
        success: function (result) {
            $('#accordion').html('');
           $('#accordion').html(result);
        },
        error: function(error){
            console.log("Error: ",error);
        }
    });
        }
</script>