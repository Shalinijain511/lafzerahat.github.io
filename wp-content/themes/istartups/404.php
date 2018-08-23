<?php
/**
 * The template for displaying 404 pages (not found)
 *
 *
 * @package istartups
 */
get_header();
$istartups_sidebar_layout = get_theme_mod('side_area_opt','right');
if($istartups_sidebar_layout == 'right'){
    $istartups_sidebar_col = 'col-sm-9';
}else{
    $istartups_sidebar_col = 'col-sm-9';
} ?>
<section class="not-found">
    <div class="container">
        <div class="row">
            <div class="<?php echo esc_attr($istartups_sidebar_col); ?> <?php if ($istartups_sidebar_layout == 'left'){ echo esc_attr('pull-right'); } ?>">
                <div class="page-not-found">
                    <p><?php esc_html_e( 'Epic 404 - Article Not Found', 'istartups' ); ?></p>
                    <p><?php esc_html_e( "This is embarassing. We can't find what you were looking for.", "istartups" ); ?></p>
                    <p><?php esc_html_e( "Whatever you were looking for was not found, but maybe try looking again or search using the form below.", "istartups" ); ?></p>
                </div>
                <?php get_search_form(); ?>
            </div>
            <?php if ($istartups_sidebar_layout == 'left') get_sidebar(); ?>
            <?php if ($istartups_sidebar_layout == 'right') get_sidebar(); ?>
        </div>
    </div>    
</section>        
<?php get_footer();