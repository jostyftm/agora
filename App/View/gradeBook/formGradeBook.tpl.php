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
						<h3 class="panel-title">Boletines de notas</h3>
					</div>
					<div class="panel-body">
						<form action="/pdf/generateGradeBookByStudent" method="POST" enctype="application/x-www-form-urlencoded">
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
					    				<label for="">Grupos</label>
					    				<select name="grupo" id="selectGrupo" class="form-control">
							    			<?php
							    				
							    			?>
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
					    		<div class="col-md-5">
					    			<div class="form-group">
					    				<select name="" id="selectStudent" class="form-control" multiple="multiple" size="13">
							    			<?php
							    				
							    			?>
							    		</select>
					    			</div>
					    		</div>
					    		<div class="col-md-2">
					    			<button type="button" class="btn btn-default btn-block" id="selectStudent_rightAll"><i class="fa fa-angle-double-right" aria-hidden="true"></i></button>
					    			<button type="button" id="selectStudent_rightSelected" class="btn btn-default btn-block"><i class="fa fa-angle-right" aria-hidden="true"></i></button>
					    			<button type="button" id="selectStudent_leftSelected" class="btn btn-default btn-block"><i class="fa fa-angle-left" aria-hidden="true"></i></button>
					    			<button type="button" id="selectStudent_leftAll" class="btn btn-default btn-block"><i class="fa fa-angle-double-left" aria-hidden="true"></i></button>
					    		</div>
					    		<div class="col-md-5">
					    			<div class="form-group">
							    		<select name="students[]" id="selectStudent_to" class="form-control" multiple="multiple" size="13">
							    			<?php
							    				
							    			?>
							    		</select>
							    	</div>
					    		</div>
					    	</div>
					    	</div>
					    	<div class="row">
					    		<div class="col-md-6">
					    			<div class="form-group text-center">
					    				<label for="">Periodo</label>
					    			</div>
					    			<div class="form-group">
					    				<label class="radio-inline">
									     	<input type="checkbox" name="periodo[]" value="eval_1_per"> Periodo 1
									    </label>
									    <label class="radio-inline">
									    	<input type="checkbox" name="periodo[]" value="eval_2_per"> Periodo 2
									    </label>
									    <label class="radio-inline">
									    	<input type="checkbox" name="periodo[]" value="eval_3_per"> Periodo 3
									    </label>
									    <label class="radio-inline">
									    	<input type="checkbox" name="periodo[]" value="eval_4_per"> Periodo 4
									    </label>
					    			</div>
					    		</div>
					    		<div class="col-md-6">
					    			<div class="form-group text-center">
					    				<label for="">Opciones</label>
					    			</div>
					    			<div class="form-group">
					    				<label class="radio-inline">
									     	<input type="checkbox" name="areasDisabled"> Desactivar Áreas
									    </label>
									    <label class="radio-inline">
									     	<input type="checkbox" name="debleCara"> Doble Cara
									    </label>
									    <label class="radio-inline">
									     	<input type="checkbox" name="escalaVAlorativa"> Escala Valorativa
									    </label>
					    			</div>
					    		</div>
					    	</div>
					    	<div class="form-group text-center">
					    		<input type="hidden" name="db" value="<?php echo DB;?>">
					    		<input type="submit" name="btn_p_superacion" class="btn btn-primary" value="Crear boletin">
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
	                url: '/ajax/getGroups/'+this.value,
	                success: function(data){
	                    $('#selectGrupo').empty();
	                    $('#selectStudent').empty();

	                    $('#selectGrupo').append(
	                    	$('<option>- Sellecione un grupo -</option>')
	                    )
	                    $('#selectGrupo').append(data);
	                },
	                error(xhr, estado){
	                    console.log(xhr);
	                    console.log(estado);
	            	}
	            });
   			});

   			// 
   			$('#selectGrupo').change(function(){
   				$.ajax({
	                type: "GET",
	                dataType: "html",
	                url: '/ajax/getStudents/'+this.value,
	                success: function(data){
	                    $('#selectStudent').empty().append(data);
	                },
	                error(xhr, estado){
	                    console.log(xhr);
	                    console.log(estado);
	            	}
	            });
   			});

   			// 
   			$('#selectStudent').multiselect({
				search: {
				 
				left: '<input type="text" name="q" class="form-control" placeholder="Buscar..." style="margin-bottom:5px;"/>',
				 
				right: '<input type="text" name="q" class="form-control" placeholder="Buscar..." style="margin-bottom:5px;"/>',
				 
				}
			});
   		});
   	</script>
</body>
</html>