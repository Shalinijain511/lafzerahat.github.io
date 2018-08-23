var $ = jQuery.noConflict();
$("#wp_user_profile_div_close").click(function(){
    $("#wp_user_profile_div").hide();
});

$(function() {
    var file_frame;

    $(".additional-user-image").on("click", function( event ){

        event.preventDefault();

        // If the media frame already exists, reopen it.
        if ( file_frame ) {
            file_frame.open();
            return;
        }

        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
            title: $( this ).data( "uploader_title" ),
            button: {
                text: $( this ).data( "uploader_button_text" ),
            },
            multiple: false
        });

        var current_id=this.id;

        // When an image is selected, run a callback.
        file_frame.on( "select", function() {
            // We set multiple to false so only get one image from the uploader
            attachment = file_frame.state().get("selection").first().toJSON();
            //$(".user_meta_image").val(attachment.url);   
            $("#img_"+current_id).val(attachment.url);
            $("#user_meta_image_attachment_id").val(attachment.id);


            // Do something with attachment.id and/or attachment.url here
        });

        // Finally, open the modal
        file_frame.open();
    });

});
$("#wpuser_update_profile_button").click(function () {
    $.ajax({
        url: wpuser.wpuser_ajax_url+'?action=wpuser_update_profile_action',
        data: $("#google_form").serialize(),
        error: function (data) {
        },
        success: function (data) {
            var parsed = $.parseJSON(data);
            $("#wpuser_errordiv_register").html('<div class="alert alert-' + parsed.status + ' alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' + parsed.message + '</div>');
            if (parsed.status == 'success') {
                $('.wpuser_profile_name').html(parsed.user_info.name);
                $('.wpuser_profile_first_name').html(parsed.user_info.first_name);
                $('.wpuser_profile_last_name').html(parsed.user_info.last_name);
                $('.wpuser_profile_description').html(parsed.user_info.description);
                $('.wpuser_profile_email').html(parsed.user_info.email);
                $('.wpuser_profile_user_url').html(parsed.user_info.user_url);
                $('.wpuser_profile_img').attr('src', parsed.user_info.profile_img);
                $('.profile_background_pic').attr('src', parsed.user_info.profile_background_pic);
                $('.wpuser_profile_strength').attr('style', 'width:' + parsed.user_info.wpuser_profile_strength + '%');
                $('.wpuser_profile_strength').html(parsed.user_info.wpuser_profile_strength + '%');
                $.each(parsed.user_info.advanced, function (i, val) {
                    $('.wpuser_profile_' + i).html(val);
                    $('.wpuser_profile_url_' + i).attr('href', val);
                });
            }
            $('#wpuser_errordiv_register').show();
        },
        type: 'POST'
    });
});

$("#wp_user_address_field_submit").click(function () {
        $.ajax({
            type: "POST",
            url: wpuser.wpuser_ajax_url + '?action=wpuser_address',
            data: $('#wp_user_address_field_form').serialize(),
            error: function (data) {
            },
            success: function (data) {
                var parsed = $.parseJSON(data);
                $("#wp_user_address_label").html(parsed.message);
                $("#wp_user_address_div").removeClass().addClass("alert alert-dismissible alert-" + parsed.status);
                $("#wp_user_address_div").show();
                $("#pass1").val("");
                $("#pass2").val("");
            }
        });
    });

$("#wp_user_address_div_close").click(function(){
    $("#wp_user_address_div").hide();
});

$("#wp_user_profile_contact_submit").click(function () {
    $.ajax({
        type: "post",
        url: wpuser.wpuser_ajax_url+'?action=wpuser_contact',
        data: $("#wp_user_profile_contact_form").serialize(),
        success: function (data) {
            var parsed = $.parseJSON(data);
            $("#wp_user_contact_div").html('<div class="alert alert-' + parsed.status + ' alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' + parsed.message + '</div>');
            if (parsed.status == 'success') {
                $("#wp_user_profile_contact_form")[0].reset();
            }
            $('#wp_user_contact_div').show();
        },
    });
});