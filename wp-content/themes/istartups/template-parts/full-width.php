<?php 
/*
* Template Name: Full Width
*/
get_header(); ?>
<section>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">  
            <?php if(have_posts()): 
                    while ( have_posts() ) : the_post(); ?>
                    <div class="single-blog-description">
                        <?php the_content(); ?>
                    </div>
                        <?php if ( comments_open() || get_comments_number() ) :
                            comments_template();
                        endif;
                    endwhile;
                endif; ?>
            </div>
        </div>
    </div>
</section>
<?php get_footer();