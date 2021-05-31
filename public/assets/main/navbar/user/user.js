const limit = document.getElementById("limit");
const order = document.getElementById("sortBy");
const name = document.getElementById("myInput");

const urlParams = new URLSearchParams(window.location.search);

if (urlParams.has("limit")) {
    limit.value = urlParams.get("limit");
} else {
    limit.value = 16;
}

if (urlParams.has("order")) {
    order.value = urlParams.get("order");
} else {
    order.value = "created_at:DESC";
}

if (urlParams.has("name")) {
    name.value = urlParams.get("name");
}

function setName() {
    var inputVal = document.getElementById("myInput").value;
    var urlSearchParams;
    if ($('body').data('route-name').indexOf('product_index') !== 0) {
        urlSearchParams = new URL(document.location.protocol + "//" + document.location.host + "/products/");
        urlSearchParams.searchParams.set("name", inputVal);
        urlSearchParams.searchParams.set("page", 1);
        window.location.replace(urlSearchParams);
    } else {
        urlSearchParams = new URLSearchParams(window.location.search);
        urlSearchParams.set("name", inputVal);
        urlSearchParams.set("page", 1);
        window.location.search = urlSearchParams;
    }
}

let $ = jQuery;
$(document).on('click', '#toggle-cart', function (e) {
    $('#cart').toggle();
});
$(document).on('click', function (e) {
    if (e.target.id !== 'toggle-cart' && $(e.target).closest('#cart').length === 0) {
        $('#cart').hide();
    }
});