<?php

include("test.php");

?>


<html>

<head>
    <title>Matt Community</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no, minimal-ui" />
    <link rel="stylesheet" href="../../cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="shortcut icon" href="images/logo/icon.png" />
    <link rel="stylesheet" href="css/css-library/bootstrap.min.css">
    <link rel="stylesheet" href="css/css-library/icon-font.css">
    <link rel="stylesheet" href="css/css-library/welcome.css">
    <link rel="stylesheet" href="css/style7e0c.css?v=0.1">
</head>
<center>
<body>

<style>
div {
  background-color: White;
  width: 70%;
  border: 1px solid lightgrey;
  padding: 50px;
  margin:  50px;
</style>


	

<div class="grid-body no-border">
                          
                                            <table class="table table-hover no-more-tables">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Domain Name</th>
                                                        <th>Date </th>
                                                        <th>Status</th>
                                                       <!--  <th>Registration Date</th> -->
                                                       <!--  <th>Order Description</th> -->
                                                    </tr>
                                                </thead>


                                                <br></br>

                                               




                                                <!-- <tbody>
                                                //<?php $ret=mysqli_query($con,"select * from orders"); 
												//$cnt=1;
												//while($row=mysqli_fetch_array($ret))
												{
													//$_SESSION['ids']= $row['id'];
												?>
                                                   <tr>
                                                        <td><?php echo $cnt;?></td>
                                                        <td><?php echo $row['name'];?></td>
                                                        <td><?php echo $row['email'];?></td>
                                                         <td><?php echo $row['mobile'];?></td>
                                                          
                                                           <td><?php echo $row['gender'];?></td>
                                                         <td><?php echo $row['morder'];?></td>

                                                          <td>
                                                          <form name="abc" action="" method="post">
                                                           <a href="edit-order.php?id=<?php echo $row['id'];?>" class="btn btn-primary btn-xs btn-mini">View n Edit</a>
                                                           <button type="button" class="btn btn-danger btn-xs btn-mini">Delete </button>
                                                           </form>
                                                          </td>
                                                    </tr>
                                                    //<?php $cnt=$cnt+1; } ?>
                                                </tbody> -->
                                            </table>
                         
                                    </div>
                             

	</body>
	</html>