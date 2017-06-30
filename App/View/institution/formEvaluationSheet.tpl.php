<!DOCTYPE html>
<html>
<head>
	<title>Planilla</title>
	<link href="<?php echo pb;?>css/bootstrap.css" rel="stylesheet" type="text/css">
	<link href="<?php echo pb;?>css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<link href="<?php echo pb;?>css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
	
	<div class="container">
		<div class="row">
			<div class="col-md-10 col-md-offset-1">
				<div class="panel panel-primary" style="margin-top: 10px;">
					<div class="panel-heading">
						<h3 class="panel-title">Planilla de Evaluación</h3>
					</div>
					<div class="panel-body">
						<form action="/pdf/testPdf" method="POST" enctype="application/x-www-form-urlencoded">
					    	<div class="row">
					    		<div class="col-md-4">
					    			<div class="form-group">
					    				<label for="">Sedes</label>
							    		<select name="sede" id="selectSede" class="form-control">
							    			<option value="">- Selecciona una sede -</option>
							    			<?php
							    				foreach ($sedes as $sede) {
							    					echo "<option value='".$sede['id_sede']."' >".utf8_encode($sede['sede'])."</option>";
							    				}
							    			?>
							    		</select>
							    	</div>
					    		</div>
					    		<div class="col-md-4">
					    			<div class="form-group">
					    				<label for="">Docentes</label>
					    				<select name="docente" id="selectDocente" class="form-control">
							    			
							    		</select>
					    			</div>
					    		</div>
					    		<div class="col-md-4">
					    			<div class="form-group">
					    				<label for="">Fecha</label>
					    				<input type="date" class="form-control" name="fecha">
					    			</div>
					    		</div>
					    	</div>
					    	<div class="row">
					    		<div class="col-md-6">
					    			<div class="form-group">
					    				<select name="" id="selectAsignatures" class="form-control" multiple="multiple" size="13">
							    			<?php
							    				
							    			?>
							    		</select>
					    			</div>
					    		</div>
					    		<div class="col-md-1">
					    			<button type="button" class="btn btn-default btn-block" id="selectAsignatures_rightAll"><i class="fa fa-angle-double-right" aria-hidden="true"></i></button>
					    			<button type="button" id="selectAsignatures_rightSelected" class="btn btn-default btn-block"><i class="fa fa-angle-right" aria-hidden="true"></i></button>
					    			<button type="button" id="selectAsignatures_leftSelected" class="btn btn-default btn-block"><i class="fa fa-angle-left" aria-hidden="true"></i></button>
					    			<button type="button" id="selectAsignatures_leftAll" class="btn btn-default btn-block"><i class="fa fa-angle-double-left" aria-hidden="true"></i></button>
					    		</div>
					    		<div class="col-md-5">
					    			<div class="form-group">
							    		<select name="grupos[]" id="selectAsignatures_to" class="form-control" multiple="multiple" size="13">
							    			<?php
							    				
							    			?>
							    		</select>
							    	</div>
					    		</div>
					    	</div>
					    	<div class="row">
					    		<div class="col-md-4">
					    			<div class="form-group">
					    				<label for="">Periodo</label>
					    				<select name="periodo" id="" class="form-control">
								    		<option value="eval_1_per">Periodo 1</option>
								    		<option value="eval_2_per">Periodo 2</option>
								    		<option value="eval_3_per">Periodo 3</option>
								    		<option value="eval_4_per">Periodo 4</option>
							    		</select>
					    			</div>
					    		</div>
					    		<div class="col-md-4">
							    	<div class="form-group">
							    		<label for="">Diseño</label>
							    		<select name="opcion[orientacion]" id="" class="form-control">
							    			<option value="l" selected="selected">Horizontal</option>
							    			<option value="p">Vertical</option>
							    		</select>
							    	</div>
					    		</div>
								<div class="col-md-4">
							    	<div class="form-group">
							    		<label for="">Tamaño de página</label>
							    		<select name="opcion[papel]" id="" class="form-control">
							    			<option value="A3">A3</option>
							    			<option value="A4" selected="selected">A4</option>
							    			<option value="A5">A5</option>
							    			<option value="Letter">Letter</option>
							    			<option value="Legal">Legal</option>
							    		</select>
							    	</div>
					    		</div>
					    	</div>
					    	<div class="form-group text-center">
					    		<input type="hidden" name="db" value="<?php echo $db;?>">
					    		<input type="submit" name="btn_p_pe" class="btn btn-primary" value="Crear planilla de Evaluación">
					    	</div>
					    </form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script src="<?php echo pb;?>js/jquery-1.12.4.js"></script>
   	<script src="<?php echo pb;?>js/bootstrap.min.js"></script>
   	<script src="<?php echo pb;?>js/multiselect.js"></script>
   	<script>
   		$(document).ready(function(){

   			// 
   			$('#selectSede').change(function(){
   				$.ajax({
	                type: "GET",
	                dataType: "html",
	                url: '/ajax/getDocentes/<?php echo DB;?>/'+this.value,
	                success: function(data){
	                    $('#selectDocente').empty();
	                    $('#selectAsignatures').empty();

	                    $('#selectDocente').append(
	                    	$('<option>- Sellecione un grupo -</option>')
	                    )
	                    $('#selectDocente').append(data);
	                    console.log(data);
	                },
	                error(xhr, estado){
	                    console.log(xhr);
	                    console.log(estado);
	            	}
	            });
   			});

   			// 
   			$('#selectDocente').change(function(){
   				$.ajax({
	                type: "GET",
	                dataType: "html",
	                url: '/ajax/getAsignaturesByTeacher/<?php echo DB;?>/'+this.value,
	                success: function(data){
	                    $('#selectAsignatures').empty().append(data);
	                },
	                error(xhr, estado){
	                    console.log(xhr);
	                    console.log(estado);
	            	}
	            });
   			});

   			// 
   			$('#selectAsignatures').multiselect({
				search: {
				 
				left: '<input type="text" name="q" class="form-control" placeholder="Buscar..." style="margin-bottom:5px;"/>',
				 
				right: '<input type="text" name="q" class="form-control" placeholder="Buscar..." style="margin-bottom:5px;"/>',
				 
				}
			});
   		});
   	</script>
</body>
</html>