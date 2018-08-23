<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

add_filter('get_avatar', 'wp_user_custom_avatar', 1, 5);

function wp_user_custom_avatar($avatar, $id_or_email, $size, $default, $alt)
{
    $user = false;


    if (is_numeric($id_or_email)) {

        $id = (int)$id_or_email;
        $user = get_user_by('id', $id);

    } elseif (is_object($id_or_email)) {

        if (!empty($id_or_email->user_id)) {
            $id = (int)$id_or_email->user_id;
            $user = get_user_by('id', $id);
        }

    } else {
        $user = get_user_by('email', $id_or_email);
    }

    if ($user && is_object($user)) {

        if ($user->data->ID == '1') {
            global $current_user, $wp_roles;
            $attachment_url = esc_url(get_the_author_meta('user_meta_image', get_current_user_id()));
            $attachment_id = get_attachment_image_by_url($attachment_url);
            // retrieve the thumbnail size of our image
            $image_thumb = wp_get_attachment_image_src($attachment_id, 'thumbnail');
            if (!empty($image_thumb[0])) {
                $avatar = $image_thumb[0];
            } else if (!empty($attachment_url)) {
                $avatar = $attachment_url;
            } else
                $avatar = $avatar;
            if (!empty($attachment_url))
                $avatar = "<img alt='{$alt}' src='{$avatar}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
        }

    }

    return $avatar;
}

function get_attachment_image_by_url($url)
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

//Only Access current user media files. 'administrator', 'author' access all user media files
add_filter('ajax_query_attachments_args', "user_restrict_media_library");
function user_restrict_media_library($query)
{
    $user_id = get_current_user_id();
    if ($user_id) {
        if (!(current_user_can('edit_others_pages'))) {
            $query['author'] = $user_id;
        }
    }

    return $query;
}

//Setup role for upload file
add_action('admin_init', 'wpuser_setup_author_role');
function wpuser_setup_author_role()
{
    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        $user_role = $user->roles ? $user->roles[0] : false;
        $contributor = get_role($user_role);
        $contributor->add_cap('upload_files');
    }
}

add_filter('authenticate', 'wpuser_authenticate', 10, 2);
function wpuser_authenticate($user, $username)
{
    //Get user object
    $user = get_user_by('login', $username);
    if (empty($user)) {
        $user = get_user_by('email', $username);
    }

    if (empty($user)) {
        return null;
    }

    //Get stored value
    $stored_value = get_user_meta($user->ID, 'wp-approve-user', true);
    //$stored_value=0;
    $wpuser_activation_key = get_user_meta($user->ID, 'wpuser_activation_key', true);

    if (!empty($user) && ($stored_value == 2 || ($stored_value == 5) || $stored_value == 3)) {
        //User note found, or no value entered or doesn't match stored value - don't proceed.
        remove_action('authenticate', 'wp_authenticate_username_password', 20);
        remove_action('authenticate', 'wp_authenticate_email_password', 20);
        //Create an error to return to user
        $error = ($stored_value == 2 && !empty($wpuser_activation_key)) ? __("Access denied : Waiting for approval.
                     Please Activate Your Account. Before you can login, you must active your account with the link sent to your email address", 'wpuser')
            : __("Access denied : Waiting for admin approval", 'wpuser');
        return new WP_Error('denied', __($error));
    }
    $wp_user_login_limit_enable = get_option('wp_user_login_limit_enable');
    if (isset($wp_user_login_limit_enable) && !empty($wp_user_login_limit_enable)) {
        $confirmResponse = wpuserAjax::confirmIPAddress($_SERVER["REMOTE_ADDR"], $user->user_login);
        if ($confirmResponse['status'] == 1) {
            $wp_user_login_limit_time = get_option('wp_user_login_limit_time');
            if (empty($wp_user_login_limit_time)) {
                $wp_user_login_limit_time = 30;
            }
            $loginLog['message'] = $error = __('Access denied for', 'wpuser') . " " . $wp_user_login_limit_time . " " . __('minuts', 'wpuser');
            $loginLog['status'] = "Failed";
            wpuserAjax::loginLog($loginLog);
            return new WP_Error('denied', __($error));

        }
    }
    //Make sure you return null
    return null;
}

add_filter('login_errors', function ($error) {
    //error_log(print_r($_SERVER,1));
    $wp_user_login_limit_enable = get_option('wp_user_login_limit_enable');
    if (isset($wp_user_login_limit_enable) && !empty($wp_user_login_limit_enable)) {
        wpuserAjax::addLoginAttempt($_SERVER["REMOTE_ADDR"]);
    }
    return $error;
});

/*add_filter('wpuser_filter_header_notification', 'wpuser_filter_header_notification', 10,1);
function wpuser_filter_header_notification($notifications=array())
{
    $notification = __(' User filter', 'wpuser');
    $mynotifications = array(
        'notification' => $notification,
        'icon' => 'fa fa-users text-red'
    );
    array_push($notifications,$mynotifications);
    return $notifications;
}*/

add_filter('wpuser_filter_header_notification_menu', 'wpuser_filter_header_notification_menu', 10, 1);
function wpuser_filter_header_notification_menu($notifications = array())
{
     $mynotifications = array(
         'name' => __('Dashboard', 'wpuser'),
         'url' => ''
     );
     array_push($notifications, $mynotifications);

    if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        $myaccount_page_id = get_option('woocommerce_myaccount_page_id');
        if ($myaccount_page_id) {
            $myaccount_page_url = get_permalink($myaccount_page_id);
            $mynotifications = array(
                'name' => __('Orders', 'wpuser'),
                'url' => $myaccount_page_url . '/orders'
            );
            array_push($notifications, $mynotifications);
        }
    }

    return $notifications;
}


