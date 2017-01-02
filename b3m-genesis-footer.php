<?php
/**
 * 	Plugin Name: 	B3M Genesis Footer
 * 	Plugin URI: 	http://rickrduncan.com/wordpress-plugins
 * 	Description: 	Customize footer credits from the Genesis admin page.
 *	Author: 		Rick R. Duncan - B3Marketing, LLC
 *	Author URI: 	https://rickrduncan.com
 *
 *  Credit: 		Nuts and Bolts Media, LLC (https://www.nutsandboltsmedia.com/genesis-custom-footer/)
 *
 * 	Version: 		1.0.1
 * 	License: 		GPLv3
 *
 *
 */


/**
* Exit if accessed directly
*
* @since 1.0.0
*/
if ( ! defined( 'ABSPATH' ) ) exit;


/**
* Check to make sure a Genesis child theme is active
*
* @since 1.0.0
*/
function b3mgf_require_genesis() {
	
	if ( 'genesis' != basename( TEMPLATEPATH ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die( sprintf( __( 'Sorry, but to activate this plugin the <a href="%s">Genesis Framework</a> is required.'), 'http://www.rickrduncan.com/go/get-genesis' ) );
	}
	
}
register_activation_hook( __FILE__, 'b3mgf_require_genesis' );


/**
* Add plugin 'Settings' link to the Plugins page
*
* @since 1.0.0
*/
function b3mgf_add_settings_link( $links ) {
    $settings_link = '<a href="admin.php?page=genesis">' . __( 'Settings' ) . '</a>';
    array_unshift($links, $settings_link);
  	return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'b3mgf_add_settings_link' );


/**
* Confirm Genesis is still running
*
* Since this is a Genesis specific plugin we only run our code on genesis_init
* Plugin is installed and at that time we knew that Genesis was installed. Now, before we run any
* Genesis specific code we test to make certain that Genesis is still running/activated. Otherwise we might blow up website.
*
* @since 1.0.0
*/
function b3mgf_init() {
	add_action( 'after_setup_theme', 'b3mgf_footer_customizations_after_setup_theme' );
	
}
add_action( 'genesis_init', 'b3mgf_init' );


/**
* Now that we know Genesis is still running, execute our footer code
*
* @since 1.0.0
*/
function b3mgf_footer_customizations_after_setup_theme(){
	add_filter( 'genesis_theme_settings_defaults', 'b3mgf_custom_footer_defaults' );
	add_action( 'genesis_settings_sanitizer_init', 'b3mgf_sanitization_filters' );
	add_action('genesis_theme_settings_metaboxes', 'b3mgf_footer_settings_box');
	add_action('after_setup_theme', 'b3mgf_remove_footer_filters' );
	add_filter('genesis_footer_output', 'b3mgf_footer_creds_text', 10, 3);
}


/**
* Register default settings using Genesis shortcodes
*
* @since 1.0.0
*/
function b3mgf_custom_footer_defaults( $defaults ) {
 
	$defaults['b3mgf_footer_creds'] = 'Copyright [footer_copyright] [footer_childtheme_link] &amp;middot; [footer_genesis_link] [footer_studiopress_link] &amp;middot; [footer_wordpress_link] &amp;middot; [footer_loginout]';
 
	return $defaults;
}


/**
* Sanitize input
*
* @since 1.0.0
*/
function b3mgf_sanitization_filters() {
	genesis_add_option_filter( 'safe_html', GENESIS_SETTINGS_FIELD, array( 'b3mgf_footer_creds' ) );
}


/**
* Register metabox
*
* @since 1.0.0
*/
function b3mgf_footer_settings_box( $_genesis_theme_settings_pagehook ) {
	add_meta_box( 'b3mgf-genesis-settings', __( 'Genesis Custom Footer' ), 'b3mgf_footer_box', $_genesis_theme_settings_pagehook, 'main', 'high' );
}


/**
* Create metabox
*
* @since 1.0.0
*/
function b3mgf_footer_box() {
	?>
	<p><?php _e("Enter your custom credits text, including HTML if desired.", 'b3mgf_footer'); ?></p>
	<label>Custom Footer Text:</label>
	<textarea id="b3mgf_footer_creds" class="large-text" name="<?php echo GENESIS_SETTINGS_FIELD; ?>[b3mgf_footer_creds]" cols="78" rows="8" /><?php echo htmlspecialchars( genesis_get_option('b3mgf_footer_creds') ); ?></textarea>
    <p><?php echo ( '<strong>Default Text:</strong><br /><br /> <code>Copyright [footer_copyright] [footer_childtheme_link] &amp;middot; [footer_genesis_link] [footer_studiopress_link] &amp;middot; [footer_wordpress_link] &amp;middot; [footer_loginout]</code>' ); ?></p>
	<?php
}


/**
* Remove other filters if they exist. Someone could have customized footer inside of functions.php
*
* @since 1.0.0
*/
function b3mgf_remove_footer_filters() {
    remove_all_filters( 'genesis_footer_creds_text' );
}


/**
* And finally, display our custom footer text
*
* @since 1.0.1 - Added <p> tags around output of data.
* @since 1.0.0
*/
function b3mgf_footer_creds_text($creds) {
	
	$custom_creds = '<p>' . genesis_get_option('b3mgf_footer_creds') . '</p>';
	if ($custom_creds) {
		return $custom_creds;
	}
	else {
		return $creds;
	}
}
