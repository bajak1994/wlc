<?php
/*
Plugin Name: WLC Form
Plugin URI: https://whitelabelcoders.com/
Description: A custom plugin for submitting form and show list of leads.
Version: 1.0
Author: Mateusz Bajak
*/

define( 'WLCFORM_TEXT_DOMAIN', 'wlcform' );
define( 'WLCFORM_LEADS_TABLE', 'wlcform_leads' );
define( 'WLCFORM_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) );
define( 'WLCFORM_PLUGIN_INCLUDES_DIR', WLCFORM_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'includes' );
define( 'WLCFORM_PLUGIN_TEMPLATES_DIR', WLCFORM_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'templates' );

require_once( WLCFORM_PLUGIN_INCLUDES_DIR . DIRECTORY_SEPARATOR . 'helpers.php' );
require_once( WLCFORM_PLUGIN_INCLUDES_DIR . DIRECTORY_SEPARATOR . 'WLCFORM.php' );

/**
 * Enqueue assets
 */
function wlcform_assets() {
  wp_enqueue_style( 'wlcform-styles', plugins_url( '/dist/main.css', __FILE__ ) );
  wp_enqueue_script( 'wlcform-script', plugins_url( '/dist/main.js', __FILE__ ), array( 'jquery' ), '1.0', true );
  wp_localize_script( 'wlcform-script', 'wclform', array( 'ajaxUrl' => admin_url( 'admin-ajax.php' ) ) );
}
add_action( 'wp_enqueue_scripts', 'wlcform_assets' );

/**
 * Initialize the WLCForm class.
 */
function wlcform() {
  $wlcform = new WLCForm();
}
add_action( 'plugins_loaded', 'wlcform', 10, 0 );

/**
 * On activation hook.
 */
function wlcform_activation() {
  global $wpdb;

  $table_name = $wpdb->prefix . WLCFORM_LEADS_TABLE;
  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE $table_name (
    id INT(11) NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    PRIMARY KEY  (id)
  ) $charset_collate;";

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql );
}
register_activation_hook( __FILE__, 'wlcform_activation' );
