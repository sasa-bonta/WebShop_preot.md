if ($('#expires-at-error').length !== 0) {
    $("#order_payment_type_1").trigger("click");
    $('#checkout').hide();
    $('#checkout-fragment').show();
    $('#card-details').show();
} else {
    $( "#order_payment_type_0" ).trigger("click");
}

$("#checkout").on('click', function () {
    if ($('#cart-content').length) {
        $('#checkout-fragment').show();
    } else {
        // if to delete these 2 lines it will be funny.
        // If the cart is empty the checkout button wil disappear
        alert("Your cart is empty");
        return;
    }
    $('#checkout').hide();

});

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

let urlParams = new URLSearchParams(window.location.search);
if (urlParams.has("success")) {
    const myModal = new bootstrap.Modal(document.getElementById('exampleModal'), {
        backdrop: true
    })
    myModal.show();
    window.history.replaceState({}, document.title, location.protocol + '//' + location.host + location.pathname);
}
