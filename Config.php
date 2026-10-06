<?php
error_reporting(1);
require_once __DIR__ . '/env.php';
include("Database.php");
     
 $domain = $_SERVER['HTTP_HOST'];
 $domain = preg_replace('/index.php.*/', '', $domain);
 $domain = strtolower($domain);
 
 $data = mysqli_query($conn, "SELECT Count(id) as count from crm_orders where domain_name='".$domain."'");
 $data = mysqli_fetch_assoc($data);
    
 if(false && strcmp($domain, "www.webhut.net") !=0  && $data['count'] == 0 ){
     
     header("Location: https://www.webhut.net/pagenotfound.php", true, 301);
     exit();
 }
 if (!empty($_SERVER['HTTPS'])) {
     $baseURL = 'https://' . $domain;
 } else {
     $baseURL = 'http://' . $domain;
 }

// All values below come from .env (see env.php / .env.example) and must not be hardcoded here.
define('ADMIN_EMAIL', env('ADMIN_EMAIL', ''));

define('COMPANY_NAME', env('COMPANY_NAME', ''));
define('COMPANY_EMAIL', env('COMPANY_EMAIL', ''));
define('COMPANY_PHONE', env('COMPANY_PHONE', ''));
define('COMPANY_WEBSITE', env('COMPANY_WEBSITE', ''));
define('ADMIN_NAME', env('ADMIN_NAME', ''));


define('SMTP_HOST', env('SMTP_HOST', ''));
define('SMTP_PORT', (int) env('SMTP_PORT', 587));
define('SMTP_SECURE', env('SMTP_SECURE', 'tls'));
define('SMTP_USER', env('SMTP_USER', ''));
define('SMTP_PASS', env('SMTP_PASS', ''));
define('SMTP_FROM_EMAIL', env('SMTP_FROM_EMAIL', ''));
define('SMTP_FROM_NAME', env('SMTP_FROM_NAME', ''));

define('RECAPTCHA_SITE_KEY', env('RECAPTCHA_SITE_KEY', ''));
define('RECAPTCHA_SECRET_KEY', env('RECAPTCHA_SECRET_KEY', ''));
?>


