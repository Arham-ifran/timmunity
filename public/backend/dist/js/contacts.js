var jqxhr = { abort: function() {} };

function search_results(url, type, s, elemen, counter) {

    jqxhr.abort();
    jqxhr = jQuery.ajax({
        type: type,
        data: s,
        url: url,
        success: function(data) {
            $(elemen).html(data);
            if (data['view'] != undefined) {
                $(elemen).html(data['view']);
            }
            $(counter).html("&nbsp;(Filtered " + data['count'] + " Records)");
        },
        error: function(e) {
            // Error
        }
    });
}