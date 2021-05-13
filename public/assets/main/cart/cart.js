function readItemsTemplate(data) {

    var read_items_html = `
        <table class='table table-hover'>
            <tr>
                <th class='w-5-pct' style="text-align: center">Code</th>
                <th class='w-5-pct' style="text-align: center">Amount</th>
<!--                <th class='w-20-pct' style="text-align: center">Product</th>-->
<!--                <th class='w-10-pct' style="text-align: center">Qty</th>-->
<!--                <th class='w-15-pct' style="text-align: center">Player</th>-->
<!--                <th class='w-30-pct' style="text-align: center">Action</th>-->
             </tr>`;

    data.forEach(element => {
        read_items_html += `<tr>
            <td style="text-align: center">`+ element['code'] +`</td>
            <td style="text-align: center">`+ element['amount'] +`</td>
        </tr>`;
    });

    read_items_html += `</table>`;

    $("#page-content").html(read_items_html);
}
// read_items_html += `<tr>
//             <td style="vertical-align: middle;text-align: center"><img src="` + val.code + `" alt="img" width="50px" height="50px"></td>
//             <td style="vertical-align: middle;text-align: center">` + val.amount + `</td>
//             <td style="vertical-align: middle;text-align: center">` + val.userId + `</td>
//             <td style="vertical-align: middle;text-align: center">
//                 <audio controls>
//                     <source src="` + val.code + `" type="audio/mp3">
//                 </audio>
//             </td>
//             <td style="vertical-align: middle;text-align: center">
//                 <button class='btn btn-dark m-r-10px read-one-song-button' data-id='` + val.code + `'>Listen</button>
//
//                 <button class='btn btn-outline-dark m-r-10px update-song-button' data-id='` + val.code + `'>Edit</button>
//
//                 <button class='btn btn-outline-danger delete-song-button' data-id='` + val.code + `'>Delete</button>
//             </td>
//         </tr>`;