<div class="col-md-12">
    <table class="table table-striped table-bordered" id="tabla">
        <thead>
            <tr>
                <th colspan="<?php echo ($periodo+3); ?>"><a class="btn btn-primary" href="/pdf/evaluationSheet/<?php echo DB.'/eval_'.$periodo.'_per'.'/'.$info['id_asignatura'].'/'.$info['id_grupo']?>">Descargar en pdf</a></th>
                <?php
                    foreach ($EP as $key => $value) {

                        if($value['id_parametro_evaluacion'] == 4)
                        {
                            echo "<th class='text-center'>
                                ".utf8_encode($value['parametro'])."
                            </th>";
                        }
                        else{
                        echo "<th colspan='5' class='text-center'>
                                ".utf8_encode($value['parametro'])."
                            </th>";
                        }
                    }
                ?>
                <th>VAL</th>
            </tr>
        </thead>
    	<thead>
        	<tr>
                <th>APELLIDOS Y NOMBRES DE ESTUDIANTES</th>
                <th>Nov</th>
                <th>Est</th>
                <?php
                    for ($i=0; $i < $periodo; $i++) { 
                        echo "<th class='text-center'>P".($i+1)."</th>";
                    }

                    // Desempeño cognitivo
                    for ($i=0; $i < 5; $i++) { 
                        echo "<th class='text-center'>&nbsp;</th>";
                    }

                    // Desempeño Personal
                    foreach ($DP as $clave => $valor) {
                        echo "<th class='text-center'>".$valor['abreviacion']."</th>";
                    }

                    // Desempeño Social
                    foreach ($DS as $clave => $valor) {
                        echo "<th class='text-center'>".$valor['abreviacion']."</th>";
                    }
                ?>
                <?php
                    foreach ($EP as $key => $value) {

                        if($value['id_parametro_evaluacion'] == 4)
                        {
                            echo "<th class='text-center'></th>";
                        }
                    }
                ?>
                <th></th>
            </tr>
        </thead>
        <tbody id="cuerpoTabla" class="text-center">
            <?php
                foreach ($datos as $clave => $valor) {
                    if($valor['alu_primer_nom'] != NULL){
                        echo "<tr>";
                            if($clave < 9){
                                echo "<td class='text-left'> 0".($clave+1)."   ".utf8_encode($valor['alu_primer_ape'].' '.$valor['alu_segundo_ape'].' '.$valor['alu_primer_nom'].' '.$valor['alu_segundo_nom'])."</td>";
                            }else{
                                echo "<td class='text-left'>    ".($clave+1)." ".utf8_encode($valor['alu_primer_ape'].' '.$valor['alu_segundo_ape'].' '.$valor['alu_primer_nom'].' '.$valor['alu_segundo_nom'])."</td>";
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

                            foreach ($EP as $key => $value) {

                                if($value['id_parametro_evaluacion'] == 4)
                                {
                                    echo "<th class='text-center'></th>";
                                }
                            }
                            echo "<td>&nbsp;</td>";
                        echo "</tr>";
                    }
                }
            ?>
        </tbody>
    </table>
</div>