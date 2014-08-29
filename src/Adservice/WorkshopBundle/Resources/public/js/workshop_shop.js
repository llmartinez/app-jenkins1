
$(document).ready(function() {

    var select  = document.querySelector('#form_data');
    var data    = select.dataset;
    var url_ajax = data.url;
    populate_shop(url_ajax);

    //si clickamos el combobox de los socios rellenamos el de tiendas
    $('form').find('select[name*=partner]').change(function() {
        var select  = document.querySelector('#form_data');
        var data    = select.dataset;
        var url_ajax = data.url;
       populate_shop(url_ajax);
    });
});