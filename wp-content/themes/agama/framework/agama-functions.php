<?php

// Do not allow direct access to the file.
if( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Include Agama Custom Header Function File
get_template_part('includes/agama-custom-header');

if ( ! isset( $content_width ) ) 
	$content_width = 1200;

/**
 * Agama setup
 *
 * @since Agama 1.0
 */
function agama_setup() {
	/*
	 * Makes Agama available for translation.
	 */
	load_theme_textdomain( 'agama', AGAMA_DIR.'languages' );

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();
	
	// Adds support for title tag
	add_theme_support( 'title-tag' );

	// Adds RSS feed links to <head> for posts and comments.
	add_theme_support( 'automatic-feed-links' );

	// This theme supports a variety of post formats.
	add_theme_support( 'post-formats', array( 'aside', 'image', 'link', 'quote', 'status' ) );

    register_nav_menu( 'top', __( 'Top Menu', 'agama' ) );
	register_nav_menu( 'primary', __( 'Primary Menu', 'agama' ) );

	/*
	 * This theme supports custom background color and image,
	 * and here we also set up the default background color.
	 */
	add_theme_support( 'custom-background', array(
		'default-color' => 'e6e6e6',
	) );

	// This theme uses a custom image size for featured images, displayed on "standard" posts.
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 800, 9999 ); // Unlimited height, soft crop
	
	// Register custom image sizes
	add_image_size( 'agama-blog-large', 776, 310, true );
    add_image_size( 'agama-blog-medium', 320, 202, true );
	add_image_size( 'agama-blog-small', 400, 300, true );
    add_image_size( 'agama-related-img', 180, 138, true );
    add_image_size( 'agama-recent-posts', 700, 441, true );
	
	/*
	 * Declare WooCommerce Support
	 */
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'agama_setup' );

/**
 * Backwards Compatibility for Title Tag
 *
 * @since Agama 1.0
 */
if ( ! function_exists( '_wp_render_title_tag' ) ) {
function agama_slug_render_title() {
?>
<title><?php wp_title( '|', true, 'right' ); ?></title>
<?php
	}
	add_action( 'wp_head', 'agama_slug_render_title' );
}

/**
 * Agama Thumb Title
 * Get post-page article title and separates it on two halfs
 *
 * @since Agama v1.0.1
 * @return string
 */
function agama_thumb_title() {
	$title = get_the_title();
	$findme   = ' ';
	$pos = strpos($title, $findme);
	if( $pos === false ) {
			$output = '<h2>'.$title.'</h2>';
		} else {
			// isolate part 1 and part 2.
			$title_part_one = strstr($title, ' ', true); // As of PHP 5.3.0
			$title_part_two = strstr($title, ' ');
			$output = '<h2>'.$title_part_one.'<span>'.$title_part_two.'</span></h2>';
		}
	echo $output;
}

/**
 * Get Attachment Image Src
 *
 * @since Agama v1.0.1
 * @return string
 */
function agama_return_image_src( $thumb_size ) {
	$att_id	 = get_post_thumbnail_id();
	$att_src = wp_get_attachment_image_src( $att_id, $thumb_size );
	return esc_url($att_src[0]);
}

/**
 * Check if $page is template page
 *
 * @since Agama v1.0.1
 * @return string
 */
function agama_is_page_template( $page ) {
	if( is_page_template( 'page-templates/'.$page ) ) {
		return true;
	}
	return false;
}

/**
 * Filter the page menu arguments.
 * Makes our wp_nav_menu() fallback -- wp_page_menu() -- show a home link.
 *
 * @since Agama 1.0
 */
function agama_page_menu_args( $args ) {
	if ( ! isset( $args['show_home'] ) )
		$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'agama_page_menu_args' );

if ( ! function_exists( 'agama_content_nav' ) ) :
/**
 * Displays navigation to next/previous pages when applicable.
 *
 * @since Agama 1.0
 */
function agama_content_nav( $html_id ) {
	global $wp_query;

	if ( $wp_query->max_num_pages > 1 ) : ?>
		<nav id="<?php echo esc_attr( $html_id ); ?>" class="navigation clearfix" role="navigation">
			<h2 class="assistive-text"><?php esc_html_e( 'Post navigation', 'agama' ); ?></h2>
			<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'agama' ) ); ?></div>
			<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'agama' ) ); ?></div>
		</nav>
	<?php endif;
}
endif;

