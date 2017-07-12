<div class="row" >
	<div class="col-md-12 content">
		<div class="panel panel-default">
		  	<div class="panel-heading clearfix">
		    	<h3 class="panel-title pull-left"><?php echo $tittle_panel; ?></h3>
		    	<?php if(isset($back) && $back != NULL): ?>
	    			<a class="btn btn-primary pull-right" href="<?php echo $back; ?>" data-request="spa">Atras</a>
	    		<?php endif;?>
		  	</div>
		  	<div class="panel-body">
		  		<form action="/teacher/saveGeneralObservation" method="POST" id="saveGeneralReport" enctype="application/x-www-form-urlencoded">
			  		<div class="row">
			  			<div class="col-md-offset-2 col-md-4">
			  				<div class="form-group">
			  					<label for="">Grupos</label>
			  					<select class="form-control" id="selectGroup" >
			  						<option value="" class="text-center"> - Seleccione un grupo - </option>
			  						<?php foreach($myGroups as $key => $group): ?>
										<option value="<?php echo $group['id_grupo'];?>"><?php echo $group['nombre_grupo']; ?></option>
			  						<?php endforeach; ?>
			  					</select>
			  				</div>
			  			</div>
			  			<div class="col-md-3">
			  				<div class="form-group">
			  					<label for="Periodo">Periodo</label>
			  					<select name="periodo" id="" class="form-control">
			  						<option value="">- Seleccione un periodo -</option>
			  						<?php foreach($periods as $key => $period): ?>
										<option value="<?php echo $period['periodos'];?>"><?php echo $period['periodos']; ?></option>
			  						<?php endforeach; ?>
			  					</select>
			  				</div>
			  			</div>
			  		</div>
			  		<div class="row">
						<div class="col-md-5">
							<div class="form-group">
						    	<select name="" id="selectClassRoom" class="form-control" multiple="multiple" size="7"></select>
						    </div>
						</div>
						<div class="col-md-2">
							<button type="button" class="btn btn-default btn-block" id="selectClassRoom_rightAll"><i class="fa fa-angle-double-right" aria-hidden="true"></i></button>
							<button type="button" id="selectClassRoom_rightSelected" class="btn btn-default btn-block"><i class="fa fa-angle-right" aria-hidden="true"></i></button>
							<button type="button" id="selectClassRoom_leftSelected" class="btn btn-default btn-block"><i class="fa fa-angle-left" aria-hidden="true"></i></button>
							<button type="button" id="selectClassRoom_leftAll" class="btn btn-default btn-block"><i class="fa fa-angle-double-left" aria-hidden="true"></i></button>
						</div>
						<div class="col-md-5">
						    <div class="form-group">
								<select name="students[]" id="selectClassRoom_to" class="form-control" multiple="multiple" size="7"></select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-offset-2 col-md-7">
							<div class="form-group">
								<label for="">Descripción</label>
								<textarea class="form-control" rows="5" name="description"></textarea>
							</div>
							<div class="form-group text-center">
								<button class="btn btn-primary">Crear Observacion General</button>
							</div>
						</div>
					</div>
				</form>
		  	</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function(){
   		
   		// Ajax para el select
   		$('#selectGroup').change(function(){
   			$.ajax({
	            type: "GET",
	            dataType: "html",
	            url: '/group/getClassRoomOptions/'+this.value,
	    	    success: function(data){
	                $('#selectClassRoom').empty().append(data);
	            },
	            error(xhr, estado){
	                console.log(xhr);
	                console.log(estado);
	         	}
	        });
   		});

   		// MultiSelect
   		$('#selectClassRoom').multiselect({
			search: {
				 
				left: '<input type="text" name="q" class="form-control" placeholder="Buscar..." style="margin-bottom:5px;"/>',
				 
				right: '<input type="text" name="q" class="form-control" placeholder="Buscar..." style="margin-bottom:5px;"/>',
				 
			}
		});

		// Guardar dinamicamente
		$("#saveGeneralReport").submit(function(e){
			var form = $("#saveGeneralReport");
			
			$.ajax({
				type: form.attr('method'),
				url: form.attr('action'),
				// dataType: 'json',
				data: form.serialize(),
				success: function(data){
					console.log(data);
				},
				error(xhr, estado){
	                console.log(xhr);
	                console.log(estado);
	         	}
			});

			return false;
		});

		// 
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