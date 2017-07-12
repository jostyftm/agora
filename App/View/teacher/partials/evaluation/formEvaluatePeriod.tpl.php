<div class="row" >
	<div class="col-md-12 content">
		<div class="panel panel-default">
	  		<div class="panel-heading clearfix">
	    		<h3 class="panel-title pull-left"><?php echo $tittle_panel; ?></h3>
	    		<?php if(isset($back) && $back != NULL): ?>
	    			<a class="btn btn-primary pull-right" href="<?php echo $back; ?>">Atras</a>
	    		<?php endif;?>
	  		</div>
	  		<div class="panel-body">
		  		<div class="row">
	            	<div class="col-md-8">
	               	<h4><?php echo utf8_encode($asignature['asignatura'])." | ".utf8_encode($group['nombre_grupo']); ?></h4>
	            	</div>
	            	<div class="col-md-4">
		               	<form action="">
		                	<div class="form-group">
		                    	<label for="">periodo</label>
		                    	<select name="" id="periodos" class="form-control">
			                        <option value="0">- Selecciona un periodo -</option>
			                        <option value="eval_1_per">Primer periodo</option>
			                        <option value="eval_2_per">Segundo periodo</option>
			                        <option value="eval_3_per">Tercer periodo</option>
			                        <option value="eval_4_per">Cuarto periodo</option>
		                     	</select>
		                     	<input type="hidden" name="id_asignature" id="asignature" value="<?php echo $asignature["id_asignatura"]; ?>">
		                     	<input type="hidden" name="id_group" id="group" value="<?php echo $group["id_grupo"]; ?>">
		                  	</div>
		               	</form>
	            	</div>
	        	</div>
	        	<div class="row" id="contenedorTabla">
      
   				</div>
	  		</div>
		</div>
	</div>
</div>

<script>
	$('#periodos').change(function(){

         if(this.value == 0){
            console.log("Nada");
         }else{
         	var asignature = $("#asignature").val(),
         		group = $("#group").val();
            $.ajax({

               type: "GET",
               dataType: "html",
               url: '/teacher/getStudentWithoutPeriodEvaluation/'+this.value+'/'+asignature+'/'+group,

               success: function(data){
                  $('#contenedorTabla').empty().append(data);
               },
               error(xhr, estado){
                  console.log(xhr);
                  console.log(estado);
               }
            });
         }
      });
</script>