if ( ! function_exists( 'agama_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own agama_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Agama 1.0
 */
function agama_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
		// Display trackbacks differently than normal comments.
	?>
	<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		<p><?php _e( 'Pingback:', 'agama' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'agama' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
		// Proceed with normal comments.
		global $post;
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>" class="comment-wrap clearfix">
			
			<div class="comment-meta">
				<div class="comment-author vcard">
					<span class="comment-avatar clearfix">
						<?php echo get_avatar( $comment, 44 ); ?>
					</span>
				</div>
			</div><!-- .comment-meta -->

			<div class="comment-content comment">
				<div class="comment-author">
				<?php
				printf( '<a href="%1$s">%2$s %3$s</a>', get_permalink(), get_comment_author_link(),
							// If current post author is also comment author, make it known visually.
							( $comment->user_id === $post->post_author ) ? '<cite>' . __( 'author', 'agama' ) . '</cite>' : ''
				);
				printf( '<span><a href="%1$s"><time datetime="%2$s">%3$s</time></a></span>',
						esc_url( get_comment_link( $comment->comment_ID ) ),
						get_comment_time( 'c' ),
						/* translators: 1: date, 2: time */
						sprintf( __( '%1$s at %2$s', 'agama' ), get_comment_date(), get_comment_time() )
				);
				?>
				</div>
				<?php comment_text(); ?>
				<?php //edit_comment_link( __( '<i class="fa fa-edit"></i> Edit', 'agama' ), '<p class="edit-link">', '</p>' ); ?>
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => '', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .comment-content -->
			
			<?php if ( '0' == $comment->comment_approved ) : ?>
				<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'agama' ); ?></p>
			<?php endif; ?>
			
		</div><!-- #comment-## -->
	<?php
		break;
	endswitch; // end comment_type check
}
endif;

/**
 * Comment Form Fields
 *
 * @since 1.2.4
 */
add_filter( 'comment_form_default_fields', 'agama_comment_form_fields' );
function agama_comment_form_fields( $fields ) {
	
	// Get the current commenter if available
    $commenter = wp_get_current_commenter();
	
	// Core functionality
    $req      = get_option( 'require_name_email' );
    $aria_req = ( $req ? " aria-required='true'" : '' );
    $html_req = ( $req ? " required='required'" : '' );
	
	$fields['author']	= '<div class="col-md-4"><label for="author">' . __( 'Name', 'agama' ) . '</label>' . ( $req ? '<span class="required">*</span>' : '' ) . '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" class="sm-form-control"' . $aria_req . ' /></div>';
	$fields['email'] 	= '<div class="col-md-4"><label for="email">' . __( 'Email', 'agama' ) . '</label>' . ( $req ? '<span class="required">*</span>' : '' ) . '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" class="sm-form-control"' . $aria_req . ' /></div>';
	$fields['url'] 		= '<div class="col-md-4"><label for="url">' . __( 'Website', 'agama' ) . '</label><input id="url" name="url" type="text" value="' . esc_url( $commenter['comment_author_url'] ) . '" class="sm-form-control" /></div>';
	
	return $fields;
}

/**
 * Comment Form Defaults
 *
 * @since 1.2.4
 */
add_filter( 'comment_form_defaults', 'agama_comment_form_defaults' );
function agama_comment_form_defaults( $defaults ) {
	global $current_user;
	
	$defaults['logged_in_as'] = '<div class="col-md-12 logged-in-as">' . sprintf(	'%s <a href="%s">%s</a>. <a href="%s" title="%s">%s</a>', __('Logged in as', 'agama'), admin_url( 'profile.php' ), $current_user->display_name, wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) ), __('Log out of this account', 'agama'), __('Log out?', 'agama') ) . '</div>';
	$defaults['comment_field'] = '<div class="col-md-12"><label for="comment">' . __( 'Comment', 'agama' ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" class="sm-form-control"></textarea></div>';
	
    // HTML Tags Usage Suggestion
    if( get_theme_mod( 'agama_comments_tags_suggestion', true ) ) {
        $defaults['comment_notes_after'] = '<div class="col-md-12" style="margin-top: 15px; margin-bottom: 15px;">' . sprintf( '%s <abbr title="HyperText Markup Language">HTML</abbr> %s: %s', __( 'You may use these', 'agama' ), __( 'tags and attributes', 'agama' ), '<code>' . allowed_tags() . '</code>') . '</div>';
    }
    
	$defaults['title_reply']	= sprintf( '%s <span>%s</span>', __( 'Leave a', 'agama' ), __( 'Comment', 'agama' ) );
	$defaults['class_submit']	= 'button button-3d button-large button-rounded';
	
	return $defaults;
}

