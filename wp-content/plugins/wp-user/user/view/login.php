<div role="tabpanel" class="tab-pane <?php echo $login_class ?>" id="loginController<?php echo $form_id ?>"
     class="login-box-body">
    <div class="box">
        <div class="wpuser_form_header box-header with-border">
            <h3 class="box-title"><?php _e('Sign In', 'wpuser') ?></h3>
            <a style="float: right" class="pull-right" href="#forgotController<?php echo $form_id ?>" aria-controls="forgotController<?php echo $form_id ?>"
               role="tab" data-toggle="tab"><?php _e('Forgot Password', 'wpuser') ?></a>
        </div>
        <div class="box-body">
        <div style="display: none;" id="wpuser_errordiv<?php echo $form_id ?>"
             class="alert alert-dismissible fade in" role="alert"><label
                id="upuser_error<?php echo $form_id ?>"></label></div>
        <form method="post" id="wpuser_login_form<?php echo $form_id ?>">
            <?php do_action('wp_user_hook_login_form_header') ?>
            <div class="row">
                <div class="col-xs-12">
            <?php
            foreach ($wp_user_options_signin_form as $array) {
                echo profileController::edit_fields($array['meta_key'], $array, $wp_user_appearance_skin, $form_id);
            }
            do_action('wp_user_hook_login_form')
            ?>
                    </div>
                </div>
            <div class="row">
                <!-- /.col -->
                <div class="col-xs-12">
                    <input type="button" id="wpuser_login<?php echo $form_id ?>" class="wpuser_button btn <?php echo $wp_user_appearance_button_type ?> btn-primary"
                           name="wpuser_login" value="<?php _e('Sign In', 'wpuser') ?>">
                </div>
                <div class="col-xs-12">
                    <?php do_action('wp_user_hook_login_form_footer') ?>
                </div>
                <!-- /.col -->
            </div>
        </form>
            </div>
        <div class="box-footer">
        <?php if (!$wp_user_register_enable) { ?>
            <a style="float: right" href="#registerController<?php echo $form_id ?>"
               aria-controls="registerController<?php echo $form_id ?>" role="tab" data-toggle="tab"
               class="text-center"><?php _e('Sign Up', 'wpuser') ?></a>
        <?php } ?>
        </div>

    </div>
</div>