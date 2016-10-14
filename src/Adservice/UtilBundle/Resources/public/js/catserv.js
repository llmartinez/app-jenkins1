$(document).ready(function() {

	// PARTNER
	    $('form').find('select[id$=partner]').empty();

	// DIAG. MACHINE
	    $('form').find('select[id$=diagnosis_machines]').empty();

	// TIPOLOGY
	    $('form').find('select[id$=typology]').empty();


    //si clickamos el combobox de categoria de servicio rellenamos los relacionados
    $('form').find('select[name*=category_service]').change(function() {
        populate_partner();
        populate_typology();
        populate_diagmachine();

    });
});