<!DOCTYPE html>
<html lang="en">
   <head>
     <meta charset="utf-8">
     <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
     <title>Planillas</title>
     <link href="/Public/css/bootstrap.css" rel="stylesheet" type="text/css">
     <link href="/Public/css/font-awesome.min.css" rel="stylesheet" type="text/css">
     <link href="/Public/css/style.css" rel="stylesheet" type="text/css">
      
      <!-- SCRIPTS -->
      <script src="/Public/js/jquery-1.12.4.js"></script>
      <script src="/Public/js/bootstrap.min.js"></script>
      <script src="/Public/js/multiselect.js"></script>
     <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
     <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
       <!--[if lt IE 9]>
         <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
         <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
         <![endif]-->
   </head>
   <body>
      
      <div class="container">
         
         <div>
             <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
              <li role="presentation" class="active"><a href="#teahcer" aria-controls="teahcer" role="tab" data-toggle="tab">Docente</a></li>
              <li role="presentation"><a href="#group" aria-controls="group" role="tab" data-toggle="tab">Listados de grupo</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
               <div role="tabpanel" class="tab-pane active" id="teahcer">
                  <?php include('teacher.tpl.php'); ?>
               </div>
               <div role="tabpanel" class="tab-pane" id="group">
                  <?php include('group.tpl.php'); ?>
               </div>
            </div>
         </div>

      </div>
   </body>
   <script>
    $(document).ready(function(){
        
        // Busca los docentes de la sede selecionada
        $('#selectSede').change(function(){

            if(this.value != 0 || this.value != ''){
                $("#typeSheet").prop('disabled', false);
            }

            if(this.value == 0){
                $("#typeSheet").prop('disabled', true);
            }

            $.ajax({
                type: "GET",
                dataType: "html",
                url: '/ajax/getDocentes/<?php echo  $db;?>/'+this.value,
                success: function(data){
                    $('#selectDocente').empty().append(data);
                },
                error(xhr, estado){
                    console.log(xhr);
                    console.log(estado);
                }
            });
        });

        // 
        $("#typeSheet").change(function(){
            var form = $("#formCreateSheets"),
            url = '/sheet/';

            if(this.value == 0){

                $("#btnCreate").prop('disabled', true);

            }else if(this.value == 'Attendance'){

                form.attr('action', url+this.value+'/<?php echo $db?>');

                $("#btnCreate").prop('disabled', false);
                $("#orientation").prop('disabled', true);
                $("#period").prop('disabled', true);

            }else if(this.value == 'Evaluation'){

                form.attr('action', url+this.value+'/<?php echo $db?>');

                $("#btnCreate").prop('disabled', false);
                $("#orientation").prop('disabled', false);
                $("#period").prop('disabled', false);
            }
        });

        // MULTISELECT
        $('#selectDocente').multiselect({
            search: {

                left: '<input type="text" name="q" class="form-control" placeholder="Buscar..." style="margin-bottom:5px;"/>',

                right: '<input type="text" name="q" class="form-control" placeholder="Buscar..." style="margin-bottom:5px;"/>',

            }
        });

        $('#selectGroup').multiselect({
            search: {

                left: '<input type="text" name="q" class="form-control" placeholder="Buscar..." style="margin-bottom:5px;"/>',

                right: '<input type="text" name="q" class="form-control" placeholder="Buscar..." style="margin-bottom:5px;"/>',

            }
        });
    });
</script>
</html>