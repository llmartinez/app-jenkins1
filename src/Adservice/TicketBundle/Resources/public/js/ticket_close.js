
$(document).ready(function() {
    // Limpiamos el combo de subsystem del formulario y cargamos subsistemas
    $('#sol_other').hide();
});

//cambiar model en funcion de brand
$(document).on('change','#close_ticket_form_solution',function(){

	if($(this).val() == 2)  { $('#sol_other').show() }
	else 					{ $('#sol_other').hide() }
});