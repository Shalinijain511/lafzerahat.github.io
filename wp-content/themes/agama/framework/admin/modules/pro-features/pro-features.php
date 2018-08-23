<?php 

// Do not allow direct access to the file.
if( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register Customizer Agama Pro Block
 *
 * @since 1.0.9
 */
function agama_customize_premium_feature($wp_customize) {
	class Agama_Customize_Agama_Pro extends WP_Customize_Control {
		public function render_content() { ?>
			<div class="theme-premium-feature">
				<label>
					<span class="customize-control-title">
						Premium Feature - 
						<a href="<?php echo esc_url('http://www.theme-vision.com/agama-pro/'); ?>" title="Agama Pro" target="_blank">
							Get Agama Pro ($49)
						</a>
					</span>
					<span class="description customize-control-description">
						<strong>Agama Pro</strong> comes with allot of features & premium plugins <strong>worth over $68</strong>.<br /><br />
						<strong>Check demo: <a href="<?php echo esc_url('http://demo.theme-vision.com'); ?>" target="_blank">here</a></strong>
					</span>
				</label>
			</div>
		<?php
		}
	}
}
add_action('customize_register', 'agama_customize_premium_feature');

/**
 * Agama Upgrade to PRO
 *
 * @since 1.2.0
 */
function agama_upgrade_to_pro() {
	wp_register_script( 'agama_customizer_script', AGAMA_JS . 'agama-customizer.js', array( 'jquery' ), uniqid(), true );
    wp_enqueue_script( 'agama_customizer_script' );
    wp_localize_script( 'agama_customizer_script', 'themevision', array(
        'URL'  	 	=> esc_url( 'http://theme-vision.com/agama-pro/' ),
        'Label' 	=> __( 'Upgrade to PRO', 'agama' ),
		'sup_url'	=> esc_url( 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BR55TPNQEK28L' ),
		'sup_dev'	=> __( 'Development Support', 'agama' )
    ) );
    //wp_register_style( 'fontawesome', AGAMA_CSS . 'font-awesome.min.css', array(), '4.6.3' );
    wp_enqueue_style( 'fontawesome' );
}
add_action( 'customize_controls_enqueue_scripts', 'agama_upgrade_to_pro' );

/**
 * Register Customizer Agama Heading
 *
 * @since 1.1.5
 */
function agama_customize_heading( $wp_customize ) {
	class Agama_Customize_Agama_Heading extends WP_Customize_Control {
		public function render_content() { ?>
			<div class="agama-customize-heading">
				<h3><?php echo esc_html( $this->label ); ?></h3>
			</div>
		<?php 
		}
	}
}
add_action('customize_register', 'agama_customize_heading');

/**
 * Implement Theme Customizer additions and adjustments.
 *
 * @since Agama v1.0.7
 *
 */
function agama_customize_support_register( $wp_customize ) {
	class Agama_Customize_Agama_Support extends WP_Customize_Control {
		public function render_content() { ?>
		<ul class="theme-info"> 
			<li>
				<a title="<?php esc_attr_e( 'Review Agama', 'agama' ); ?>" href="<?php echo esc_url( 'https://wordpress.org/support/theme/agama/reviews/?filter=5' ); ?>" target="_blank">
                    <i class="fa fa-star"></i> 
					<?php _e( 'Rate Agama', 'agama' ); ?>
				</a>
			</li>
			<li>
				<a href="<?php echo esc_url( 'https://wordpress.org/support/theme/agama' ); ?>" title="<?php esc_attr_e( 'Support Forum', 'agama' ); ?>" target="_blank">
                    <i class="fa fa-support"></i> 
					<?php _e( 'Support Forum', 'agama' ); ?>
				</a>
			</li>
			<li>
				<a href="<?php echo esc_url( 'http://demo.theme-vision.com' ); ?>" title="<?php esc_attr_e( 'Agama PRO Demo', 'agama' ); ?>" target="_blank">
                    <i class="fa fa-external-link-square"></i> 
					<?php _e( 'Agama PRO Demo', 'agama' ); ?>
				</a>
			</li>
			<li>
				<a href="<?php echo esc_url( 'http://theme-vision.com/agama-pro/' ); ?>" title="<?php esc_attr_e( 'Agama PRO Buy', 'agama' ); ?>" target="_blank">
                    <i class="fa fa-external-link-square"></i> 
					<?php _e( 'Agama PRO Buy', 'agama' ); ?>
				</a>
			</li>
			<li>
				<a title="<?php esc_attr_e( 'Support Development', 'agama' ); ?>" href="<?php echo esc_url( 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BR55TPNQEK28L' ); ?>" target="_blank">
                    <i class="fa fa-paypal"></i> 
					<?php _e( 'Support Development', 'agama' ); ?>
				</a>
			</li>
		</ul>
		<?php
		}
	}
}
add_action('customize_register', 'agama_customize_support_register');

/**
 * Define Customizer Settings, Controls etc...
 *
 * @since Agama 1.0
 */
function agama_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
	
	$wp_customize->remove_section('colors');
	
###################################################################################
# AGAMA SUPPORT
###################################################################################
	$wp_customize->add_setting( 'agama_support', array(
		'default'			=> false,
		'capability'		=> 'edit_theme_options',
		'sanitize_callback'	=> 'wp_filter_nohtml_kses',
	));
	$wp_customize->add_control(
		new Agama_Customize_Agama_Support(
			$wp_customize,'agama_support', array(
				'label'			=> __( 'Agama Upgrade', 'agama' ),
				'section'		=> 'agama_support_section',
				'settings'		=> 'agama_support',
			)
		)
	);
###################################################################################
# PRO FEATURE
###################################################################################
	$wp_customize->add_setting( 'agama_frontpage_box_5', array(
		'default'			=> false,
		'capability'		=> 'edit_theme_options',
		'sanitize_callback'	=> 'wp_filter_nohtml_kses',
	));
	$wp_customize->add_control(
		new Agama_Customize_Agama_Pro(
			$wp_customize,'agama_frontpage_box_5', array(
				'label'			=> __( 'Frontpage Box #5', 'agama' ),
				'section'		=> 'agama_frontpage_boxes_section_5',
				'settings'		=> 'agama_frontpage_box_5',
			)
		)
	);
###################################################################################
# PRO FEATURE
###################################################################################
	$wp_customize->add_setting( 'agama_frontpage_box_6', array(
		'default'			=> false,
		'capability'		=> 'edit_theme_options',
		'sanitize_callback'	=> 'wp_filter_nohtml_kses',
	));
	$wp_customize->add_control(
		new Agama_Customize_Agama_Pro(
			$wp_customize,'agama_frontpage_box_6', array(
				'label'			=> __( 'Frontpage Box #6', 'agama' ),
				'section'		=> 'agama_frontpage_boxes_section_6',
				'settings'		=> 'agama_frontpage_box_6',
			)
		)
	);
###################################################################################
# PRO FEATURE
###################################################################################
	$wp_customize->add_setting( 'agama_frontpage_box_7', array(
		'default'			=> false,
		'capability'		=> 'edit_theme_options',
		'sanitize_callback'	=> 'wp_filter_nohtml_kses',
	));
	$wp_customize->add_control(
		new Agama_Customize_Agama_Pro(
			$wp_customize,'agama_frontpage_box_7', array(
				'label'			=> __( 'Frontpage Box #7', 'agama' ),
				'section'		=> 'agama_frontpage_boxes_section_7',
				'settings'		=> 'agama_frontpage_box_7',
			)
		)
	);
###################################################################################
# PRO FEATURE
###################################################################################
	$wp_customize->add_setting( 'agama_frontpage_box_8', array(
		'default'			=> false,
		'capability'		=> 'edit_theme_options',
		'sanitize_callback'	=> 'wp_filter_nohtml_kses',
	));
	$wp_customize->add_control(
		new Agama_Customize_Agama_Pro(
			$wp_customize,'agama_frontpage_box_8', array(
				'label'			=> __( 'Frontpage Box #8', 'agama' ),
				'section'		=> 'agama_frontpage_boxes_section_8',
				'settings'		=> 'agama_frontpage_box_8',
			)
		)
	);
###################################################################################
# PRO FEATURE
###################################################################################
	$wp_customize->add_setting( 'agama_slide_3', array(
		'default'			=> false,
		'capability'		=> 'edit_theme_options',
		'sanitize_callback'	=> 'wp_filter_nohtml_kses',
	));
	$wp_customize->add_control(
		new Agama_Customize_Agama_Pro(
			$wp_customize,'agama_slide_3', array(
				'label'			=> __( 'Slide #3', 'agama' ),
				'section'		=> 'agama_slide_3_section',
				'settings'		=> 'agama_slide_3',
			)
		)
	);
###################################################################################
# PRO FEATURE
###################################################################################
	$wp_customize->add_setting( 'agama_slide_4', array(
		'default'			=> false,
		'capability'		=> 'edit_theme_options',
		'sanitize_callback'	=> 'wp_filter_nohtml_kses',
	));
	$wp_customize->add_control(
		new Agama_Customize_Agama_Pro(
			$wp_customize,'agama_slide_4', array(
				'label'			=> __( 'Slide #4', 'agama' ),
				'section'		=> 'agama_slide_4_section',
				'settings'		=> 'agama_slide_4',
			)
		)
	);
###################################################################################
# PRO FEATURE
###################################################################################
	$wp_customize->add_setting( 'agama_slide_5', array(
		'default'			=> false,
		'capability'		=> 'edit_theme_options',
		'sanitize_callback'	=> 'wp_filter_nohtml_kses',
	));
	$wp_customize->add_control(
		new Agama_Customize_Agama_Pro(
			$wp_customize,'agama_slide_5', array(
				'label'			=> __( 'Slide #5', 'agama' ),
				'section'		=> 'agama_slide_5_section',
				'settings'		=> 'agama_slide_5',
			)
		)
	);
###################################################################################
# PRO FEATURE
###################################################################################
	$wp_customize->add_setting( 'agama_slide_6', array(
		'default'			=> false,
		'capability'		=> 'edit_theme_options',
		'sanitize_callback'	=> 'wp_filter_nohtml_kses',
	));
	$wp_customize->add_control(
		new Agama_Customize_Agama_Pro(
			$wp_customize,'agama_slide_6', array(
				'label'			=> __( 'Slide #6', 'agama' ),
				'section'		=> 'agama_slide_6_section',
				'settings'		=> 'agama_slide_6',
			)
		)
	);
###################################################################################
# PRO FEATURE
###################################################################################
	$wp_customize->add_setting( 'agama_slide_7', array(
		'default'			=> false,
		'capability'		=> 'edit_theme_options',
		'sanitize_callback'	=> 'wp_filter_nohtml_kses',
	));
	$wp_customize->add_control(
		new Agama_Customize_Agama_Pro(
			$wp_customize,'agama_slide_7', array(
				'label'			=> __( 'Slide #7', 'agama' ),
				'section'		=> 'agama_slide_7_section',
				'settings'		=> 'agama_slide_7',
			)
		)
	);
###################################################################################
# PRO FEATURE
###################################################################################
	$wp_customize->add_setting( 'agama_slide_8', array(
		'default'			=> false,
		'capability'		=> 'edit_theme_options',
		'sanitize_callback'	=> 'wp_filter_nohtml_kses',
	));
	$wp_customize->add_control(
		new Agama_Customize_Agama_Pro(
			$wp_customize,'agama_slide_8', array(
				'label'			=> __( 'Slide #8', 'agama' ),
				'section'		=> 'agama_slide_8_section',
				'settings'		=> 'agama_slide_8',
			)
		)
	);
###################################################################################
# PRO FEATURE
###################################################################################
	$wp_customize->add_setting( 'agama_slide_9', array(
		'default'			=> false,
		'capability'		=> 'edit_theme_options',
		'sanitize_callback'	=> 'wp_filter_nohtml_kses',
	));
	$wp_customize->add_control(
		new Agama_Customize_Agama_Pro(
			$wp_customize,'agama_slide_9', array(
				'label'			=> __( 'Slide #9', 'agama' ),
				'section'		=> 'agama_slide_9_section',
				'settings'		=> 'agama_slide_9',
			)
		)
	);
###################################################################################
# PRO FEATURE
###################################################################################
	$wp_customize->add_setting( 'agama_slide_10', array(
		'default'			=> false,
		'capability'		=> 'edit_theme_options',
		'sanitize_callback'	=> 'wp_filter_nohtml_kses',
	));
	$wp_customize->add_control(
		new Agama_Customize_Agama_Pro(
			$wp_customize,'agama_slide_10', array(
				'label'			=> __( 'Slide #10', 'agama' ),
				'section'		=> 'agama_slide_10_section',
				'settings'		=> 'agama_slide_10',
			)
		)
	);
###################################################################################
# PRO FEATURE
###################################################################################
	$wp_customize->add_setting( 'agama_headings', array(
		'default'			=> '',
		'capability'		=> 'edit_theme_options',
		'transport'			=> 'refresh',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(
		new Agama_Customize_Agama_Pro(
			$wp_customize, 'agama_headings', array(
				'label'			=> __( 'Headings', 'agama' ),
				'section'		=> 'agama_headings_section',
				'settings'		=> 'agama_headings'
			)
		)
	);
###################################################################################
# PRO FEATURE
###################################################################################
	$wp_customize->add_setting( 'agama_share_icons', array(
		'default'			=> '',
		'capability'		=> 'edit_theme_options',
		'transport'			=> 'refresh',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(
		new Agama_Customize_Agama_Pro(
			$wp_customize, 'agama_share_icons', array(
				'label'			=> __( 'Share Box', 'agama' ),
				'section'		=> 'agama_share_icons_section',
				'settings'		=> 'agama_share_icons'
			)
		)
	);
###################################################################################
# PRO FEATURE
###################################################################################
	$wp_customize->add_setting( 'agama_typography', array(
		'default'			=> false,
		'capability'		=> 'edit_theme_options',
		'transport'			=> 'refresh',
		'sanitize_callback' => 'sanitize_text_field'
	));
	$wp_customize->add_control( 
		new Agama_Customize_Agama_Pro( 
			$wp_customize, 'agama_typography', array(
				'label'			=> __( 'Typography', 'agama' ),
				'section'		=> 'agama_typography_section',
				'settings'		=> 'agama_typography',
			)
		)
	);
###################################################################################
# PRO FEATURE
###################################################################################
	$wp_customize->add_setting( 'agama_lightbox', array(
		'default'			=> '',
		'capability'		=> 'edit_theme_options',
		'transport'			=> 'refresh',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(
		new Agama_Customize_Agama_Pro(
			$wp_customize, 'agama_lightbox', array(
				'label'			=> __( 'Lightbox', 'agama' ),
				'section'		=> 'agama_lightbox_section',
				'setting'		=> 'agama_lightbox'
			)
		)
	);
###################################################################################
# PRO FEATURE
###################################################################################
	$wp_customize->add_setting( 'agama_pages', array(
		'default'			=> '',
		'capability'		=> 'edit_theme_options',
		'transport'			=> 'refresh',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(
		new Agama_Customize_Agama_Pro(
			$wp_customize, 'agama_pages', array(
				'label'			=> __( 'Pages', 'agama' ),
				'section'		=> 'agama_pages_section',
				'setting'		=> 'agama_pages'
			)
		)
	);
###################################################################################
# PRO FEATURE
###################################################################################
	$wp_customize->add_setting( 'agama_portfolio', array(
		'default'			=> '',
		'capability'		=> 'edit_theme_options',
		'transport'			=> 'refresh',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(
		new Agama_Customize_Agama_Pro(
			$wp_customize, 'agama_portfolio', array(
				'label'			=> __( 'Portfolio', 'agama' ),
				'section'		=> 'agama_portfolio_section',
				'setting'		=> 'agama_portfolio'
			)
		)
	);
###################################################################################
# PRO FEATURE
###################################################################################
	$wp_customize->add_setting( 'agama_preloader', array(
		'default'			=> '',
		'capability'		=> 'edit_theme_options',
		'transport'			=> 'refresh',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(
		new Agama_Customize_Agama_Pro(
			$wp_customize, 'agama_preloader', array(
				'label'			=> __( 'Pre-Loader', 'agama' ),
				'section'		=> 'agama_preloader_section',
				'setting'		=> 'agama_preloader'
			)
		)
	);
###################################################################################
# PRO FEATURE
###################################################################################
	$wp_customize->add_setting( 'agama_contact', array(
		'default'			=> '',
		'capability'		=> 'edit_theme_options',
		'transport'			=> 'refresh',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(
		new Agama_Customize_Agama_Pro(
			$wp_customize, 'agama_contact', array(
				'label'			=> __( 'Contact', 'agama' ),
				'section'		=> 'agama_contact_section',
				'setting'		=> 'agama_contact'
			)
		)
	);
###################################################################################
# PRO FEATURE
###################################################################################
	$wp_customize->add_setting( 'agama_woocommerce', array(
		'default'			=> '',
		'capability'		=> 'edit_theme_options',
		'transport'			=> 'refresh',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control(
		new Agama_Customize_Agama_Pro(
			$wp_customize, 'agama_woocommerce', array(
				'label'			=> __( 'WooCommerce', 'agama' ),
				'section'		=> 'agama_woocommerce_section',
				'setting'		=> 'agama_woocommerce'
			)
		)
	);
}
add_action( 'customize_register', 'agama_customize_register' );