
$(document).ready(function() {

    var id_shop = $('form').find('select[id*=_shop]').val();
    populate_shop(id_shop);

    var partner = $('form').find('select[name*=partner]').val();
    if (partner != '') {
    	fill_code_partner(partner);
    }

    //si clickamos el combobox de los socios rellenamos el de tiendas
    $('form').find('select[name*=partner]').change(function() {
        populate_shop();
        fill_code_partner($(this).val());
    });
});