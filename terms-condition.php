<?php require './Config.php';?>
<?php
// Database connection
require './Database.php';


// Get page data

$sql = "SELECT * FROM crm_pages where slug='term-service'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
}
$conn->close();
?>
<!--header-->
<?php require './header.php'; ?>
<!-- <div class="py-1" id="comparison-table">
    <div class="container">
        <div class="text-center">
            <h1 class="mb-0">About Us</h1>
        </div>
    </div>
</div> -->

<div class="section about-bg">
    <div class="container">
        <div class="text-left text-white">
            <h1 class=" wow fadeInUp"><?php echo $row['title']?></h1>
        </div>
    </div>
</div>



<section class="about-section">
    <div class="container">
          <div class="row mt-5">
           
            <div class="col-md-12">
                <p> <?php echo $row['content'];?></p> 
            </div>
        </div>
    </div>
</section>

<?php require './footer.php' ?>