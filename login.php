<?php
session_start();
error_reporting(0);
include("dbconnection.php");


if(isset($_POST['login']))
{
$ret=mysqli_query($con,"SELECT * FROM user WHERE email='".$_POST['email']."' and password='".$_POST['password']."'");
$num=mysqli_fetch_array($ret);
if($num>0)
{
$_SESSION['login']=$_POST['email'];
$_SESSION['id']=$num['id'];
$_SESSION['name']=$num['name'];


// header("Location: ../index.php?Login=success");



$val3 =date("Y/m/d");

date_default_timezone_set("Asia/Calcutta");

$time=date("h:i:sa");

$tim = $time;

$ip_address=$_SERVER['REMOTE_ADDR'];
$geopluginURL='http://www.geoplugin.net/php.gp?ip='.$ip_address;
$addrDetailsArr = unserialize(file_get_contents($geopluginURL)); 
$city = $addrDetailsArr['geoplugin_city']; 
$country = $addrDetailsArr['geoplugin_countryName'];
ob_start();
system('ipconfig /all');
$mycom=ob_get_contents();
ob_clean();
$findme = "Physical";
$pmac = strpos($mycom, $findme);
$mac=substr($mycom,($pmac+36),17);
$ret=mysqli_query($con,"insert into usercheck(logindate,logintime,user_id,username,email,ip,mac,city,country)values('".$val3."','".$tim."','".$_SESSION['id']."','".$_SESSION['name']."','".$_SESSION['login']."','$ip_address','$mac','$city','$country')");

$extra="dashboard.php";
echo "<script>window.location.href='".$extra."'</script>";
exit();
}
else
{
$_SESSION['action1']="Invalid username or password";
$extra="login.php";

echo "<script>window.location.href='".$extra."'</script>";
exit();
}
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Matt Community | Login</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no, minimal-ui" />
    <link rel="stylesheet" href="../../cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="shortcut icon" href="images/logo/icon.png" />
    <link rel="stylesheet" href="css/css-library/bootstrap.min.css">
    <link rel="stylesheet" href="css/css-library/icon-font.css">
    <link rel="stylesheet" href="css/css-library/welcome.css">
    <link rel="stylesheet" href="css/style7e0c.css?v=0.1">


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>


<body class="error-body no-top">

<div id="home">

        <nav id="navbar" class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="index-2.php">
                    <img src="images/logo/logo.png" height="" width="180" alt="Matt Community">
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#site-nav" aria-controls="site-nav" aria-expanded="false" aria-label="Toggle navigation"> <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="site-nav">
                    <ul class="navbar-nav text-sm-left ml-auto">
                        <li class="nav-item"> 
                            <a class="nav-link" href="index-2.php">Home</a>
                        </li>
                        <li class="nav-item"> 
                            <a class="nav-link" href="about.php">About Us</a>
                        </li>
                    <li class="nav-item dropdown"> 
                            <a class="nav-link" href="#" data-toggle="dropdown">Features 
                                <span class="pe-2x pe-7s-angle-down"></span>  
                            </a>
                            <div class="dropdown-menu"> 
                                <a class="dropdown-item" href="comparison-table.php">Features Plan Comparison</a>
                                <!-- <a class="dropdown-item" href="#">Earn More Money</a>
                                <a class="dropdown-item" href="#">Instant Messaging</a>
                                <a class="dropdown-item" href="#">All payment methods are supported!</a>
                                <a class="dropdown-item" href="#">Powerful Live Streaming</a> -->
                            </div>
                        </li>
                        <li class="nav-item"> 
                            <a class="nav-link" href="pricing.php">Pricing</a>
                        </li>
                        <li class="nav-item"> 
                            <a class="nav-link" href="contact.php">Contact</a>
                        </li>
                        <li class="nav-item"> 
                            <a class="nav-link" href="#">Plugins</a>
                        </li>
                        <li class="nav-item"> 
                            <a class="nav-link" href="#">FAQ</a>
                     <li class="nav-item dropdown"> 
                            <a class="nav-link" href="#" data-toggle="dropdown">More 
                                <span class="pe-2x pe-7s-angle-down"></span>  
                            </a>
                            <div class="dropdown-menu"> 
                                <a class="dropdown-item" href="#">Customization</a>
                                <a class="dropdown-item" href="#">Documentation</a>
                                <a class="dropdown-item" href="change-log.php">Change Log</a>
                            </div>
                        </li>
                        <li class="nav-item"> 
                            <a href="login.php" class="btn align-middle btn-outline-light my-2 my-lg-0">Demo</a>
                        </li>
                         <a href="http://localhost/codeigniter/rise/index.php/signin" class="btn align-middle btn-primary my-2 my-lg-0">Buy Now</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
<style>
body {font-family: Arial, Helvetica, sans-serif;}
form {border: 1px solid #eaf1f7;}

body {
background-color: #eaf1f7;
}

input[type=email], input[type=password] {
  width: 86%;
  padding: 10px 10px;
  margin: 8px 0;
  display: inline-block;
  border: 0.5px solid #ccc;
  box-sizing: border-box;
  background-color: white;
}

button {
  background-color: #2b84d1;
 
  color: #eaf1f7;
  padding: 14px 20px;
  margin: 8px 0;
  border: none;
  cursor: pointer;
  width: 86%;
}

button:hover {
  opacity: 0.8;
}

.cancelbtn {
  width: auto;
  padding: 10px 18px;
  background-color: #f44336;
}

.imgcontainer {
  text-align: center;
  margin: 24px 0 12px 0;
}


.container {
  padding: 16px;
}

span.psw {
  float: right;
  padding-top: 16px;
}

/* Change styles for span and cancel button on extra small screens */
@media screen and (max-width: 300px) {
  span.psw {
     display: block;
     float: none;
  }
  
}
</style>


<div class="container">
  <div class="row login-container column-seperation">  
        <div class="col-md-5 col-md-offset-1">
          <h2>Sign in to Matt Community</h2>
         
          <br>

		   
        </div>
        <div class="col-md-5 "> <br>
             <p style="color:#F00"><?php echo $_SESSION['action1'];?><?php echo $_SESSION['action1']="";?></p>
		 <form id="login-form" class="login-form" action="" method="post">
		 <div class="row">
		 <div class="form-group col-md-10">
           <!--  <label class="form-label">Email</label> -->
            <div class="controls">
				<div class="input-with-icon  right">                                       
					<i class="fa fa-user" style="font-size:20px;color:#2b84d1"></i>
					<input type="email" name="email" placeholder="Enter Your Email" id="txtusername" class="form-control" required="true">                                 
				</div>
            </div>
          </div>
          </div>
		  <div class="row">
          <div class="form-group col-md-10">
           <!--  <label class="form-label">Password</label> -->
            <span class="help"></span>
            <div class="controls">
				<div class="input-with-icon  right">                                       
					<i class="fa fa-lock" style="font-size:20px;color:#2b84d1"></i>
					<input type="password" name="password" placeholder="Enter Your Password" id="txtpassword" class="form-control" required="true">                                 
				</div>
            </div>
          </div>
          </div>
		  
          <div class="row">
            <div class="col-md-10">
              <button class="btn btn-primary btn-cons pull-right" name="login" type="submit">Login</button>
            </div>
          </div>
           <div class="row">
          <div class="control-group  col-md-10">
            <div class="checkbox checkbox check-success"> <a href="forgot-password.php">Forgot Password </a>&nbsp;&nbsp;
         </div>
          </div>
          </div>
           <br>
            <a href="registration.php">Sign up Now!</a><br>

		  </form>
        </div>
     
    
  </div>
</div>
<script src="assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script>
<script src="assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="assets/plugins/pace/pace.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="assets/js/login.js" type="text/javascript"></script>
</body>
</html>