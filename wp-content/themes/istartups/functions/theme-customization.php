<?php
function istartups_sanitize_select( $input, $setting ) {
  
  // Ensure input is a slug.
  $input = sanitize_key( $input );
  
  // Get list of choices from the control associated with the setting.
  $choices = $setting->manager->get_control( $setting->id )->choices;
  
  // If the input is a valid key, return it; otherwise, return the default.
  return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
}
function istartups_sanitize_checkbox( $checked ) {
  // Boolean check.
  return ( ( isset( $checked ) && true == $checked ) ? true : false );
}
/**
* Customization options
**/
function istartups_customize_register( $wp_customize ) {
  $wp_customize->remove_section("colors");
  /*-----------color option-----------*/
    $wp_customize->add_section( 'color_section' , array(
        'title'       => __( 'Colors', 'istartups' ),
        'capability'     => 'edit_theme_options',
        'priority' => 41
    ) );
  $wp_customize->add_setting(
      'theme_color',
      array(
          'default' => '#D90429',
          'capability'     => 'edit_theme_options',
          'sanitize_callback' => 'sanitize_hex_color',
      )
  );
  $wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      'theme_color',
      array(
          'label'      => __('Theme Color ', 'istartups'),
          'section' => 'color_section',
          'priority' => 10
      )
    )
  );
  $wp_customize->add_setting(
      'secondary_color',
      array(
          'default' => '#2B2D42',
          'capability'     => 'edit_theme_options',
          'sanitize_callback' => 'sanitize_hex_color',
      )
  );
  $wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      'secondary_color',
      array(
          'label'      => __('Secondary Color ', 'istartups'),
          'section' => 'color_section',
          'priority' => 10
      )
    )
  );
  $wp_customize->add_setting(
      'footer_bg_color',
      array(
          'default' => '#2B2D42',
          'capability'     => 'edit_theme_options',
          'sanitize_callback' => 'sanitize_hex_color',
      )
  );
  $wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      'footer_bg_color',
      array(
          'label'      => __('Footer Background Color ', 'istartups'),
          'section' => 'color_section',
          'priority' => 10
      )
    )
  );
  $wp_customize->add_setting(
      'footer_txt_color',
      array(
          'default' => '#ccc',
          'capability'     => 'edit_theme_options',
          'sanitize_callback' => 'sanitize_hex_color',
      )
  );
  $wp_customize->add_control(
    new WP_Customize_Color_Control(
      $wp_customize,
      'footer_txt_color',
      array(
          'label'      => __('Footer Text Color ', 'istartups'),
          'section' => 'color_section',
          'priority' => 10
      )
    )
  );
  /*---------------end-------------------*/
  $wp_customize->add_panel(
    'sidebar_setting',
    array(
        'title' => __( 'Sidebar Settings', 'istartups' ),
        'description' => __('Sidebar options','istartups'),
        'priority' => 41
    )
    );
    $wp_customize->add_section( 'posts_sidebar' , array(
        'title'       => __( 'Sidebar', 'istartups' ),
        'capability'     => 'edit_theme_options',
        'panel' => 'sidebar_setting'
    ) );
    $wp_customize->add_setting(
        'side_area_opt',
        array(
            'default' => 'right',
            'capability'     => 'edit_theme_options',
            'sanitize_callback' => 'istartups_sanitize_select',
            'priority' => 20, 
        )
    );
    $wp_customize->add_control(
        'side_area_opt',
        array(
            'section' => 'posts_sidebar',                
            'label'   => __('Sidebar Area','istartups'),
            'type'    => 'select',
            'choices'        => array(
                "left"   => esc_html__( "Left", 'istartups' ),
                "right"   => esc_html__( "Right", 'istartups' ),
            ),
        )
    );
    /*---------------meta data ---------------------------*/
    $wp_customize->add_section( 'show_meta_data' , array(
        'title'       => __( 'Show Meta Data', 'istartups' ),
        'capability'     => 'edit_theme_options',
        'panel' => 'sidebar_setting'
    ) );
    $wp_customize->add_setting(
        'show_meta',
        array(
            'default' => 1,
            'capability'     => 'edit_theme_options',
            'sanitize_callback' => 'istartups_sanitize_checkbox',
            'priority' => 20, 
        )
    );
    $wp_customize->add_control(
        'show_meta',
        array(
            'section' => 'show_meta_data',                
            'label'   => __('Show Meta Data','istartups'),
            'type'    => 'checkbox'
        )
    );
    /*------------end meta data----------------------------*/
    $wp_customize->add_setting(
        'menu_style',
        array(
            'default' => 'style-1',
            'capability'     => 'edit_theme_options',
            'sanitize_callback' => 'istartups_sanitize_select',
            'priority' => 20, 
        )
    );
    $wp_customize->add_control(
        'menu_style',
        array(
            'section' => 'title_tagline',                
            'label'   => __('Menu Style','istartups'),
            'type'    => 'select',
            'choices'        => array(
                "style-1"   => esc_html__( "Style 1", 'istartups' ),
                "style-2"   => esc_html__( "Style 2", 'istartups' ),
            ),
        )
    );
    $wp_customize->add_setting( 'header_height', array(
        'default'        => 80,
        'sanitize_callback' => 'sanitize_text_field',
        'capability'     => 'edit_theme_options',
    ) );
    $wp_customize->add_control( 'header_height', array(
        'label'   => __('Header Menu Height','istartups'),
        'section' => 'title_tagline',
        'type'    => 'text',
    ) );
