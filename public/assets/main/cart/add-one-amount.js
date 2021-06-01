$(document).ready(function () {

    $(document).on('click', '#addOneItem', function () {
        var form_data = JSON.stringify($(this).serializeObject());
        var product_code = $(this).attr('data-code');

        $.ajax({
            url: "http://localhost:8000/api/v1/cart/increase/" + product_code,
            type: "PATCH",
            contentType: 'application/json',
            data: form_data,
            success: function (result) {
                showCart();
                if (result.message === "item out of stock") alert("There are not so many products in stock");
            },
            error: function (xhr, resp, text) {
                console.log(xhr, resp, text);
            }
        });

        return false;
    });
});

