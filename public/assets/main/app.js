$(document).ready(function(){
    var app_html=`
        <div class='container'>
            <div id='page-content'></div>
        </div>`;

    $("#app").html(app_html);
});

function changePageTitle(page_title){

    $('#page-title').text(page_title);
    document.title=page_title;
}

$.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};