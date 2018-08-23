var $ = jQuery.noConflict();
$('document').ready(function () {

    $('#demo').jplist({
        itemsBox: '.list'
        , itemPath: '.list-item'
        , panelPath: '.jplist-panel'
    });

    $('#login_log').jplist({
        itemsBox: '.list-login'
        , itemPath: '.list-item-login'
        , panelPath: '.jplist-panel-login'
    });

    $("#member_list_button").click(function () {
        $("#wpuser_member_list").css("display", "block");
        $("#wpuser_member_profile").css("display", "none");
    });

});

function sendMail(id, name) {
    $("#wpuser_mail_to_userid").val(id);
    $("#wpuser_mail_to_name").html(name);
    $("#wpuser_myModal").modal();
    var modal = $("#wpuser_myModal"),
        dialog = modal.find('.modal-dialog');
    modal.css('display', 'block');
    // Dividing by two centers the modal exactly, but dividing by three
    // or four works better for larger screens.
    // dialog.css("margin-top", Math.max(0, ($(window).height() - dialog.height()) / 2));
}

$(".wpuser_sendmail").click(function () {
    $("#wpuser_myModal").modal();
    var modal = $("#wpuser_myModal"),
        dialog = modal.find('.modal-dialog');
    modal.css('display', 'block');
    // Dividing by two centers the modal exactly, but dividing by three
    // or four works better for larger screens.
    // dialog.css("margin-top", Math.max(0, ($(window).height() - dialog.height()) / 2));
});


function viewProfile(id) {
    $.ajax({
        type: "post",
        dataType: "json",
        url: wpuser_member.wpuser_ajax_url+'?action=wpuser_user_details',
        data: 'id=' + id + '&wpuser_update_setting='+wpuser_member.wpuser_update_setting,
        success: function (response) {
            if (response.status == 0)
                $("#response_message").html('<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h4><i class="icon fa fa-ban"></i> Error!</h4>' + response.message + '</div>');
            else if (response.status == 1) {
                $("#wpuser_member_list").css("display", "none");
                $("#wpuser_member_profile").css("display", "block");
                $(".wpuser_profile_name").html(response.name);
                $("#wpuser_mail_to_name").html(response.name);
                $("#wpuser_profile_title").html(response.labels);
                $(".wpuser_mail_to_userid").val(response.id);
                $("#wpuser_mail_to_userid").val(response.id);
                $('#wpuser_profile_image').attr('src', response.wp_user_profile_img);

                var user_row = '';
                $.each(response.user_info, function (i, val) {
                    if(i=='wpuser_profile_strength') {
                        $('.wpuser_profile_strength').html(val + '%');
                        $('.wpuser_profile_strength').css("width",val+ '%');
                    }else{
                        user_row = user_row + '<tr class="user_info"><td>' + i + '</td><td>' + val + '</td></tr>';
                    }
                });
                $(".wpuser_user_info").html(user_row);
                var user_header = '';
                $.each(response.header_block_info, function (ar, arval) {
                    var header_attr= ' ';
                    var header_onclick =' ';
                    if(arval["url"]!='#'){
                        header_attr='target="_blank" href="' + arval["url"] + '"';
                    }
                    if(arval["id"]=='wpuser_profile_follower' || arval["id"]=='wpuser_profile_following'){
                        header_onclick ='onclick="getFollower(\''+arval["type"]+'\')"';
                    }
                    user_header = user_header + '<div class="navbar-header"><a class="navbar-brand fontfollow"  '+header_attr+' style="margin:0px;"'+header_onclick+' ><i class="' + arval["icon"] + '"> ' + arval["name"] + '(' + arval["count"] + ')</i></a> </div>';
                });
                $(".wpuser_user_header").html(user_header);
                $("#wpuser_member_header").css("background-image", 'url("' + response.wp_user_background_img + '")');
                $("#profile_follow_button").html(response.user_header_follow_button);
                $("#wpuser_profile_badge").html(response.user_badge);
            }
        }
    });
    $('#wpuser_followModal').modal('hide');
}
$("#wpuser_send_mail").click(function () {
     if(wpuser_member.wp_user_security_reCaptcha_enable){
        if (grecaptcha.getResponse() == '') {
            $('#wpuser_errordiv_send_mail').html("Please verify Captcha");
            $('#wpuser_errordiv_send_mail').removeClass().addClass('alert alert-dismissible alert-warning');
            $('#wpuser_errordiv_send_mail').show();
            return false;
        }
     }
    $.ajax({
        url: wpuser_member.wpuser_ajax_url+'?action=wpuser_send_mail_action',
        data: $("#google_form").serialize(),
        error: function (data) {
        },
        success: function (data) {
            var parsed = $.parseJSON(data);
            $('#wpuser_errordiv_send_mail').html(parsed.message);
            $('#wpuser_errordiv_send_mail').removeClass().addClass('alert alert-dismissible alert-' + parsed.status);
            if (parsed.status == 'success') {
                $("#google_form")[0].reset();
            }
            $('#wpuser_errordiv_send_mail').show();
        },
        type: 'POST'
    });
});
