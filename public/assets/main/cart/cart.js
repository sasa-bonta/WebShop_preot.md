function readItemsTemplate(data) {

    var read_items_html = `
        <table class='cart-table table'>
            <tr>
                <th class='example w-5-pct' style="text-align: center; align-items: center; vertical-align: middle"></th>
                <th class='w-5-pct' style="text-align: center">Product</th>
                <th class='w-5-pct' style="text-align: center">Price</th>
                <th class='w-5-pct' style="text-align: center">Qty</th>
                <th class='w-5-pct' style="text-align: center">Total</th>
                <th class='w-5-pct' style="text-align: center"></th>
             </tr>`;

    data.forEach(cartItem => {
        console.log(cartItem['product']['imgPath']);
        read_items_html += `
        <tr>
            <td style="text-align: center; vertical-align: bottom"><img src="/`+ cartItem['product']['imgPath'] +`" alt="img" width="100" height="100"></td>
            <td style="text-align: center">`+ cartItem['product']['name'] +`</td>
            <td style="text-align: center">`+ cartItem['product']['price'] +`</td>
            <td style="text-align: center">`+ cartItem['amount'] +`</td>
            <td style="text-align: center">`+ cartItem['amount'] * cartItem['product']['price'] +`</td>
            <td style="text-align: center"><button class="btn btn-danger">X</button></td>
        </tr>`;
    });

    read_items_html += `</table>`;

    $("#page-content").html(read_items_html);
}
