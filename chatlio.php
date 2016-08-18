<?php

/**
 *	Plugin Name: Chatlio for WordPress
 *	Plugin URI: http://chatlio.pragmatic-web.co.uk/
 *	Description: Chatlio plugin for WordPress
 *	Version: 1.0
 *	Author: James Morrison / Pragmatic Web
 *	Author URI: https://www.pragmatic-web.co.uk/
 **/


/**
 * Security Check
 *
 * @since 1.0
 **/

defined( 'ABSPATH' ) || die( 'Direct access to this file is forbidden.' );


/**
 * Chatlio class
 *
 * @since 1.0
 **/

class Chatlio {

	/**
	 * Starter defines and vars for use later
	 *
	 * @since 1.0
	 **/

	// Holds option data.
	var $option_name = 'pwl_chatlio_options';
	var $options = array();
	var $option_defaults;

	// DB version, for schema upgrades.
	var $db_version = 1;

	// Instance
	static $instance;


	/**
	 * Constuct
	 * Fires when class is constructed, adds init hook
	 *
	 * @since 1.0
	 **/

	function __construct() {

		// Allow this instance to be called from outside the class
		self::$instance = $this;

		// Add frontend wp_head hook
		add_action( 'wp_head',    array( &$this, 'wp_head' ) );

		// Add admin init hook
		add_action( 'admin_init', array( &$this, 'admin_init' ) );

		// Add admin panel
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );

		// Setting plugin defaults here
		$this->option_defaults = array(
			'widget_id'  => '',
			'db_version' => $this->db_version,
		);

	}


	/**
	 * Frontend wp_head Callback
	 *
	 * @since 1.0
	 **/

	function wp_head() {

		// Get options
		$this->options = wp_parse_args( get_option( 'chatlio_options' ), $this->option_defaults );

		if ( isset ( $this->options[ 'widget_id' ] ) ) {

			$this->options[ 'widget_id' ] = esc_attr( $this->options[ 'widget_id' ] );

			echo '<script type="text/javascript">
			window._chatlio = window._chatlio||[];
			! function() {
				var t=document.getElementById("chatlio-widget-embed");if(t&&window.ChatlioReact&&_chatlio.init)return void _chatlio.init(t,ChatlioReact);for(var e=function(t){return function(){_chatlio.push([t].concat(arguments)) }},i=["configure","identify","track","show","hide","isShown","isOnline"],a=0;a<i.length;a++)_chatlio[i[a]]||(_chatlio[i[a]]=e(i[a]));var n=document.createElement("script"),c=document.getElementsByTagName("script")[0];n.id="chatlio-widget-embed",n.src="https://w.chatlio.com/w.chatlio-widget.js",n.async=!0,n.setAttribute("data-embed-version","2.1");
				n.setAttribute("data-widget-id", "' . $this->options[ 'widget_id' ] . '" );
				c.parentNode.insertBefore(n,c);
			}();
			</script>';

		}
	
	
	}
	
	/**
	 * Admin init Callback
	 *
	 * @since 1.0
	 **/

	function admin_init() {

		// Fetch and set up options.
		$this->options = wp_parse_args( get_option( 'chatlio_options' ), $this->option_defaults );

		// Register Settings
		$this::register_settings();

	}


	/**
	 * Admin Menu Callback
	 *
	 * @since 1.0
	 **/

	function admin_menu() {

		// Add settings page on Tools
		add_management_page( __('Chatlio'), __('Chatlio'), 'manage_options', 'chatlio-settings', array( &$this, 'chatlio_settings' ) );

	}


	/**
	 * Register Admin Settings
	 *
	 * @since 1.0
	 **/

	function register_settings() {

		register_setting( 'chatlio', 'chatlio_options', array( $this, 'chatlio_sanitize' ) );

		// The main section
		add_settings_section( 'chatlio_settings_section', 'Chatlio Settings', array( &$this, 'chatlio_settings_callback'), 'chatlio-settings' );

		// The Fields
		add_settings_field( 'widget_id', 'Widget ID', array( &$this, 'widget_id_callback'), 'chatlio-settings', 'chatlio_settings_section' );

	}


	/**
	 * Settings Callback
	 *
	 * @since 1.0
	 **/

	function chatlio_settings_callback() {}


	/**
	 * Widget ID Statuses Callback
	 *
	 * @since 1.0
	 **/

	function widget_id_callback() {
	?>

		<input type="input" id="chatlio_options[widget_id]" name="chatlio_options[widget_id]" value="<?php esc_attr_e( $this->options['widget_id'] ); ?>" >
		<label for="chatlio_options[widget_id]"><?php _e('Add your Widget ID to enable Chatlio', 'chatlio'); ?></label>

	<?php
	}


	/**
	 * Call settings page
	 *
	 * @since 1.0
	 **/

	function chatlio_settings() { 
	?>

		<div class="wrap">

			<h2><?php _e( 'Chatlio', 'chatlio' ); ?></h2>

			<form action="options.php" method="POST">
				<?php 
				settings_fields( 'chatlio' );
				do_settings_sections( 'chatlio-settings' );
				submit_button();
				?>
			</form>

		</div>

	<?php
	}


	/**
	 * Options sanitization and validation
	 *
	 * @param $input the input to be sanitized
	 * @since 1.0
	 **/
	function chatlio_sanitize( $input ) {

		$options = $this->options;

		$input[ 'db_version' ] = $this->db_version;

		foreach ( $options as $key => $value ) {
			$output[$key] = sanitize_text_field( $input[ $key ] );
		}

		return $output;

	}


	/**
	 * Add settings link on plugin
	 *
	 * @since 1.0
	 **/

	function add_settings_link( $links, $file ) {

		if ( plugin_basename( __FILE__ ) == $file ) {

			$settings_link = '<a href="' . admin_url( 'tools.php?page=chatlio-settings' ) .'">' . __( 'Settings', 'chatlio' ) . '</a>';
			array_unshift( $links, $settings_link );

		}

		return $links;

	}

}

new Chatlio();
