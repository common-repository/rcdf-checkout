<?php
/*
  Plugin Name: RCDF Checkout page
  Description: This plugin recording and saving customer data from the checkout page after a user-defined event
  Authot URI: https://github.com/nevredimiy
  Author:      Artem Litvinov
  Text Domain: rcdf-checkout
  Domain Path:  /languages/
  Version: 1.6
  Requires PHP: 7.4
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
  License: GPLv2 or later
 
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define('RCDF_CHECKOUT_VER', '1.6');

add_action('init', 'rcdf_checkout_add_textdomain');
function rcdf_checkout_add_textdomain() {
	load_plugin_textdomain( 'rcdf-checkout', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}


add_action( 'wp_ajax_get_client_data', 'rcdf_checkout_ajax_save_client_data' );
add_action( 'wp_ajax_nopriv_get_client_data', 'rcdf_checkout_ajax_save_client_data' );

function rcdf_checkout_ajax_save_client_data() {
	if ( empty( $_POST['rcdf_checkout_nonce'] ) ) {
		wp_die( '0' );
	}
	check_ajax_referer( 'rcdf_checkout', 'rcdf_checkout_nonce', true );
	$arr_data = array(
		"first_name" => isset ( $_POST['rcdf-checkout-firstName']) ? sanitize_text_field( wp_unslash( $_POST['rcdf-checkout-firstName'] ) ) : '',
		"last_name" => isset ( $_POST['rcdf-checkout-lastName']) ? sanitize_text_field( wp_unslash( $_POST['rcdf-checkout-lastName'] ) ) : '',
		"phone" => isset ( $_POST['rcdf-checkout-phone']) ? sanitize_option( 'gmt_offset', preg_replace( '/[^0-9]/', '', sanitize_text_field( wp_unslash( $_POST['rcdf-checkout-phone'] ) ) ) ) : '',
		"email" => isset ( $_POST['rcdf-checkout-email']) ? sanitize_email( wp_unslash( $_POST['rcdf-checkout-email'] ) ) : '',
		"product_name" => isset ( $_POST['rcdf-checkout-productName']) ? sanitize_text_field( wp_unslash( $_POST['rcdf-checkout-productName'] ) ) : '',
		"price" => isset ( $_POST['rcdf-checkout-price']) ? sanitize_text_field( wp_unslash( $_POST['rcdf-checkout-price'] ) ) : '',
		"time" => isset ( $_POST['rcdf-checkout-dNow']) ? sanitize_option( 'date_format', wp_unslash( $_POST['rcdf-checkout-dNow'] ) ) : ''
		);

	$arr_data = wp_unslash($arr_data);
	
	rcdf_checkout_insert_table_data( $arr_data );
	wp_die();
}

function rcdf_checkout_create_table() {
	global $wpdb;
  	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . "rcdf_checkout";
	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		first_name tinytext DEFAULT NULL,
		last_name tinytext DEFAULT NULL,
		phone varchar(12) DEFAULT '' NOT NULL,
		email varchar(55) DEFAULT '' NULL,
		product_name varchar(55) DEFAULT '' NULL,
		price varchar(55) DEFAULT '' NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

function rcdf_checkout_insert_table_data( $arr_data ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'rcdf_checkout';
	if($wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) ) ) != $table_name ) {
		rcdf_checkout_create_table();
	}
	$time = $arr_data["time"];
	$first_name = $arr_data["first_name"];
	$last_name = $arr_data["last_name"];
	$phone = $arr_data["phone"];
	$email = $arr_data["email"];
	$product_name = $arr_data["product_name"];
	$price = $arr_data["price"];
	$wpdb->query( $wpdb->prepare( "INSERT INTO %i ( `time`, `first_name`, `last_name`, `phone`, `email`, `product_name`, `price` ) values ( %s, %s, %s, %s, %s, %s, %s )", $table_name, $time, $first_name, $last_name, $phone, $email, $product_name, $price ) );
}

add_action( 'wp_enqueue_scripts', 'rcdf_checkout_assets', 99 );
function rcdf_checkout_assets() {
	$href_checkout = '';
	if(function_exists( 'wc_get_checkout_url' ) ){
		$href_checkout = wc_get_checkout_url();
	}

	if(is_checkout()) {
		wp_enqueue_script( 'rcdf_checkout', plugins_url( '/assets/rcdf-checkout-script.js', __FILE__ ), array( 'jquery' ), RCDF_CHECKOUT_VER, true );
		wp_localize_script('rcdf_checkout', 'rcdfCheckoutPlugin', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'href_checkout' => $href_checkout,
			'rcdf_checkout_nonce' => wp_create_nonce( 'rcdf_checkout' ),
			'selectors' => array(
				'first_name' => get_option( 'rcdf_checkout_first_name' ),
				'last_name' => get_option( 'rcdf_checkout_last_name' ),
				'phone' => get_option( 'rcdf_checkout_phone' ),
				'email' => get_option( 'rcdf_checkout_email' ),
				'product_name' => get_option( 'rcdf_checkout_product_name' ),
				'price' => get_option( 'rcdf_checkout_price' ),
				'trigger_element' => get_option( 'rcdf_checkout_trigger_element' ),
				'event_el' => get_option( 'rcdf_checkout_event_el' ),
				)
		));
	}
}

add_action( 'admin_menu', 'rcdf_checkout_admin_page' );
function rcdf_checkout_admin_page() {
	$hook_suffix = add_menu_page( __( 'RCDF Checkout', 'rcdf-checkout' ), __( 'RCDF Checkout', 'rcdf-checkout' ), 'manage_options', 'rcdf_checkout', 'rcdf_checkout_menu_page', plugins_url( '/assets/images/icon.png', __FILE__ ), 80 );
	add_action( "admin_print_scripts-{$hook_suffix}", 'rcdf_checkout_admin_scripts' );
	add_submenu_page( 'rcdf_checkout', __( 'Settings', 'rcdf-checkout' ), __( 'List of data', 'rcdf-checkout' ), 'manage_options', 'rcdf_checkout', 'rcdf_checkout_menu_page', 1 );
	add_submenu_page( 'rcdf_checkout', __( 'Settings', 'rcdf-checkout' ), __( 'Settings', 'rcdf-checkout' ), 'manage_options', 'rcdf-checkout-settings', 'rcdf_checkout_settings_page', 2 );
}

function rcdf_checkout_menu_page() {
	require plugin_dir_path( __FILE__ ) . 'assets/template/rcdf-checkout-options.php';
}

function rcdf_checkout_settings_page() {
	require plugin_dir_path( __FILE__ ) . 'assets/template/rcdf-checkout-settings.php';
}

function rcdf_checkout_admin_scripts() {
	wp_enqueue_style( 'rcdf-checkout-main-style', plugins_url('/assets/admin-main.css', __FILE__ ), false, RCDF_CHECKOUT_VER );
	wp_enqueue_script( 'rcdf-checkout-main-js', plugins_url( '/assets/admin-main.js', __FILE__ ), array( 'jquery' ), RCDF_CHECKOUT_VER, true );
}

function rcdf_checkout_activate() {
	rcdf_checkout_create_table ();
}
register_activation_hook( __FILE__, 'rcdf_checkout_activate' );

function rcdf_checkout_uninstall() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'rcdf_checkout';
    $wpdb->query( $wpdb->prepare( "DROP TABLE IF EXISTS %i", $table_name ) );
	wp_cache_flush();
}
register_uninstall_hook( __FILE__, 'rcdf_checkout_uninstall' );

// ---------------- RCDF Checkout Options -------------------------

add_action( 'admin_init', 'rcdf_checkout_custom_settings' );
function rcdf_checkout_custom_settings() {
	$args = array(
		'type' 				=> 'string',
		'sanitize_callback' => 'sanitize_text_field',
		'default'           => null
	);
	register_setting( 'rcdf_checkout_general_group', 'rcdf_checkout_first_name', $args );
	register_setting( 'rcdf_checkout_general_group', 'rcdf_checkout_last_name', $args );
	register_setting( 'rcdf_checkout_general_group', 'rcdf_checkout_phone', $args );
	register_setting( 'rcdf_checkout_general_group', 'rcdf_checkout_email', $args );
	register_setting( 'rcdf_checkout_general_group', 'rcdf_checkout_product_name', $args );
	register_setting( 'rcdf_checkout_general_group', 'rcdf_checkout_price', $args );
	register_setting( 'rcdf_checkout_general_group', 'rcdf_checkout_trigger_element', $args );
	register_setting( 'rcdf_checkout_general_group', 'rcdf_checkout_event_el', $args );

	add_settings_section( 'rcdf_checkout_general_section', __( 'Selectors of elements for tracking', 'rcdf-checkout' ), function() {
		esc_attr_e( 'Select the element and event for which data will be recorded. There are three events: blur, focus, click', 'rcdf-checkout' );
	}, 'rcdf_checkout' );
	add_settings_section( 'rcdf_checkout_trigger_section', __('Select the appropriate selector and event', 'rcdf-checkout'), function() {
		esc_attr_e( 'This is the field that triggers the recording of customer data', 'rcdf-checkout' );
	}, 'rcdf_checkout' );
	add_settings_field( 'first_name', __( 'First Name', 'rcdf-checkout' ), 'rcdf_checkout_first_name', 'rcdf_checkout', 'rcdf_checkout_general_section', array( 'label_for' => 'first_name' ) );
	add_settings_field( 'last_name', __( 'Last Name', 'rcdf-checkout' ), 'rcdf_checkout_last_name', 'rcdf_checkout', 'rcdf_checkout_general_section', array( 'label_for' => 'last_name' ) );
	add_settings_field( 'phone', __( 'Phone', 'rcdf-checkout' ), 'rcdf_checkout_phone', 'rcdf_checkout', 'rcdf_checkout_general_section', array( 'label_for' => 'phone' ) );
	add_settings_field( 'email', __( 'Email', 'rcdf-checkout' ), 'rcdf_checkout_email', 'rcdf_checkout', 'rcdf_checkout_general_section', array( 'label_for' => 'email' ) );
	add_settings_field( 'product_name', __( 'Product Name', 'rcdf-checkout' ), 'rcdf_checkout_product_name', 'rcdf_checkout', 'rcdf_checkout_general_section', array( 'label_for' => 'product_name' ) );
	add_settings_field( 'price', __( 'Price', 'rcdf-checkout' ), 'rcdf_checkout_price', 'rcdf_checkout', 'rcdf_checkout_general_section', array( 'label_for' => 'price' ) );
	add_settings_field( 'trigger_element', __( 'Selector element', 'rcdf-checkout' ), 'rcdf_checkout_trigger_event', 'rcdf_checkout', 'rcdf_checkout_trigger_section', array( 'label_for' => 'trigger_element' ) );
	add_settings_field( 'event_el', __( 'Event', 'rcdf-checkout' ), 'rcdf_checkout_event', 'rcdf_checkout', 'rcdf_checkout_trigger_section' );
}

function rcdf_checkout_first_name() {
	echo sprintf( '<input type="text" name="rcdf_checkout_first_name" id="first_name" value="%s" placeholder="#shipping-first_name">', esc_attr( get_option( 'rcdf_checkout_first_name' ) ) );
}

function rcdf_checkout_last_name() {
	echo sprintf( '<input type="text" name="rcdf_checkout_last_name" id="last_name" value="%s" placeholder="#shipping-last_name" >', esc_attr( get_option( 'rcdf_checkout_last_name' ) ) );
}

function rcdf_checkout_phone() {
	echo sprintf( '<input type="text" name="rcdf_checkout_phone" id="phone" value="%s" placeholder="#shipping-phone">', esc_attr( get_option( 'rcdf_checkout_phone' ) ) );
}

function rcdf_checkout_email() {
	echo sprintf( '<input type="text" name="rcdf_checkout_email" id="email" value="%s" placeholder="#email" >', esc_attr( get_option( 'rcdf_checkout_email' ) ) );
}

function rcdf_checkout_product_name() {
	echo sprintf( '<input type="text" name="rcdf_checkout_product_name" id="product_name" value="%s" placeholder=".wc-block-components-product-name">', esc_attr( get_option( 'rcdf_checkout_product_name' ) ) );
}

function rcdf_checkout_price() {
	echo sprintf( '<input type="text" name="rcdf_checkout_price" id="price" value="%s" placeholder=".wc-block-components-product-price__value" >', esc_attr( get_option( 'rcdf_checkout_price' ) ) );
}

function rcdf_checkout_trigger_event() {
	echo sprintf( '<input type="text" name="rcdf_checkout_trigger_element" id="trigger_element" value="%s" placeholder="#shipping-phone" >', esc_attr( get_option( 'rcdf_checkout_trigger_element' ) ) );
}

function rcdf_checkout_event () {
	$events = array( 'blur', 'focus', 'click' );
	$event_el = esc_attr( get_option( 'rcdf_checkout_event_el' ) );
	echo '<select name="rcdf_checkout_event_el">';
		foreach ( $events as $event ) {
			if( $event == $event_el ) {
		?>
				<option value="<?php echo esc_attr( $event ); ?>" selected="selected"><?php echo esc_attr( $event ); ?></option>
		<?php
			} else {
		?>
				<option value="<?php echo esc_attr( $event ); ?>"><?php echo esc_attr( $event ); ?></option>
		<?php
			}
		}
	echo '</select>';
}