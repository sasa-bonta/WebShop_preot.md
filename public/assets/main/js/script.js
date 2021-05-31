var list = document.getElementsByClassName('paginationButtons'),
    ul = document.getElementById('pagination'),
    li = document.createElement("li"),
    btn = document.createElement("div");

var element1 = document.getElementById("pagBtn1");

li.className = 'paginationButtons page-item';
btn.className = 'page-link';
btn.appendChild(document.createTextNode("..."));

li.appendChild(btn);

if (!(window.location.href.includes('page'))) {
    element1.style.setProperty('background', 'black');
    element1.style.setProperty('color', 'white');

    for (let i = 4; i <= list.length - 2; i++) {
        list[i].style.setProperty('display', 'none');
    }

    if (list.length > 5) {
        ul.insertBefore(li, ul.childNodes[list.length]);
    }
} else {
    var url = new URL(window.location.href);
    var page = url.searchParams.get("page");

    var element2 = document.getElementById("pagBtn" + page);


    element1.style.setProperty('background', 'none');
    element1.style.setProperty('color', 'black');

    element2.style.setProperty('background', 'black');
    element2.style.setProperty('color', 'white');

    current = page - 1;
    var flag1 = false,
        flag2 = false;

    for (let i = 1; i <= current - 4; i++) {
        list[i].style.setProperty('display', 'none');
        flag1 = true;
    }

    for (let i = current + 4; i <= list.length - 2; i++) {
        list[i].style.setProperty('display', 'none');
        flag2 = true;
    }

    var liClone = li.cloneNode(true);

    if (flag1 === true) ul.insertBefore(li, ul.firstElementChild.nextElementSibling);
    if (flag2 === true) ul.insertBefore(liClone, ul.lastElementChild);
}

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
}

function expandImage(imgs) {
    var expandImg = document.getElementById("expandedImg");
    expandImg.src = imgs.src;
    expandImg.parentElement.style.display = "block";
}