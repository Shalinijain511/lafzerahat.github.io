<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 *
 * @package istartups
 */
?>
<section class="background-image" style="background-image: linear-gradient(rgba(230, 230, 230, 0.1), rgba(230, 230, 230, 0.5)),url(<?php echo esc_url(get_header_image()); ?>);">
    <div class="head-content">
        <h1><?php esc_html_e( 'Nothing Found', 'istartups' ); ?></h1>
        <hr>
    </div>
</section>
<?php $istartups_sidebar_layout = get_theme_mod('side_area_opt','right');
if($istartups_sidebar_layout == 'right'){
    $istartups_sidebar_col = 'col-sm-9';
}else{
    $istartups_sidebar_col = 'col-sm-9';
}    
?>
<section class="search">
    <div class="container">
        <div class="row">
            <div class="<?php echo esc_attr($istartups_sidebar_col); ?> <?php if ($istartups_sidebar_layout == 'left'){ echo esc_attr('pull-right'); } ?>">
                <div class="search-title">
                    <h3><?php printf( esc_html__( 'Search Results for: %s', 'istartups' ), '<span>' . get_search_query() . '</span>' ); ?></h3>
                </div>
                <div class="search-result">
            		<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
            			<p><?php printf( wp_kses( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'istartups' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( admin_url( 'post-new.php' ) ) ); ?></p>
            		<?php elseif ( is_search() ) : ?>
            				<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'istartups' ); ?></p>
            			<div class="search-input">
                        <?php get_search_form(); ?>
                        </div>    
            			<?php else : ?>
            				<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'istartups' ); ?></p>
            			<div class="search-input">
                            <?php get_search_form(); ?>
            		    </div>
                    <?php endif; ?>
            	</div>
            </div>
            <?php if ($istartups_sidebar_layout == 'left') get_sidebar(); ?>
            <?php if ($istartups_sidebar_layout == 'right') get_sidebar(); ?>
        </div>
    </div>
</section>