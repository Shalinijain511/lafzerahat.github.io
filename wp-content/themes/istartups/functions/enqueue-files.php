<?php
/*
 * Enqueue css and js files
 */
function istartups_enqueue() {
	/*----------------------css-----------------------*/
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	wp_enqueue_style( 'google-font', 'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,900', array() );
	wp_enqueue_style( 'font-awesome', get_template_directory_uri().'/css/font-awesome'.$suffix.'.css',true,'4.7.0' );	
	wp_enqueue_style( 'bootstrap', get_template_directory_uri().'/css/bootstrap'.$suffix.'.css',true,'3.3.7');
	wp_enqueue_style( 'istartups-default', get_template_directory_uri().'/css/default.css',true,'' );
	wp_enqueue_style( 'istartups-style', get_stylesheet_uri() );
	/*----------------------end css-----------------------*/
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	wp_enqueue_script( 'bootstrap-min', get_template_directory_uri() . '/js/bootstrap'.$suffix.'.js', array('jquery'), '', true );
	wp_enqueue_script( 'istartups-custom', get_template_directory_uri() . '/js/custom.js', array('jquery'), '', true );
}
add_action( 'wp_enqueue_scripts', 'istartups_enqueue' );