<?php
/**
 * The template for displaying the footer
 *
 */
if(get_theme_mod('show_footer_area', 1) == 1){ ?>
<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="footer-section">
                    <ul class="list-inline social-links">
                        <?php for($istartups_i=1;$istartups_i<=10;$istartups_i++):
                         if (get_theme_mod('istartups_social_icon'.$istartups_i) != '' && get_theme_mod('istartups_social_icon_link'.$istartups_i) != '') { ?>
                            <li class="list-item"><a href="<?php echo esc_url(get_theme_mod('istartups_social_icon_link'.$istartups_i)); ?>"><i class="fa <?php echo esc_attr(get_theme_mod('istartups_social_icon'.$istartups_i)); ?>"></i></a></li>
                        <?php }
                        endfor; ?>
                    </ul>
                    <p><?php echo wp_kses_post(get_theme_mod('footer_copyrights')); ?></p>
                    <p><?php esc_html_e('Powered by','istartups'); ?> <a href="<?php echo esc_url('https://champthemes.com/wordpress-themes/istartups-wordpress-theme/'); ?>"><?php esc_html_e('iStartups WordPress Theme','istartups'); ?></a></p>
                </div>
            </div>
        </div>
    </div>
</footer>
<?php } 
 wp_footer(); ?> 
</div>
</body>
</html>