$( "#order_payment_type_0" ).trigger( "click" );

$("#checkout").on('click', function () {
    $("#checkout-fragment").show();
    $("#card-details").hide();
});

$("#order_payment_type_0").on('click', function () {
    $('#card-details').hide();
});

$("#order_payment_type_1").on('click', function () {
    $('#card-details').show();
});