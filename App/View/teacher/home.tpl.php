<!DOCTYPE html>
<html>
<head>
	<title>Agora | Docente | Asginaturas </title>
	<link href="/Public/css/bootstrap.css" rel="stylesheet" type="text/css">
   <link href="/Public/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css">
   <link href="/Public/css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="container">
   <h4>Asignaturas </h4>
   <div class="row">
      <table class="table" id="tabla">
         <thead>
            <tr>
               <th>N°</th>
               <th>Asignatura</th>
               <th>Grupo</th>
               <th>Evaluar Periodos</th>
               <th>Planillas</th>
            </tr>
         </thead>
         <tbody>
            <?php

               foreach($asginatures  as $key => $asginature){
               echo "<tr>
                        <td>".$key."</td>
                        <td>".utf8_encode($asginature['asignatura'])."</td>
                        <td>".utf8_encode($asginature['nombre_grupo'])."</td>
                        <td><a class='btn btn-primary' href='/teacher/showFormEvaluatePeriod/".DB.'/'.$asginature['id_asignatura']."/".$asginature['id_grupo']."'>Evaluar Periodo</a></td>
                        <td>
                           <div class='btn-group' role='group'>
                              <button type='button' class='btn btn-default dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                 Planilla
                                 <span class='caret'></span>
                              </button>
                              <ul class='dropdown-menu'>
                                 <li><a href='/teacher/showFormEvaluationSheet/".$asginature['id_asignatura']."/".$asginature['id_grupo']."' target='_blank'>Evaluación</a></li>
                                 <li><a href='/pdf/studentAttendance/".$asginature['id_asignatura']."/".$asginature['id_grupo']."' target='_blank'>Asistencia</a></li>
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

   <script src="/Public/js/jquery-1.12.4.js"></script>
   <script src="/Public/js/bootstrap.min.js"></script>
   <script src="/Public/js/jquery.dataTables.min.js"></script>
   <script src="/Public/js/dataTables.bootstrap.min.js"></script>
   
   <script type="text/javascript">
      $('#tabla').dataTable( {
        // "searching": false,
        "lengthChange": false
      } );
   </script>
</body>
</html>