$(document).ready(function () {

    $(document).on('click', '#deleteOneItem', function () {

        var form_data = JSON.stringify($(this).serializeObject());
        var product_code = $(this).attr('data-code');

        $.ajax({
            url: "http://localhost:8000/api/v1/cart/decrease/" + product_code,
            type: "PATCH",
            contentType: 'application/json',
            data: form_data,
            success: function (result) {
                showCart();
                if (result.message === "amount cannot be 0") alert("Amount of the product cannot be 0");
            },
            error: function (xhr, resp, text) {
                console.log(xhr, resp, text);
            }
        });

        return false;
    });
});