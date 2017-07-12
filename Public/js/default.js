$(document).ready(function(){
	
	// Colapsar el menu
	$('[data-toggle="collapse"]').click(function(){
		$('#sidebar').toggleClass('hidden-xs')
	});

	// Peticiones para los subHeaders
	$('[data-toggle="tab"]').each(function(){
		// Creamos la peticion ajax y renderizamos la vista
		$(this).click(function(){
			var that = $(this);

			if(!that.parent().hasClass('active'))
			{
				$.ajax({
					type: 	  'GET',
					url: 	  that.attr('data-link'),
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
			}
		});
	});

});