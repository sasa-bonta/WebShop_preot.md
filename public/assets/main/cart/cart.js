function readItemsTemplate(data) {

    var read_items_html = ``;
    var total = 0;

    if (data.length > 0) {
        read_items_html += `
        <table class='cart-table table' id="cart-content">
            <tr>
                <th class='w-5-pct' colspan="2" style="text-align: center">Product</th>
                <th class='w-5-pct' style="text-align: center">Price</th>
                <th class='w-5-pct' style="text-align: center">Qty</th>
                <th class='w-5-pct' style="text-align: center">Total</th>
                <th class='w-5-pct' style="text-align: center"></th>
             </tr>`;
    }

    var formatter = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    });

    data.forEach(cartItem => {

        var icon = JSON.parse(cartItem['product']['imgPath']);

        total += cartItem['product']['price'] * cartItem['amount'];

        read_items_html += `
        <tr>
            <td style="text-align: center"><img src="/assets/main/images/gallery/` + icon[0] + `" alt="img" width="100" height="100"></td>
            <td style="vertical-align: middle;text-align: center">` + cartItem['product']['name'] + `</td>
            <td style="vertical-align: middle;text-align: center">` + formatter.format(cartItem['product']['price']) + ` $</td>
            <td style="vertical-align: middle;text-align: center">` + cartItem['amount'] + `</td>
            <td style="vertical-align: middle;text-align: center">` + formatter.format(cartItem['amount'] * cartItem['product']['price']) + ` $</td>
            <td style="vertical-align: middle;text-align: center"><button class="delete-item-btn btn btn-danger" id="deleteItem" data-code="` + cartItem['product']['code'] + `">X</button></td>
        </tr>
        `;
    });

    if ($('body').data('route-name').indexOf('cart_index') === 0) {
        $("#cart-container").css("overflow-y", "hidden").css("height", "auto");
    }

    if (total === 0) {
        read_items_html += `<p class="empty-cart">Your shopping cart is empty now</p>`;
    } else {
        read_items_html += `
            <tr>
                <td style="vertical-align: middle;text-align: right" colspan="4"><b>Total : </b></td>
                <td style="vertical-align: middle;text-align: center"><b>` + formatter.format(total) + ` $</b></td>
                <td></td>
            </tr>
        </table>`;
    }

    $("#page-content").html(read_items_html);
}
