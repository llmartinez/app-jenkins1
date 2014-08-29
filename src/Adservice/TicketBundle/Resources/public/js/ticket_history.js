
    $(document).ready(function() {

        $("#slct_historyTickets").change(function() {

            var select = document.querySelector('#slct_historyTickets');
            var data   = select.dataset;
            var url    = data.url;
            url_show = url.replace("plc_option", $(this).val());

            window.open(url_show, "_self");

        });
    });