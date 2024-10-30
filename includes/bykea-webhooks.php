<?php
function save_webhooks_details(){
	$json = file_get_contents('php://input');
	
	if(!empty($json)){
		if($handle = fopen(BYDIZ_DIR."/includes/webhook-logs/".date("Y-m")."-bykea-last-webhook-post.txt", "a")){
			fwrite( $handle, "RECEIVED DATE: ".date("Y-m-d H:i:s")." \n".$json."\n\n" );
			  
			fclose($handle);
		}
		
		$event_logs=json_decode($json, true);
		
		$args["event_id"]=0;
		$args["service_code"]=0;
		$args["event_time"]="";
		$args["event"]="";
		$args["trip_id"]="";
		$args["tracking_link"]="";
		$args["lat"]="24.8649064";
		$args["lng"]="67.0814624";
		$args["partner_name"]="";
		$args["partner_plate_no"]="";
		$args["partner_mobile"]="";
		$args["invoice_total"]=0;
		
		if(isset($event_logs["event"])){
			$event=explode(".", $event_logs["event"]);
			$event=$event[count($event)-1];		
			$args["event"]=$event;
		}
		if(isset($event_logs["event_id"])){
			$args["event_id"]=$event_logs["event_id"];
		}
		if(isset($event_logs["event_time"])){
			$args["event_time"]=$event_logs["event_time"];
		}
		if(isset($event_logs["data"]["trip_id"])){
			$args["trip_id"]=$event_logs["data"]["trip_id"];
		}
		if(isset($event_logs["data"]["service_code"])){
			$args["service_code"]=$event_logs["data"]["service_code"];
		}
		if(isset($event_logs["data"]["tracking_link"])){
			$args["tracking_link"]=$event_logs["data"]["tracking_link"];
		}
		if(isset($event_logs["loc"]["lat"])){
			$args["lat"]=$event_logs["loc"]["lat"];
		}
		if(isset($event_logs["loc"]["lng"])){
			$args["lng"]=$event_logs["loc"]["lng"];
		}
		if(isset($event_logs["data"]["partner"]["name"])){
			$args["partner_name"]=$event_logs["data"]["partner"]["name"];
		}
		if(isset($event_logs["data"]["partner"]["plate_no"])){
			$args["partner_plate_no"]=$event_logs["data"]["partner"]["plate_no"];
		}
		if(isset($event_logs["data"]["partner"]["mobile"])){
			$args["partner_mobile"]=$event_logs["data"]["partner"]["mobile"];
		}
		if(isset($event_logs["data"]["invoice"]["total"])){
			$args["invoice_total"]=$event_logs["data"]["invoice"]["total"];
		}
		
		bykea_new_webhooks_details($args);
	}
}

function bykea_getWebhookInfo($trip_id, $key=""){
	$webhooks_details=bykea_get_webhooks_details(array(
		"trip_id"=>$trip_id,
		"order_by"=>"ID",
		"order"=>"ASC"
	));
	
	$info=array();
	
	if(!empty($webhooks_details)){
		if(!empty($key)){
			$index=0;
			foreach($webhooks_details as $webhooks_detail){
				if(isset($webhooks_detail[$key])){
					$info[$index]=$webhooks_detail[$key];
					$index++;
				}
			}
		}
		else{
			$info=$webhooks_details;
		}
	}
	
	return $info;
}

function bykea_new_webhooks_details($args=array()){
	global $wpdb;
	
	$success=false;
	
	if(!isset($args["event_id"])){
		$args["event_id"]=0;
	}
	if(!isset($args["service_code"])){
		$args["service_code"]=0;
	}
	if(!isset($args["event_time"])){
		$args["event_time"]="";
	}
	if(!isset($args["event"])){
		$args["event"]="";
	}
	if(!isset($args["trip_id"])){
		$args["trip_id"]="";
	}
	if(!isset($args["tracking_link"])){
		$args["tracking_link"]="";
	}
	if(!isset($args["lat"])){
		$args["lat"]="";
	}
	if(!isset($args["lng"])){
		$args["lng"]="";
	}
	if(!isset($args["partner_name"])){
		$args["partner_name"]="";
	}
	if(!isset($args["partner_plate_no"])){
		$args["partner_plate_no"]="";
	}
	if(!isset($args["partner_mobile"])){
		$args["partner_mobile"]="";
	}
	if(!isset($args["invoice_total"])){
		$args["invoice_total"]=0;
	}
	if(!isset($args["log_date"])){
		$args["log_date"]=date("Y-m-d H:i:s");
	}

	$sqlStatement="INSERT INTO ".$wpdb->prefix."bykea_webhooks_details(event_id, service_code, event_time, event, trip_id, tracking_link, lat, lng, partner_name, partner_plate_no, partner_mobile, invoice_total, log_date) VALUES($args[event_id], $args[service_code], '$args[event_time]', '$args[event]', '$args[trip_id]', '$args[tracking_link]', '$args[lat]', '$args[lng]', '$args[partner_name]', '$args[partner_plate_no]', '$args[partner_mobile]', '$args[invoice_total]', '$args[log_date]')";
	
	if($wpdb->query($sqlStatement)){
		$success=true;
	}
	
	return $success;
}

