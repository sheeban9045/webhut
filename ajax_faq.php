<?php
require './Database.php';

if (!isset($_REQUEST['id']) && empty($_REQUEST['id'])) {
    exit('Category id missing');
}

$id  = $_REQUEST['id'];
$data = [];

$sql = "SELECT f.*, fc.id FROM crm_faqs as f left join crm_faq_categories as fc on f.category_id = fc.id where f.deleted=0 and category_id = $id";
// exit($sql);
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

$conn->close();

?>
<!-- Content -->

<?php 
if(!empty($data)){

foreach ($data as $key => $row) { ?>
    <div class="card">
        <div class="card-header" id="heading<?php echo $key; ?>">
            <h5 class="mb-0">
                <button class="btn btn-link" data-toggle="collapse" data-target="#collapse<?php echo $key; ?>" aria-expanded="true" aria-controls="collapse<?php echo $key; ?>">
                    <?php echo $row['title']; ?>
                </button>
            </h5>
        </div>

        <div id="collapse<?php echo $key; ?>" class="collapse" aria-labelledby="heading<?php echo $key; ?>" data-parent="#accordion">
            <div class="card-body">
                <?php echo $row['description']; ?>
            </div>
        </div>
    </div>
<?php }} else{ ?>
    <div class="card">
        <div class="alert alert-danger">Currently there is no question in this category.</div>
    </div>
    <?php }?>