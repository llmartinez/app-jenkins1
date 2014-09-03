
    $(document).ready(function() {

        // var select = document.querySelector('#data_locale');
        // var data   = select.dataset;
        // var locale = data.locale;

        var locale = $(document).find("#data_locale").val();

        if        (locale == 'en') { $('#selectLang').eq(0).attr("selected","selected"); }
        else { if (locale == 'es') { $('#selectLang').eq(1).attr("selected","selected"); }
        else { if (locale == 'fr') { $('#selectLang').eq(2).attr("selected","selected"); }
        }}

        $('#slct_lang').change(function(){

            var url = $('#slct_lang').val();
            window.open(url,'_self');
        });
    });