<!DOCTYPE html>
<html>
<head>
	<title>Plantilla para evaluacion</title>
	<link href="<?php echo pb;?>css/bootstrap.css" rel="stylesheet" type="text/css">
   <link href="<?php echo pb;?>css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css">
   <link href="<?php echo pb;?>css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="container">
   <div class="panel panel-primary">
      <div class="panel-heading">
         <h3 class="panel-title"><?php echo date('d-m-Y');?></h3>
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
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
   
   <div class="row" id="contenedorTabla">
      
   </div>
</div>
   <script src="<?php echo pb;?>js/jquery-1.12.4.js"></script>
   <script src="<?php echo pb;?>js/jquery.dataTables.min.js"></script>
   <script src="<?php echo pb;?>js/dataTables.bootstrap.min.js"></script>
   
   <script type="text/javascript">
      

      $('#periodos').change(function(){

         if(this.value == 0){
            console.log("Nada");
         }else{
            $.ajax({

               type: "GET",
               // dataType: "json",
               url: '/ajax/getEvaluationSheet/<?php echo DB.'/'.$model;?>/'+this.value+'/'+<?php echo $asignature["id_asignatura"]?>+'/'+<?php echo $group["id_grupo"]?>,

               success: function(data){
                  $('#contenedorTabla').empty().append(data);
                  // console.log(data);
               },
               error(xhr, estado){
                  console.log(xhr);
                  console.log(estado);
               }
            });
         }
      });
   </script>
</body>
</html>