add_action('wpuser_profile_header', 'wpuser_profile_header', 10,1);
function wpuser_profile_header($atts)
{
    if (is_user_logged_in()) {
        $notifications = array();
        $notifications_menu = array();

        $user_id = get_current_user_id();
        $title = (get_user_meta($user_id, 'user_title', true));
// retrieve the thumbnail size of our image
        $attachment_url = esc_url(get_the_author_meta('user_meta_image', $user_id));
        $attachment_id = profileController::get_attachment_image_by_url($attachment_url);
// retrieve the thumbnail size of our image

        $image_thumb = wp_get_attachment_image_src($attachment_id, 'thumbnail');
// return the image thumbnail
        if (!empty($image_thumb[0])) {
            $wp_user_profile_img = $image_thumb[0];
        } else if (!empty($attachment_url)) {
            $wp_user_profile_img = $attachment_url;
        } else {
            $args = get_avatar_data($user_id);
            if (!empty($args['url']))
                $wp_user_profile_img = $args['url'];
            else
                $wp_user_profile_img = WPUSER_PLUGIN_URL . 'assets/images/wpuser.png';
        }

        //  $name = get_the_author_meta('first_name', $user_id) . " " . get_the_author_meta('last_name', $user_id);
        // if (empty(str_replace(' ', '', $name))) {
        $user_info = get_userdata($user_id);
        $name = $user_info->display_name;
        if (empty($name)) {
            $name = $user_info->user_nicename;
        }
        if (empty($name)) {
            $name = $user_info->user_login;
        }
        $full_name = get_the_author_meta('first_name', $user_id) . " " . get_the_author_meta('last_name', $user_id);
        if ((empty(str_replace(' ', '', $full_name)))) {
            $full_name = $name;
        }
        $notifications = apply_filters('wpuser_filter_header_notification', $notifications);
        $notifications_menu = apply_filters('wpuser_filter_header_notification_menu', $notifications_menu);

        $wp_user_appearance_skin_color = (isset($atts['skin']) && !empty($atts['skin'])) ? $atts['skin'] :
            (get_option('wp_user_appearance_skin_color') ? get_option('wp_user_appearance_skin_color') : 'blue');

        ?>
        <header class="skin-<?php echo $wp_user_appearance_skin_color ?>">
            <div class="main-header wpuser-custom-header">
                <div class="">
                    <nav class="navbar navbar-static-top wpuser-custom-header-nav" role="navigation">
                        <!-- Sidebar toggle button-->
                        <div class="">
                            <?php if (!empty($notifications_menu)) {
                                foreach ($notifications_menu as $menu) {
                                    echo '<a target="_blank" href="' . $menu['url'] . '" class="sidebar-toggle"> ' . $menu['name'] . '</a>';
                                }
                            }
                            ?>
                            <?php do_action('wpuser_header_notification_menu_link'); ?>
                        </div>
                        <div  class="navbar-right" style="margin-right: 0px;">

                            <ul class="nav navbar-nav">
                                <?php
                                do_action('wpuser_header_notification_menu');
                                $notification_count = count($notifications);
                                if ($notification_count > 0) {
                                    ?>
                                    <li id="notification_dropdown" class="dropdown notifications-menu">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="fa fa-bell-o"></i>
                                            <span class="label label-warning notification_count" id="notification_count" val="<?php echo $notification_count ?>"><?php echo $notification_count ?></span>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li class="header"><?php _e('You have <span class="notification_count">' . $notification_count . '</span> notifications', 'wpuser') ?></li>
                                            <li>
                                                <!-- inner menu: contains the actual data -->
                                                <div class="slimScrollDiv"
                                                     style="position: relative; overflow: hidden; width: auto; height: 200px;">
                                                    <ul class="menu"
                                                        style="overflow: hidden; width: 100%; height: 200px;">
                                                        <?php foreach ($notifications as $notification) {
                                                            $notification_call= ( $notification['is_unread']==1)? $notification_call="alert-info" : "";
                                                            if($notification['type_of_notification']=='follow'){
                                                                $notification_icon="fa fa-users";
                                                            }else  if($notification['type_of_notification']=='order'){
                                                                $notification_icon="fa fa-shopping-cart";
                                                            }else  if($notification['type_of_notification']=='support'){
                                                                $notification_icon="fa fa-support";
                                                            }else  if($notification['type_of_notification']=='rate'){
                                                                $notification_icon="fa fa-star";
                                                            }else  if($notification['type_of_notification']=='comment'){
                                                                $notification_icon="fa fa-comment";
                                                            }else  if($notification['type_of_notification']=='post'){
                                                                $notification_icon="fa  fa-thumb-tack";
                                                            }else{
                                                                $notification_icon="fa fa-check";
                                                            }
                                                            echo ' 
                                                <li class="'.$notification_call.' alert-dismissible notification notification_' .$notification['id'] .'" onclick="readNotification(' .$notification['id'] .')">
                                                <a  href="#" ><i class="'.$notification_icon.'"></i> '. $notification['title_html'] . ' </a>   
                                                </li>';
                                                        } ?>

                                                    </ul>
                                                    <div class="slimScrollBar"
                                                         style="width: 3px; position: absolute; top: 0px; opacity: 0.4; display: block; border-radius: 0px; z-index: 99; right: 1px; background: rgb(0, 0, 0);"></div>
                                                    <div class="slimScrollRail"
                                                         style="width: 3px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 0px; opacity: 0.2; z-index: 90; right: 1px; background: rgb(51, 51, 51);"></div>
                                                </div>
                                            </li>
                                            <li class="footer"><a onclick="getNotification()">View all</a></li>
                                        </ul>
                                    </li>
                                <?php } ?>
                                <li class="dropdown user user-menu wpuser-custom-header-user">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <img src="<?php echo $wp_user_profile_img ?>"
                                             class="user-image wpuser_profile_img"
                                             alt="User Image">
                                        <span class="hidden-xs"><?php echo $name ?> <i class="caret"></i></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <!-- User image -->
                                        <li class="user-header bg-light-<?php echo $wp_user_appearance_skin_color ?>">
                                            <img src="<?php echo $wp_user_profile_img ?>"
                                                 class="img-circle wpuser_profile_img"
                                                 alt="User Image">
                                            <p>
                                                <span class="wpuser_profile_name"><?php echo $full_name ?></span>
                                                <small><?php echo $title ?></small>
                                                <small><?php
                                                    $info['atts'] = $atts;
                                                    $user_value = new \stdClass();
                                                    $user_value->ID = $user_id;
                                                    $info['value'] =$user_value;
                                                    do_action('wp_user_hook_member_profile_icon',$info)?></small>
                                            </p>
                                        </li>

                                        <li class="user-footer">
                                            <div class="pull-left">
                                                <?php
                                                $wp_user_page_permalink = get_permalink(get_option('wp_user_page'));
                                                ?>
                                                <a href="<?php echo $wp_user_page_permalink ?>"
                                                   class="btn btn-default btn-flat">
                                                    <?php _e('Profile', 'wpuser'); ?>
                                                </a>
                                            </div>
                                            <div class="pull-right">
                                                <?php echo '<a class="btn btn-default btn-flat" href="' . wp_logout_url(get_permalink()) . '" title="">';
                                                _e('Logout', 'wpuser');
                                                echo '</a>';
                                                ?>
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </header>       
        <?php
        $isUserLogged = (is_user_logged_in()) ? 1 : 0;
        $localize_script_data = array(
            'wpuser_ajax_url' => admin_url('admin-ajax.php'),
            'wpuser_update_setting' => wp_create_nonce('wpuser-update-setting'),
            'wpuser_site_url' => site_url(),
            'plugin_url' => WPUSER_PLUGIN_URL,
            'wpuser_templateUrl' => WPUSER_TEMPLETE_URL,
            'plugin_dir' => WPUSER_PLUGIN_DIR,
            'isUserLogged' => $isUserLogged,
        );
        wp_enqueue_script('wpusernotification', WPUSER_PLUGIN_URL . "assets/js/user_notification.min.js");
        wp_localize_script('wpusernotification', 'wpuser', $localize_script_data);

    }
}

