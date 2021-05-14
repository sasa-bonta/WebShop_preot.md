$(document).ready(function () {
    $.fn.serializeObject = function () {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function () {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };

    $('button[data-productCode]').click(function () {

        var form_data = JSON.stringify($(this).serializeObject());
        var productCode = $(this).attr("data-productCode");

        $.ajax({
            url: "http://localhost/api/v1/cart/" + productCode,
            type: "POST",
            contentType: 'application/json',
            data: form_data,
            success: function (result) {
                showCart();
            },
            error: function (xhr, resp, text) {
                console.log(xhr, resp, text);
            }
        });
        // $.ajax({
        //     url: "http://localhost/api/v1/cart/" + productCode,
        //     type: 'POST',
        //     dataType: 'jsonp',
        //     data: form_data,
        //     success: function(data,textStatus,xhr){
        //
        //         console.log(data);
        //     },
        //     error: function(xhr,textStatus,errorThrown){
        //         console.log('Error Something');
        //     },
        //     beforeSend: function(xhr) {
        //
        //         xhr.setRequestHeader("Authorization", "Basic OTdlMjVmNWJiMTdjNzI2MzVjOGU3NjlhOTI3ZTA3M2Q5MWZmMTA3ZDM2YTZkOWE5Og==");
        //     }
        // });

        return false;
    });
});