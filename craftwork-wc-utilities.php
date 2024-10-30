<?php

/*
Plugin Name: Craftwork Utilities for Woocommerce
Plugin URI: https://wordpress.org/plugins/craftwork-utilities-for-woocommerce/
Description: A set of woocommerce utilities for increase customer engagement.
Version: 1.3.3
Author: palagorn.p
Author URI: https://palamike.com
Domain Path: /languages
License: GPL2
*/

define('CWUT_VERSION', '1.3.3');
define('CWUT_DIR', plugin_dir_path(__FILE__));
define('CWUT_LANG_DIR', dirname( plugin_basename( __FILE__ ) ) . '/languages');
define('CWUT_DIR_URL', plugin_dir_url( __FILE__ ));

require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
    require CWUT_DIR.'includes/class-cwut-core.php';
    CWUT_Core::init();
}
