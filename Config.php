<?php
// echo "<pre>";
// print_r($_SERVER);
// die;

error_reporting(1);
include("Database.php");
     
 $domain = $_SERVER['HTTP_HOST'];
 $domain = preg_replace('/index.php.*/', '', $domain);
 $domain = strtolower($domain);
 
 $data = mysqli_query($conn, "SELECT Count(id) as count from crm_orders where domain_name='".$domain."'");
 $data = mysqli_fetch_assoc($data);
 //print_r($data);die;
    
 if(false && strcmp($domain, "www.webhut.net") !=0  && $data['count'] == 0 ){
     
     header("Location: https://www.webhut.net/pagenotfound.php", true, 301);
     exit();
 }
 if (!empty($_SERVER['HTTPS'])) {
     $baseURL = 'https://' . $domain;
 } else {
     $baseURL = 'http://' . $domain;
 }
?>


