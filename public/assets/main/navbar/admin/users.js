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
    order.value = "email:ASC";
}

if (urlParams.has("search")) {
    name.value = urlParams.get("search");
}

let $ = jQuery;

function setUser() {
    var inputVal = document.getElementById("myInput").value;
    var urlSearchParams;
    if ($('body').data('route-name').indexOf('user_index') !== 0) {
        urlSearchParams = new URL(document.location.protocol + "//" + document.location.host + "/admin/users/");
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