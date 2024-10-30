<?php
$bykea_order_ids=array();
$bykea_orders=array();

function bykea_init(){
	bykea_create_tables();
	
	add_filter('manage_edit-shop_order_columns', 'bykea_columns_head');
	
	add_action('manage_shop_order_posts_custom_column', 'bykea_columns_content', 10, 2);
	
	add_filter( 'bulk_actions-edit-shop_order', 'bykea_request_delivery_bulk_actions' );
	
	add_filter( 'handle_bulk_actions-edit-shop_order', 'bykea_bulk_action_handler', 10, 3 );
	
	add_action( 'admin_notices', 'bykea_admin_notices' );
	
	add_action( 'admin_head-edit.php', 'bykea_header_html' );
	
	add_action( 'admin_footer-edit.php', 'bykea_footer_html' );
	
	add_filter( 'woocommerce_settings_tabs_array', 'bykea_wc_custom_settings_tabs', 50 );
	
	add_action( 'woocommerce_settings_tabs_bykea_api_setings', 'bykea_api_settings_tab' );
	
	add_action( 'woocommerce_settings_tabs_bykea_pickup_addresses', 'bykea_pickup_addresses_tab' );
	
	add_action( 'woocommerce_update_options_bykea_api_setings', 'bykea_save_api_settings' );
	
	add_action('admin_enqueue_scripts', 'bykea_admin_scripts');

	add_action('wp_enqueue_scripts', 'bykea_client_scripts');
	
	bykea_custom_form_submissions();
}

function bykea_admin_scripts(){
	wp_enqueue_script( 'bykea-admin-script', plugins_url('js/admin-script.js', __DIR__ ), array(), '1.0');
}

function bykea_client_scripts(){
	wp_enqueue_script( 'bykea-client-script', plugins_url('js/client-script.js', __DIR__ ), array(), '1.0');
}

function bykea_header_html(){
    global $current_screen;
	
	if(isset($current_screen->post_type)){
		if($current_screen->post_type=="shop_order"){
			include(BYDIZ_DIR."/templates/header-html.php");
		}
	}
}

function bykea_footer_html(){
    global $current_screen;
	
	if(isset($current_screen->post_type)){
		if($current_screen->post_type=="shop_order"){
			include(BYDIZ_DIR."/templates/footer-html.php");
		}
	}
}

function bykea_request_delivery_bulk_actions($bulk_actions) {
	$bulk_actions['bykea_request_bulk_delivery'] = __( 'Ship with Bykea', 'bykea_request_bulk_delivery');
	return $bulk_actions;
}

function bykea_bulk_action_handler( $redirect_to, $doaction, $post_ids ) {
	if ( $doaction !== 'bykea_request_bulk_delivery' ) {
		return $redirect_to;
	}
	
	foreach ( $post_ids as $post_id ){
	}
	
	$redirect_to = add_query_arg( 'bykea_bulk_delivery_request', count( $post_ids ), $redirect_to );
	
	return $redirect_to;
}
 
function bykea_admin_notices(){
	if ( isset( $_POST['request_ship_with_bykea'] ) ) {
		_e(bykea_system_message("request_ship_with_bykea_success"));
		_e(bykea_system_message("request_ship_with_bykea_error"));
	}
}

function bykea_system_message($id){
	$message_html='';
	
	if(isset($_SESSION[$id])){
		$message_type="updated";
		$type=sanitize_text_field($_SESSION[$id]["type"]);
		$message=sanitize_text_field($_SESSION[$id]["message"]);
		
		if($type=="success"){
			$message_type="updated";
		}
		if($type=="error"){
			$message_type="error";
		}
		if($type=="warning"){
			$message_type="update-nag";
		}
		
		$message_html='<div id="message" class="'.$message_type.' fade"><p>'.$message.'</p></div>';
		
		unset($_SESSION[$id]);
	}
	
	return $message_html;
}

function bykea_set_message($id, $type, $message){
	if(!session_id()) {
		session_start();
	}
	$_SESSION[$id]["type"]=$type;
	$_SESSION[$id]["message"]=$message;
}

function bykea_columns_head($defaults){
	unset($defaults['order_total']);
	
	$defaults['bykea_delivery_details'] = 'Delivery Details';
	$defaults['order_total'] = 'Total';
	$defaults['bykea_request_delivery'] = '<div style="display:block; padding:0px 0px; text-align:right;" >Actions<div>';
	
    return $defaults;
}

