$("#order_payment_type_0").trigger('click');

$("#order_payment_type_0").on('click', function () {
    $('#card-details').hide();
    $('#order_payment_card_code').attr("required", false);
    $('#order_payment_card_cvv').attr("required", false);
    $('#order_payment_card_expiresAt').attr("required", false);
});

$("#order_payment_type_1").on('click', function () {
    $('#card-details').show();
    $('#order_payment_card_code').attr("required", true);
    $('#order_payment_card_cvv').attr("required", true);
    $('#order_payment_card_expiresAt').attr("required", true);
});