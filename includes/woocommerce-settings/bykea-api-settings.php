<?php
function bykea_api_settings_tab() {
    woocommerce_admin_fields( bykea_get_api_settings_form() );
?>
<script>
jQuery(".forminp-textarea p").insertAfter("#wc_bykea_api_setings_web_tracking_content");
</script>
<?php
}

function bykea_save_api_settings() {
    woocommerce_update_options( bykea_get_api_settings_form() );
	
	$token=bykea_requestAPIClientToken();
	
	bykea_update_api_settings("token", $token);
	
	bykea_update_api_settings("webhook_url", site_url("bykea-webhook"));
	
	$auth_url=bykea_api_settings("auth_url");
	$tracking_url=str_replace("/v2/authenticate/customer", "/api/v1/trip/route/", $auth_url);
	
	bykea_update_api_settings("tracking_url", $tracking_url);
}

function bykea_update_api_settings($key, $value){
	update_option("wc_bykea_api_setings_".$key, $value);
}

function bykea_api_settings($key=""){
	$value="";
	
	if(!empty($key)){
		$value=get_option("wc_bykea_api_setings_".$key);
	}
	
	return $value;
}

function bykea_requestAPIClientToken(){
	$username=bykea_api_settings("login");
	$password=bykea_api_settings("password");
	$auth_url=bykea_api_settings("auth_url");

	$headers=array(
		'Content-Type' => 'application/json; charset=utf-8'
	);
	
	$postfields["username"]=$username;
	$postfields["password"]=$password;
	
	$token="";
	
	$postfields = array(
		'username'    => $username,
		'password'   => $password
	);
	
	$body=json_encode($postfields);
	
	$args = array(
		'body'        => $body,
		'headers'     => $headers
	);
	
	$response = wp_remote_post( $auth_url, $args );
	
	if(!is_wp_error($response)){
		if(isset($response["body"])){
			$body=json_decode($response["body"], true);
			
			if(isset($body["data"]["token"])){
				$token=$body["data"]["token"];
			}
		}
	}
	
	return $token;
}

function bykea_requestBykeaDelivery($args=array()){
	$delivery_url=bykea_api_settings("delivery_url");
	$token=bykea_api_settings("token");
	
	$headers=array(
		'Content-Type' => 'application/json; charset=utf-8',
		'x-api-customer-token' => $token
	);
	
	if(!isset($args["service_code"])){
		$args["service_code"]=100;
	}
	
	$postfields["meta"]["service_code"]=$args["service_code"]; //required number
	$postfields["meta"]["external_reference"]="any";
	
	$postfields["customer"]["phone"]=$args["customer_phone"]; //required
	
	$postfields["pickup"]["name"]=$args["pickup_name"];
	$postfields["pickup"]["phone"]=$args["pickup_phone"]; //required
	$postfields["pickup"]["lat"]=$args["pickup_lat"]; //required
	$postfields["pickup"]["lng"]=$args["pickup_lng"]; //required
	$postfields["pickup"]["address"]=$args["pickup_address"]; //required
	$postfields["pickup"]["gps_address"]=$args["pickup_gps_address"];
	
	$index=0;
	
	foreach($args["bookings"] as $booking){
		if(!isset($booking["service_code"])){
			$booking["service_code"]="21";
		}
		if(!isset($booking["dropoff_gps_address"])){
			$booking["dropoff_gps_address"]=$booking["dropoff_address"];
		}
		if(!isset($booking["parcel_value"])){
			$booking["parcel_value"]=0;
		}
		if(!isset($booking["reference"])){
			$booking["reference"]="0";
		}
		if(!isset($booking["insurance"])){
			$booking["insurance"]=false;
		}
		
		$postfields["bookings"][$index]["meta"]["service_code"]=$booking["service_code"]; //required number 21 or 22
		$postfields["bookings"][$index]["dropoff"]["name"]=$booking["dropoff_name"];
		$postfields["bookings"][$index]["dropoff"]["phone"]=$booking["dropoff_phone"]; //required
		$postfields["bookings"][$index]["dropoff"]["address"]=$booking["dropoff_address"];
		$postfields["bookings"][$index]["dropoff"]["gps_address"]=$booking["dropoff_gps_address"]; //required
		$postfields["bookings"][$index]["details"]["created_by"]="5e96c19ce406840b4613fbe6"; //required
		
		$postfields["bookings"][$index]["details"]["parcel_value"]=$booking["parcel_value"]; //required number
		$postfields["bookings"][$index]["details"]["reference"]=$booking["reference"];
		$postfields["bookings"][$index]["details"]["insurance"]=$booking["insurance"];
		
		
		if(isset($booking["voice_note"])){
			$postfields["bookings"][$index]["details"]["voice_note"]=$booking["voice_note"]; //required
		}
		
		$index++;
	}
	
	$body=json_encode($postfields);
	
	$args = array(
		'body'        => $body,
		'headers'     => $headers
	);
	
	$response = wp_remote_post( $delivery_url, $args );
	
	$results["success"]=false;
	$results["message"]="";
	$results["data"]=array();
		
	if(!is_wp_error($response)){
		if(isset($response["body"])){
			$body=json_decode($response["body"], true);
			
			if(isset($body["data"])){
				$results=$body["data"];
				$results["success"]=true;
			}
			elseif(isset($body["message"])){
				$results["message"]=$body["message"];
			}
		}
	}
	
	return $results;
}

