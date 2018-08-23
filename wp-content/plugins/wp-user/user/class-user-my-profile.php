<?php

class WPUserMyProfile
{
    public function __construct()
    {

    }

    static function my_account($atts = array())
    {
        global $current_user, $wp_roles,$wp_user_appearance_button_type;


        $wp_user_profile_field['basic'] =
            array(
                'title' => __('Basic Information', 'wpuser'),
                'fields' => array(
                    'first_name' => array(
                        'label' => __('First Name', 'wpuser'),
                        'icon' => '',
                        'description' => '',
                        'type' => 'text',
                    ),
                    'last_name' => array(
                        'label' => __('Last Name', 'wpuser'),
                        'icon' => '',
                        'description' => '',
                        'type' => 'text',
                    ),
                    'user_email' => array(
                        'label' => __('Email', 'wpuser'),
                        'icon' => '',
                        'description' => '',
                        'type' => 'email',
                        'required' => 'required'
                    ),
                    'user_url' => array(
                        'label' => __('Website', 'wpuser'),
                        'icon' => '',
                        'description' => '',
                        'type' => 'text',
                    ),
                    'description' => array(
                        'label' => __('Description', 'wpuser'),
                        'description' => '',
                        'icon' => '',
                        'type' => 'textarea'
                    )
                )
            );


        if (isset($atts['id']) && !empty($atts['id'])) {
            //Validation
            $userplus_field_order = get_post_meta($atts['id'], 'userplus_field_order', true);
            $form_fields = get_post_meta($atts['id'], 'fields', true);;
            if ($userplus_field_order) {
                $fields_count = count($userplus_field_order);
                for ($i = 0; $i < $fields_count; $i++) {
                    $key = $userplus_field_order[$i];
                    $array = $form_fields[$key];
                    if (!in_array($array['type'], array('image_upload')) && !in_array($array['meta_key'],
                            array('user_login', 'user_pass', 'user_url', 'first_name', 'description', 'user_email', 'last_name'))
                    ) {
                        $profile_fields[$array['meta_key']] = array(
                            'label' => $array['label'],
                            'icon' => $array['icon'],
                            'description' => (isset($array['description']) && !empty($array['description'])) ? $array['description'] : '',
                            'type' => $array['type'],
                        );
                    }
                }
            }

            if (!empty($profile_fields)) {
                $wp_user_profile_field['advanced'] = array(
                    'title' => __('Advanced Information', 'wpuser'),
                    'fields' => $profile_fields
                );
            }
        }

        do_action('wp_user_profile_my_account_header');


        $wp_user_profile_field_filter = apply_filters('wp_user_profile_field_filter', $wp_user_profile_field);

        foreach ($wp_user_profile_field_filter as $key => $array) {
            echo '<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default">
          <div class="panel-heading" role="tab" id="headingOne">
            <label class="panel-title">
              <a role="button" data-toggle="collapse" data-parent="#accordion" href="#my_accout_collapse' . $key . '" aria-expanded="true" aria-controls="collapseOne">';
            echo $array['title'];
            echo '</a>
            </label>
          </div>
          <div id="my_accout_collapse' . $key . '" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                    <div class="panel-body">
                    <div class="row">
                    ';
            if ($key == 'basic') {
                echo '<div class="form-group col-md-6">
                     <label for="First name" class=" control-label">';
                _e('Username', 'wpuser');
                echo ':</label>
                     <label id="" class="" style="color:Gray !important">' . $current_user->user_login . '</label>
                  </div>';

                echo '<div class="form-group col-md-6">
                     <label for="First name" class=" control-label">';
                _e('Display name', 'wpuser');
                echo ':</label>
                     <label id="" class="text-muted" style="color:Gray !important">' . $current_user->display_name . '</label>
                  </div>';
            }


            foreach ($array['fields'] as $key => $value) {
                $textValue = get_the_author_meta($key, get_current_user_id());
                if ($value['type'] != 'password' && !empty($textValue)) {
                    $icon = (!get_option('wp_user_appearance_icon') && !empty($value['icon'])) ? '<i class="' . $value['icon'] . '"> </i> ' : '';
                    $class = ($value['type'] == 'textarea') ? 'col-md-12' : 'col-md-6';
                    $link_open = ($value['type'] == 'url') ? "<a class='wpuser_profile_url_' . $key . '' href='" . $textValue . "' target='_blank'>" : '';
                    $link_close = (!empty($link_open)) ? '</a>' : '';
                    echo '<div class="form-group ' . $class . '">
                     <label for="First name" class=" control-label">' . $link_open . $icon . $link_close . $value['label'] . ':</label>
                     <label id="' . $key . '" class="text-muted wpuser_profile_' . $key . '" style="color:Gray !important">' . $textValue . '</label>
                  </div>';
                }
            }
            echo '</div>
          </div>
          </div>
          </div>
          </div>';
        }
        do_action('wp_user_profile_my_account_footer');
    }

