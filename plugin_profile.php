<?php require './header.php'; ?>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<?php 
	error_reporting(0);
	include("Database.php");

	// $user = mysqli_query($conn,"SELECT is_admin FROM crm_users WHERE id=".$_SESSION['user_id']);
	// $admin = (mysqli_fetch_assoc($user)['is_admin']);

	//if($admin == 1){
	//getting main plugin
		if(!isset($_GET['plugin_id'])){

			$main = mysqli_query($conn,"SELECT * FROM crm_plugins WHERE  plugin_id=(Select MIN(plugin_id) from crm_plugins where status=1)");
		    	
		}else{
			$main = mysqli_query($conn,"SELECT * FROM crm_plugins WHERE plugin_id=".$_GET['plugin_id']);
				
		}

		$data=mysqli_fetch_assoc($main);


		//for getting all of the active plugins
		$result = mysqli_query($conn,"SELECT * FROM crm_plugins WHERE plugin_id !=".$_GET['plugin_id']." ORDER BY RAND () LIMIT 10");

	  	//For getting Screenshots
	  	$ss = mysqli_query($conn,"SELECT * FROM crm_screenshots WHERE plugin_id=".$data['plugin_Id']);
	  	

	  	//For getting Logs
		$logs = mysqli_query($conn,"SELECT * FROM crm_changelog WHERE plugin_id=".$data['plugin_Id']);

		$plans = mysqli_query($conn,"SELECT name FROM `crm_plan` LEFT JOIN `crm_planmapping` ON crm_planmapping.plan_id = crm_plan.plan_Id Where crm_planmapping.plan_id =".$data['plugin_Id']);

	//}

	//Select all plugins for user section
 ?>
<style type="text/css">
		button{
			cursor:pointer
		}

		.ratings i {
		  color: green;
		}

		.install span {
		  font-size: 12px;
		}

		.col-md-4 {
		  margin-top: 27px;
		}

/* CSS */
		.button-50 {
		  appearance: button;
		  background-color: #000;
		  background-image: none;
		  border: 1px solid #000;
		  border-radius: 4px;
		  box-shadow: #fff 4px 4px 0 0,#000 4px 4px 0 1px;
		  box-sizing: border-box;
		  color: #fff;
		  cursor: pointer;
		  display: inline-block;
		  font-family: ITCAvantGardeStd-Bk,Arial,sans-serif;
		  font-size: 14px;
		  font-weight: 400;
		  line-height: 20px;
		  margin: 0 5px 10px 0;
		  overflow: visible;
		  padding: 12px 40px;
		  text-align: center;
		  text-transform: none;
		  touch-action: manipulation;
		  user-select: none;
		  -webkit-user-select: none;
		  vertical-align: middle;
		  white-space: nowrap;
		}

		.button-50:focus {
		  text-decoration: none;
		}

		.button-50:hover {
		  text-decoration: none;
		}

		.button-50:active {
		  box-shadow: rgba(0, 0, 0, .125) 0 3px 5px inset;
		  outline: 0;
		}

		.button-50:not([disabled]):active {
		  box-shadow: #fff 2px 2px 0 0, #000 2px 2px 0 1px;
		  transform: translate(2px, 2px);
		}

		@media (min-width: 768px) {
		  .button-50 {
		    padding: 12px 50px;
		  }
		}
</style>