if ( ! function_exists( 'agama_entry_meta' ) ) :
/**
 * Set up post entry meta.
 *
 * Prints HTML with meta information for current post: categories, tags, permalink, author, and date.
 *
 * Create your own agama_entry_meta() to override in a child theme.
 *
 * @since Agama 1.0
 */
function agama_entry_meta() {
	// Translators: used between list items, there is a space after the comma.
	$categories_list = get_the_category_list( __( ', ', 'agama' ) );

	// Translators: used between list items, there is a space after the comma.
	$tag_list = get_the_tag_list( '', __( ', ', 'agama' ) );

	$date = sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a>',
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date('c') ),
		esc_html( get_the_date() )
	);

	$author = sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_attr( sprintf( __( 'View all posts by %s', 'agama' ), get_the_author() ) ),
		get_the_author()
	);

	// Translators: 1 is category, 2 is tag, 3 is the date and 4 is the author's name.
	if ( $tag_list ) {
		$utility_text = __( 'This entry was posted in %1$s and tagged %2$s on %3$s<span class="by-author"> by %4$s</span>.', 'agama' );
	} elseif ( $categories_list ) {
		$utility_text = __( 'This entry was posted in %1$s on %3$s<span class="by-author"> by %4$s</span>.', 'agama' );
	} else {
		$utility_text = __( 'This entry was posted on %3$s<span class="by-author"> by %4$s</span>.', 'agama' );
	}

	printf(
		$utility_text,
		$categories_list,
		$tag_list,
		$date,
		$author
	);
}
endif;

/**
 * .article-wrapper Grid, List - Style
 *
 * @since Agama v1.0.1
 */
function agama_article_wrapper_class() {
	$blog_layout = get_theme_mod('agama_blog_layout', 'list');
	switch( $blog_layout ):
		
		case 'list':
			echo 'list-style';
		break;
		
		case 'grid':
			echo 'grid-style';
		break;
		
		case 'small_thumbs':
			echo 'small_thumbs';
		break;
	
	endswitch;
}

/**
 * Render Blog Post Date & Icon
 *
 * @since 1.0.1
 */
if( ! function_exists( 'agama_render_blog_post_date' ) ) {
	function agama_render_blog_post_date() {
		global $post;
		if( get_theme_mod( 'agama_blog_post_meta', true ) ) {
            // Display blog post date only on posts loop page.
            if( ! is_single() && get_theme_mod( 'agama_blog_post_date', true ) ) {
                echo '<div class="entry-date">';
                    echo '<div class="date-box updated">';
                        printf( '<span class="date">%s</span>', get_the_time('d') ); // Get day
                        printf( '<span class="month-year">%s</span>', get_the_time('m, Y') ); // Get month, year
                    echo '</div>';
                    echo '<div class="format-box">';
                        printf( '%s', Agama::post_format() );
                    echo '</div>';
                echo '</div>';
            }
        }
	}
}
add_action( 'agama_blog_post_date_and_format', 'agama_render_blog_post_date', 10 );

/**
 * Render Blog Post Meta
 *
 * @since 1.0.1
 */
if( ! function_exists( 'agama_render_blog_post_meta' ) ) {
	function agama_render_blog_post_meta() {
		if( get_theme_mod( 'agama_blog_post_meta', true ) ) {
            echo '<p class="single-line-meta">';
                // Display blog post author.
                if( get_theme_mod( 'agama_blog_post_author', true ) ) {
                    printf( 
                        '%s <span class="vcard"><span class="fn">%s</span></span>', 
                        '<i class="fa fa-user"></i>', 
                        get_the_author_link() 
                    );
                }

                // Display blog post publish date.
                if( get_theme_mod( 'agama_blog_post_date', true ) ) {
                    printf( 
                        '%s %s <span>%s</span>',
                        '<span class="inline-sep">/</span>',				
                        '<i class="fa fa-calendar"></i>', 
                        get_the_time('F j, Y') 
                    );
                }

                // Display next details only on list blog layout or on single post page.
                if( get_theme_mod('agama_blog_layout', 'list') == 'list' || is_single() ) {
                    // Display post category.
                    if( get_theme_mod( 'agama_blog_post_category', true ) ) {
                        printf( 
                            '%s %s %s', 
                            '<span class="inline-sep">/</span>',
                            '<i class="fa fa-folder-open"></i>', 
                            get_the_category_list(', ') 
                        );
                    }

                    // Display post comment counter.
                    if( comments_open() && get_theme_mod( 'agama_blog_post_comments', true ) ) {
                        printf( 
                            '%s %s <a href="%s">%s</a>', 
                            '<span class="inline-sep">/</span>',
                            '<i class="fa fa-comments"></i>', 
                            get_comments_link(), 
                            get_comments_number().__( ' comments', 'agama' ) 
                        );
                    }
                }
            echo '</p>';
		}
	}
}
add_action( 'agama_blog_post_meta', 'agama_render_blog_post_meta', 10 );