/*--------------start footer-----------------------*/
  $wp_customize->add_panel(
    'footer',
    array(
      'title' => esc_html__( 'Footer','istartups' ),
      'description' => esc_html__('layout options', 'istartups'), 
      'priority' => 45,
    )
  );
  /* Content Widget Layout */
  $wp_customize->add_section(
    'footer_copyrights_section',
    array(
      'title' => esc_html__('Footer Copyrights Section','istartups'),
      'panel' => 'footer'
    )
  );
  $wp_customize->add_section(
    'footer_socials',
    array(
      'title' => esc_html__('Social Accounts','istartups'),
      'description' => __( 'In first input box, you need to add Font Awesome class which you can find <a target="_blank" href="https://fortawesome.github.io/Font-Awesome/icons/">here</a> and in second input box, you need to add your social media profile URL.<br /> Leave it empty to hide the icon.' , 'istartups'),
      'panel' => 'footer'
    )
  );
  $wp_customize->add_section(
    'footer_area',
    array(
      'title' => esc_html__('Footer Area','istartups'),
      'panel' => 'footer'
    )
  );
  $wp_customize->add_setting(
        'show_footer_area',
        array(
            'default' => 1,
            'capability'     => 'edit_theme_options',
            'sanitize_callback' => 'istartups_sanitize_checkbox',
            'priority' => 20, 
        )
    );
    $wp_customize->add_control(
        'show_footer_area',
        array(
            'section' => 'footer_area',                
            'label'   => __('Show Footer Area','istartups'),
            'type'    => 'checkbox'
        )
    );
  //adding setting for footer text area
  $wp_customize->add_setting('footer_copyrights',
    array(
      'sanitize_callback' => 'wp_kses',
    )
  );
  $wp_customize->add_control('footer_copyrights',
    array(
      'label'   => esc_html__('Footer Copy Rights','istartups'),
      'section' => 'footer_copyrights_section',
      'type'    => 'textarea',
    )
  );
  /* End Content Widget Layout */
  $istartups_social_icon = array();
  $istartups_social_icon[] =  array( 'slug'=>'istartups_social_icon1', 'default' => '', 'label' => esc_html__( 'Social Account 1', 'istartups' ),'priority' => '1' );
  $istartups_social_icon[] =  array( 'slug'=>'istartups_social_icon2', 'default' => '', 'label' => esc_html__( 'Social Account 2', 'istartups' ),'priority' => '3' );
  $istartups_social_icon[] =  array( 'slug'=>'istartups_social_icon3', 'default' => '', 'label' => esc_html__( 'Social Account 3', 'istartups' ),'priority' => '5' );
  $istartups_social_icon[] =  array( 'slug'=>'istartups_social_icon4', 'default' => '', 'label' => esc_html__( 'Social Account 4', 'istartups' ),'priority' => '7' );
  $istartups_social_icon[] =  array( 'slug'=>'istartups_social_icon5', 'default' => '', 'label' => esc_html__( 'Social Account 5', 'istartups' ),'priority' => '9' );
  foreach($istartups_social_icon as $istartups_social_icons){
    $wp_customize->add_setting(
      $istartups_social_icons['slug'],
      array(
        'default' => '',
        'capability'     => 'edit_theme_options',
        'type' => 'theme_mod',
        'sanitize_callback' => 'sanitize_text_field',
      )
    );
    $wp_customize->add_control(
      $istartups_social_icons['slug'],
      array(
        'type'  => 'text',
        'section' => 'footer_socials',
        'input_attrs' => array( 'placeholder' => esc_attr__('Enter Icon','istartups') ),
        'label'      =>   $istartups_social_icons['label'],
        'priority' => $istartups_social_icons['priority']
      )
    );
  }
  $istartups_social_icon_link = array();
  $istartups_social_icon_link[] =  array( 'slug'=>'istartups_social_icon_link1', 'default' => '', 'label' => esc_html__( 'Social Link 1', 'istartups' ),'priority' => '1' );
  $istartups_social_icon_link[] =  array( 'slug'=>'istartups_social_icon_link2', 'default' => '', 'label' => esc_html__( 'Social Link 2', 'istartups' ),'priority' => '3' );
  $istartups_social_icon_link[] =  array( 'slug'=>'istartups_social_icon_link3', 'default' => '', 'label' => esc_html__( 'Social Link 3', 'istartups' ),'priority' => '5' );
  $istartups_social_icon_link[] =  array( 'slug'=>'istartups_social_icon_link4', 'default' => '', 'label' => esc_html__( 'Social Link 4', 'istartups' ),'priority' => '7' );
  $istartups_social_icon_link[] =  array( 'slug'=>'istartups_social_icon_link5', 'default' => '', 'label' => esc_html__( 'Social Link 5', 'istartups' ),'priority' => '9' );
  foreach($istartups_social_icon_link as $istartups_social_icons){
    $wp_customize->add_setting(
      $istartups_social_icons['slug'],
      array(
        'default' => '',
        'capability'     => 'edit_theme_options',
        'type' => 'theme_mod',
        'sanitize_callback' => 'esc_url_raw',
      )
    );
    $wp_customize->add_control(
      $istartups_social_icons['slug'],
      array(
        'type'  => 'text',
        'section' => 'footer_socials',
        'priority' => $istartups_social_icons['priority'],
        'input_attrs' => array( 'placeholder' => esc_html__('Enter URL','istartups')),
      )
    );
  }
}
add_action( 'customize_register', 'istartups_customize_register' );
function hexToRgb($hex, $alpha = false) {
   $hex      = str_replace('#', '', $hex);
   $length   = strlen($hex);
   $rgb['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
   $rgb['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
   $rgb['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
   $rgb['a'] = $alpha;
   if ( $alpha ) {
      $rgb['a'] = $alpha;
   }
   return implode(array_keys($rgb)) . '(' . implode(', ', $rgb) . '0.19)';
}
/*-----------------custom css-------------------*/
function istartups_custom_css(){ ?>
  <style type="text/css">
  h1, h2, h3, h4, h5, h6,.startup .blog-title h3,.startup .main-sidebar .search-form label:after, .startup .main-sidebar .footer-widget .search-form label:after,.startup .search .search-input .search-form label:after, .startup .main-sidebar .footer-widget a:hover,.startup .head-content p,.startup .main-sidebar a:hover, .startup .main-sidebar .footer-widget a, .startup .main-sidebar .footer-widget .widget-title,.startup .search .search-input .search-form label:after, .startup .not-found .search-form label:after,#istartupsmenu ul,.comment-respond a:hover,.comment-form input[type="submit"]:hover,a:focus, a:hover,.startup .footer-section a:hover,#istartupsmenu ul ul li:hover > a, #istartupsmenu ul ul li a:hover,.startup .read-more a{ color: <?php echo esc_attr(get_theme_mod('theme_color','#d90429')); ?>; }
  .startup .main-sidebar a:hover{ color: <?php echo esc_attr(get_theme_mod('theme_color','#d90429')); ?>; }
   #istartupsmenu #menu-button{color: <?php echo esc_attr(get_theme_mod('theme_color','#d90429')); ?>; }

  .comment-form textarea:focus, .comment-form input:focus{ border-color: <?php echo esc_attr(get_theme_mod('theme_color','#D90429')); ?>; }
  .startup .main-sidebar .tagcloud a, .startup .main-sidebar .footer-widget .tagcloud a{ background-color: <?php echo esc_attr(get_theme_mod('theme_color','#D90429')); ?>;  }
  
  .startup .head-content{background-color: <?php echo esc_attr(hexToRgb(get_theme_mod('theme_color','#D90429'))); ?>;  }
  .startup .page-numbers.current, .startup a.page-numbers:hover{ background-color: <?php echo esc_attr(get_theme_mod('secondary_color','#2B2D42')); ?>; }
  .startup .page-numbers.current, .startup a.page-numbers:hover,.startup .page-numbers{ border-color: <?php echo esc_attr(get_theme_mod('secondary_color','#2B2D42')); ?>; }
  .startup .blog-space .date{ background-color: <?php echo esc_attr(get_theme_mod('theme_color','#d90429')); ?>;  }
  .button:focus, .button:hover, button:focus, button:hover, input[type='button']:focus, input[type='button']:hover, input[type='reset']:focus, input[type='reset']:hover, input[type='submit']:focus, input[type='submit']:hover{ border:0.1rem solid <?php echo esc_attr(get_theme_mod('theme_color','#D90429')); ?>; color: <?php echo esc_attr(get_theme_mod('theme_color','#D90429')); ?>; }
  .comment-form input[type="submit"]:hover{ border: 1px solid <?php echo esc_attr(get_theme_mod('theme_color','#D90429')); ?>; }
  #istartupsmenu ul ul li:hover > a, #istartupsmenu ul ul li a:hover,.button, button, input[type='button'], input[type='reset'], input[type='submit']{ background-color: <?php echo esc_attr(get_theme_mod('secondary_color','#2B2D42')); ?>; }
  .startup .footer{ background-color: <?php echo esc_attr(get_theme_mod('footer_bg_color','#2B2D42')); ?>; }
  .startup .footer-section p,.startup .footer-section a{ color: <?php echo esc_attr(get_theme_mod('footer_txt_color','#ccc')); ?>; }
  .main-logo, #istartupsmenu > ul > li > a, #istartupsmenu #menu-button, #istartupsside #menu-button{ height: <?php echo esc_attr(get_theme_mod('header_height','80')); ?>px; }
  #istartupsmenu #menu-button{font-size: <?php echo esc_attr(get_theme_mod('menu_size','26')); ?>px; }
  @media (max-width:1024px){
    #istartupsmenu ul ul li a{ background-color: transparent; color: <?php echo esc_attr(get_theme_mod('theme_color','#D90429')); ?>; }
    .main-logo, #istartupsmenu > ul > li > a, #istartupsmenu #menu-button{height: <?php echo esc_attr(get_theme_mod('header_height','40')); ?>px; }
     #istartupsmenu>ul>li>a{ color: <?php echo esc_attr(get_theme_mod('theme_color','#D90429')); ?>; }
    #istartupsmenu ul ul li:hover > a, #istartupsmenu ul ul li a:hover{ background-color: <?php echo esc_attr(get_theme_mod('secondary_color','#2B2D42')); ?>; }
  }
  </style>
<?php }
add_action('wp_head','istartups_custom_css',900);