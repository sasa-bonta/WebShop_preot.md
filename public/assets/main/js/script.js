function setCategory(value) {
    var urlSearchParams = new URLSearchParams(window.location.search);
    urlSearchParams.set('category', value);
    urlSearchParams.set('page', 1);
    window.location.search = urlSearchParams;
}

function setPage(value) {
    var urlSearchParams = new URLSearchParams(window.location.search);
    urlSearchParams.set('page', value);
    window.location.search = urlSearchParams;

    var element = document.querySelectorAll("button[value=" + value + "]");
    element.style.transform = "background: black;";
}

function expandImage(imgs) {
    var expandImg = document.getElementById("expandedImg");
    expandImg.src = imgs.src;
    expandImg.parentElement.style.display = "block";
}