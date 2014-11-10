
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

        var sentence = $(this).val();

        $('#ticket_form_message').val(sentence);
        $('#post_form_message').val(sentence);

    });