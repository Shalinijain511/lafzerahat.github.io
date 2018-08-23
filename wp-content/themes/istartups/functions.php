<?php
/**
 * istartups functions and definitions
 *
 * 
 * @package istartups
 */
if ( ! function_exists( 'istartups_setup' ) ) :
	function istartups_setup() {
		load_theme_textdomain( 'istartups', get_template_directory() . '/languages' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		register_nav_menus( array(
			'primary' => esc_html__( 'Top Menu', 'istartups' ),
		) );
		add_theme_support( 'custom-header', apply_filters( 'istartups_custom_header_args', array(
			'default-image'      => get_template_directory_uri(). '/images/header.jpg',
			'width'              => 2000,
			'height'             => 1200,
			'flex-height'        => true,
			'uploads'       => true,
		) ) );
		register_default_headers( array(
			'default-image' => array(
				'url'           => get_template_directory_uri(). '/images/header.jpg',
				'thumbnail_url' => get_template_directory_uri().'/images/header.jpg',
				'description'   => esc_html__( 'Default Header Image', 'istartups' ),
			),
		) );
		add_theme_support( 'html5', array(
			'comment-list',
			'gallery',
			'caption',
		) );
		add_theme_support( 'custom-logo', array(
	        'height'      => 30,
	        'width'       => 160,
	        'flex-height' => true,
			'flex-width'  => true,
	        'header-text' => array( 'img-responsive', 'site-description' ), 
	    ) );
		add_theme_support( 'custom-background', apply_filters( 'istartups_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) ) );
		add_theme_support( 'customize-selective-refresh-widgets' );
	}
	add_action( 'after_setup_theme', 'istartups_setup' );
endif;
function istartups_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'istartups_content_width', 640 );
}
add_action( 'after_setup_theme', 'istartups_content_width', 0 );
function istartups_custom_excerpt_length_more( $more ) {
    if ( is_admin() ) {
	    return;
	}
    return ' ...';
}
add_filter( 'excerpt_more', 'istartups_custom_excerpt_length_more' );
function istartups_excerpt_length( $length ) {
    if ( is_admin() ) {
	    return;
	}
    return 50;
}
add_filter( 'excerpt_length', 'istartups_excerpt_length', 999 );
add_action('admin_menu', 'istartups_options_add_page');

function istartups_options_add_page() {
  add_theme_page( esc_html__('iStartupsPro Features', 'istartups'), esc_html__('iStartupsPro Features', 'istartups'), 'manage_options', 'istartupspro-features', 'istartups_features', 400 );
}
function istartups_features(){ ?>
	<div class="istartupspro-version">
		<a href="<?php echo esc_url('https://champthemes.com/wordpress-themes/istartupspro-wordpress-theme/'); ?>" target="_blank">
			<img src ="<?php echo esc_url(get_template_directory_uri().'/images/pro-features.jpg') ?>" width="98%" height="auto" />
		</a>
	</div>
<?php }

require get_template_directory() . '/functions/theme-default-setup.php';
require get_template_directory() . '/functions/enqueue-files.php';
require get_template_directory() . '/functions/tgmpa.php';
require get_template_directory() . '/functions/theme-customization.php';