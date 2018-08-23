<?php 

// Do not allow direct access to the file.
if( ! defined( 'ABSPATH' ) ) {
    exit;
} ?>

<div class="section notopmargin notopborder section-blog">
	<div class="container clearfix">
		<div class="heading-block center nomargin">
			<h3><?php echo esc_html( get_theme_mod( 'agama_blue_blog_heading', __( 'Latest from the Blog', 'agama-blue' ) ) ); ?></h3>
		</div>
	</div>
</div>

<div class="container container-blog clear-bottommargin clearfix">
	<div class="row">
	
		<?php if ( have_posts() ) :
            
			// Get posts per page.
            $posts_per_page = esc_attr( get_theme_mod( 'agama_blue_blog_posts_number', '4' ) );
			
            // Fix Paged on Static Homepage
			if( get_query_var('paged') ) { 
				$paged = get_query_var('paged'); 
			}
			elseif( get_query_var('page') ) { 
				$paged = get_query_var('page'); 
			}
			else { $paged = 1; }
            
            // Args
			$args = array(
				'posts_per_page' => $posts_per_page,
				'paged' => $paged
			);
			
			$the_query = new WP_Query( $args ); ?>
	
			<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
	
				<div class="col-md-3 col-sm-6 bottommargin">
					<div class="ipost clearfix">
					
						<?php if( has_post_thumbnail() ): ?>
						<div class="entry-image">
							<a href="<?php the_permalink(); ?>">
								<img class="image_fade" src="<?php echo agama_return_image_src('agama-blog-small'); ?>" alt="<?php the_title(); ?>">
							</a>
						</div>
						<?php endif; ?>
						
						<div class="entry-title">
							<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						</div>
						<ul class="entry-meta clearfix">
							<li><i class="fa fa-calendar"></i> <?php echo get_the_time('d M, Y'); ?></li>
							<li><a href="<?php echo get_comments_link(); ?>"><i class="fa fa-comments"></i> <?php echo get_comments_number(); ?></a></li>
						</ul>
					</div>
				</div>
				
			<?php endwhile; ?>
        
			<?php wp_reset_query(); ?>
				
		<?php endif; ?>
	
	</div>
</div>