    static function edit_profile($atts = array())
    {
        global $wpdb;
        global $current_user, $wp_roles, $wp_user_appearance_button_type;

        $wp_user_login_limit_password = get_option('wp_user_login_limit_password');
        $wp_user_login_limit_password_enable = get_option('wp_user_login_limit_password_enable');
        $wp_user_login_password_valid_message = (isset($wp_user_login_limit_password_enable) && isset($wp_user_login_limit_password)) ?
            get_option('wp_user_login_password_valid_message') : '';

        include('view/option.php');

        // print_r($wp_user_options_signup_form);
        // print_r($atts);

        $form_id = $atts['form_id'];
        $wp_user_appearance_icon = (isset($atts['icon'])) ? $atts['icon'] : get_option('wp_user_appearance_icon');
        $wp_user_appearance_skin = (isset($atts['skin']) && !empty($atts['skin'])) ? $atts['skin'] :
            (get_option('wp_user_appearance_skin') ? get_option('wp_user_appearance_skin') : 'default');
        $wp_user_register_enable = get_option('wp_user_disable_signup');
        $user_id = get_current_user_id();

        echo '<div style="display: none;" id="wpuser_errordiv_register">         
            </div>';

        echo '<form method="post" id="google_form">
                <input name="wpuser_update_setting" type="hidden"
               value="' .$atts['wpuser_update_setting_nonce'] . '"/>';

        do_action('wp_user_hook_myprofile_form_header');
        $wpuser_form_id = get_user_meta($user_id, 'wpuser_form_id', true);
        if (isset($wpuser_form_id) && !empty($wpuser_form_id)) {
            $atts['id']=$wpuser_form_id;
        }
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
                    if ($key != 'user_login')
                        echo profileController::edit_fields($key, $array, $wp_user_appearance_skin, $form_id, $user_id);
                }
            }
        } else {
            foreach ($wp_user_options_my_profile_form as $array) {
                echo profileController::edit_fields($array['meta_key'], $array, $wp_user_appearance_skin, $form_id, $user_id);
            }
        }
        do_action('wp_user_hook_myprofile_form');

        $button_name = __('Save', 'wpuser');

        echo '<div class="row">
                    <!-- /.col -->
                    <div class="col-xs-12">
                        <input type="button" class="wpuser_button btn btn-primary '.$wp_user_appearance_button_type.' wpuser-custom-button"
                               id="wpuser_update_profile_button" name="wpuser_register"
                               value="' . $button_name . '">        
                 </div>
        </div>
        </form>';
        ?>
        <script>
            var $ = jQuery.noConflict();

         
        </script>
<?php
    }

    static function address($atts = array())
    {
        if (class_exists('WC_Admin_Profile')) {
            echo ' 
   <div style="display: none;" id="wp_user_address_div" class="alert alert-dismissible fade in" role="alert"><label id="wp_user_address_label"></label>
                        <button id="wp_user_address_div_close" class="close" type="button">
                          <span aria-hidden="true">&times;</span>
                      </button>
                         </div>
                          <form  id="wp_user_address_field_form" class="" name="wp_user_address_field_form" method="post" action="">
                          <div class="row">';
            $array = WC_Admin_Profile::get_customer_meta_fields();
            foreach ($array as $array) {
                echo '<div class="col-md-6">';
                echo '<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default">
          <div class="panel-heading" role="tab" id="headingOne">
            <label class="panel-title">
              <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse" aria-expanded="true" aria-controls="collapseOne">';
                echo $array['title'];
                echo '</a>
            </label>
          </div>
          <div id="collapse" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
            <div class="panel-body">';
                foreach ($array['fields'] as $key => $value) {
                    echo '<div class="form-group"> <label>' . $value['label'] . '</label>';
                    if (empty($value['type'])) {
                        echo '<input type="text" class="form-control" id="' . $key . '" placeholder="' . $value['label'] . '" name="' . $key . '" value="' . get_user_meta(get_current_user_id(), $key, true) . '">';
                    }
                    if (($value['type'] == 'select')) {
                        echo '<select class="form-control" id="' . $key . '"  name="' . $key . '">';
                        foreach ($value['options'] as $optionKey => $optionValue) {
                            $selected = (get_user_meta(get_current_user_id(), $key, true) == $optionKey) ? 'selected' : '';
                            echo '<option id="' . $optionKey . '" ' . $selected . ' value="' . $optionKey . '">' . $optionValue . '</option>';
                        }
                        echo '</select>';

                    }
                    echo '<p>' . $value['description'] . '</p>';
                    echo '</div>';
                }
                echo '</div>
          </div>
        </div>
                </div>';
                echo '</div>';
            }
            echo '</div>
      <input name="wpuser_action" type="hidden" value="address_wp_user">
        <input name="wpuser_update_setting" type="hidden" value="' . $atts['wpuser_update_setting_nonce'] . '"/> 
        <input type="submit" id="wp_user_address_field_submit" class="wpuser_button btn '.$wp_user_appearance_button_type.' btn-primary wpuser-custom-button" name="wpuser_address" value="Save">      
      </form>';         
            
        }
    }

    static function contact_us($atts = array())
    {
        global $wp_user_appearance_button_type;

        echo '<div class="row">
                <div class="col-sm-12">
                <div style="display:none;" id="wp_user_contact_div">
                </div>                                
                <form  id="wp_user_profile_contact_form" class="form-horizontal" name="wp_user_profile_contact_form">
                <input name="wpuser_update_setting" type="hidden" value="' .$atts['wpuser_update_setting_nonce'] . '"/> 

                  <div class="form-group">                  

                    <div class="col-sm-12">
                      <input type="text" class="form-control" id="wp_user_email_subject" name="wp_user_email_subject" placeholder="Subject" required>
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-sm-12">
                      <textarea placeholder="Mail Content" id="wp_user_email_content" name="wp_user_email_content" required> </textarea>
                    </div>
                    </div>
             
                  <div class="form-group">
                    <div class="col-sm-offset-9">
                <label id="wp_user_profile_contact_submit" class="wpuser_button btn '.$wp_user_appearance_button_type.' btn-primary wpuser-custom-button">';
            _e('Send', 'wpuser');
        echo '</label>';
        echo '</div>
              </div>
              </form>
              </div>
              </div>';
    }
    
    public static function tab_content_function($tab_content){
        echo apply_filters( 'the_content', $tab_content);
    }

    public static function tab_link_function(){
        echo 'tab_function';
    }

    public static function array_sort($array, $on, $order=SORT_ASC){

        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }

}