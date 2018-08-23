<?php
function istartups_widgets_init() {
    register_sidebar(array(
        'name' => esc_html__('Main Sidebar', 'istartups'),
        'id' => 'main-sidebar',
        'description' => esc_html__('Main sidebar that appears on the right.', 'istartups'),
        'before_widget' => '<aside class="side-area-post">',
        'after_widget' => '</aside>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ));
}
add_action('widgets_init', 'istartups_widgets_init');
function istartups_single_meta() {
    $istartups_categories_list = get_the_category_list(', ','');
    $post_categories = get_the_category( get_the_ID() );
    $istartups_tags_list = get_the_tag_list('TAGGED ','');
    $istartups_author= ucfirst(get_the_author());
    $istartups_author_url= esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) );
    $istartups_comments = wp_count_comments(get_the_ID());     
    $total_comments = $istartups_comments->total_comments;
    $istartups_date = sprintf('<time datetime="%1$s">%2$s</time>', esc_attr(get_the_date('c')), esc_html(get_the_date('F d, Y')));
    if(is_single()){ ?>   
    <span class="byline"><?php esc_html_e('BY','istartups'); ?><span class="author"><a href="<?php echo $istartups_author_url; ?>"><?php echo $istartups_author; ?></a></span></span>
    <span class="posted-on"><?php esc_html_e('ON','istartups'); ?><a href="#"><?php echo $istartups_date; ?></a></span>
    <?php if($istartups_categories_list != ''){ ?><span class="cat-links"><?php esc_html_e('POSTED IN','istartups'); ?><?php echo str_replace(",", " ",$istartups_categories_list); ?></span><?php } ?>
    <?php if($istartups_tags_list != ''){ ?><span class="tags-link"><?php echo $istartups_tags_list; ?></span><?php } ?>
    <?php }else{ ?>
    <span class="sub-title"><?php esc_html_e('Posted By','istartups'); ?><a href="<?php echo $istartups_author_url; ?>" class="sub-detail"><?php echo $istartups_author; ?></a></span>
    <?php if($post_categories){ ?>
    <span class="sub-title"><?php esc_html_e('Category','istartups'); ?> <?php foreach ($post_categories as $cat){ ?><a class="sub-detail" href="<?php echo esc_url( get_category_link($cat) ); ?>"><?php echo esc_html($cat->name).''; ?></a><?php } ?></span>
    <?php } if ( has_post_thumbnail() == NULL ) { ?>
    <span class="sub-title"><?php esc_html_e('Date','istartups'); ?> <a href="#"><?php echo $istartups_date; ?></a></span>
    <?php } ?>
    <?php if($total_comments != 0){ ?>
    <span class="sub-title"><a href="<?php comments_link(); ?>" class="sub-detail"><span class="fa fa-comment"></span><span><?php echo $total_comments; ?><?php esc_html_e('Comments','istartups'); ?></span></a></span>
    <?php }
     } ?>
<?php }
