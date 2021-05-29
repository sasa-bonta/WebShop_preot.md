$(document).ready(function () {
    showCart();

    $(document).on('click', '.read-songs-button', function () {
        showCart();
    });

});

function showCart() {
    alert("read-items.js");
    $.getJSON("http://localhost:8000/api/v1/cart", function (data) {
        readItemsTemplate(data);

    });
}