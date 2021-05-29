const limit = document.getElementById("limit");
const order = document.getElementById("sortBy");
const name = document.getElementById("myInput");

const urlParams = new URLSearchParams(window.location.search);

if (urlParams.has("limit")) {
    limit.value = urlParams.get("limit");
} else {
    limit.value = 10;
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
    if ($('body').data('route-name').indexOf('product_list') !== 0) {
        urlSearchParams = new URL(document.location.protocol + "//" + document.location.host + "/admin/products/");
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