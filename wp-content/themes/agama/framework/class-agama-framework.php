<?php

// Do not allow direct access to the file.
if( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Agama Framework Class
 *
 * @since Agama v1.0.1
 * @rewritten @since 1.2.9
 */
class Agama_Framework {
	
	/**
	 * Class Constructor
	 *
	 * @since 1.2.9
	 */
	public function __construct() {
        add_action( 'tgmpa_register', array( $this, 'tgmpa_register' ) );
		self::get_template_parts();
	}
	
	/**
	 * Get Template Parts
	 *
	 * @since 1.2.9
	 */
	private static function get_template_parts() {
        get_template_part( 'framework/class-agama-plugin-activation' );
		get_template_part( 'framework/class-agama-helper' );
		get_template_part( 'framework/class-agama-slider' );
		get_template_part( 'framework/class-agama-core' );
		get_template_part( 'framework/class-agama' );
		get_template_part( 'framework/class-agama-wc' );
		get_template_part( 'framework/class-agama-breadcrumb' );
		get_template_part( 'framework/class-agama-frontpage-boxes' );
		get_template_part( 'framework/widgets/widgets' );
		get_template_part( 'framework/admin/admin-init' );
	}
    
    /**
     * Register the required plugins for this theme.
     *
     * In this example, we register five plugins:
     * - one included with the TGMPA library
     * - two from an external source, one from an arbitrary source, one from a GitHub repository
     * - two from the .org repo, where one demonstrates the use of the `is_callable` argument
     *
     * The variables passed to the `tgmpa()` function should be:
     * - an array of plugin arrays;
     * - optionally a configuration array.
     * If you are not changing anything in the configuration array, you can remove the array and remove the
     * variable from the function call: `tgmpa( $plugins );`.
     * In that case, the TGMPA default settings will be used.
     *
     * This function is hooked into `tgmpa_register`, which is fired on the WP `init` action on priority 10.
     *
     * @since 1.3.5
     */
    public function tgmpa_register() {
        /*
         * Array of plugin arrays. Required keys are name and slug.
         * If the source is NOT from the .org repo, then source is also required.
         */
        $plugins = array(
            array(
                'name'              => 'WPForms Lite',
                'slug'              => 'wpforms-lite',
                'required'          => false,
                'force_activation'  => false
            )
        );
        
        /*
         * Array of configuration settings. Amend each line as needed.
         *
         * TGMPA will start providing localized text strings soon. If you already have translations of our standard
         * strings available, please help us make TGMPA even better by giving us access to these translations or by
         * sending in a pull-request with .po file(s) with the translations.
         *
         * Only uncomment the strings in the config array if you want to customize the strings.
         */
        $config = array(
            'id'           => 'agama',                 // Unique ID for hashing notices for multiple instances of TGMPA.
            'default_path' => '',                      // Default absolute path to bundled plugins.
            'menu'         => 'tgmpa-install-plugins', // Menu slug.
            'has_notices'  => true,                    // Show admin notices or not.
            'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
            'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
            'is_automatic' => false,                   // Automatically activate plugins after installation or not.
            'message'      => '',                      // Message to output right before the plugins table.

            /*
            'strings'      => array(
                'page_title'                      => __( 'Install Required Plugins', 'agama' ),
                'menu_title'                      => __( 'Install Plugins', 'agama' ),
                /* translators: %s: plugin name. * /
                'installing'                      => __( 'Installing Plugin: %s', 'agama' ),
                /* translators: %s: plugin name. * /
                'updating'                        => __( 'Updating Plugin: %s', 'agama' ),
                'oops'                            => __( 'Something went wrong with the plugin API.', 'agama' ),
                'notice_can_install_required'     => _n_noop(
                    /* translators: 1: plugin name(s). * /
                    'This theme requires the following plugin: %1$s.',
                    'This theme requires the following plugins: %1$s.',
                    'agama'
                ),
                'notice_can_install_recommended'  => _n_noop(
                    /* translators: 1: plugin name(s). * /
                    'This theme recommends the following plugin: %1$s.',
                    'This theme recommends the following plugins: %1$s.',
                    'agama'
                ),
                'notice_ask_to_update'            => _n_noop(
                    /* translators: 1: plugin name(s). * /
                    'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.',
                    'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.',
                    'agama'
                ),
                'notice_ask_to_update_maybe'      => _n_noop(
                    /* translators: 1: plugin name(s). * /
                    'There is an update available for: %1$s.',
                    'There are updates available for the following plugins: %1$s.',
                    'agama'
                ),
                'notice_can_activate_required'    => _n_noop(
                    /* translators: 1: plugin name(s). * /
                    'The following required plugin is currently inactive: %1$s.',
                    'The following required plugins are currently inactive: %1$s.',
                    'agama'
                ),
                'notice_can_activate_recommended' => _n_noop(
                    /* translators: 1: plugin name(s). * /
                    'The following recommended plugin is currently inactive: %1$s.',
                    'The following recommended plugins are currently inactive: %1$s.',
                    'agama'
                ),
                'install_link'                    => _n_noop(
                    'Begin installing plugin',
                    'Begin installing plugins',
                    'agama'
                ),
                'update_link' 					  => _n_noop(
                    'Begin updating plugin',
                    'Begin updating plugins',
                    'agama'
                ),
                'activate_link'                   => _n_noop(
                    'Begin activating plugin',
                    'Begin activating plugins',
                    'agama'
                ),
                'return'                          => __( 'Return to Required Plugins Installer', 'agama' ),
                'plugin_activated'                => __( 'Plugin activated successfully.', 'agama' ),
                'activated_successfully'          => __( 'The following plugin was activated successfully:', 'agama' ),
                /* translators: 1: plugin name. * /
                'plugin_already_active'           => __( 'No action taken. Plugin %1$s was already active.', 'agama' ),
                /* translators: 1: plugin name. * /
                'plugin_needs_higher_version'     => __( 'Plugin not activated. A higher version of %s is needed for this theme. Please update the plugin.', 'agama' ),
                /* translators: 1: dashboard link. * /
                'complete'                        => __( 'All plugins installed and activated successfully. %1$s', 'agama' ),
                'dismiss'                         => __( 'Dismiss this notice', 'agama' ),
                'notice_cannot_install_activate'  => __( 'There are one or more required or recommended plugins to install, update or activate.', 'agama' ),
                'contact_admin'                   => __( 'Please contact the administrator of this site for help.', 'agama' ),

                'nag_type'                        => '', // Determines admin notice type - can only be one of the typical WP notice classes, such as 'updated', 'update-nag', 'notice-warning', 'notice-info' or 'error'. Some of which may not work as expected in older WP versions.
            ),
            */
        );
        tgmpa( $plugins, $config );
    }
}

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
