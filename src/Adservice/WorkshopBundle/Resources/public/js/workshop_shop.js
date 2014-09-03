
$(document).ready(function() {

    var route   = 'shops_from_partner';
    var id_shop = $('form').find('select[id*=_shop]').val();
    populate_shop(route, id_shop);

    //si clickamos el combobox de los socios rellenamos el de tiendas
    $('form').find('select[name*=partner]').change(function() {
        var route = 'shops_from_partner';
        populate_shop(route);
    });
});