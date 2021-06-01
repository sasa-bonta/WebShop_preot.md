$(document).ready(function () {

    $(document).on("keypress", ".enter-amount", function (event) {

        var keyCode = event.which || event.keyCode;

        if (keyCode === 13) {
            var form_data = JSON.stringify($(this).serializeObject());
            var product_code = $(this).attr('data-code'),
                amount = $(this).val();

            $.ajax({
                url: "http://localhost:8000/api/v1/cart/introduce/" + product_code + "?amount=" + amount,
                type: "PATCH",
                contentType: 'application/json',
                data: form_data,
                success: function (result) {
                    showCart();
                    if (result.message === "amount should be greater than 0") alert("The introduced amount is invalid");
                    if (result.message === "amount is greater than the stock quantity") alert("There are not so many products in stock");
                },
                error: function (xhr, resp, text) {
                    console.log(xhr, resp, text);
                }
            });

            return false;
        }
    });
});