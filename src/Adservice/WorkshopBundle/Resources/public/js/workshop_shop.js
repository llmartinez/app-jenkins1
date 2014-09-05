
$(document).ready(function() {

    var id_shop = $('form').find('select[id*=_shop]').val();
    populate_shop(id_shop);

    //si clickamos el combobox de los socios rellenamos el de tiendas
    $('form').find('select[name*=partner]').change(function() {
        populate_shop();
    });
});