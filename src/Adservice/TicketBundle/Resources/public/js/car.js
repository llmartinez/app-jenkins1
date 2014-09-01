
//Funciones para el autorellenado de los combos de campos de coches de los formularios

    $(document).ready(function() {
        //cambiar model en funcion de brand
        $('#new_car_form_brand').change(function() {

            var route = 'car_model';
            fill_model(route);
        });

        //cambiar version en funcion de model
        $('#new_car_form_model').change(function() {
            var select = document.querySelector('#new_car_form_model');
            var data   = select.dataset;
            var url    = data.url;
            fill_version(url);
        });
    });