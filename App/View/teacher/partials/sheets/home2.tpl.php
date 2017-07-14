<div class="row" >
	<div class="col-md-12 content">
		<div class="panel panel-default">
		  <!-- 	<div class="panel-heading">
		    	<h3 class="panel-title"></h3>
		  	</div> -->
		  	<div class="panel-body">
			  	<table class="table" id="tabla">
		        	<thead>
		            	<tr>
			               <th>N°</th>
			               <th>Grupo</th>
			               <th>Asignatura</th>
			               <th></th>
			            </tr>
		         	</thead>
		         	<tbody>
		            <?php

		            foreach($asignatures  as $key => $asignature){
		            echo "<tr>
		                    <td>".($key+1)."</td>
		                    <td>".utf8_encode($asignature['nombre_grupo'])."</td>
		                    <td>".utf8_encode($asignature['asignatura'])."</td>
		                    <td>
		                    	<div class='btn-group' role='group'>
		                            <button type='button' class='btn btn-primary dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
		                                 Planilla
		                                 <span class='caret'></span>
		                            </button>
		                            <ul class='dropdown-menu'>
		                            	<li>
											<a href='#' data-toggle='modal' data-target='#mev' data-asignature='".$asignature['id_asignatura']."' data-group='".$asignature['id_grupo']."'>Evaluación
		                    				</a>
		                            	</li>
		                            	<li>
		                            		<a href='/pdf/studentAttendance/".$asignature['id_asignatura']."/".$asignature['id_grupo']."' target='_blank'>Asistencia
		                    				</a>
		                            	</li>
		                            </ul>
		                        </div>
		                    </td>
		                   
		                </tr>";
		               }
		            ?>
		         	</tbody>
		      	</table>
			</div>
		</div>
	</div>
</div>

<!-- Modal view -->
<div class="modal fade" id="mev" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  	<div class="modal-dialog modal-sm" role="document">
    	<div class="modal-content">
	    	<form action="/pdf/evaluationSheet" method="GET" id="form_ev">
	    		<div class="modal-header">
		      	</div>
		      	<div class="modal-body" id="modalB">
		      		
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

<script type="text/javascript">
	// DataTables
    $('#tabla').dataTable({

	   	"lengthChange": false,
	   	"pageLength": 5,
	    language: {
	      	url: '/Public/json/Spanish.json'
	    }
	});

	$('[data-request="crud"]').click(function(e){
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


	// 
	$("#mev").on('show.bs.modal', function(e){
		
		var that = $(this);
		console.log();
		that.find('div.modal-body').append(
			$('<div>', {class: 'form-group'}).append(
				$('<label>Periodo</label>'),
				$('<select>', {class: 'form-control', name: 'period'}).append(
					<?php foreach($periods as $period): ?>
						$('<option value=<?php echo $period["periodos"]?> ><?php echo $period["periodos"]?></option>'),
					<?php endforeach; ?>
				)
			)
		);
	});

	// 
	$("#mev").on('hidden.bs.modal', function(e){
		var modal = $(this),
			mDialog = modal.find('div.modal-dialog'),
				mContent = mDialog.find('div.modal-body');

			if(mDialog.hasClass('modal-lg')){
				mDialog.removeClass('modal-lg');
				mDialog.addClass('modal-sm');
			}

		modal.find('div.modal-body').empty();
	});

	// 
	$("#form_ev").submit(function(e){
		e.preventDefault();

		var modal = $("#mev"),
			mDialog = modal.find('div.modal-dialog'),
			mContent = mDialog.find('div.modal-body');

		if(mDialog.hasClass('modal-sm')){
			mDialog.removeClass('modal-sm');
			mDialog.addClass('modal-lg');
		}

		mContent.empty();
		PDFObject.embed('/pdf/studentAttendance/128/24', "#modalB");
	});
</script>