<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 *
 * @package istartups
 */
get_header(); 
$istartups_sidebar_layout = get_theme_mod('side_area_opt','right');
if ($istartups_sidebar_layout == 'right')
    $istartups_sidebar_col = 'col-sm-9';
else
    $istartups_sidebar_col = 'col-sm-9'; ?>
<section class="single-blog-section">
    <div class="container">
        <div class="row">
            <div class="<?php echo esc_attr($istartups_sidebar_col); ?> <?php if ($istartups_sidebar_layout == 'left'){ echo esc_attr('pull-right'); } ?>">
			<?php if(have_posts()):
					while ( have_posts() ) : the_post();
                    if(has_post_thumbnail()){ ?>
                    <div class="single-blog-image">
                        <?php the_post_thumbnail('full'); ?>
					</div>
                    <?php } ?>
                    <div class="single-blog-description">
                    	<?php the_content(); ?>
                    </div>
						<?php if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;
                        wp_link_pages();
					endwhile;
				endif; ?>
			</div>
			<?php if($istartups_sidebar_layout == 'right'){ get_sidebar(); } ?>
			<?php if($istartups_sidebar_layout == 'left'){ get_sidebar(); } ?>
        </div>
    </div>
</section>
<?php get_footer();