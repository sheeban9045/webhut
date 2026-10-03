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

define('ADMIN_EMAIL', 'sheebanhasan7@gmail.com');
// define('ADMIN_EMAIL', 'friendsforlife28@gmail.com');

define('COMPANY_NAME', 'WebHut');
define('COMPANY_EMAIL', 'friendsforlife28@gmail.com');
define('COMPANY_PHONE', '+1 123456789');
define('COMPANY_WEBSITE', 'https://webhut.net');
define('ADMIN_NAME', 'Matt');


define('SMTP_HOST', 'smtp-relay.brevo.com');
define('SMTP_PORT', 2525);
define('SMTP_SECURE', 'tls');
define('SMTP_USER', 'bc0c37001@smtp-brevo.com');
define('SMTP_PASS', 'YOUR_BREVO_SMTP_KEY');
define('SMTP_FROM_EMAIL', 'support@webhut.net');
define('SMTP_FROM_NAME', 'WebHut');
?>


