<?php

// Do not allow direct access to the file.
if( ! defined( 'ABSPATH' ) ) {
    exit;
} ?>

<div class="sticky-header clear">
	<div class="sticky-header-inner clear">
		
		<!-- Logo -->
		<div class="pull-left">
            
            <?php if( is_customize_preview() ) { echo '<div class="customize_preview_logo" style="display:block;">'; } ?>
            
			<?php if( get_theme_mod( 'agama_logo', '' ) ): ?>
				<a href="<?php echo esc_url( home_url('/') ); ?>" 
                   title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>">
					<img src="<?php echo esc_url( get_theme_mod( 'agama_logo' ) ); ?>" class="logo" 
                         alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>">
				</a>
			<?php else: ?>
				<h1 class="site-title">
                    <a href="<?php echo esc_url( home_url('/') ); ?>"><?php bloginfo( 'name' ); ?></a>
                </h1>
			<?php endif; ?>
            
            <?php if( is_customize_preview() ) { echo '</div>'; } ?>
            
		</div><!-- Logo End -->
		
		<!-- Primary Navigation -->
		<nav id="vision-primary-nav" class="pull-right" role="navigation">
			<?php echo Agama::menu( 'primary', 'sticky-nav' ); ?>
		</nav><!-- Primary Navigation End -->
		
		<?php Agama_Helper::get_mobile_menu_toggle_icon(); ?>
		
	</div>
</div>

<!-- Mobile Navigation -->
<nav id="vision-mobile-nav" class="mobile-menu collapse" role="navigation">
	<?php echo Agama::menu( 'primary', 'menu' ); ?>
</nav><!-- Mobile Navigation End -->
