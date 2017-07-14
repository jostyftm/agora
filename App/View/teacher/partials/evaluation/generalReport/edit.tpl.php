<div class="row" >
	<div class="col-md-12 content">
		<div class="panel panel-default">
		  	<div class="panel-heading clearfix">
		    	<h3 class="panel-title pull-left"><?php echo $tittle_panel; ?></h3>
		    	<?php if(isset($back) && $back != NULL): ?>
	    			<a class="btn btn-primary pull-right" href="<?php echo $back; ?>" data-request="backHistory">Atras</a>
	    		<?php endif;?>
		  	</div>
		  	<div class="panel-body">
		  		<div class="row">
		  			<div class="col-md-12">
		  				<h2><?php echo 
		  					$report['p_n_alu']." ".
		  					$report['s_n_alu']." ".
		  					$report['p_a_alu']." ".
		  					$report['s_a_alu']
		  				?></h2>
		  				<span>GRUPO: <strong><?php echo $report['nombre_grupo']?></strong></span>
		  				<span>Periodo: <strong><?php echo $report['id_periodo']; ?> </strong></span>
		  			</div>
		  		</div>
		  		<div class="row">
		  			<div class="col-md-12">
		  				<form action="/generalReportPeriod/update" method="POST" id="generalReportPeriod" enctype="application/x-www-form-urlencoded">
							<div class="form-group">
								<label for="">Observación</label>
								<textarea class="form-control" id="id_textReportEdit" rows="5" name="observation"><?php echo utf8_encode($report['observaciones'])?></textarea>
							</div>
							<div class="form-group text-center">
								<input type="hidden" id="id_report" name="id_report" value="<?php echo $report['id_reporte']?>">
								<input type="submit" class="btn btn-primary" value="Actualizar Informe General de Periodo" />
							</div>
						</form>
					</div>
		  		</div>
		  	</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function(){

		CKEDITOR.replace('id_textReportEdit');

		// Peticiones para los enlaces
		$('[data-request="backHistory"]').click(function(e){
			// Se Previene el redireccionamiento
			e.preventDefault();
			var that = $(this);
			$.ajax({
				type: 'GET',
				url: that.attr('href'),
				dataType: 'html',
				beforeSend: function(xhr){
					$("#content").empty().append(
						$('<div>', {class: 'col-md-12 content'}).append(
							$('<div>', {class: 'panel panel-default panel-loading text-center'}).append(
								$('<div>', {class: 'panel-body'}).append(
									$('<div>', {class: 'fa fa-spinner fa-spin fa-3x fa-fw'}),
									$('<span>Cargando...</span>')
								)
							)
						)
					);
				},
				success: function(data){
					$("#content").empty().append(data);
				},
				error(xhr, estado){
	               	console.log(xhr);
	               	console.log(estado);
	           	}
			});
		});

		// Guardar dinamicamente
		$("#generalReportPeriod").submit(function(e){
			e.preventDefault();

			var form = $("#generalReportPeriod");

			var	observation = CKEDITOR.instances.id_textReportEdit.getData(),
				id_report = $("#id_report").val();

			$.ajax({
				type: form.attr('method'),
				url: form.attr('action'),
				dataType: 'json',
				data: {
					id_report,
					observation
				},
				success: function(data){
					$('[data-request="backHistory"]').click();
				},
				error(xhr, estado){
	                console.log(xhr);
	                console.log(estado);
	         	}
			});
		});
	});
</script>