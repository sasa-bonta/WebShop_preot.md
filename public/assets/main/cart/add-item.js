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
        var product_code = $(this).attr("data-productCode");

        $.getJSON("http://localhost:8000/api/v1/cart/" + product_code, function(data) {
            console.log(data);

            if (data.message === "item out of stock") alert("There are not so many products in stock");
        });

        $.ajax({
            url: "http://localhost:8000/api/v1/cart/" + product_code,
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

        return false;
    });
});