function bykea_columns_content($column_name, $post_ID){
	if ($column_name == 'bykea_delivery_details'){
		$ship_to_html='';
		
		$show_order_details=bykea_api_settings("show_order_details");
		$show_delivery_address=bykea_api_settings("show_delivery_address");
		
		$order = new WC_Order($post_ID); // Order id
		
		$address_1=$order->get_shipping_address_1();
		
		if(empty($address_1)){
			$address_1=$order->get_billing_address_1();
		}
		
		$city=$order->get_shipping_city();
		
		if(empty($city)){
			$city=$order->get_billing_city();
		}
		
		$state=$order->get_shipping_state();
		
		if(empty($state)){
			$state=$order->get_billing_state();
		}
		
		if($show_delivery_address=="yes"){
			$ship_to_html=$address_1.", ".$city.", ".$state;
		}
		
		$booking_info=bykea_getDeliveryInfo($post_ID);
		if(isset($booking_info["booking_id"])){
			$booking_id=$booking_info["booking_id"];
			$booking_no=$booking_info["booking_no"];
			
			$WebhookInfo=bykea_getWebhookInfo($booking_id);
			
			$trackinglink="";
			$event="";
			
			if(!empty($WebhookInfo)){
				foreach($WebhookInfo as $info){
					if($info["event"]=="trackinglink"){
						$trackinglink=$info["tracking_link"];
					}
					
					$event=$info["event"];
					
					if(strtolower($event)=="partner"){
						$event="Feedback";
					}
				}
			}
			
			if($show_order_details=="yes"){
				$ship_to_html.="<br/>";
				
				if(!empty($trackinglink)){
					$ship_to_html.='<a href="'.$trackinglink.'" target="_blank" >'.$booking_no.'</a>';
				}
				else{
					$ship_to_html.=$booking_no;
				}
				
				if(!empty($event)){
					$ship_to_html.=" - ";
					$ship_to_html.=ucfirst($event);
				}
			}
		}
		
		_e($ship_to_html);
	}
	
	if ($column_name == 'bykea_request_delivery'){
		global $bykea_order_ids, $bykea_orders; //global variable in functions.php used to identify the order IDs that have bykea as shipping method.
		
		$order_index=count($bykea_orders);
		
		$booking_id=bykea_getDeliveryInfo($post_ID, "booking_id");
		
		$bykea_orders[$order_index]["order_id"]=bykea_getDeliveryInfo($post_ID, "order_id");
		$bykea_orders[$order_index]["batch_no"]=bykea_getDeliveryInfo($post_ID, "batch_no");
		$bykea_orders[$order_index]["booking_no"]=bykea_getDeliveryInfo($post_ID, "booking_no");
		
		$webhooks_expired_details=array();
		
		if(!empty($booking_id)){
			$webhooks_expired_details=bykea_get_webhooks_details(array(
				"trip_id"=>$booking_id,
				"event"=>"expired"
			));
		}
		
		if(empty($booking_id) || !empty($webhooks_expired_details)){
			_e('<div style="display:block; padding:0px 10px; text-align:right;" ><button type="button" style="border:0px; background:#090; color:#fff; padding:3px 10px 5px 10px; border-radius:3px; cursor:pointer; margin:0px auto;" onclick="bydiz_shipWithByKeaSingle('.$post_ID.')" title="Ship with Bykea" >B</button><div>');
		}
		else{
			$bykea_order_ids[]=$post_ID;
			
			_e('<div style="display:block; padding:0px 10px; text-align:right;" ><button type="button" style="border:0px; background:#9c9; color:#fff; padding:3px 10px 5px 10px; border-radius:3px;" disabled="disabled" title="Ship with Bykea" >B</button><div>');
		}
	}
}

function bykea_send_email($args){
	$attachments=array();
	$subject="";
	$message="";
	$to_email="";
	
	$headers[] = 'Content-Type: text/html; charset=UTF-8';
	
	if(isset($args["from_email"])){
		if(!isset($args["from_name"])){
			$args["from_name"]="";
		}
		$headers[] = 'From: '.$args["from_name"].' <'.$args["from_email"].'>';
	}
	
	if(isset($args["reply_email"])){
		if(!isset($args["reply_name"])){
			$args["reply_name"]="";
		}
		$headers[] = 'Reply-To: '.$args["reply_name"].' <'.$args["reply_email"].'>';
	}
	
	if(isset($args["attachments"])){
		$attachments=$args["attachments"];
	}
	
	if(isset($args["subject"])){
		$subject=$args["subject"];
	}
	
	if(isset($args["to_email"])){
		$to_email=$args["to_email"];
	}
	
	if(isset($args["message"])){
		$message='<html>
		<head>
		<title>'.$subject.'</title>
		</head>
		<body>
		'.$args["message"].'
		</body>
		</html>';
	}
	
	return wp_mail( $to_email, $subject, $message, $headers, $attachments );
}