<body>
	<br>
	<!-- Admin View Is Started From Here -->
	
	<div class="row">
		<div class="col-sm-1"></div>
		<div class="col-sm-6">
			<div class="card">
				<div class="card-header jumotron">
					<div class="row">
						<div class="col-md-3">
							<img src="\store-admin\files\plugin_profiles\<?php echo $data['image'] ?>" width="100" height="100">
						</div>
						<div class="col-md-6">
							<h3><?php echo $data['title']; ?></h3>
						</div>
					</div>
					<br><br>
					<div class="d-inline" style="color:lightblue;">
						<button class="btn-sm button-50" href="" id="Description">Description</button>
						<button class="btn-sm" href="" id="logs">Check Logs</button>
						<button class="btn-sm" href="" id="screenshots">Screenshots</button>
						<button class="btn-sm" href="" id="plans">Plans</button>
					</div>
				</div>
				<div class="card-body output">

					<div id="desciptionContainer">
						<?php echo $data['body']; ?>
					</div>

					<div id="logsContainer" style="display:none">
						<table class="table">
							  <thead>
							    <tr>
							      <th scope="col">Version</th>
							      <th scope="col">Changes</th>
							    </tr>
							  </thead>
							  <tbody>
							  	<?php while($log = mysqli_fetch_assoc($logs)){ ?>
							    <tr>
							      <td><?php echo $log['version']; ?></td>
							      <td><?php echo $log['body']; ?></td>
							    </tr>
							   <?php } ?>
							  </tbody>
						</table>
					</div>

					<div id="screenshotsContainer" style="display:none">
						<div class="row">
							<?php while($screenshot = mysqli_fetch_assoc($ss)){ ?>

								<div class="col-md-6">
									<img src="\store-admin\files\screenshots\<?php echo $screenshot['image'] ?>" height="100%" width="100%">
								</div>

							<?php } ?>
						</div>
					</div>

					<div id="plansContainer" style="display:none">
						<div >
							<?php while($plan = mysqli_fetch_assoc($plans)){ ?>
								<div class="alert alert-success" role="alert">
									<?php echo $plan['name']; ?>
								</div>
							<?php } ?>
						</div>
					</div>
					
				</div>
			</div>
		</div>
		<!-- <div class="col-md-1"></div> -->

		<!-- All Other Active Plugins Code  -->
		<div class="col-sm-4">
			<?php while($row = mysqli_fetch_assoc($result)){ ?>

				<div class="card">
					<div class="card-header jumotron">
						<div class="row">
							<div class="col-md-3">
								<img src="\store-admin\files\plugin_profiles\<?php echo $row['image'] ?>" width="100%" height="100%">
							</div>
							<div class="col-md-6">
								<a href="/plugins.php?plugin_id=<?php echo $row['plugin_Id']; ?>" ><h6><?php echo $row['title']; ?></h6></a>
							</div>
						</div>
					</div>
					<div class="card-body">
						<?php echo substr($row['body'],0,60); ?> 
					</div>
				</div>
				<br>
			<?php } ?>
		</div>
	</div>
	



	<!-- User View Is Started From Here -->
	
	<script type="text/javascript">
		$(document).ready(function(){

			$("#Description").click(function(){
				//Border On Button
				
				$("#Description").addClass('button-50');
				$("#logs").removeClass('button-50');
				$("#plans").removeClass('button-50');
				$("#screenshots").removeClass('button-50');


				$("#desciptionContainer").css("display", "block");
				$("#screenshotsContainer").css("display", "none");
				$("#logsContainer").css("display", "none");
				$("#plansContainer").css("display", "none");
				
			})

			$("#logs").click(function(){

				$("#Description").removeClass('button-50');
				$("#logs").addClass('button-50');
				$("#plans").removeClass('button-50');
				$("#screenshots").removeClass('button-50');

				$("#desciptionContainer").css("display", "none");
				$("#screenshotsContainer").css("display", "none");
				$("#logsContainer").css("display", "block");
				$("#plansContainer").css("display", "none");
			})

			$("#screenshots").click(function(){

				$("#Description").removeClass('button-50');
				$("#logs").removeClass('button-50');
				$("#plans").removeClass('button-50');
				$("#screenshots").addClass('button-50');

				$("#desciptionContainer").css("display", "none");
				$("#screenshotsContainer").css("display", "block");
				$("#logsContainer").css("display", "none");
				$("#plansContainer").css("display", "none");
			})

			$("#plans").click(function(){

				$("#Description").removeClass('button-50');
				$("#logs").removeClass('button-50');
				$("#plans").addClass('button-50');
				$("#screenshots").removeClass('button-50');

				$("#desciptionContainer").css("display", "none");
				$("#screenshotsContainer").css("display", "none");
				$("#logsContainer").css("display", "none");
				$("#plansContainer").css("display", "block");
			})
		});
	</script>
</body>

<?php require './footer.php' ?>

