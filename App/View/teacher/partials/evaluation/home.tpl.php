<div class="row" >
	<div class="col-md-12 content">
	<div class="panel panel-default">
	  	<!-- <div class="panel-heading">
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
	    				foreach($groupsAndAsign  as $key => $group){
		               	echo "<tr>
		                        <td>".($key+1)."</td>
		                        <td>".utf8_encode($group['nombre_grupo'])."</td>
		                        <td>".utf8_encode($group['asignatura'])."</td>
		                        <td>
		                           <div class='btn-group' role='group'>
		                              	<button type='button' class='btn btn-primary dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
		                                 Evaluación
		                                 <span class='caret'></span>
		                             	</button>
		                           		<ul class='dropdown-menu'>
		                           			<li>
		                           				<a href='#' data-request='spa'>Evaluar Periodo
				                        		</a>
		                           			</li>
				                        	<li>
					                        	<a href='#' data-request='spa'>Superaciones
					                        	</a>
				                        	</li>
				                        	<li>
				                        		<a href='/teacher/showFormEvaluatePeriod' data-asignature='".$group['id_asignatura']."' data-group='".$group['id_grupo']."' data-request='spa'>Evaluar Periodo Pendiente
				                        		</a>
				                        	</li>
				                        	<li>
				                        		<a >Refuerzo Academico
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
						id_asignature: that.attr('data-asignature'),
						id_group: that.attr('data-group'),
						options: {
							request: 'spa',
							back:  decodeURIComponent(that[0].baseURI)
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