<div class="col-md-10 col-md-offset-1">
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
               foreach($info  as $clave => $valor){
               echo "<tr>
               			<td>".($clave+1)."</td>
						<td>".utf8_encode($valor['estudiante'])."</td>
					";
					if($valor['periodo'] != NULL) { 
                        echo "<td class='editable'><input data-student='".$valor['id_estudiante']."' data-asignatura='".$valor['id_asignatura']."' data-periodo='".$periodo."' type='text' value='".$valor['periodo']."' class='form-control'/></td>";
                    }else{ 
                        echo "<td class='editable'><input data-student='".$valor['id_estudiante']."' data-asignatura='".$valor['id_asignatura']."' data-periodo='".$periodo."' type='text' value='0.0' class='form-control'/></td>";}	
               	echo "</tr>";
               }
            ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">
	$('#tabla').dataTable( {
           // "searching": false,
       	"lengthChange": false,
       	"paging": false,
        language: {
            search: "Buscar:",
            emptyTable:"No se encontraron resultados",
            info: "Mostrando registros del _PAGE_  al _END_ de un total de _PAGES_ registros",
            infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
        }
    });

    $(function(){

        $("input").keydown(function(event){
        	if(event.which == 40){
        		$(this).parent().parent().next().find("input").focus();
        	}else if(event.which == 38){
        		$(this).parent().parent().prev().find("input").focus();
        	}
        });

        $("td input").focus(function(){
            var oldContent = $(this).val();

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
            url: '/ajax/updatePeriod/'+periodo+'/'+id_estudiante+'/'+id_asignatura+'/'+valor,

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