let code = $('#order_payment_card_code').attr('value');
let cvv = $('#order_payment_card_cvv').attr('value');
let expiresAt = $('#order_payment_card_expiresAt').attr('value');

$("#order_payment_type_0").on('click', function () {
    $('#card-details').hide();

    $('#order_payment_card_code').attr("required", false);
    code = $('#order_payment_card_code').val();
    $('#order_payment_card_code').val('');

    $('#order_payment_card_cvv').attr("required", false);
    cvv = $('#order_payment_card_cvv').val();
    $('#order_payment_card_cvv').val('');

    $('#order_payment_card_expiresAt').attr("required", false);
    expiresAt = $('#order_payment_card_expiresAt').val();
    $('#order_payment_card_expiresAt').val('');
});

$("#order_payment_type_1").on('click', function () {
    $('#card-details').show();

    $('#order_payment_card_code').attr("required", true);
    $('#order_payment_card_code').val(code);

    $('#order_payment_card_cvv').attr("required", true);
    $('#order_payment_card_cvv').val(cvv);

    $('#order_payment_card_expiresAt').attr("required", true);
    $('#order_payment_card_expiresAt').val(expiresAt);
});