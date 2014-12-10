
//Funciones para el autorellenado de los combos de campos de coches de los formularios

    $(document).ready(function() {

        var code = $('form').find('#code').val();

        if(code != 0){ $('form').find('select[name*=code_workshop]').val(code); }

        var id_partner = $('form').find('#id_partner').val();
        var partners   = $('form').find('#select[id=partners]').val();

        if(id_partner == 0){

            $('#workshopOrder_newOrder_partner').empty();

            $('#partners option').appendTo("#workshopOrder_newOrder_partner");
        }else{
            $('form').find('select[name*=partner]').val(id_partner);
        }
        populate_shop();

        //si clickamos el combobox de los socios rellenamos el de tiendas
        $('form').find('select[name*=partner]').change(function() {
            populate_shop();
        });
    });