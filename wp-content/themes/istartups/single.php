<?php
/**
 * The template for displaying all single posts
 *
 * @package istartups
 */
get_header();
$show_meta = get_theme_mod('show_meta', 1); 
$istartups_sidebar_layout = get_theme_mod('side_area_opt','right');
if($istartups_sidebar_layout == 'right'){
    $istartups_sidebar_col = 'col-sm-9';
}else{
    $istartups_sidebar_col = 'col-sm-9';
} ?>
<section class="single-blog-section">
    <div class="container">
        <div class="row">
            <div class="<?php echo esc_attr($istartups_sidebar_col); ?> <?php if ($istartups_sidebar_layout == 'left'){ echo esc_attr('pull-right'); } ?>">
                <?php while ( have_posts() ) : the_post();
                if(has_post_thumbnail()){ ?>
                <div class="single-blog-image">
                    <?php the_post_thumbnail('full'); ?>
                </div>
                <?php } ?>
                <div class="single-blog-description">
                    <?php the_content(); ?>
                </div>
            <?php if($show_meta == 1){ ?>    
                <div class="single-blog-details">
                    <?php istartups_single_meta(); ?>
                </div>
            <?php } 
            $prev_button = "<button>".esc_html__('Previous','istartups')."</button>";
            $next_button = "<button>".esc_html__('Next','istartups')."</button>"; 
            $istartups_defaults = array(
                            'before'           => '<p>' . esc_html__( 'Pages:', 'istartups' ),
                            'after'            => '</p>',
                            'link_before'      => '',
                            'link_after'       => '',
                            'next_or_number'   => 'number',
                            'separator'        => ' ',
                            'nextpagelink'     => esc_html__( 'Next', 'istartups' ),
                            'previouspagelink' => esc_html__( 'Previous', 'istartups' ),
                            'pagelink'         => '%',
                            'echo'             => 1
                        );
                        wp_link_pages( $istartups_defaults );
                        the_post_navigation( array(
                            'prev_text'                 => $prev_button,
                            'next_text'                 => $next_button,
                            'screen_reader_text'        => ' ',
                        ) );
                        comments_template('', true);
                endwhile; ?>
            </div>
            <?php if ($istartups_sidebar_layout == 'left') get_sidebar(); ?>
            <?php if ($istartups_sidebar_layout == 'right') get_sidebar(); ?>
        </div>
    </div>
</section>
<?php get_footer();