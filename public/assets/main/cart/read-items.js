$(document).ready(function () {
    showCart();

    $(document).on('click', '.read-songs-button', function () {
        showCart();
    });

});

function showCart() {
    $.getJSON("http://localhost:8000/api/v1/cart", function (data) {
        readItemsTemplate(data);

    });
}