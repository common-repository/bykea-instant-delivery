<?php
/*
Plugin Name:    Bykea Instant Delivery
Plugin URI:     https://profiles.wordpress.org/dizyn/
Description:    A WooCommerce delivery plugin for Pakistani ecommerce sellers to manage their on-demand hyperlocal deliveries in tier one cities of Pakistan.
Version:        1.0
Author:         Dizyn
Author URI:     https://profiles.wordpress.org/dizyn/
*/

define("BYDIZ_DIR", __DIR__);
define("BYDIZ_URL", plugin_dir_url( __FILE__ ));

require(BYDIZ_DIR."/includes/loader.php");

add_action("init", "bykea_init");