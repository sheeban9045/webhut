<?php
// Database connection
require './Database.php';
require './Config.php';


// Get page data

$sql = "SELECT * FROM crm_pages where slug='about-us'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row_data = $result->fetch_assoc();
}

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
<!--header-->
<?php require './header.php' ?>
<!-- <div class="py-1" id="comparison-table">
    <div class="container">
        <div class="text-center">
            <h1 class="mb-0">About Us</h1>
        </div>
    </div>
</div> -->
    <div class="announcement-div">
            <?php
            foreach ($annouce as $announcement) {
                ?>
                <div id="<?php echo "announcement-$announcement->id"; ?>" class="alert alert-danger">
                    <h5 style="color:red;margin-top:0px;padding-top:0px">Announcement</h5>
                    <i data-feather="volume-2" class="icon-18 mr10"></i> 
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-volume-2 icon-18 mr10"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
                    <a style="font-weight: bold;color:black;" href="store-admin/index.php/announcements" style="color:Red"><?php echo $announcement['title'];?></a>
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
<div class="section about-bg">
    <div class="container">
        <div class="text-left text-white">
            <h1 class=" wow fadeInUp"><?php echo $row_data['title'] ?></h1>
        </div>
    </div>
</div>


<section class="about-section">
    <div class="container">
        <div class="row mt-5">
            <div class="col-md-6">
                <div class="browser-window limit-height">
                    <div class="content">
                        <img src="images/about-us.jpg" alt="image">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="media">
                    <div class="media-body">
                        <h3 class="mt-0">Company Info</h3>
                        <div class="feature-border"></div>
                        <p align=" justify"> <?php echo $row_data['content']; ?></p> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require './footer.php' ?>
