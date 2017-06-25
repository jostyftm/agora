<div class="col-md-12">
    <table class="table table-striped table-bordered" id="tabla">
        <thead>
            <tr>
                <th colspan="<?php echo ($periodo+3); ?>"><a class="btn btn-primary" href="/pdf/evaluationSheet/<?php echo DB.'/'.$model.'/eval_'.$periodo.'_per'.'/'.$info['id_asignatura'].'/'.$info['id_grupo']?>">Descargar en pdf</a></th>
                <th colspan="5" class="text-center">DESEMPEÑO COGNITIVO</th>
                <th colspan="<?php echo count($DP); ?>" class="text-center">DESEMPEÑO PERSONAL</th>
                <th colspan="<?php echo count($DS); ?>" class="text-center">DESEMPEÑO SOCIAL</th>
            </tr>
        </thead>
    	<thead>
        	<tr>
                <th>APELLIDOS Y NOMBRES DE ESTUDIANTES</th>
                <th>Nov</th>
                <th>Est</th>
                <?php
                    for ($i=0; $i < $periodo; $i++) { 
                        echo "<th>P".($i+1)."</th>";
                    }

                    for ($i=0; $i < 5; $i++) { 
                        echo "<th>&nbsp;</th>";
                    }

                    foreach ($DP as $clave => $valor) {
                        echo "<th>".$valor['abreviacion']."</th>";
                    }

                    foreach ($DS as $clave => $valor) {
                        echo "<th>".$valor['abreviacion']."</th>";
                    }
                ?>
                <th>VAL</th>
            </tr>
        </thead>
        <tbody id="cuerpoTabla" class="text-center">
            <?php
                foreach ($datos as $clave => $valor) {
                    if($valor['estudiante'] != NULL){
                        echo "<tr>";
                            if($clave < 9){
                                echo "<td class='text-left'> 0".($clave+1)."   ".utf8_encode($valor['estudiante'])."</td>";
                            }else{
                                echo "<td class='text-left'>    ".($clave+1)." ".utf8_encode($valor['estudiante'])."</td>";
                            }
                            echo "<td>".$valor['novedad']."</td>";
                            echo "<td>".$valor['estatus']."</td>";
                            
                            for ($i=0; $i < $periodo; $i++) { 
                                if($valor['periodo'.($i+1)] > 0)
                                    if(!strstr($valor['periodo'.($i+1)], "."))
                                        echo "<td>".$valor['periodo'.($i+1)].".0</td>";
                                    else
                                        echo "<td>".$valor['periodo'.($i+1)]."</td>";
                                else
                                    echo "<td>0.0</td>";
                            }

                            for ($i=0; $i < 5; $i++) { 
                                echo "<td>&nbsp;</td>";
                            }

                            foreach ($DP as $clave => $valor) {
                                 echo "<td>&nbsp;</td>";
                            }

                            foreach ($DS as $clave => $valor) {
                                 echo "<td>&nbsp;</td>";
                            }

                            echo "<td>&nbsp;</td>";
                        echo "</tr>";
                    }
                }
            ?>
        </tbody>
    </table>
</div>