function bykea_price_value($value){
	$value_parts=explode(".", $value);
	
	$value=$value_parts[0];
	
	if(isset($value_parts[1])){
		if(($value_parts[1]*1)>0){
			$value=$value+1;
		}
	}
	
	return $value;
}

function bykea_custom_form_submissions(){
	global $wpdb;
	
	$url_parts=bykea_url_parts();
	
	if(bykea_valued(0, $url_parts)=="download-orders-csv"){
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=export-wc-orders-".date("d-m-Y-H-i-s").".csv");
		header("Pragma: no-cache");
		header("Expires: 0");
		
		$listingsCSV[0]=array("customer_name", "mobile_number", "delivery_address", "city", "amount", "booking_no", "order_no", "payment_method", "order_date", "customer_email", "status", "shipping_method", "order_notes");
		
		$post_date=bykea_minus_days(7, date("Y-m-d"), "Y-m-d H:i:s");
		
		$sqlStatement="SELECT ID FROM ".$wpdb->prefix."posts WHERE post_type='shop_order' AND post_date>='$post_date' ORDER BY post_date DESC";
		
		$shop_orders = $wpdb->get_results($sqlStatement, "ARRAY_A");
		
		if(count($shop_orders)<250){
			$sqlStatement="SELECT ID, post_date FROM ".$wpdb->prefix."posts WHERE post_type='shop_order' ORDER BY post_date DESC LIMIT 250";
		
			$shop_orders = $wpdb->get_results($sqlStatement, "ARRAY_A");
		}
		
		if(!empty($shop_orders)){
			foreach($shop_orders as $shop_order){
				$order_id=$shop_order["ID"];
				$order_date=$shop_order["post_date"];
				
				$order = new WC_Order($order_id); // Order id
				$customer_name=$order->get_billing_first_name().' '.$order->get_billing_last_name();
				$mobile_number=$order->get_billing_phone();
				
				$address_1=$order->get_shipping_address_1();
		
				if(empty($address_1)){
					$address_1=$order->get_billing_address_1();
				}
				
				$city=$order->get_shipping_city();
				
				if(empty($city)){
					$city=$order->get_billing_city();
				}
				
				$state=$order->get_shipping_state();
				
				if(empty($state)){
					$state=$order->get_billing_state();
				}
				
				$delivery_address=$address_1.", ".$city.", ".$state;
				
				$order_total=$order->get_total();
				$bykea_shipping_ID="";
				$payment_method=$order->get_payment_method();
				$customer_email=$order->get_billing_email();
				$status=$order->get_status();
				$shipping_method=$order->get_shipping_method();
				
				$delivery_info=bykea_getDeliveryInfo($order_id);
				if(!empty($delivery_info)){
					$shipping_method="bykea";
				}
				
				$order_notes=$order->get_customer_note();
				
				$delivery_requests=bykea_get_delivery_requests(array("order_id"=>$order_id));
				
				if(isset($delivery_requests[0]["booking_id"])){
					$bykea_shipping_ID=$delivery_requests[0]["booking_no"];
				}
				
				$listingsCSV[]=array($customer_name, $mobile_number, $delivery_address, $city, $order_total, $bykea_shipping_ID, $order_id, $payment_method, $order_date, $customer_email, $status, $shipping_method, $order_notes);
			}
		}
		
		if(!empty($listingsCSV)){
			$fp = fopen('php://output', 'w');

			foreach ($listingsCSV as $fields) {
				fputcsv($fp, $fields);
			}
			
			fclose($fp);
		}
		
		exit;
	}
	
	if(bykea_valued(0, $url_parts)=="bykea-webhook"){
		save_webhooks_details();
		
		exit;
	}
	
	if(bykea_valued(0, $url_parts)=="ajax"){
		if(isset($_POST["get_order_fields_details"])){
			$order_ids=sanitize_text_field($_POST["order_ids"]);
			
			$order_details=array();
			
			if(!empty($order_ids)){
				$order_ids=explode(",", $order_ids);
				
				foreach($order_ids as $order_id){
					$order = new WC_Order($order_id); // Order id
					$customer_name=$order->get_billing_first_name()." ".$order->get_billing_last_name();
					
					$order_total=$order->get_total();
					$order_total=bykea_price_value($order_total);
					
					$address_1=$order->get_shipping_address_1();
		
					if(empty($address_1)){
						$address_1=$order->get_billing_address_1();
					}
					
					$city=$order->get_shipping_city();
					
					if(empty($city)){
						$city=$order->get_billing_city();
					}
					
					$state=$order->get_shipping_state();
					
					if(empty($state)){
						$state=$order->get_billing_state();
					}
					
					$shipping_address=$address_1.", ".$city.", ".$state;
					
					$order_details[]=array(
						"order_id"=>$order_id,
						"customer_name"=>$customer_name,
						"shipping_address"=>$shipping_address,
						"order_total"=>$order_total
					);
				}
			}
			
			$output["order_details"]=$order_details;
			
			_e(json_encode($output));
		}
		
		exit;
	}
	
	if(isset($_POST["request_ship_with_bykea"])){
		$order_ids=sanitize_text_field($_POST["order_ids"]);
		$delivery_addresses=sanitize_text_field($_POST["delivery_addresses_values"]);
		$CODs=sanitize_text_field($_POST["CODsValues"]);
		
		$delivery_location=0;
		
		if(isset($_POST["delivery_locations"])){
			$delivery_location=sanitize_text_field($_POST["delivery_locations"]);
		}
		
		$service_code=100;
		$customer_phone="";
		
		$pickup_location=array();
		$pickup_location_name="";
		$dropoff_lat=0;
		$dropoff_lng=0;
		
		if($delivery_location>0){
			$pickup_addresses=bykea_get_pickup_addresses(array(
				"address_ID"=>$delivery_location
			));
			
			if(isset($pickup_addresses[0])){
				$pickup_location_name=$pickup_addresses[0]["location_name"];
				
				$pickup_location["pickup_name"]=$pickup_addresses[0]["contact_name"];
				$pickup_location["pickup_phone"]=$pickup_addresses[0]["contact_mobile"];
				$pickup_location["pickup_lat"]=$pickup_addresses[0]["latitude"];
				$pickup_location["pickup_lng"]=$pickup_addresses[0]["longitude"];
				$pickup_location["pickup_address"]=$pickup_addresses[0]["address"];
				$pickup_location["pickup_gps_address"]=$pickup_addresses[0]["gps_address"];
				
				$dropoff_lat=$pickup_addresses[0]["latitude"];
				$dropoff_lng=$pickup_addresses[0]["longitude"];
			}
		}
		
		$dropoff_locations=array();
		$dropoffIndex=0;
		$successful_request=array();
		$unsuccessful_request=array();
		
		if(!empty($order_ids) && !empty($delivery_addresses) && !empty($CODs)){
			$order_ids=explode(",", $order_ids);
			$delivery_addresses=explode("==", $delivery_addresses);
			$CODs=explode("==", $CODs);
			
			foreach($order_ids as $order_key=>$order_id){
				if(isset($CODs[$order_key])){
					$order = new WC_Order($order_id); // Order id
					
					$dropoff_phone=$order->get_billing_phone();
					
					$parcel_value=$CODs[$order_key];
					$parcel_value=bykea_price_value($parcel_value);
					
					$dropoff_locations[$dropoffIndex]["dropoff_name"]=$order->get_billing_first_name()." ".$order->get_billing_last_name();
					$dropoff_locations[$dropoffIndex]["dropoff_phone"]=$dropoff_phone;
					$dropoff_locations[$dropoffIndex]["dropoff_address"]=$delivery_addresses[$order_key];
					$dropoff_locations[$dropoffIndex]["dropoff_gps_address"]=$delivery_addresses[$order_key];
					$dropoff_locations[$dropoffIndex]["lat"]="";
					$dropoff_locations[$dropoffIndex]["lng"]="";
					$dropoff_locations[$dropoffIndex]["parcel_value"]=$parcel_value;
					$dropoff_locations[$dropoffIndex]["reference"]=$order_id;
					
					$dropoffIndex++;
				}
			}
			
			if(!empty($pickup_location)){
				$args=$pickup_location;
				
				$args["customer_phone"]=bykea_api_settings("login");
				$args["bookings"]=$dropoff_locations;
				
				
				$delivery_request=bykea_requestBykeaDelivery($args);
				
				if(isset($delivery_request["batch_booking_id"])){
					foreach($order_ids as $order_id){
						foreach($delivery_request["bookings"] as $booking){
							if($booking["reference"]==$order_id){
								bykea_new_delivery_requests(array(
									"order_id"=>$order_id,
									"batch_booking_id"=>$delivery_request["batch_booking_id"],
									"batch_id"=>$delivery_request["batch_id"],
									"batch_no"=>$delivery_request["batch_no"],
									"booking_id"=>$booking["booking_id"],
									"booking_no"=>$booking["booking_no"],
									"display_tag"=>$booking["display_tag"],
									"reference"=>$booking["reference"]
								));
								
								$order = new WC_Order($order_id);
								
								$successful_request[]=array(
									"location"=>$pickup_location_name,
									"batch_no"=>$delivery_request["batch_no"],
									"booking_no"=>$booking["booking_no"],
									"order"=>"#".$order_id." ".$order->get_billing_first_name()." ".$order->get_billing_last_name()
								);
							}
						}
					}
				}
				else{
					if(isset($delivery_request["message"])){
						$unsuccessful_request[]=array(
							"location"=>$pickup_location_name,
							"message"=>$delivery_request["message"]
						);
					}
				}
			}
		}
		
		if(!empty($successful_request)){
			$success_message='<style>
				.msgtable{
					width:100%;
				}
				.msgtable .tr-header{
					background:#eee;
				}
				.msgtable tr td{
					 border-bottom:1px solid #eee;
					 padding:8px 5px;
				}
			</style>
			<table class="msgtable" cellpadding="3" cellspacing="0" >
				<tr class="tr-header" >
					<td width="50%" ><b>Pickup Location</b></td>
					<td width="" ><b>Batch No</b></td>
					<td width="" ><b>Booking No</b></td>
					<td width="" ><b>Order</b></td>
				</tr>';
			
			foreach($successful_request as $request){
				$success_message.='<tr>
					<td width="" >'.$request["location"].'</td>
					<td width="" >'.$request["batch_no"].'</td>
					<td width="" >'.$request["booking_no"].'</td>
					<td width="" >'.$request["order"].'</td>
				</tr>';
			}
			
			$success_message.='</table>';
			
			bykea_set_message("request_ship_with_bykea_success", "success", 'Delivery request submittted successfully.<br/><i>'.$success_message.'</i>');
		}
		
		if(!empty($unsuccessful_request)){
			$error_message='<style>
				.msgtable{
					width:100%;
				}
				.msgtable .tr-header{
					background:#eee;
				}
				.msgtable tr td{
					 border-bottom:1px solid #eee;
					 padding:8px 5px;
				}
			</style>
			<table class="msgtable" cellpadding="3" cellspacing="0" >
				<tr class="tr-header" >
					<td width="50%" ><b>Address</b></td>
					<td width="" ><b>Error</b></td>
				</tr>';
			
			foreach($unsuccessful_request as $request){
				$error_message.='<tr>
					<td width="" >'.$request["location"].'</td>
					<td width="" >'.$request["message"].'</td>
				</tr>';
			}
			
			$error_message.='</table>';
			
			bykea_set_message("request_ship_with_bykea_error", "error", 'Error occured while sending delivery request.<br/><i>'.$error_message.'</i>');
		}
	}
	
	if(isset($_POST["delete_pickup_location"])){
		$address_ID=sanitize_text_field($_POST["delete_pickup_location"]);
		
		if(!empty($address_ID)){
			bykea_delete_pickup_addresses(array("address_ID"=>$address_ID));
		}
	}
	
	if(isset($_POST["save_pickup_address_location"])){
		$address_ID=sanitize_text_field($_POST["save_pickup_address_location"]);
		
		$args=array(
			"location_name"=>sanitize_text_field($_POST["wc_bykea_pickup_addresses_location_name"]),
			"contact_name"=>sanitize_text_field($_POST["wc_bykea_pickup_addresses_contact_name"]),
			"contact_mobile"=>sanitize_text_field($_POST["wc_bykea_pickup_addresses_contact_mobile"]),
			"address"=>sanitize_text_field($_POST["wc_bykea_pickup_addresses_address"]),
			"gps_address"=>sanitize_text_field($_POST["wc_bykea_pickup_addresses_gps_address"]),
			"latitude"=>sanitize_text_field($_POST["wc_bykea_pickup_addresses_latitude"]),
			"longitude"=>sanitize_text_field($_POST["wc_bykea_pickup_addresses_longitude"])
		);
		
		if(!empty($address_ID)){
			bykea_update_pickup_addresses($args, array("address_ID"=>$address_ID));
		}
		else{
			bykea_new_pickup_address($args);
			
			$pickup_addresses=bykea_get_pickup_addresses($args);
			
			if(isset($pickup_addresses[0]["address_ID"])){
				$address_ID=$pickup_addresses[0]["address_ID"];
			}
		}
		
		if(isset($_POST["wc_bykea_pickup_addresses_is_default"])){
			bykea_update_pickup_address("default_pickup_address", $address_ID);
		}
	}
}
