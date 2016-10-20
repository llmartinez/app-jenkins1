$(document).ready(function() {
    var partner = $('#adservice_workshopbundle_workshoptype_partner').val();
                    
    // PARTNER
        $('#adservice_workshopbundle_workshoptype_partner').empty();

    if($('#flt_catserv').val() != undefined ){
        populate_partner2(partner);

    }
    else if($('form').find('select[name*=category_service]') != undefined ){
        populate_partner2(partner);

    }
            
    //si clickamos el combobox de categoria de servicio rellenamos los relacionados
    $('form').find('select[name*=category_service]').change(function() {
        populate_partner();
        populate_typology();
        populate_diagmachine();

    });
});