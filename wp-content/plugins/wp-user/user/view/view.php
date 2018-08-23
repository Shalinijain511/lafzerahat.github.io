<?php
$wp_user_appearance_icon = (isset($atts['icon'])) ? $atts['icon'] : get_option('wp_user_appearance_icon');
$wp_user_appearance_skin = (isset($atts['layout']) && !empty($atts['layout'])) ? $atts['layout'] :
    (get_option('wp_user_appearance_skin') ? get_option('wp_user_appearance_skin') : 'default');
$wp_user_register_enable = get_option('wp_user_disable_signup');

include('option.php');
?>

<div class="tab-content">
    <?php
    include('login.php');
    include('forgot.php');
    if (!get_option('wp_user_disable_signup')) {
        include('register.php');
    } ?>
</div>
