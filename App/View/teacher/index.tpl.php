<!DOCTYPE html>
<html lang="es">
   	<head>
      	<meta charset="utf-8">
      	<meta http-equiv="X-UA-Compatible" content="IE=edge">
      	<meta name="viewport" content="width=device-width, initial-scale=1">
      	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
      	<title></title>

      	<!-- Bootstrap -->
      	<link href="<?php echo pb;?>css/bootstrap.css" rel="stylesheet" type="text/css">
   		<link href="<?php echo pb;?>css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css">

        <!-- Font Awesome -->
        <link href="<?php echo pb;?>css/font-awesome.min.css" rel="stylesheet" type="text/css">

   		<!-- Style -->
   		<link href="<?php echo pb;?>css/default.css" rel="stylesheet" type="text/css">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  	</head>
	<body>
      <div class="container-fluid display-table">
         <div class="row display-table-row">
            <div class="col-md-2 col-sm-1 hidden-xs display-table-cell valign-top" id="sidebar">
              <!-- Sidebar -->
              <?php
              	include ('dashboard/sidebar.tpl.php');
              ?>
            </div>
            <div class="col-md-10 col-sm-11 display-table-cell valign-top">
               <div class="row">
                  	<!-- Header -->
					       <?php
		             	include ('dashboard/header.tpl.php');
		            ?>
		            <!-- Content -->
                    <?php
                    	include ($include);
                    ?>
               </div>
               <div class="row">
               		<!-- Footer -->
                  	<footer id="footerAd" class="clearfix">
                    	<div class="pull-left">
							<b>Derechos Reservados </b>&copy; <?php  echo date('Y') ?>
						</div>
						<div class="pull-right">
							@tenea
						</div> 
                  	</footer>
               </div>
            </div>
         </div>
      </div>

      <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
      <script src="<?php echo pb;?>js/jquery-1.12.4.js"></script>
      <!-- Include all compiled plugins (below), or include individual files as needed -->
      <script src="<?php echo pb;?>js/bootstrap.js"></script>
      <!--  -->
      <script src="<?php echo pb;?>js/default.js"></script>
   </body>
</html>