/*add_action('wpuser_setting_appearance','wpuser_setting_appearance');
function wpuser_setting_appearance(){
    echo '<div class="form-group">
                <label>Color picker:</label>
                <input type="text" class="form-control my-colorpicker1 colorpicker-element">
              </div>
              
              <div class="input-group my-colorpicker2 colorpicker-element">
                  <input type="text" class="form-control">

                  <div class="input-group-addon">
                    <i></i>
                  </div>
                </div>';
    echo '<script>
  //Colorpicker
    jQuery(\'.my-colorpicker1\').colorpicker();
    jQuery(\'.my-colorpicker2\').colorpicker();

</script>';
}
*/

// Start Restrict Content filter
add_filter('the_content', 'wpuser_the_content');

function wpuser_the_content($content)
{
    global $post;
    $wpuser_user_role = get_post_meta($post->ID, 'wpuser_user_role', true);

    if (!empty($wpuser_user_role)) {
        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            if (isset($wpuser_user_role) && !empty($wpuser_user_role) && count(array_intersect($user->roles, explode(",", strtolower($wpuser_user_role)))) >= 1) {
                return $content;
            }
            if (isset($wpuser_user_role) && $wpuser_user_role == 'logged_in') {
                return $content;
            }

            return __("You do not have permission to access this content", 'wpuser');

        } else {
            $message = __('We’re sorry. You do not have permission to access this content. Please sign In to be granted access.', 'wpuser');
            return $message . " " . do_shortcode("[wp_user popup='1' width='700px']");
        }
    } else {
        return $content;
    }
}
// End Restrict Content filter

