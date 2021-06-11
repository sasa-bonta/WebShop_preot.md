$(document).ready(function () {

    $(document).on('click', '.delete-item-btn', function () {

        $('#checkout-fragment').hide();
        $('#checkout').show();

        var product_code = $(this).attr('data-code');

        $.ajax({
            url: "http://localhost:8000/api/v1/cart/" + product_code,
            type: "DELETE",
            dataType: 'json',
            data: JSON.stringify({id: product_code}),
            success: function (result) {

                showCart();
            },
            error: function (xhr, resp, text) {
                console.log(xhr, resp, text);
            }
        });

    });

});