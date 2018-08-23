<?php
/**
 * The sidebar containing the main widget area
 *
 * 
 * @package istartups
 */

if ( ! is_active_sidebar( 'main-sidebar' ) ) {
	return;
} ?>
<div class="col-md-3 col-sm-12 col-xs-12 main-sidebar">
    <?php dynamic_sidebar( 'main-sidebar' ); ?>
</div>