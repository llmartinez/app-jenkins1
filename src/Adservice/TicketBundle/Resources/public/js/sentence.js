
    $(document).ready(function() {
    //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
        $('#flt_country').click(function() {

            var country = $('#flt_country').val();
            if(country == null) country = 'none';

            var route = 'sentence_list';
            var locale = $(document).find("#data_locale").val();
            var url = Routing.generate(route, {_locale: locale, page: 1, country: country });

            window.open(url, "_self");
        });
    });

    $(document).on('change','#default_sentences',function(){
        // Recojo el texto actual
        if ($('#ticket_form_message').val() != undefined )  var actual_txt = $('#ticket_form_message').val();
        else                                                var actual_txt = $('#post_form_message').val();
        // Recojo la sentencia
        var sentence = $(this).val();
        // Si la sentencia no es "default"
        if (sentence != '0') {
            // Si el texto no esta vacio añadir un salto de linea
            if (actual_txt != '') actual_txt = actual_txt +"\n";
            // Inserta el texto en el textbox
            $('#ticket_form_message').val(actual_txt + sentence);
            $('#post_form_message'  ).val(actual_txt + sentence);
        }
    });