
    $(document).ready(function() {
    //REDIRIGE A LA PAGINA EN LA QUE SE HAYA HECHO CLICK
        $('#flt_country').click(function() {

            var country = $('#flt_country').val();

            if(country == null) country = 'none';

            var select = document.querySelector('#flt_country');
            var data   = select.dataset;
            var url    = data.url;

            url = url.replace("plc_page", 1);
            url = url.replace("plc_country", country);

            window.open(url, "_self");
        });
    });

    $(document).on('change','#default_sentences',function(){

        var sentence = $(this).val();

        $('#ticket_form_message').val(sentence);

    });