<?php

// Do not allow direct access to the file.
if( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Include Customizer File
get_template_part( 'includes/customizer' );

/**
 * Theme Setup
 *
 * @since 1.0
 */
add_action( 'after_setup_theme', 'agama_blue_after_setup_theme' );
function agama_blue_after_setup_theme() {
	
	/**
	 * THEME SETUP
	 */
	
}
 
/**
 * Enqueue Agama && Agama Blue Stylesheets
 *
 * @since 1.0
 */
 add_action( 'wp_enqueue_scripts', 'agama_blue_enqueue_scripts' );
 function agama_blue_enqueue_scripts() {
	// Roboto Condensed Font
	wp_enqueue_style( 'RobotoCondensed', esc_url( 'https://fonts.googleapis.com/css?family=Roboto+Condensed:400,700' ) );
	// Agama Stylesheet
	wp_enqueue_style( 'agama-style', get_template_directory_uri() . '/style.css', array(), Agama_Core::version() );
	// Agama Blue Stylesheet
	wp_enqueue_style( 'agama-blue-style', get_stylesheet_directory_uri() . '/style.css', array( 'agama-style' ), Agama_Core::version() );
 }
 
/**
 * After Theme Switch
 *
 * @since 1.0.5
 */
add_action( 'after_switch_theme', 'agamablue_setup_options' );
function agamablue_setup_options() {
	
	set_theme_mod( 'agama_primary_color', '#00a4d0' );
	
}
 
/**
 * Agama Blue Frontpage Features
 *
 * @since 1.0.1
 */
add_action( 'agama_blue_contents', 'agama_blue_contents' );
function agama_blue_contents() {
	if( get_theme_mod( 'agama_blue_blog', true ) && is_home() ) {
		get_template_part( 'includes/frontpage-blog' ); 
	}
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