add_action('wpuser_addNotification', 'wpuser_addNotification');
function wpuser_addNotification($notification=array()){
    if (get_option('wp_user_disable_user_notification')!='1') {
        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'wpuser_notification', $notification);
    }
}

add_action('wpuser_deleteNotification', 'wpuser_deleteNotification');
function wpuser_deleteNotification($notification=array()){
    global $wpdb;
    $wpdb->delete($wpdb->prefix . 'wpuser_notification', $notification);
}

add_action( 'woocommerce_order_status_changed', 'wpuser_notification_woocommerce_orde', 99, 3 );
function wpuser_notification_woocommerce_orde( $order_id, $old_status, $new_status ){
    if (get_option('wp_user_disable_user_notification')!='1' && get_option('wp_user_disable_user_notification_order')!='1') {
        if ($new_status == "completed" || $new_status == 'refunded') {
            $order = wc_get_order($order_id);
            $notification['recipient_id'] = $order->post->post_author;
            $notification['type_of_notification'] = 'order';
            $notification['title_html'] = __(" Order no $order_id has been $new_status", 'wpuser');
            $myaccount_page_id = get_option('woocommerce_myaccount_page_id');
            $myaccount_page_url = ($myaccount_page_id) ? $myaccount_page_url = get_permalink($myaccount_page_id) . 'view-order/' . $order_id : '';
            $notification['href'] = $myaccount_page_url;
            do_action('wpuser_addNotification', $notification);
        }
    }
}

add_action( 'comment_post', 'wpuser_notification_comment_post', 10, 2 );
function wpuser_notification_comment_post( $comment_ID, $comment_approved ) {
    if (get_option('wp_user_disable_user_notification')!='1' && get_option('wp_user_disable_user_notification_comment')!='1' ) {
        if (1 === $comment_approved) {
            $comment=get_comment( $comment_ID, 'ARRAY_A' );
            $notification['sender_id'] = $comment['user_id'];
            $comment_post_ID=$comment['comment_post_ID'];
            $content_post = get_post($comment_post_ID);
            $post_title = $content_post->post_title;
            $notification['recipient_id'] = get_post_field( 'post_author', $content_post );
            $notification['type_of_notification'] = 'comment';
            $notification['title_html'] = __("A new comment on the post $post_title", 'wpuser');
            $comment_author=$comment['comment_author'];
            $notification['body_html'] = __("$comment_author has been commented on your post $post_title", 'wpuser');
            $notification['href'] =get_permalink($comment_post_ID);
            do_action('wpuser_addNotification', $notification);
        }
    }
}