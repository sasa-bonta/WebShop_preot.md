$(document).ready(function(){
    var app_html=`
        <div id="cart-container" class='cart-container'>
            <div id='page-content'></div>
        </div>`;

    $("#app").html(app_html);
});

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

$('.js-datepicker').datepicker({
    format: 'yyyy-mm',
    minViewMode: 1,
    autoclose : true
});