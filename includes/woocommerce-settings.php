<?php
require(BYDIZ_DIR."/includes/woocommerce-settings/bykea-pickup-addresses.php");
require(BYDIZ_DIR."/includes/woocommerce-settings/bykea-api-settings.php");

function bykea_wc_custom_settings_tabs( $settings_tabs ) {
	$settings_tabs['bykea_api_setings'] = __( 'Bykea Settings', 'woocommerce-bykea-api-settings' );
	$settings_tabs['bykea_pickup_addresses'] = __( 'Pickup Address', 'bykea-pickup-addresses' );
	
	return $settings_tabs;
}