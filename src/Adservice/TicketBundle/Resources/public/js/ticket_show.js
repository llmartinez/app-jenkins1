
    $(document).ready(function() {

        $("tr#ticketRow").click(function() {

    		var locale = $(document).find("#data_locale").val();
            var url    = Routing.generate('showTicket', {_locale: locale, id: 'PLACEHOLDER'});
            url_show   = url.replace("PLACEHOLDER", $(this).children('#id_ticket').text());
            window.open(url_show, "_self");

        });
    });