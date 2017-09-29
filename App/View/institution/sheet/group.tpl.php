<div class="row">
	<div class="col-md-10 col-md-offset-1">
		<div class="panel panel-primary" style="margin-top: 10px;">
			<div class="panel-heading">
				<h3 class="panel-title">Planillas</h3>
			</div>
			<div class="panel-body">
				<form action="/sheet/group/<?php echo $db?>" method="POST" id="formCreateSheets" enctype="application/x-www-form-urlencoded" target="_blank">
					<div class="row">
						<div class="col-md-offset-2 col-md-3">
							<div class="form-group">
								<label for="">Sedes</label>
								<select class="form-control" id="sedeSheetGroup" name="sedeSheetGroup">
									<option value="0" class="text-center"> - Seleccione una sede - </option>
									<?php foreach($sedes as $key => $sede): ?>
										<option value="<?php echo $sede['id_sede']?>"><?php echo utf8_encode($sede['sede']);?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="grade_id">Grado</label>
								<select class="form-control" name="grade_id" id="grade_id">
									<option value="0" class="text-center"> - Seleccione un grado - </option>
									<?php foreach($grados as $key => $grado): ?>
										<option value="<?php echo $grado['id_grado']?>"><?php echo utf8_encode($grado['grado']);?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="working_day_id">Jornada</label>
								<select class="form-control" name="working_day_id" id="working_day_id">
									<option value="0" class="text-center"> - Seleccione una jornada - </option>
									<?php foreach($journeys as $key => $workingDay): ?>
										<option value="<?php echo $workingDay['id_jornada']?>"><?php echo utf8_encode($workingDay['jornada']);?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-5">
							<div class="form-group">
								<select name="" id="selectGroup" class="form-control" multiple="multiple" size="8">

								</select>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<button type="button" class="btn btn-default btn-block" id="selectGroup_rightAll"><i class="fa fa-angle-double-right" aria-hidden="true"></i></button>
								<button type="button" id="selectGroup_rightSelected" class="btn btn-default btn-block"><i class="fa fa-angle-right" aria-hidden="true"></i></button>
								<button type="button" id="selectGroup_leftSelected" class="btn btn-default btn-block"><i class="fa fa-angle-left" aria-hidden="true"></i></button>
								<button type="button" id="selectGroup_leftAll" class="btn btn-default btn-block"><i class="fa fa-angle-double-left" aria-hidden="true"></i></button>
							</div>
						</div>
						<div class="col-md-5">
							<div class="form-group">
								<select name="groups[]" id="selectGroup_to" class="form-control" multiple="multiple" size="8"></select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group text-center">
								<input type="submit" name="btn_file" id="btn_pdf" class="btn btn-danger" value="Descargar PDF" />
								<input type="submit" name="btn_file" id="btn_excel" class="btn btn-success" value="Descargar Excel" />
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script>
	(function(){

		$("#sedeSheetGroup, #grade_id, #working_day_id").change(function(){

			var sede = $("#sedeSheetGroup").val(),
				grade = $("#grade_id").val(),
				workingDay = $("#working_day_id").val();

			if(sede > 0 && grade > 0 && workingDay > 0){

				$.ajax({
					type: "GET",
	                dataType: "html",
	                url: '/ajax/getGroupBySedeAndGrade/'+sede+'/'+grade+'/'+workingDay+'/'+'<?php echo  $db;?>/',
	                success: function(data){
	                    $('#selectGroup').empty().append(data);
	                },
	                error(xhr, estado){
	                    console.log(xhr);
	                    console.log(estado);
	                }
				});
			}

		});

		$('#btn_excel, #btn_pdf').click(function(){

		});

	}());
</script>