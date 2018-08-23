<?php
/**
 * System Info
 *
 * These are functions are used for exporting data from Easy Digital Downloads.
 *
 * @package     WFC
 * @subpackage  Admin/System
 * @copyright   Copyright (c) 2014, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * System info
 *
 * Shows the system info panel which contains version data and debug info.
 * The data for the system info is generated by the Browser class.
 *
 * @since 1.12
 * @global $wpdb
 * @global object $wpdb Used to query the database using the WordPress
 *   Database API
 * @return void
 */


class TC_back_system_info {

    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;
    public $plug_lang;

    function __construct () {

        self::$instance =& $this;
		add_action( 'admin_menu'				, array($this , 'tc_add_system_info_page' ), 10 );
		$this -> plug_lang = TC_font_customizer::$instance -> plug_lang;
    }

    function tc_add_system_info_page() {
		add_submenu_page( 
			$parent_slug 		= null,
			$page_title 		= __( 'System Info', $this -> plug_lang ), 
			$menu_title 		= __( 'System Info', $this -> plug_lang ), 
			$capability 		= 'edit_theme_options', 
			$menu_slug 			= 'tc-system-info', 
			$function 			= array($this , 'tc_system_info')
		);
	}


	function tc_system_info() {
		global $wpdb;

		if ( ! class_exists( 'Browser' ) )
			require_once ( dirname( dirname( __FILE__ ) ) . '/librairies/class_browser.php' );

		$browser = new Browser();
		if ( get_bloginfo( 'version' ) < '3.4' ) {
			$theme_data = get_theme_data( get_stylesheet_directory() . '/style.css' );
			$theme      = $theme_data['Name'] . ' ' . $theme_data['Version'];
		} else {
			$theme_data = wp_get_theme();
			$theme      = $theme_data->Name . ' ' . $theme_data->Version;
		}

		// Try to identifty the hosting provider
		$host = false;
		if( defined( 'WPE_APIKEY' ) ) {
			$host = 'WP Engine';
		} elseif( defined( 'PAGELYBIN' ) ) {
			$host = 'Pagely';
		}
	?>
		<div class="wrap">
			<h2><?php _e( 'System Information', $this -> plug_lang ); ?></h2><br/>
			<form action="<?php echo esc_url( admin_url( 'edit.php?post_type=download&page=tc-system-info' ) ); ?>" method="post" dir="ltr">
<textarea readonly="readonly" onclick="this.focus();this.select()" id="system-info-textarea" name="tc-sysinfo" title="<?php _e( 'To copy the system info, click below then press Ctrl + C (PC) or Cmd + C (Mac).', $this -> plug_lang ); ?>" style="width: 800px;min-height: 800px;font-family: Menlo,Monaco,monospace;background: 0 0;white-space: pre;overflow: auto;display:block;">
### Begin System Info ###
## Please include this information when posting support requests ##
<?php do_action( 'tc_system_info_before' ); ?>
<?php //gets upgraded from option
$plug_options = get_option(TC_font_customizer::$instance -> plug_option_prefix);
$upgraded_from = isset($plug_options['tc_upgraded_from']) ? $plug_options['tc_upgraded_from'] : '';
?>
SITE_URL:                 <?php echo site_url() . "\n"; ?>
HOME_URL:                 <?php echo home_url() . "\n"; ?>
Multisite:                <?php echo is_multisite() ? 'Yes' . "\n" : 'No' . "\n" ?>

Plugin Version:           <?php echo TC_font_customizer::$instance -> plug_name . ' | v' . TC_font_customizer::$instance -> plug_version . "\n"; ?>
Upgraded From:            <?php echo $upgraded_from . "\n"; ?>
WordPress Version:        <?php echo get_bloginfo( 'version' ) . "\n"; ?>
Permalink Structure:      <?php echo get_option( 'permalink_structure' ) . "\n"; ?>
Active Theme:             <?php echo $theme . "\n"; ?>
<?php if( $host ) : ?>
Host:                     <?php echo $host . "\n"; ?>
<?php endif; ?>

<?php echo $browser ; ?>

ACTIVE PLUGINS:
<?php
$plugins = get_plugins();
$active_plugins = get_option( 'active_plugins', array() );

foreach ( $plugins as $plugin_path => $plugin ) {
	// If the plugin isn't active, don't show it.
	if ( ! in_array( $plugin_path, $active_plugins ) )
		continue;

	echo $plugin['Name'] . ': ' . $plugin['Version'] ."\n";
}

if ( is_multisite() ) :
?>
NETWORK ACTIVE PLUGINS:
<?php
$plugins = wp_get_active_network_plugins();
$active_plugins = get_site_option( 'active_sitewide_plugins', array() );

foreach ( $plugins as $plugin_path ) {
	$plugin_base = plugin_basename( $plugin_path );

	// If the plugin isn't active, don't show it.
	if ( ! array_key_exists( $plugin_base, $active_plugins ) )
		continue;

	$plugin = get_plugin_data( $plugin_path );

	echo $plugin['Name'] . ' :' . $plugin['Version'] ."\n";
}

endif;
?>

PHP Version:              <?php echo PHP_VERSION . "\n"; ?>
MySQL Version:            <?php echo mysql_get_server_info() . "\n"; ?>
Web Server Info:          <?php echo $_SERVER['SERVER_SOFTWARE'] . "\n"; ?>

WordPress Memory Limit:   <?php echo ( $this -> tc_let_to_num( WP_MEMORY_LIMIT )/( 1024 ) )."MB"; ?><?php echo "\n"; ?>
PHP Safe Mode:            <?php echo ini_get( 'safe_mode' ) ? "Yes" : "No\n"; ?>
PHP Memory Limit:         <?php echo ini_get( 'memory_limit' ) . "\n"; ?>
PHP Upload Max Size:      <?php echo ini_get( 'upload_max_filesize' ) . "\n"; ?>
PHP Post Max Size:        <?php echo ini_get( 'post_max_size' ) . "\n"; ?>
PHP Upload Max Filesize:  <?php echo ini_get( 'upload_max_filesize' ) . "\n"; ?>
PHP Time Limit:           <?php echo ini_get( 'max_execution_time' ) . "\n"; ?>
PHP Max Input Vars:       <?php echo ini_get( 'max_input_vars' ) . "\n"; ?>
PHP Arg Separator:        <?php echo ini_get( 'arg_separator.output' ) . "\n"; ?>
PHP Allow URL File Open:  <?php echo ini_get( 'allow_url_fopen' ) ? "Yes" : "No\n"; ?>

WP_DEBUG:                 <?php echo defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' . "\n" : 'Disabled' . "\n" : 'Not set' . "\n" ?>

WP Table Prefix:          <?php echo "Length: ". strlen( $wpdb->prefix ); echo " Status:"; if ( strlen( $wpdb->prefix )>16 ) {echo " ERROR: Too Long";} else {echo " Acceptable";} echo "\n"; ?>

Show On Front:            <?php echo get_option( 'show_on_front' ) . "\n" ?>
Page On Front:            <?php $id = get_option( 'page_on_front' ); echo get_the_title( $id ) . ' (#' . $id . ')' . "\n" ?>
Page For Posts:           <?php $id = get_option( 'page_for_posts' ); echo get_the_title( $id ) . ' (#' . $id . ')' . "\n" ?>

Session:                  <?php echo isset( $_SESSION ) ? 'Enabled' : 'Disabled'; ?><?php echo "\n"; ?>
Session Name:             <?php echo esc_html( ini_get( 'session.name' ) ); ?><?php echo "\n"; ?>
Cookie Path:              <?php echo esc_html( ini_get( 'session.cookie_path' ) ); ?><?php echo "\n"; ?>
Save Path:                <?php echo esc_html( ini_get( 'session.save_path' ) ); ?><?php echo "\n"; ?>
Use Cookies:              <?php echo ini_get( 'session.use_cookies' ) ? 'On' : 'Off'; ?><?php echo "\n"; ?>
Use Only Cookies:         <?php echo ini_get( 'session.use_only_cookies' ) ? 'On' : 'Off'; ?><?php echo "\n"; ?>

DISPLAY ERRORS:           <?php echo ( ini_get( 'display_errors' ) ) ? 'On (' . ini_get( 'display_errors' ) . ')' : 'N/A'; ?><?php echo "\n"; ?>
FSOCKOPEN:                <?php echo ( function_exists( 'fsockopen' ) ) ? 'Your server supports fsockopen.' : 'Your server does not support fsockopen.'; ?><?php echo "\n"; ?>
cURL:                     <?php echo ( function_exists( 'curl_init' ) ) ? 'Your server supports cURL.' : 'Your server does not support cURL.'; ?><?php echo "\n"; ?>
SOAP Client:              <?php echo ( class_exists( 'SoapClient' ) ) ? 'Your server has the SOAP Client enabled.' : 'Your server does not have the SOAP Client enabled.'; ?><?php echo "\n"; ?>
SUHOSIN:                  <?php echo ( extension_loaded( 'suhosin' ) ) ? 'Your server has SUHOSIN installed.' : 'Your server does not have SUHOSIN installed.'; ?><?php echo "\n";
do_action( 'tc_system_info_after' );
?>
### End System Info ###
</textarea>
				<!-- <p class="submit">
					<input type="hidden" name="tc-action" value="download_sysinfo" />
					<?php //submit_button( 'Download System Info File', 'primary', 'tc-download-sysinfo', false ); ?>
				</p> -->
			</form>
			</div>
		</div>
	<?php
	}//end of function

	/**
	 * Generates the System Info Download File
	 *
	 * @since 1.4
	 * @return void
	 */
	/*function tc_generate_sysinfo_download() {
		nocache_headers();

		header( "Content-type: text/plain" );
		header( 'Content-Disposition: attachment; filename="tc-system-info.txt"' );

		echo wp_strip_all_tags( $_POST['tc-sysinfo'] );
		edd_die();
	}
	add_action( 'tc_download_sysinfo', 'tc_generate_sysinfo_download' );*/


	/**
	 * TC Let To Num
	 *
	 * Does Size Conversions
	 *
	 * @since 1.12
	 */
	function tc_let_to_num( $v ) {
		$l   = substr( $v, -1 );
		$ret = substr( $v, 0, -1 );

		switch ( strtoupper( $l ) ) {
			case 'P': // fall-through
			case 'T': // fall-through
			case 'G': // fall-through
			case 'M': // fall-through
			case 'K': // fall-through
				$ret *= 1024;
				break;
			default:
				break;
		}

		return $ret;
	}
}//end of class