<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 *
 * @package istartups
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<div class="startup">
    <header>
        <nav class="navbar istartups">
            <div class="container">
                <!-- Logo start -->
                <div class="main-logo">
                    <?php $istartups_logo_image = '';
                    if (function_exists('get_custom_logo')) {
                        $istartups_logo_image = has_custom_logo(); 
                        $istartups_logo = get_custom_logo();
                    }
                    if($istartups_logo != ''){
                        echo $istartups_logo;
                    } else{ ?>
                        <div class="brand-text">
                            <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                                <h4><?php bloginfo('name'); ?></h4>
                            </a>
                            <h6><?php echo get_bloginfo('description'); ?></h6>
                        </div>
                    <?php } ?>
                </div>
                <!-- Logo start -->
                <!-- Menu start -->
                <?php $menu_style = get_theme_mod('menu_style','style-1'); 
                if($menu_style == 'style-1'){
                    $istartupsmenu = 'istartupsmenu';
                }else{
                    $istartupsmenu = 'istartupsside';
                } ?>
                <div id="<?php echo esc_attr($istartupsmenu); ?>">
                    <?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => '', 'container' => 'ul')); ?>
                </div>
                <!-- Menu end -->
            </div>
        </nav>
    </header>
    <?php 
    if(!is_search()){ ?>
    <section class="background-image" style="background-image: linear-gradient(rgba(230, 230, 230, 0.1), rgba(230, 230, 230, 0.5)),url(<?php echo esc_url(get_header_image()); ?>);">
        <div class="head-content">
         <?php if(is_home()){ ?>
            <h1><?php $istartups_blog_page = get_option('page_for_posts');
            if(!empty($istartups_blog_page)){ 
                echo get_the_title(get_option( 'page_for_posts' )); 
            } else{ 
                esc_html_e( "Blog", 'istartups' );
            } ?></h1>
            <hr>
         <?php }elseif(is_archive()){ ?>
            <h1><?php the_archive_title(); ?></h1>
            <hr>
         <?php }elseif(is_404()){ ?>
            <h1><?php esc_html_e( "Oops! That page can't be found.", 'istartups' ); ?></h1>
            <hr>
         <?php }else{
            if(get_the_title()){ ?>
            <h1><?php the_title(); ?></h1>
            <hr>
         <?php } } ?>
        </div>
    </section>
<?php }