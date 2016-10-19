
    $(document).ready(function() {

    	$("#slct_numRows").val($('#num_rows').val());

        $("#slct_historyTickets").change(function() {

            var str      = $('#href_tickets').val();
            var country  = $('#country').val();
            var catserv  = $('#catserv').val();
            if(catserv == undefined) var catserv  = $('#flt_catserv').val();
            var lang     = $('#flt_language').val();
            var num_rows = $('#num_rows').val();
            var option   = $(this).val();

            var url = str.replace('plc_country', country);
            var url = url.replace('plc_catserv', catserv);
            var url = url.replace('plc_lang', lang);
            var url = url.replace('plc_rows', num_rows);
            var url = url.replace('plc_option', option);

            window.open(url, "_self");
        });

        $("#slct_numRows").change(function() {

            var str      = $('#href_tickets').val();
            var country  = $('#country').val();
            var catserv  = $('#catserv').val();
            if(catserv == undefined) var catserv  = $('#flt_catserv').val();
            var lang     = $('#flt_language').val();
            var num_rows = $(this).val();
            var option   = $('#option').val();

            var url = str.replace('plc_country', country);
            var url = url.replace('plc_catserv', catserv);
            var url = url.replace('plc_lang', lang);
            var url = url.replace('plc_rows', num_rows);
            var url = url.replace('plc_option', option);

            window.open(url, "_self");
        });

        $("#flt_catserv").change(function() {

            var str      = $('#href_tickets').val();
            var country  = $('#country').val();
            var catserv  = $(this).val();
            var lang     = $('#flt_language').val();
            var num_rows = $('#num_rows').val();
            var option   = $('#option').val();

            var url = str.replace('plc_country', country);
            var url = url.replace('plc_catserv', catserv);
            var url = url.replace('plc_lang', lang);
            var url = url.replace('plc_rows', num_rows);
            var url = url.replace('plc_option', option);

            window.open(url, "_self");
        });

        $('#flt_country').change(function() {

            var str      = $('#href_tickets').val();
            var country  = $(this).val();
            var catserv  = $('#catserv').val();
            if(catserv == undefined) var catserv  = $('#flt_catserv').val();
            var lang     = $('#flt_language').val();
            var num_rows = $('#num_rows').val();
            var option   = $('#option').val();

            var url = str.replace('plc_country', country);
            var url = url.replace('plc_catserv', catserv);
            var url = url.replace('plc_lang', lang);
            var url = url.replace('plc_rows', num_rows);
            var url = url.replace('plc_option', option);

            window.open(url, "_self");
        });

        $('#flt_language').change(function() {

            var str      = $('#href_tickets').val();
            var country  = $('#country').val();
            var catserv  = $('#catserv').val();
            if(catserv == undefined) var catserv  = $('#flt_catserv').val();
            var lang     = $(this).val();
            var num_rows = $('#num_rows').val();
            var option   = $('#option').val();

            var url = str.replace('plc_country', country);
            var url = url.replace('plc_catserv', catserv);
            var url = url.replace('plc_lang', lang);
            var url = url.replace('plc_rows', num_rows);
            var url = url.replace('plc_option', option);

            window.open(url, "_self");
        });
    });