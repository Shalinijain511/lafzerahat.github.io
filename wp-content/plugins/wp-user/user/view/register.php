<div role="tabpanel" class="tab-pane <?php echo $register_class ?>"
     id="registerController<?php echo $form_id ?>">
    <div class="box">
        <div class="wpuser_form_header box-header with-border">
            <h3 class="box-title"><?php _e('Sign Up', 'wpuser') ?></h3>
        </div>
        <div class="box-body">
            <div style="display: none;" id="wpuser_errordiv_register<?php echo $form_id ?>"
                 class="alert alert-dismissible" role="alert"><label
                    id="wpuser_error_register<?php echo $form_id ?>"></label></div>
            <form method="post" id="google_form<?php echo $form_id ?>">
                <input name="wpuser_update_setting" type="hidden"
                       value="<?php echo wp_create_nonce('wpuser-update-setting'); ?>"/>
                <div class="row">
                    <div class="col-xs-12">
                        <?php
                        if (isset($atts['role']) && !empty($atts['role'])) {
                            echo '<input name="role" type="hidden"
                       value="' . $atts['role'] . '">';
                        }
                        do_action('wp_user_hook_register_form_header');
                        if (isset($atts['id']) && !empty($atts['id'])) {
                            echo '<input name="wpuser_form_id" type="hidden"
                       value="' . $atts['id'] . '">';
                            global $userplus;
                            $userplus_field_order = get_post_meta($atts['id'], 'userplus_field_order', true);
                            $form_fields = get_post_meta($atts['id'], 'fields', true);;
                            if ($userplus_field_order) {
                                $fields_count = count($userplus_field_order);
                                for ($i = 0; $i < $fields_count; $i++) {
                                    $key = $userplus_field_order[$i];
                                    $array = $form_fields[$key];
                                    echo profileController::edit_fields($key, $array, $wp_user_appearance_skin, $form_id);
                                }
                            }
                        } else {
                            foreach ($wp_user_options_signup_form as $array) {
                                echo profileController::edit_fields($array['meta_key'], $array, $wp_user_appearance_skin, $form_id);
                            }
                        }
                        do_action('wp_user_hook_register_form') ?>
                    </div>
                </div>
                <?php if (get_option('wp_user_tern_and_condition')) { ?>
                    <div class="row">
                        <div class="col-xs-12">
                            <div>
                                <input id="wp_user_term_condition" type="checkbox" name="wp_user_term_condition">
                                <?php _e('I agree to the', 'wpuser') ?> <a data-toggle="collapse" data-target="#wpuser_term"><?php _e('terms', 'wpuser') ?></a>
                                <div id="wpuser_term" class="collapse"><?php echo stripslashes(get_option('wp_user_show_term_data')) ?></div>
                            </div>
                            <br>
                        </div>
                    </div>

                <?php }

                if (get_option('wp_user_security_reCaptcha_enable') && !empty(get_option('wp_user_security_reCaptcha_secretkey'))) { ?>

                    <div class="row">
                        <div class="col-xs-12">
                            <div id="recaptcha<?php echo $form_id ?>" class="g-recaptcha"
                                 data-sitekey="<?php echo get_option('wp_user_security_reCaptcha_secretkey') ?>"></div>
                            <input type="hidden" title="Please verify this" class="required" name="keycode"
                                   id="keycode">
                        </div>
                    </div>
                <?php } ?>

                <div class="row">
                    <br>
                    <!-- /.col -->
                    <div class="col-xs-12">
                        <input type="button" class="wpuser_button btn <?php echo $wp_user_appearance_button_type ?> btn-primary"
                               id="wpuser_register<?php echo $form_id ?>" name="wpuser_register"
                               value="<?php _e('Sign Up', 'wpuser') ?>">

                    </div>
                    <div class="col-xs-12">
                        <?php do_action('wp_user_hook_register_form_footer') ?>
                    </div>
                    <!-- /.col -->
                </div>
            </form>
        </div>
        <div class="box-footer">
            <a aria-controls="loginController<?php echo $form_id ?>" role="tab" data-toggle="tab"
               href="#loginController<?php echo $form_id ?>" class="text-center"><?php _e('Sign In', 'wpuser') ?></a>
        </div>
    </div>
</div>