<?php
require './Config.php';
require './mailer.php';

if(isset($_GET['test-email'])){
    var_dump(smtp_send_mail('sheebanhasan7@gmail.com', 'SMTP Test', 'Test email by WebHut'));
}