<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class profileController
{


    public static function get_attachment_image_by_url($url)
    {

        // Split the $url into two parts with the wp-content directory as the separator.
        $parse_url = explode(parse_url(WP_CONTENT_URL, PHP_URL_PATH), $url);

        // Get the host of the current site and the host of the $url, ignoring www.
        $this_host = str_ireplace('www.', '', parse_url(home_url(), PHP_URL_HOST));
        $file_host = str_ireplace('www.', '', parse_url($url, PHP_URL_HOST));

        // Return nothing if there aren't any $url parts or if the current host and $url host do not match.
        if (!isset($parse_url[1]) || empty($parse_url[1]) || ($this_host != $file_host)) {
            return;
        }

        // Now we're going to quickly search the DB for any attachment GUID with a partial path match.
        // Example: /uploads/2013/05/test-image.jpg
        global $wpdb;

        $prefix = $wpdb->prefix;
        $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM " . $prefix . "posts WHERE guid RLIKE %s;", $parse_url[1]));

        // Returns null if no attachment is found.
        return $attachment[0];
    }

    public static function edit_fields($key, $array, $form_type = 'default', $form_id = 0, $user_id = null)
    {
        global $userplus;
        $readonly = '';
        $image='';
        $res='';
        $value = (!empty($array['default_value'])) ? $array['default_value'] : '';        
        $wp_user_appearance_icon = get_option('wp_user_appearance_icon');

        if ($array['type'] == 'image_upload') {
            /*  $default_image = WPUSER_PLUGIN_URL."assets/images/profiledefault.png";
              $value = "<img src='".$default_image."' class='default'>";
            */
        }
        if (isset($user_id)) {
            $value = SELF::wpuser_profile_details($key, $user_id);


            if ($array['type'] == 'image_upload' && !in_array($array['meta_key'],array('user_meta_image','profile_pic'))) {
                if (empty($value)) {
                    $value = WPUSER_PLUGIN_URL . "assets/images/profiledefault.png";
                }
                $image ='<div class="col-sm-12">
                      <img class="img-responsive profile_background_pic" id="'.$array['meta_key'].'" src="'. $value . '"  alt="Image">
                    </div>';
            }
            if (isset($array['user_edit']) && !$array['user_edit']) {
                $readonly = 'readonly';
            }
        }
        $data = '';
        foreach ($array as $data_option => $data_value) {
            if (!is_array($data_value) && $data_option != 'edit_choices') {
                $data .= " data-$data_option='$data_value'";
            }
        }
$style='';
        $field = '<div class="col-xs-12">';
        $field .= ' <div class="' . $form_type . ' form-group has-feedback">';
        if ($form_type == 'float' || $form_type == 'block' || $form_type == 'rounded') {
            $style= "style='margin-bottom: 10px;'";
            $classLabel = ($form_type == 'float') ? 'col-xs-2' : '';
            $classLabel = ($form_type == 'rounded') ? 'col-xs-4' : $classLabel;
            $field .= '<label class="' . $classLabel . '" for="' . $array['meta_key'] . $form_id . '">';
            if ($form_type == 'rounded' && !$wp_user_appearance_icon && !empty($array['icon'])) {
                $field .= '<span style="margin-right: 10px;" class="skin_rounded">';
                $field .= '<span class="glyphicon ' . $array['icon'] . '"></span> ';
                $field .= '</span>';
            }
            $field .= isset($array['label']) ? $array['label'] :'';
            if (isset ($array['is_required']) && $array['is_required'] == 1) {
                $field .= "<span class='userplus-required'>*</span>";
            }

            $field .= '</label>';
        }
        if ($form_type == 'float') {
            $field .= '<div class="col-xs-10">';
        } else if ($form_type == 'rounded') {
            $field .= '<div class="col-xs-8">';
        }
        $readonly = '';

        switch ($array['type']) {

            case 'text':

                $field .= "<input $style type='text' class='form-control' name='" . $array['meta_key'] . "' id='" . $array['meta_key'] . "' value='" . $value . "' placeholder='" . $array['placeholder'] . "' $data $readonly />";
                if (!$wp_user_appearance_icon && !empty($array['icon']) && $form_type != 'rounded') {
                    $field .= '<span class="glyphicon ' . $array['icon'] . ' form-control-feedback"></span>';
                }
                break;

            case 'textarea':
                $field .= "<textarea  type='text' class='form-control' name='" . $array['meta_key'] . "' id='" . $array['meta_key'] . "' $data $readonly />$value</textarea>";
                break;

            case 'password':
                $field .= "<input $style type='password' class='form-control' name='" . $array['meta_key'] . "' id='" . $array['meta_key'] . "' placeholder='" . $array['placeholder'] . "' autocomplete='off'/ $data>";
                if (!$wp_user_appearance_icon && !empty($array['icon']) && $form_type != 'rounded') {
                    $field .= '<span class="glyphicon ' . $array['icon'] . ' form-control-feedback"></span>';
                }
                if (isset($array['add_confirm_password_field']) && $array['add_confirm_password_field'] && is_user_logged_in()) {
                    $field .= "<div class='userplus-clear'></div>";
                    $field .= "<label for='confirm_pass'>" . __('Confirm Password', 'wpuser') . "</label>";
                    $field .= "<input type='password' class='form-control' name='confirm_pass' id='confirm_pass' autocomplete='off' data-is_required=1 />";
                }
                break;
            case 'image_upload':
                /* $output = get_user_meta( $user_id, $key, true );
                 if(!is_array($output) && strpos(str_replace(' ','',$output),'">')===0)
                 {
                     $output = substr_replace(trim($output), "", 0,2);
                 }
                 $allowed_types = implode(',',$array['allowed_types']);
                 $field .= "<div class='userplus-image' data-remove_text='".__('Remove','wpuser')."'>".$value."</div>";
                 $field .= "<div class='userplus-image-upload' data-filetype='picture' data-allowed_extensions=".$allowed_types.">".$array['upload_button_text']."</div>";
                 $field .= "<input data-required='".$array['is_required']."' type='hidden' name='".$array['meta_key']."' id='".$array['meta_key']."' value='".$output."' />";
               */
                if(isset($user_id)) {
                    $field .= $image . '                      
                     <div class="input-group input-group-sm user_meta_image">
                        <input type="text" name="' . $array['meta_key'] . '" id="img_' . $array['meta_key'] . '" value="' . $value . '" class="form-control" />               
                    <span style="vertical-align: top;" class="input-group-btn">
                      <button type="button" id="' . $array['meta_key'] . '" class="additional-user-image btn btn-info btn-flat" value="Upload Image"/>Upload Image</button>
                    </span>
                   </div>
                   ';
                }
                break;


            case 'url':
                $field .= "<input type='text' class='form-control' name='" . $array['meta_key'] . "' id='" . $array['meta_key'] . "' value='" . $value . "' placeholder='" . $array['placeholder'] . "' $data $readonly />";
                if (!$wp_user_appearance_icon && !empty($array['icon']) && $form_type != 'rounded') {
                    $field .= '<span class="glyphicon ' . $array['icon'] . ' form-control-feedback"></span>';
                }
                break;

            case 'radio':
                if (isset($array['edit_choices'])) {
                    $array['edit_choices'] = explode("\r\n", $array['edit_choices']);
                    $field .= "<div class='userplus-radio-container' data-required='" . $array['is_required'] . "'>";
                    foreach ($array['edit_choices'] as $k => $v) {

                        $v = stripslashes($v);
                        $field .= "<label class='wpuser-radio'><span";
                        if (checked($v, $value, 0)) {
                            $res = 'checked';
                        }
                        $field .= '></span><input class="'.$res.'" type="radio" value="' . $v . '" name="' . $array['meta_key'] . '" ';
                        $field .= checked($v, $value, 0);
                        $field .= " />$v</label>";
                    }
                    $field .= "</div>";
                }
                break;

            case 'checkbox':
                if (isset($array['edit_choices'])) {
                    $field .= "<div class='userplus-checkbox-container' data-required='" . $array['is_required'] . "'>";
                    if (!empty($array['edit_choices']))
                        $array['edit_choices'] = explode("\r\n", $array['edit_choices']);
                    foreach ($array['edit_choices'] as $v) {
                        $v = stripslashes($v);
                        $field .= "<label class='userplus-checkbox'><span";
                        if ((is_array($value) && in_array($v, $value)) || $v == $value) {
                            $res = 'checked';
                        }
                        $field .= '></span><input class="'.$res.'" type="checkbox" value="' . $v . '" name="' . $array['meta_key'] . '[]" ';
                        $field .= " />$v</label>";
                    }
                    $field .= "</div>";
                }
                break;

            case 'select':

                $field .= "<select name='" . $array['meta_key'] . "' id='" . $array['meta_key'] . "' class='chosen-select' data-placeholder='" . $array['placeholder'] . "' $data >";
                $field .= "<option value=null>" . $array['default_value'] . "</option>";
                if (isset($array['edit_choices'])) {
                    $options = explode("\r\n", $array['edit_choices']);
                    $options_count = count($options);
                    for ($i = 0; $i < $options_count; $i++) {
                        $field .= "<option value='" . $options[$i] . "'" . selected($options[$i], $value, 0) . ">" . $options[$i] . "</option>";
                    }
                }
                $field .= "</select>";
                break;

            case 'multiselect':
                break;

        }
        if(isset($array['description']) && !empty($array['description'])){
            $field .= '<p>'.$array['description'].'</p>';

        }
        if ($form_type == 'float' || $form_type == 'rounded') {
            $field .= "</div>";
        }

        $field .= "</div>";
        $field .= "</div>";

        $field .= "<div class='userplus-clear'></div>";
        return $field;
    }



    public static function wpuser_profile_details( $field, $user_id ) {
        global $userplus;
        $user = get_userdata( $user_id );
        $output = '';
        if ($user != false) {
            switch($field){
                default:
                    $output = get_user_meta( $user_id, $field, true );
                    if(!is_array($output) && strpos(str_replace(' ','',$output),'">')===0)
                    {
                        $output = substr_replace(trim($output), "", 0,2);
                    }
                    break;
                case 'id':
                    $output = $user_id;
                    break;
                case 'display_name':
                    $output = $user->display_name;

                    break;
                case 'user_url':
                    $output = $user->user_url;
                    break;
                case 'user_email':
                    $output = $user->user_email;
                    break;
                case 'user_login':
                    $output = $user->user_login;
                    break;
                case 'role':
                    $user_roles = $user->roles;
                    $user_role = array_shift($user_roles);
                    $output = $user_role;
                    break;
            }
        }
        return $output;
    }

    public static function getNotification($recipient_id=0,$is_unread='',$type_of_notification=array(),$sender_id=0,$limit=5){
        global $wpdb;
        if (get_option('wp_user_disable_user_notification')!='1') {
            $condition = ' 1=1 ';
            $condition .= (!empty($recipient_id)) ? " AND recipient_id = $recipient_id" : ' ';
            $condition .= (!empty($is_unread)) ? " AND is_unread = $recipient_id" : ' ';
            $condition .= (!empty($sender_id)) ? " AND recipient_id = $sender_id" : ' ';
            $condition .= (!empty($type_of_notification)) ? " follower_id IN ('" . implode('\',\'', $type_of_notification) . "')" : ' ';
            $querystr = "
                                SELECT
                                 id,
                                 type_of_notification,
                                 title_html,
                                 body_html,
                                 is_unread,
                                 href,
                                 created_time
                                FROM
                                 " . $wpdb->prefix . "wpuser_notification
                                WHERE
                                 " . $condition . "
                                 ORDER by created_time DESC";
            //error_log($querystr);
            return $wpdb->get_results($querystr, ARRAY_A);
        }
        return array();
    }

    
}
