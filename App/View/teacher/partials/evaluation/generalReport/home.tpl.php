<div class="row" >
	<div class="col-md-12 content">
		<div class="panel panel-default">
		  	<div class="panel-heading">
		    	<h3 class="panel-title"><?php echo $tittle_panel; ?></h3>
		  	</div>
		  	<div class="panel-body">
		  		<div class="row">
		  			<div class="col-md-12 text-center">
		  				<a href="/teacher/createGeneralReportPeriod" class="btn btn-primary" data-request='spa'>Agregar Informe General de Periodo</a>
		  			</div>
		  		</div>
		  		<div class="row">
		  			<div class="col-md-12">
		  				<table class="table" id="tabla">
		  					<thead>
		  						<tr>
		  							<th></th>
		  							<th>Estudiante</th>
		  							<th>Grupo</th>
		  							<th>Periodo</th>
		  							<th>Observación</th>
		  							<th>Año lectivo</th>
		  						</tr>
		  					</thead>
		  					<tbody>
		  						<?php foreach ($reports as $key => $report):?>
									<tr class="text-center">
										<td>
											<a href="" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i></a>
											<a href="" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>
										</td>
										<td class="text-left">
											<?php echo
												utf8_encode(
												$report['p_a_alu']." ".
												$report['s_a_alu']." ".
												$report['p_n_alu']." ".
												$report['s_n_alu']
												);
											?>
										</td>
										<td>
											<?php echo $report['nombre_grupo']; ?>
										</td>
										<td>
											<?php echo $report['id_periodo'];?>
										</td>
										<td>
											<?php echo substr(utf8_encode($report['observaciones']), 0, 60)."...";?>
										</td>
										<td>
											<?php echo date('Y');?>
										</td>
									</tr>
		  						<?php endforeach;?>
		  					</tbody>
		  				</table>
		  			</div>
		  		</div>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		
		// DataTables
    	$('#tabla').dataTable({

	       	"lengthChange": false,
	       	"pageLength": 5,
	        language: {
	        	url: '/Public/json/Spanish.json'
	        }
	    });

	    // Peticiones para los enlaces
		$('[data-request="spa"]').each(function(){
			// 
			$(this).click(function(e){
				// Se Previene el redireccionamiento
				e.preventDefault();
				var that = $(this);
				$.ajax({
					type: 'GET',
					url: that.attr('href'),
					dataType: 'html',
					data: {
						options: {
							request: 'spa',
							back:  '/teacher/generalReportPeriod'
						}
					},
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
			})
		});
	});
</script>