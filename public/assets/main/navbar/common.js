function changeLimit(value) {
    var urlSearchParams = new URLSearchParams(window.location.search);
    urlSearchParams.set('limit', value);
    urlSearchParams.set('page', 1);
    window.location.search = urlSearchParams;
}

function changeSort(value) {
    var urlSearchParams = new URLSearchParams(window.location.search);
    urlSearchParams.set('order', value);
    window.location.search = urlSearchParams;
}