<!DOCTYPE html>
<html lang="es">
   	<head>
      	<meta charset="utf-8">
      	<meta http-equiv="X-UA-Compatible" content="IE=edge">
      	<meta name="viewport" content="width=device-width, initial-scale=1">
      	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
      	<title></title>

      	<!-- Bootstrap -->
      	<link href="/Public/css/bootstrap.css" rel="stylesheet" type="text/css">
   		  <link href="/Public/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css">

        <!-- Font Awesome -->
        <link href="/Public/css/font-awesome.min.css" rel="stylesheet" type="text/css">

   		<!-- Style -->
   		<link href="/Public/css/default.css" rel="stylesheet" type="text/css">
    
      <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
      <script src="/Public/js/jquery-1.12.4.js"></script>
    
      <!-- DataTables -->
      <script src="/Public/js/jquery.dataTables.min.js"></script>
      <script src="/Public/js/dataTables.bootstrap.min.js"></script>
      
      <!-- MultiSelect -->
      <script src="/Public/js/multiselect.js"></script>

      <!-- CKEDITOR -->
      <script src="/Public/plugin/ckeditor/ckeditor.js"></script>

      <!-- PDFObject -->
      <script src="/Public/plugin/PDFObject/pdfobject.js"></script>
      
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
                <!-- SubHeader -->
                <?php
                  include ('dashboard/subheader.tpl.php');
                ?>
		            <!-- Content -->
                  <div  class="container" id="content">
                      <?php
                        include ($include);
                      ?>
                  </div>
               </div>
               <div class="row">
               		<!-- Footer -->
                  	<footer id="footerAd" class="clearfix">
                    	<div class="pull-left">
          							<b>Derechos Reservados </b>&copy; <?php  echo date('Y') ?>
          						</div>
          						<dsiv class="pull-right">
          							@tenea
          						</div> 
                  	</footer>
               </div>
            </div>
         </div>
      </div>

      
      <!-- Include all compiled plugins (below), or include individual files as needed -->
      <script src="/Public/js/bootstrap.js"></script>
      <!--  -->
      <script src="/Public/js/default.js"></script>
   </body>
</html>