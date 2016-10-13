$(document).ready(function() {

    $('form').find('select[id$=partner]').empty();

    //si clickamos el combobox de categoria de servicio rellenamos el de socios
    $('form').find('select[name*=category_service]').change(function() {
        populate_partner();
    });
});