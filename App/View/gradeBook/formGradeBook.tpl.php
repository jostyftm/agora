<!DOCTYPE html>
<html>
<head>
	<title>Boletin</title>
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
						<form action="/pdf/gradeBook" method="POST" enctype="application/x-www-form-urlencoded" target="_blank">
					    	<div class="row">
					    		<div class="col-md-4">
					    			<div class="form-group">
					    				<label for="">Sedes</label>
							    		<select name="sede" id="selectSede" class="form-control" required>
							    			<option value="">- Selecciona una sede -</option>
							    			<?php
							    				foreach ($sedes as $sede) {
							    					echo "<option value='".$sede['id_sede']."' >".utf8_encode($sede['sede'])."</option>";
							    				}
							    			?>
							    		</select>
							    	</div>
					    		</div>
					    		<div class="col-md-3">
					    			<div class="form-group">
					    				<label for="">Grupos</label>
					    				<select name="grupo" id="selectGrupo" class="form-control" required>
							    			<?php
							    				
							    			?>
							    		</select>
					    			</div>
					    		</div>
					    		<div class="col-md-2">
					    			<div class="form-group">
					    				<label for="">Periodo</label>
					    				<select name="period" class="form-control" required>
					    					<option value="">- periodo -</option>
					    					<?php foreach($periods as $key => $period):?>
												<option value="<?= $period['periodos']?>"><?= $period['periodos']?></option>
					    					<?php endforeach;?>
					    				</select>
					    			</div>
					    		</div>
					    		<div class="col-md-3">
					    			<div class="form-group">
					    				<label for="">Fecha</label>
					    				<input type="date" name="fecha" class="form-control">
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
					    	<hr>
					    	<div class="row">
					    		<div class="col-md-12 text-center">
					    			<div class="form-group text-center">
					    				<a class="#" role="button" data-toggle="collapse" href="#configGradeBook" aria-expanded="false" aria-controls="configGradeBook">
					    					<i class="fa fa-plus"></i> 
					    					Configuración del boletin
					    				</a>
					    			</div>
					    			<div id="configGradeBook" class="collapse">
					    				<div class="form-group">
										    <label class="radio-inline">
										    	<input type="checkbox" name="showTeacher"> Mostrar Docente
										    </label>
											<label class="radio-inline">
										     	<input type="checkbox" name="valorationScale"> Escala Valorativa
										    </label>
										    <label class="radio-inline">
										     	<input type="checkbox" name="showPerformance"> Mostra Desempeños
										    </label>
										    <label class="radio-inline">
										     	<input type="checkbox" name="areasDisabled"> Desactivar Areas
										    </label>
										    <label class="radio-inline">
										     	<input type="checkbox" name="doubleFace"> Doble Cara
										    </label>
										    <label class="radio-inline">
										     	<input type="checkbox" name="generalReportPeriod"> Informe general del periodo
										    </label>
						    			</div>
						    			<div class="form-group">
						    				<label class="radio-inline">
										     	<input type="checkbox" name="showFaces"> Imprimir caritas
										    </label>
										    <label class="radio-inline">
										     	<input type="checkbox" name="CombinedEvaluation" checked> Valoraciones Acomuladas
										    </label>
										    <label class="radio-inline">
										     	<input type="checkbox" name="NumberValoration" checked> Valoración Numérica
										    </label>
										    <label class="radio-inline">
										     	<input type="checkbox" name="performanceRating" checked> Clasificar Desempeños
										    </label>
										    <label class="radio-inline">
										     	<input type="checkbox" name="tableDetail" checked> Cuadro Detallado
										    </label>
						    			</div>
					    			</div>
					    		</div>
					    	</div>
					    	<div class="form-group text-center">
					    		<input type="hidden" id="db" name="db" value="<?php echo $db;?>">
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
   				var db = $("#db").val();
   				$.ajax({
	                type: "GET",
	                dataType: "html",
	                url: '/ajax/getGroups/'+this.value+"/"+db,
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
   				var db = $("#db").val();
   				$.ajax({
	                type: "GET",
	                dataType: "html",
	                url: '/ajax/getStudents/'+this.value+"/"+db,
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
				 
				left: '<input type="text" name="ql" class="form-control" placeholder="Buscar..." style="margin-bottom:5px;"/>',
				 
				right: '<input type="text" name="qr" class="form-control" placeholder="Buscar..." style="margin-bottom:5px;"/>',
				 
				}
			});
   		});
   	</script>
</body>
</html>