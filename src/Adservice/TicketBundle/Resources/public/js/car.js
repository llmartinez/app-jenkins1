
//Funciones para el autorellenado de los combos de campos de coches de los formularios

    $(document).ready(function() {
        //cambiar model en funcion de brand
        $('#new_car_form_brand').change(function() {

            var select = document.querySelector('#new_car_form_brand');
            var data   = select.dataset;
            var url    = data.url;
            var id_brand = parseInt($('form[id=contact]').find('select[id=new_car_form_brand]').val())
            var url_ajax = url.replace("PLACEHOLDER", id_brand);
            fill_model(url_ajax);
        });

        //cambiar version en funcion de model
        $('#new_car_form_model').change(function() {
            var select = document.querySelector('#new_car_form_model');
            var data   = select.dataset;
            var url    = data.url;
            fill_version(url);
        });
    });