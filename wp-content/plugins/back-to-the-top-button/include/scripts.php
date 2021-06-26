<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<script>
    
jQuery("document").ready(function($) {

    // ==================================================
    // Dealing with uploading images
    // ==================================================

    $('.yydev_upload_image, .yydev_light_img_bg').click(function(e) {
        
        var uploadButtonParent = $(this).parent();

        e.preventDefault();
        var image = wp.media({
        title: 'Upload Image'}).open()
        .on('select', function(e){

            // This will return the selected image from the Media Uploader, the result is an object
            var images_length = image.state().get("selection").length;
            var images = image.state().get("selection").models;

            console.log(images);

            var image_url = images[0].toJSON().url;
            var image_alt = images[0].toJSON().alt;
            var image_caption = images[0].toJSON().caption;
            var image_title = images[0].toJSON().title;
            var image_id = images[0].toJSON().id;

            // Let's assign the url value to the input field
            uploadButtonParent.find('.yydev_light_img_bg').attr('src', image_url); // changing image icon
            uploadButtonParent.find('.yydev_image_input').val(image_url); // changing input url

        }); // .on('select', function(e){

    }); // $('.edit-button-image-upload').click(function(e) {

    // ==================================================
    // Changing the background of icon image on background change
    // ==================================================

    $('input#background_color').change(function() {
        
        var backgroundValue = $(this).val();
        $("img.yydev_light_img_bg").css("background", backgroundValue);

    }); // $('input#background_color').change(function() {

}); // jQuery("document").ready(function($) {

</script>