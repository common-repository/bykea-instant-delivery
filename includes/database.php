<?php
function bykea_create_tables(){
	bykea_pickup_addresses_table();
	bykea_webhooks_details_table();
	bykea_delivery_requests_table();
}

function bykea_drop_tables($tables=array()){
	global $wpdb;
	
	$sqlStatement="";
	
	$tables=implode(",", $tables);
	
	$sqlStatement="DROP TABLE ".$tables;
		
	$wpdb->query($sqlStatement);
}

function bykea_add_db_column($args=array()){
	global $wpdb;
	
	$type=$args["type"];
	if(isset($args["length"])){
		$type.="($args[length])";
	}
	
	$sqlStatement="ALTER TABLE ".$wpdb->prefix."$args[table_name] ADD $args[new_column] $type NOT NULL AFTER $args[after_column]";
	
	$wpdb->query($sqlStatement);
}

function bykea_edit_db_column($args=array()){
	global $wpdb;
	
	$type=$args["type"];
	if(isset($args["length"])){
		$type.="($args[length])";
	}
	
	$sqlStatement="ALTER TABLE ".$wpdb->prefix."$args[table_name] CHANGE $args[column_name] $args[new_column_name] $type NOT NULL;";
	
	$wpdb->query($sqlStatement);
}

function bykea_delete_tables($tables){
	global $wpdb;
	
	$table_list=array();
	
	foreach($tables as $table){
		$table_list[]=$wpdb->prefix.$table;
	}
	
	if(!empty($table_list)){
		$table_list=implode(",", $table_list);
		
		$sqlStatement="DROP TABLE ".$table_list;
		$wpdb->query($sqlStatement);
	}
}

function bykea_pickup_addresses_table(){
	global $wpdb;
	
	$sqlStatement="CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."bykea_pickup_addresses(
		address_ID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY, 
		location_name varchar(200) NOT NULL, 
		contact_name varchar(200) NOT NULL, 
		contact_mobile varchar(100) NOT NULL, 
		address varchar(300) NOT NULL,
		gps_address varchar(300) NOT NULL,
		latitude varchar(100) NOT NULL,
		longitude varchar(100) NOT NULL
	)";
	
	return $wpdb->query($sqlStatement);
}

function bykea_delivery_requests_table(){
	global $wpdb;
	
	$sqlStatement="CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."bykea_delivery_requests(
		ID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY, 
		order_id INT(10) NOT NULL, 
		batch_booking_id varchar(200) NOT NULL, 
		batch_id varchar(200) NOT NULL, 
		batch_no varchar(100) NOT NULL, 
		booking_id varchar(200) NOT NULL, 
		booking_no varchar(100) NOT NULL, 
		display_tag varchar(100) NOT NULL, 
		reference varchar(100) NOT NULL, 
		request_date DATETIME NOT NULL
	)";
	
	return $wpdb->query($sqlStatement);
}

function bykea_webhooks_details_table(){
	global $wpdb;
	
	$sqlStatement="CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."bykea_webhooks_details(
		ID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY, 
		event_id INT(10) NOT NULL, 
		service_code INT(10) NOT NULL, 
		event_time varchar(100) NOT NULL, 
		event varchar(100) NOT NULL, 
		trip_id varchar(200) NOT NULL, 
		tracking_link varchar(300) NOT NULL, 
		lat varchar(300) NOT NULL, 
		lng varchar(300) NOT NULL, 
		partner_name varchar(200) NOT NULL, 
		partner_plate_no varchar(200) NOT NULL, 
		partner_mobile varchar(200) NOT NULL, 
		invoice_total FLOAT(10) NOT NULL, 
		log_date DATETIME NOT NULL
	)";
	
	return $wpdb->query($sqlStatement);
}