/**
 * Infinite Scroll Init
 * 
 * @since 1.0.3
 */
function agama_infinite_scroll_init() { ?>
<script>
jQuery(function($){
	<?php if( get_theme_mod( 'agama_blog_layout', 'list' ) == 'grid' && ! is_singular() ): ?>
	var $container = $('#content').ready(function(){
        
		$container.isotope({
			itemSelector : '.article-wrapper'
		});
        
		$container.infinitescroll({
			navSelector  : '.navigation',
			nextSelector : '.nav-previous a',
			itemSelector : '.article-wrapper',
			loading: {
				finishedMsg: '<?php _e( 'No more posts to load.', 'agama' ); ?>',
				img: '<?php echo AGAMA_IMG .'loader.gif'; ?>'
			  },
			errorCallback: function(){
				$('#infscr-loading').replaceWith(
					"<div id='infscr-loading'><?php _e( 'No more posts to load.', 'agama' ); ?></div>"
				);
			  },
			},
			// call Isotope as a callback
			function( newElements ) {
                var $newElems = $(newElements);
                $newElems.imagesLoaded(function(){
                    $container.isotope( 'appended', $newElems );
                    $container.isotope( 'layout' );
                });
			}
		);
		
		<?php if( get_theme_mod('agama_blog_infinite_trigger', 'button') == 'button'): ?>
		jQuery(window).unbind('.infscr');
		jQuery('.navigation').css('display', 'none');
		
		jQuery('#infinite-loadmore').click(function() {
			$container.infinitescroll('retrieve');
            return false;
		});
		
		jQuery(document).ajaxError(function(e,xhr,opt) {
			if(xhr.status==404)
				jQuery('.navigation .nav-previous a').remove();
		});
		<?php endif; ?>
	});
	<?php else: ?>
    
	jQuery('#content').infinitescroll({
		navSelector  : '.navigation',
		nextSelector : '.navigation .nav-previous a',
		itemSelector : '.article-wrapper',
		loading: {
			finishedMsg: '<?php _e( 'No more posts to load.', 'agama' ); ?>',
			img: '<?php echo AGAMA_IMG .'loader.gif'; ?>'
		},
	});
	
	<?php if( get_theme_mod( 'agama_blog_infinite_trigger', 'button' ) == 'button' ): ?>
	jQuery(window).unbind('.infscr');
	jQuery('.navigation').css('display', 'none');
	
	jQuery('#infinite-loadmore').click(function() {
		jQuery('#content').infinitescroll('retrieve');
			return false;
	});
	
	jQuery(document).ajaxError(function(e,xhr,opt) {
		if(xhr.status==404)
			jQuery('.navigation .nav-previous a').remove();
	});
	<?php endif; ?>
	<?php endif; ?>
});
</script>
<?php }

/**
 * Set the WPForms ShareASale ID.
 *
 * @param string $shareasale_id The the default Shareasale ID.
 *
 * @since 1.3.5
 * @return string $shareasale_id
 */
function agama_wpforms_shareasale_id( $shareasale_id ) {
	
	// If this WordPress installation already has an WPForms Shareasale ID
	// specified, use that.
	if ( ! empty( $shareasale_id ) ) {
		return $shareasale_id;
	}
	
	// Define the Shareasale ID to use.
	$shareasale_id = '1355534';
	
	// This WordPress installation doesn't have an Shareasale ID specified, so 
	// set the default ID in the WordPress options and use that.
	update_option( 'wpforms_shareasale_id', $shareasale_id );
	
	// Return the Shareasale ID.
	return $shareasale_id;
}
add_filter( 'wpforms_shareasale_id', 'agama_wpforms_shareasale_id' );

/**
 * Agama Credits
 *
 * @since Agama v1.0.1
 */
if( ! function_exists( 'agama_render_credits' ) ) {
	function agama_render_credits() {
		echo html_entity_decode( get_theme_mod( 'agama_footer_copyright', sprintf( __( '2015 &copy; Powered by %s.', 'agama' ), '<a href="http://www.theme-vision.com" target="_blank">Theme-Vision</a>' ) ) );
	}
}
add_action( 'agama_credits', 'agama_render_credits' );

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
