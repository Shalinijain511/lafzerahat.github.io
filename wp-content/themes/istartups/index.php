<?php
/**
 * The main template file.
 */	
get_header(); 
$show_meta = get_theme_mod('show_meta', 1);
$istartups_sidebar_layout = get_theme_mod('side_area_opt','right');
if($istartups_sidebar_layout == 'right'){
    $istartups_sidebar_col = 'col-sm-9';
    $masonry_columns = 'masonry-columns-2';
}else{
    $istartups_sidebar_col = 'col-sm-9';
    $masonry_columns = 'masonry-columns-2';
}  ?>
<section class="blog-section">
    <div class="container" id="main-blog">
        <div class="row">
            <div class="<?php echo esc_attr($istartups_sidebar_col); ?> <?php if ($istartups_sidebar_layout == 'left'){ echo esc_attr('pull-right'); } ?>">
                <div class="masonry <?php echo  esc_attr($masonry_columns); ?>">
                    <?php if ( have_posts() ) :  while ( have_posts() ) : the_post(); ?>
                    <div class="<?php echo esc_attr($istartups_sidebar_col); ?> masonry-item">
                        <div class="blog-box">
                            <article class="blog-space">
                                <?php if((get_the_post_thumbnail_url())){ ?>
                                <div class="blog-image-date">
                                    <span class="date"><?php echo esc_html(get_the_date('j')); ?><br><?php echo esc_html(get_the_date('M')); ?></span>
                                        <a href="<?php the_permalink(); ?>">
                                           <?php the_post_thumbnail('full'); ?>
                                        </a>
                                 </div>
                                 <?php } ?>
                                <div class="blog-details">
                                    <div class="blog-title">
                                        <a href="<?php the_permalink(); ?>"><h3><?php the_title(); ?></h3></a>
                                    </div>
                                <?php if($show_meta == 1){ ?>
                                    <div class="blog-sub-title">
                                        <?php istartups_single_meta(); ?>
                                    </div>
                                <?php } ?>    
                                    <div class="blog-description">
                                        <?php the_excerpt(); ?>
                                    </div>
                                </div>
                                <div class="read-more">
                                    <a href="<?php the_permalink(); ?>"><?php esc_html_e('Read More','istartups'); ?></a>
                                </div>
                            </article>
                        </div>
                    </div>             
                    <?php endwhile;
                    endif; ?>
                </div>
                <div class="page-links">
                    <?php the_posts_pagination( array( 'mid_size'  => 3,'screen_reader_text' => ' ') ); ?>
                </div>
            </div>    
            <?php if ($istartups_sidebar_layout == 'left') get_sidebar(); ?>
            <?php if ($istartups_sidebar_layout == 'right') get_sidebar(); ?>
        </div>
    </div>    
</section>
<?php get_footer();