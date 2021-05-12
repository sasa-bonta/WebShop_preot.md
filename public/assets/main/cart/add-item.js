/**
 * TO DO: Use ajax to add items to cart
 */

// $(document).ready(function () {
//
//     $(document).on('click', '.create-song-button', function () {
//         $.getJSON("http://localhost/api/v1/cart", function (data) {
//             var categories_options_html = `<select name='category_id' class='form-control'>`;
//             $.each(data.records, function (key, val) {
//                 categories_options_html += `<option value='` + val.id + `'>` + val.name + `</option>`;
//             });
//             categories_options_html += `</select>`;
//
//             var create_song_html = `
//                 <div id='read-songs' class='btn btn-outline-dark pull-right m-b-15px read-songs-button' style="margin: 100px 0 50px 0">See full list</div>
//                 <form id='create-song-form' action='#' method='post'>
//                     <table class='table table-hover table-responsive table-bordered'>
//                         <tr>
//                             <td style="vertical-align: middle;text-align: center">Name</td>
//                             <td style="vertical-align: middle;text-align: center"><input type='text' name='name' class='form-control' required /></td>
//                         </tr>
//                         <tr>
//                             <td style="vertical-align: middle;text-align: center">Artist</td>
//                             <td style="vertical-align: middle;text-align: center"><input type='text' name='artist' class='form-control' required /></td>
//                         </tr>
//                         <tr>
//                             <td style="vertical-align: middle;text-align: center">Audio</td>
//                             <td style="vertical-align: middle;text-align: center"><textarea name='audio_path' class='form-control' required></textarea></td>
//                         </tr>
//                         <tr>
//                             <td style="vertical-align: middle;text-align: center">Image</td>
//                             <td style="vertical-align: middle;text-align: center"><textarea name='image_path' class='form-control' required></textarea></td>
//                         </tr>
//                         <tr>
//                             <td style="vertical-align: middle;text-align: center">Category</td>
//                             <td style="vertical-align: middle;text-align: center">` + categories_options_html + `</td>
//                         </tr>
//                         <tr>
//                             <td></td>
//                             <td style="vertical-align: middle;text-align: center">
//                                 <button type='submit' class='btn btn-outline-dark'>Add song</button>
//                             </td>
//                         </tr>
//                     </table>
//                 </form>`;
//
//             $("#page-content").html(create_song_html);
//             changePageTitle("Add Song");
//         });
//     });
//
//     $(document).on('submit', '#create-song-form', function(){
//         var form_data=JSON.stringify($(this).serializeObject());
//
//         $.ajax({
//             url: "http://localhost/api/music/create.php",
//             type : "POST",
//             contentType : 'application/json',
//             data : form_data,
//             success : function(result) {
//                 showSongs();
//             },
//             error: function(xhr, resp, text) {
//                 console.log(xhr, resp, text);
//             }
//         });
//
//         return false;
//     });
// });