function bykea_get_api_settings_form(){
    $settings = array(
        'section_title' => array(
            'name'     => __( 'Bykea API Settings', 'woocommerce-bykea-api-settings' ),
            'type'     => 'title',
            'desc'     => '',
            'id'       => 'wc_bykea_api_setings_section_title'
        ),
        'bykea_login' => array(
            'name' => __( 'Login', 'woocommerce-bykea-api-settings' ),
            'type' => 'text',
            'desc' => '',
            'id'   => 'wc_bykea_api_setings_login'
        ),
        'bykea_password' => array(
            'name' => __( 'Password', 'woocommerce-bykea-api-settings' ),
            'type' => 'password',
            'desc' => '',
            'id'   => 'wc_bykea_api_setings_password'
        ),
        'bykea_auth_url' => array(
            'name' => __( 'Authentcated URL', 'woocommerce-bykea-api-settings' ),
            'type' => 'text',
            'desc' => '',
            'id'   => 'wc_bykea_api_setings_auth_url'
        ),
        'bykea_token' => array(
            'name' => __( 'Token', 'woocommerce-bykea-api-settings' ),
            'type' => 'text',
            'desc' => '',
            'custom_attributes' => array(
				'readonly' => 'readonly'
			),
            'id'   => 'wc_bykea_api_setings_token'
        ),
        'bykea_delivery_url' => array(
            'name' => __( 'Delivery URL', 'woocommerce-bykea-api-settings' ),
            'type' => 'text',
            'desc' => '',
            'id'   => 'wc_bykea_api_setings_delivery_url'
        ),
        'webhook_url' => array(
            'name'     => __( 'Webhook URL', 'woocommerce-bykea-api-settings' ),
            'type'     => 'text',
            'desc'     => '',
            'custom_attributes' => array(
				'readonly' => 'readonly',
			),
            'id'       => 'wc_bykea_api_setings_webhook_url'
        ),
        'show_order_details' => array(
            'name'     => __( 'Orders Page Settings', 'woocommerce-bykea-api-settings' ),
            'type'     => 'checkbox',
            'desc'     => 'Show booking number &amp; status on orders column',
            'id'       => 'wc_bykea_api_setings_show_order_details'
        ),
        'show_delivery_address' => array(
            'name'     => __( '', 'woocommerce-bykea-api-settings' ),
            'type'     => 'checkbox',
            'desc'     => 'Show delivery address on orders on orders column',
            'id'       => 'wc_bykea_api_setings_show_delivery_address'
        ),
        'sms_sending_url' => array(
            'name'     => __( 'SMS Sending URL', 'woocommerce-bykea-api-settings' ),
            'type'     => 'text',
            'desc'     => '',
            'id'       => 'wc_bykea_api_setings_sms_sending_url'
        ),
        'web_tracking_content' => array(
            'name'     => __( 'Web Tracking Content', 'woocommerce-bykea-api-settings' ),
            'type'     => 'textarea',
            'desc'     => '<p>In you message below, insert #WebTrack# where you want the tracking link to appear.</p><p>Example:</p><p><i>Hello Sir, Your delivery is on the way, please track here: #WebTrack#</i></p>',
            'custom_attributes' => array(
				'rows' => '4',
			),
            'id'       => 'wc_bykea_api_setings_web_tracking_content'
        ),
        'send_tracking_sms' => array(
            'name'     => __( '', 'woocommerce-bykea-api-settings' ),
            'type'     => 'checkbox',
            'desc'     => 'Send web tracking sms',
            'id'       => 'wc_bykea_api_setings_send_tracking_sms'
        ),
        'section_end' => array(
             'type' => 'sectionend',
             'id' => 'wc_bykea_api_setings_section_end'
        )
    );
	
    return apply_filters( 'wc_bykea_api_setings', $settings );
}