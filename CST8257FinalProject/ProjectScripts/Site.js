/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

 //passes the image ID to the hidden input that is sent in the GET request
$(document).ready(function(){
    $("img[name='imgThumbnail']").on("click", function(){
      $("input[name='selectedImage']").val($(this).attr('id'));
      $("form").submit();
    });
});