function bykea_get_webhooks_details($args=array()){
	global $wpdb;
	
	$sqlStatement="SELECT * FROM ".$wpdb->prefix."bykea_webhooks_details WHERE ID>0";
	
	if(isset($args["ID"])){
		$sqlStatement.=" AND ID=$args[ID]";
	}
	if(isset($args["event_id"])){
		$sqlStatement.=" AND event_id=$args[event_id]";
	}
	if(isset($args["service_code"])){
		$sqlStatement.=" AND service_code=$args[service_code]";
	}
	if(isset($args["event_time"])){
		$sqlStatement.=" AND event_time='$args[event_time]'";
	}
	if(isset($args["gt_event_time"])){
		$sqlStatement.=" AND event_time>'$args[gt_event_time]'";
	}
	if(isset($args["lt_event_time"])){
		$sqlStatement.=" AND event_time<'$args[lt_event_time]'";
	}
	if(isset($args["event"])){
		$sqlStatement.=" AND event='$args[event]'";
	}
	if(isset($args["trip_id"])){
		$sqlStatement.=" AND trip_id='$args[trip_id]'";
	}
	if(isset($args["partner_name"])){
		$sqlStatement.=" AND partner_name='$args[partner_name]'";
	}
	if(isset($args["partner_plate_no"])){
		$sqlStatement.=" AND partner_plate_no='$args[partner_plate_no]'";
	}
	if(isset($args["partner_mobile"])){
		$sqlStatement.=" AND partner_mobile='$args[partner_mobile]'";
	}
	if(isset($args["log_date"])){
		$sqlStatement.=" AND log_date='$args[log_date]'";
	}
	if(isset($args["gt_log_date"])){
		$sqlStatement.=" AND log_date>'$args[gt_log_date]'";
	}
	if(isset($args["lt_log_date"])){
		$sqlStatement.=" AND log_date<'$args[lt_log_date]'";
	}
	if(isset($args["order_by"])){
		$sqlStatement.=" ORDER BY $args[order_by]";
		
		if(isset($args["order"])){
			$sqlStatement.=" $args[order]";
		}
	}
	if(isset($args["limit"])){
		$sqlStatement.=" LIMIT $args[limit]";
	}
	
	$sources = $wpdb->get_results($sqlStatement, "ARRAY_A");
	
	return $sources;
}

function bykea_update_webhooks_details($args=array(), $where=array()){
    global $wpdb;
    
    $success=false;
    
    if($wpdb->update(
		$wpdb->prefix."bykea_webhooks_details",
		$args,
		$where
	)){
		$success=true;
	}
    
    return $success;
}


function bykea_delete_webhooks_details($args=array()){
    global $wpdb;
    
    $success=false;
    
	$sqlStatement="DELETE FROM ".$wpdb->prefix."bykea_webhooks_details WHERE ID>0";
	$where="";
	
	if(isset($args["ID"])){
		$where.=" AND ID=$args[ID]";
	}
	if(isset($args["event_id"])){
		$where.=" AND event_id=$args[event_id]";
	}
	if(isset($args["service_code"])){
		$where.=" AND service_code=$args[service_code]";
	}
	if(isset($args["event_time"])){
		$where.=" AND event_time='$args[event_time]'";
	}
	if(isset($args["gt_event_time"])){
		$where.=" AND event_time>'$args[gt_event_time]'";
	}
	if(isset($args["lt_event_time"])){
		$where.=" AND event_time<'$args[lt_event_time]'";
	}
	if(isset($args["event"])){
		$where.=" AND event='$args[event]'";
	}
	if(isset($args["trip_id"])){
		$where.=" AND trip_id='$args[trip_id]'";
	}
	if(isset($args["log_date"])){
		$where.=" AND log_date='$args[log_date]'";
	}
	if(isset($args["gt_log_date"])){
		$where.=" AND log_date>'$args[gt_log_date]'";
	}
	if(isset($args["lt_log_date"])){
		$where.=" AND log_date<'$args[lt_log_date]'";
	}
    
	if(!empty($where)){
		if($wpdb->query($sqlStatement.$where)){
			$success=true;
		}
    }
    
    return $success;
}