<div class="row" >
	<div class="col-md-12 content">
		<div class="panel panel-default">
		  	<div class="panel-heading">
		    	<h3 class="panel-title"><?php echo "GRUPO: ".$observations[0]['nombre_grupo']; ?></h3>
		  	</div>
		  	<div class="panel-body">
		  		<div class="row">
		  			<div class="col-md-12 text-center">
		  				<a href="/generalObservation/create" class="btn btn-primary" data-method="create" data-request="crud" data-rol="teacher" data-historyBack="<?php echo $history['current']?>">Agregar Observacion General</a>
		  			</div>
		  		</div>
		  		<div class="row">
		  			<div class="col-md-12">
		  				<table class="table" id="tabla">
		  					<thead>
		  						<tr>
		  							<th></th>
		  							<th>Estudiante</th>
		  							<th>Periodo</th>
		  							<th>Observación</th>
		  						</tr>
		  					</thead>
		  					<tbody>
		  						<?php foreach ($observations as $key => $observation):?>
									<tr class="text-center">
										<td class="text-left">
											<a href="/generalObservation/edit/<?php echo $observation['id_observacion']?>" class="btn btn-primary btn-xs" data-method="edit" data-request="crud" data-rol="teacher" data-historyBack="<?php echo $history['current']?>"><i class="fa fa-edit"></i></a>
											<a href="/generalObservation/show/<?php echo $observation['id_observacion']?>" class="btn btn-default btn-xs" data-method="show" data-request="crud" data-rol="teacher" data-historyBack="<?php echo $history['current']?>"><i class="fa fa-eye"></i></a>
											<a href="#" data-id="<?php echo $observation['id_observacion']?>" class="btn btn-danger btn-xs" data-method="delete" data-request="crud" data-rol="teacher" data-historyBack="<?php echo $history['current']?>"><i class="fa fa-trash"></i></a>
										</td>
										<td class="text-left">

											<?php 
											if($key < 9){
												echo utf8_encode('0'.($key+1).'  '.
													$observation['p_a_alu']." ".
													$observation['s_a_alu']." ".
													$observation['p_n_alu']." ".
													$observation['s_n_alu']
													);
											}
											else{
												echo utf8_encode(($key+1).'  '.
													$observation['p_a_alu']." ".
													$observation['s_a_alu']." ".
													$observation['p_n_alu']." ".
													$observation['s_n_alu']
													);
											}
											
											?>
										</td>
										<td>
											<?php echo $observation['id_periodo'];?>
										</td>
										<td class="text-left">
											<?php echo strip_tags(substr(utf8_encode($observation['observaciones']), 0, 50))."...";?>
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

<!-- Modal view -->
<div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  	<div class="modal-dialog" role="document">
    	<div class="modal-content">
      		<form action="/generalObservation/delete" method="POST" id="deleteObservationGeneral" enctype="application/x-www-form-urlencoded">
      			<div class="modal-header">
	        		<h4 class="modal-title" id="myModalLabel">Eliminar Observación</h4>
	      		</div>
	      		<div class="modal-body">
	        		<span>¿Este seguro que desea eliminar esta Observación ?</span>
	      		</div>
	      		<div class="modal-footer">
	      			<input type="hidden" name="request" value="crud">
	      			<input type="hidden" name="role" value="tacher">
	      			<input type="hidden" name="id_observation" id="id_observation" value="">
	        		<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
	        		<button type="submit" class="btn btn-primary">Continuar</button>
	      		</div>
      		</form>
    	</div>
  	</div>
</div>

<script>
	$(document).ready(function(){
		
		// Eliminar Observacion
		$("#deleteObservationGeneral").submit(function(e){
			e.preventDefault();

			var form = $("#deleteObservationGeneral");

			$.ajax({
				type: form.attr('method'),
				url: form.attr('action'),
				dataType: 'html',
				data: form.serialize(),
				beforeSend: function(){
					form.find("button[type=submit]").text('');
					form.find("button[type=submit]").append(
						$('<i>', {class: 'fa fa-spinner fa-spin fa-fw'}),
						$('<span>Eliminando...</span>')
					);
					form.find("button").prop('disabled', true);
				},
				success: function(data){
					
					form.find("button[type=submit]").empty().append(
						$('<i>', {class: 'fa fa-check fa-fw'}),
						$('<span>Eliminado</span>')
					);
					form.find("button").prop('disabled', false);

					$('#modalDelete').modal('toggle');
					
					if($(".modal-backdrop") && $('body').hasClass('modal-open') ){
						$(".modal-backdrop").remove();
						$('body').removeClass('modal-open');
					}

					$("#content").empty().append(data);
				},
				error(xhr, estado){
	                console.log(xhr);
	                console.log(estado);
	         	}
			});
		});

		// Data-CRUD
		$('[data-request="crud"]').each(function(){
			
			$(this).click(function(e){
				e.preventDefault();

				var that = $(this);
				if(that.attr('data-method') == 'delete')
				{	
					
					$("#id_observation").val(that.attr('data-id'));
					$('#modalDelete').modal({
						show: true,
						backdrop: 'static',
						keyboard: false
					});
				}
				else
				{
					$.ajax({
						type: 'GET',
						url: that.attr('href'),
						dataType: 'html',
						data: {
							request: that.attr('data-request'),
							rol: that.attr('data-rol'),
							options: {
								back:  that.attr('data-historyBack')
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
				}
			});
		});

		// DataTables
    	$('#tabla').dataTable({

	       	"lengthChange": false,
	       	"pageLength": 5,
	       	retrieve: true,
	        language: {
	        	url: '/Public/json/Spanish.json'
	        }
	    });
	});
</script>