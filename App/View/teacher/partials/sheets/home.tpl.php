<div class="row" >
	<div class="col-md-12 content">
		<div class="panel panel-default">
		  <!-- 	<div class="panel-heading">
		    	<h3 class="panel-title"></h3>
		  	</div> -->
		  	<div class="panel-body">
		  		<form action="" method="POST" id="formCreateSheets" enctype="application/x-www-form-urlencoded" target="_blank">
			  		<div class="row">
			  			<div class="col-md-3">
			  				<div class="form-group">
			  					<label for="">Tipo de planilla</label>
			  					<select class="form-control" id="sheet" name="type_sheet">
			  						<option value="0" class="text-center"> - Seleccione un tipo de planilla - </option>
			  						<option value="Attendance">Asistencia</option>
			  						<option value="Evaluation">Evaluacion</option>
			  					</select>
			  				</div>
			  			</div>
			  			<div class="col-md-3">
			  				<div class="form-group">
			  					<label for="Periodo">Periodo</label>
			  					<select name="period" id="period" class="form-control" disabled="">
			  						<option value="">- Seleccione un periodo -</option>
			  						<?php foreach($periods as $key => $period): ?>
										<option value="<?php echo $period['periodos'];?>"><?php echo $period['periodos']; ?></option>
			  						<?php endforeach; ?>
			  					</select>
			  				</div>
			  			</div>
			  			<div class="col-md-3">
							    	<div class="form-group">
							    		<label for="">Diseño</label>
							    		<select name="orientation" id="orientation" class="form-control" disabled>
							    			<option value="l" selected="selected">Horizontal</option>
							    			<option value="p">Vertical</option>
							    		</select>
							    	</div>
					    		</div>
								<div class="col-md-3">
							    	<div class="form-group">
							    		<label for="">Tamaño de página</label>
							    		<select name="papper" id="" class="form-control">
							    			<option value="A3">A3</option>
							    			<option value="A4" selected="selected">A4</option>
							    			<option value="A5">A5</option>
							    			<option value="Letter">Letter</option>
							    			<option value="Legal">Legal</option>
							    		</select>
							    	</div>
					    		</div>
			  		</div>
			  		<div class="row">
						<div class="col-md-5">
							<div class="form-group">
						    	<select name="" id="gaa" class="form-control" multiple="multiple" size="7">
						    		<?php foreach($asignatures as $key => $asignature): ?>
										<option value="<?php echo $asignature['id_asignatura'].'-'.$asignature['id_grupo'];?>"><?php 
											echo utf8_encode(
												$asignature['nombre_grupo'].' - '.
												$asignature['asignatura']) 
											?>
										</option>
			  						<?php endforeach; ?>
						    	</select>
						    </div>
						</div>
						<div class="col-md-2">
							<button type="button" class="btn btn-default btn-block" id="gaa_rightAll"><i class="fa fa-angle-double-right" aria-hidden="true"></i></button>
							<button type="button" id="gaa_rightSelected" class="btn btn-default btn-block"><i class="fa fa-angle-right" aria-hidden="true"></i></button>
							<button type="button" id="gaa_leftSelected" class="btn btn-default btn-block"><i class="fa fa-angle-left" aria-hidden="true"></i></button>
							<button type="button" id="gaa_leftAll" class="btn btn-default btn-block"><i class="fa fa-angle-double-left" aria-hidden="true"></i></button>
						</div>
						<div class="col-md-5">
						    <div class="form-group">
								<select name="groups[]" id="gaa_to" class="form-control" multiple="multiple" size="7"></select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-offset-2 col-md-7">
							<div class="form-group text-center">
								<input type="hidden" name="role" value="teacher">
								<input type="hidden" name="saveGO" value="saveGO">
								<input type="submit" class="btn btn-primary" id="btnCreate" value="Crear Planilla" disabled />
							</div>
						</div>
					</div>
				</form>
		  	</div>
		</div>
	</div>
</div>

<script>

	// 
	$("#sheet").change(function(){
		var form = $("#formCreateSheets"),
			url = '/sheet/';

		if(this.value == 0){

			$("#btnCreate").prop('disabled', true);
			$("#period").prop('disabled', true);

		}else if(this.value == 'Attendance'){

			form.attr('action', url+this.value);

			$("#btnCreate").prop('disabled', false);
			$("#orientation").prop('disabled', true);
			$("#period").prop('disabled', true);

		}else if(this.value == 'Evaluation'){

			form.attr('action', url+this.value);

			$("#btnCreate").prop('disabled', false);
			$("#orientation").prop('disabled', false);
			$("#period").prop('disabled', false);
		}
	});


	// Multi Select
	$('#gaa').multiselect({
		search: {
			left: '<input type="text" name="q" class="form-control" placeholder="Buscar..." style="margin-bottom:5px;"/>',
				 
			right: '<input type="text" name="q" class="form-control" placeholder="Buscar..." style="margin-bottom:5px;"/>',
			 
		}
	});
</script>