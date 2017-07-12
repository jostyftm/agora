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
		  			<div class="col-md-12">
		  				<h4>OBSERVACIÓN</h4>
		  				<p class="">
		  					<?php echo $observation['observaciones']?>
		  				</p>
		  			</div>
		  		</div>
		  	</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function(){


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
	});
</script>
