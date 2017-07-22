<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link rel="stylesheet" href="/Public/css/bootstrap.css">
</head>
<body>
	
	<nav class="navbar navbar-default">
		<div class="container-fluid">
		    <!-- Brand and toggle get grouped for better mobile display -->
		    <div class="navbar-header">
		    	<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
			        <span class="sr-only">Toggle navigation</span>
			        <span class="icon-bar"></span>
			        <span class="icon-bar"></span>
			        <span class="icon-bar"></span>
		      	</button>
		      	<a class="navbar-brand" href="#">Brand</a>
		    </div>

		    <!-- Collect the nav links, forms, and other content for toggling -->
		    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
	     		<ul class="nav navbar-nav">
		        	<li class="active"><a href="#">Link <span class="sr-only">(current)</span></a></li>
		        	<li><a href="#">Link</a></li>
	      		</ul>
	      		<ul class="nav navbar-nav navbar-right">
	        		<li><a href="#">Link</a></li>
	        		<li class="dropdown">
	          			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
	          			<ul class="dropdown-menu">
				            <li><a href="#">Action</a></li>
				            <li><a href="#">Another action</a></li>
				            <li><a href="#">Something else here</a></li>
				            <li role="separator" class="divider"></li>
				            <li><a href="#">Separated link</a></li>
		          		</ul>
		        	</li>
	      		</ul>
	    	</div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
	</nav>
	
	<div class="container">
		<div class="row">
			<table class="table">
				<thead>
					<tr>
						<th>MODULO INSTITUCIÓN</th>
					</tr>
				</thead>
				<thead>
					<tr>
						<th>N°</th>
						<th>Funcion</th>
						<th>Descripción</th>
						<th>Estado</th>
						<th>Enlace</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>1</td>
						<td>Planilla de asistencia</td>
						<td>Funcion que genera pdf's de las listas de asistencia por docente</td>
						<td class=""><span class="label label-danger">Pendiente</span></td>
						<td><a href="#">Link</a></td>
					</tr>
					<tr>
						<td>2</td>
						<td>Planilla de superaciones</td>
						<td>Funcion que genera pdf's de los estudiantes que perdienron uno o varios periodos</td>
						<td class=""><span class="label label-danger">Pendiente</span></td>
						<td><a href="#">Link</a></td>
					</tr>
					<tr>
						<td>3</td>
						<td>Planilla de Evaluación</td>
						<td>Funcion que genera pdf's de la planilla de evaluación</td>
						<td class=""><span class="label label-success">Terminado</span></td>
						<td><a href="/institution/showFormEvaluationSheet/<?php echo DB;?>" target="_blank">Link</a></td>
					</tr>
					<tr>
						<td>4</td>
						<td>Boletin</td>
						<td>Funcion que genera pdf's de los boletines estudiantiles de los estudiantes</td>
						<td class=""><span class="label label-warning">En proceso</span></td>
						<td><a href="/institution/showFormgradeBook/<?php echo DB;?>" target="_blank">Link</a></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="row">
			<table class="table">
				<thead>
					<tr>
						<th>MODULO DOCENTE</th>
					</tr>
				</thead>
				<thead>
					<tr>
						<th>N°</th>
						<th>Funcion</th>
						<th>Descripción</th>
						<th>Estado</th>
						<th>Enlace</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>1</td>
						<td>Evaluar periodo</td>
						<td>Funcion que evalua periodos pendientes de los alumnos</td>
						<td class=""><span class="label label-success">Terminado</span></td>
						<td><a href="/teacher" target="_blank">Link</a></td>
					</tr>
					<tr>
						<td>2</td>
						<td>Plantilla Evaluación</td>
						<td>Funcion que genera pdf de de los modelos de plantilla</td>
						<td class=""><span class="label label-success">En Proceso</span></td>
						<td><a href="/teacher" target="_blank">Link</a></td>
					</tr>
					<tr>
						<td>3</td>
						<td>Plantilla de asistencia</td>
						<td>Funcion que genera pdf's de las listas de asistencia de los salones o grupos</td>
						<td class=""><span class="label label-success">Terminado</span></td>
						<td><a href="/teacher" target="_blank">Link</a></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<script type="text/javascript" src="/Public/js/jquery-1.12.4.js"></script>
	<script type="text/javascript" src="/Public/js/bootstrap.js"></script>
</body>
</html>