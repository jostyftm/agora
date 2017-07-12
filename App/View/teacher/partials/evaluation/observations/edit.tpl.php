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
		  					$observation['p_n_alu']." ".
		  					$observation['s_n_alu']." ".
		  					$observation['p_a_alu']." ".
		  					$observation['s_a_alu']
		  				?></h2>
		  				<span>GRUPO: <strong><?php echo $observation['nombre_grupo']?></strong></span>
		  				<span>Periodo: <strong><?php echo $observation['id_periodo']; ?> </strong></span>
		  			</div>
		  		</div>
		  		<div class="row">
		  			<div class="col-md-offset-2 col-md-7">
		  				<form action="/GeneralObservation/update" method="POST" id="updateObservationGeneral" enctype="application/x-www-form-urlencoded">
							<div class="form-group">
								<label for="">Observación</label>
								<textarea class="form-control" id="editor1" rows="5" name="observation"><?php echo $observation['observaciones']?></textarea>
							</div>
							<div class="form-group text-center">
								<input type="hidden" id="id_observation" name="id_observation" value="<?php echo $observation['id_observacion']?>">
								<input type="submit" class="btn btn-primary" value="Crear Observacion General" />
							</div>
						</form>
					</div>
		  		</div>
		  	</div>
		</div>
	</div>
</div>

<script src="/Public/plugin/ckeditor/ckeditor.js"></script>
<script>
	$(document).ready(function(){

		CKEDITOR.replace( 'editor1' );

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
		$("#updateObservationGeneral").submit(function(e){
			e.preventDefault();

			var form = $("#updateObservationGeneral");

			var	observation = CKEDITOR.instances.editor1.getData(),
				id_observation = $("#id_observation").val();

			$.ajax({
				type: form.attr('method'),
				url: form.attr('action'),
				dataType: 'json',
				data: {
					id_observation,
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