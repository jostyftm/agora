<div class="row">
	<div class="col-md-10 col-md-offset-1">
		<div class="panel panel-primary" style="margin-top: 10px;">
			<div class="panel-heading">
				<h3 class="panel-title">Planillas</h3>
			</div>
			<div class="panel-body">
				<form action="/pdf/testPdf" method="POST" id="formCreateSheets" enctype="application/x-www-form-urlencoded" target="_blank">
					<div class="row">
						<div class="col-md-offset-2 col-md-3">
							<div class="form-group">
								<label for="">Sedes</label>
								<select class="form-control" id="selectSede" name="sede">
									<option value="0" class="text-center"> - Seleccione una sede - </option>
									<?php foreach($sedes as $key => $sede): ?>
										<option value="<?php echo $sede['id_sede']?>"><?php echo utf8_encode($sede['sede']);?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="">Tipo de planilla</label>
								<select class="form-control" name="type_sheet" id="typeSheet" disabled>
									<option value="0" class="text-center"> - Seleccione un tipo de planilla - </option>
									<option value="Attendance">Asistencia</option>
									<option value="Evaluation">Evaluacion</option>
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="">Periodo</label>
								<select class="form-control" name="period" id="period" disabled>
									<option value="0" class="text-center"> - Seleccione un periodo - </option>
									<?php foreach($periods as $period):?>
										<option value="<?= $period['periodos']?>">Periodo <?= $period['periodos']?></option>
									<?php endforeach;?>
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-5">
							<div class="form-group">
								<select name="" id="selectDocente" class="form-control" multiple="multiple" size="7">

								</select>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<button type="button" class="btn btn-default btn-block" id="selectDocente_rightAll"><i class="fa fa-angle-double-right" aria-hidden="true"></i></button>
								<button type="button" id="selectDocente_rightSelected" class="btn btn-default btn-block"><i class="fa fa-angle-right" aria-hidden="true"></i></button>
								<button type="button" id="selectDocente_leftSelected" class="btn btn-default btn-block"><i class="fa fa-angle-left" aria-hidden="true"></i></button>
								<button type="button" id="selectDocente_leftAll" class="btn btn-default btn-block"><i class="fa fa-angle-double-left" aria-hidden="true"></i></button>
							</div>
						</div>
						<div class="col-md-5">
							<div class="form-group">
								<select name="teacher[]" id="selectDocente_to" class="form-control" multiple="multiple" size="7"></select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-offset-3 col-md-3">
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
									<option value="Letter">Carta</option>
									<option value="Legal">Oficio</option>
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group text-center">
								<input type="hidden" name="role" value="teacher">
								<input type="hidden" name="saveGO" value="saveGO">
								<input type="submit" class="btn btn-primary" id="btnCreate" name="btn_p_pe" value="Imprimir" disabled />
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>