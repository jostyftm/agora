<div class="col-md-12">
	<table class="table table-striped" id="tabla">
	    <thead>
	       	<tr>
	            <th width="50px">N°</th>
	            <th width="400px">Nombre</th>
	            <th width="100px">Periodo: <?php echo split("_", $periodo)[1] ?></th>
	        </tr>
	    </thead>
	    <tbody id="cuerpoTabla">
	    <?php
	        foreach($students  as $clave => $valor){
		        echo "<tr>
		      			<td>".($clave+1)."</td>
						<td>".
							utf8_encode(
								$valor['primer_ape_alu']." ".
								$valor['segundo_ape_alu']." ".
								$valor['primer_nom_alu']." ".
								$valor['segundo_nom_alu']
							).
						"</td>";

					if($valor['periodo'] != NULL) { 
		                echo "<td class='editable'><input data-student='".$valor['idstudents']."' data-asignatura='".$valor['id_asignatura']."' data-periodo='".$periodo."' type='text' value='".$valor['periodo']."' class='form-control'/></td>";
		            }else{ 
		                echo "<td class='editable'><input data-student='".$valor['idstudents']."' data-asignatura='".$valor['id_asignatura']."' data-periodo='".$periodo."' type='text' value='' class='form-control'/></td>";}	
		        echo "</tr>";
	        }
	    ?>
	    </tbody>
	</table>
</div>

<script>
	// DataTables
    $('#tabla').dataTable({
      	"lengthChange": false,
       	"paging": false,
        language: {
        	url: '/Public/json/Spanish.json'
        }
    });	

    // 
    $(function(){

        $("input").keydown(function(event){
        	if(event.which == 40){
        		var ele = $(this).parent().parent().next().find("input");
        		ele.focus();
        	}else if(event.which == 38){
        		var ele = $(this).parent().parent().prev().find("input");
        		ele.focus();
        	}
        });

        $("td input").focus(function(){
            var oldContent = $(this).val();
            this.select();

            $(this).blur(function() {
                var newContent = $(this).val(),
                id_estudiante = $(this).attr('data-student'),
                id_asignatura = $(this).attr('data-asignatura'),
                periodo = $(this).attr('data-periodo');

                if(newContent != oldContent){
                    $(this).attr("value", newContent);
                    actualizarPeriodo(periodo, id_estudiante,id_asignatura,newContent);
                }
                $(this).unbind("blur");
            });
        });

        function actualizarPeriodo(periodo, id_estudiante, id_asignatura, valor){
            $.ajax({

            type: "GET",
            // dataType: "json",
            url: '/teacher/updatePeriod/'+periodo+'/'+id_estudiante+'/'+id_asignatura+'/'+valor,

            success: function(data){
                console.log(data);
            },
            error(xhr, estado){
               console.log(xhr);
               console.log(estado);
            }
         });
        }
    });
</script>