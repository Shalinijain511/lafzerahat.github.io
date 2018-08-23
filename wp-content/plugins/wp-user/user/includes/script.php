<?php
if(!is_user_logged_in()) {
    $wp_user_security_reCaptcha_enable = (get_option('wp_user_security_reCaptcha_enable') && !empty(get_option('wp_user_security_reCaptcha_secretkey'))) ? 1 : 0;
 ?>
    <script>
        var wpuser = {wpuser_ajax_url:'<?php echo admin_url('admin-ajax.php')?>',wp_user_security_reCaptcha_enable:<?php echo $wp_user_security_reCaptcha_enable?>,login_redirect:'<?php echo $login_redirect?>'};
    var $ = jQuery.noConflict();
    $("#wpuser_register<?php echo $form_id ?>").click(function () {
        if(wpuser.wp_user_security_reCaptcha_enable==1){
            if (grecaptcha.getResponse() == '') {
                $('#wpuser_error_register<?php echo $form_id ?>').html("Please verify Captcha");
                $('#wpuser_errordiv_register<?php echo $form_id ?>').removeClass().addClass('alert alert-dismissible alert-warning');
                $('#wpuser_errordiv_register<?php echo $form_id ?>').show();
                return false;
            }
        }
        $.ajax({
        url: wpuser.wpuser_ajax_url+'?action=wpuser_register_action',
        data: $("#google_form<?php echo $form_id ?>").serialize(),
        error: function (data) {
        },
        success: function (data) {
            var parsed = $.parseJSON(data);
            $('#wpuser_error_register<?php echo $form_id ?>').html(parsed.message);
            $('#wpuser_errordiv_register<?php echo $form_id ?>').removeClass().addClass('alert alert-dismissible alert-' + parsed.status);
            if (parsed.status == 'success') {
                $("#google_form<?php echo $form_id ?>")[0].reset();
            }
              if (parsed.message == 'Registration completed') {
                        window.location.reload(true);
               }
            $('#wpuser_errordiv_register<?php echo $form_id ?>').show();
        },
        type: 'POST'
    });
});

    $("#wpuser_login<?php echo $form_id ?>").click(function () {
        $.ajax({
        url: wpuser.wpuser_ajax_url+'?action=wpuser_login_action',
        data: $("#wpuser_login_form<?php echo $form_id ?>").serialize(),
        error: function (data) {
        },
        success: function (data) {
            var parsed = $.parseJSON(data);
            $('#upuser_error<?php echo $form_id ?>').html(parsed.message);
            $('#wpuser_errordiv<?php echo $form_id ?>').removeClass().addClass('alert alert-dismissible alert-' + parsed.status);
            $('#wpuser_errordiv<?php echo $form_id ?>').show();
            if (parsed.status == 'success') {
                $("#wpuser_login_form<?php echo $form_id ?>")[0].reset();
                if (wpuser.login_redirect == null || wpuser.login_redirect.length === 0) {
                    location.reload();
                } else {
                    window.location.href =wpuser.login_redirect;
                }
            }
        },
        type: 'POST'
    });
});

    $("#wpuser_forgot<?php echo $form_id ?>").click(function () {
        $.ajax({
        url: wpuser.wpuser_ajax_url+'?action=wpuser_forgot_action',
        data: $("#wpuser_forgot_form<?php echo $form_id ?>").serialize(),
        error: function (data) {
        },
        success: function (data) {
            var parsed = $.parseJSON(data);
            $('#upuser_error_forgot<?php echo $form_id ?>').html(parsed.message);
            $('#wpuser_errordiv_forgot<?php echo $form_id ?>').removeClass().addClass('alert alert-dismissible alert-' + parsed.status);
            if (parsed.status == 'success') {
                $("#wpuser_forgot_form<?php echo $form_id ?>")[0].reset();
            }
            $('#wpuser_errordiv_forgot<?php echo $form_id ?>').show();
        },
        type: 'POST'
    });
});

    $("#wp_login_btn<?php echo $form_id ?>").click(function () {
        $('#wp_login<?php echo $form_id ?>').modal();
        var modal = $("#wp_login<?php echo $form_id ?>"),
        dialog = modal.find('.modal-dialog');
    modal.css('display', 'block');
    // Dividing by two centers the modal exactly, but dividing by three
    // or four works better for larger screens.
    dialog.css("margin-top", Math.max(0, ($(window).height() - dialog.height()) / 2));
});

    $("#wp_user_profile_div_close").click(function () {
        $("#wp_user_profile_div").hide();
    });
    $(function () {
        var file_frame;

        $(".additional-user-image").on("click", function (event) {

            event.preventDefault();

            // If the media frame already exists, reopen it.
            if (file_frame) {
                file_frame.open();
                return;
            }

            // Create the media frame.
            file_frame = wp.media.frames.file_frame = wp.media({
            title: $(this).data("uploader_title"),
            button: {
                text: $(this).data("uploader_button_text"),
            },
            multiple: false
        });

        var current_id = this.id;

        // When an image is selected, run a callback.
        file_frame.on("select", function () {
            // We set multiple to false so only get one image from the uploader
            attachment = file_frame.state().get("selection").first().toJSON();
            //$(".user_meta_image").val(attachment.url);
            $("#img_" + current_id).val(attachment.url);
            $("#user_meta_image_attachment_id").val(attachment.id);


            // Do something with attachment.id and/or attachment.url here
        });

        // Finally, open the modal
        file_frame.open();
    });

    });
    </script>
<?php
}
