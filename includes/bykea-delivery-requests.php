<?php
function bykea_getDeliveryInfo($order_id, $key=""){
	$delivery_requests=bykea_get_delivery_requests(array(
		"order_id"=>$order_id,
		"order_by"=>"ID",
		"order"=>"DESC",
		"limit"=>1
	));
	
	$info=null;
	
	if(isset($delivery_requests[0])){
		$delivery_request=$delivery_requests[0];
		
		if(isset($delivery_request[$key])){
			$info=$delivery_request[$key];
		}
		elseif(empty($key)){
			$info=$delivery_request;
		}
	}
	
	return $info;
}

function bykea_new_delivery_requests($args=array()){
	global $wpdb;
	
	$success=false;
	
	if(!isset($args["order_id"])){
		$args["order_id"]=0;
	}
	if(!isset($args["batch_booking_id"])){
		$args["batch_booking_id"]="";
	}
	if(!isset($args["batch_id"])){
		$args["batch_id"]="";
	}
	if(!isset($args["batch_no"])){
		$args["batch_no"]="";
	}
	if(!isset($args["booking_id"])){
		$args["booking_id"]="";
	}
	if(!isset($args["booking_no"])){
		$args["booking_no"]="";
	}
	if(!isset($args["display_tag"])){
		$args["display_tag"]="";
	}
	if(!isset($args["reference"])){
		$args["reference"]="";
	}
	if(!isset($args["request_date"])){
		$args["request_date"]=date("Y-m-d H:i:s");
	}

	$sqlStatement="INSERT INTO ".$wpdb->prefix."bykea_delivery_requests(order_id, batch_booking_id, batch_id, batch_no, booking_id, booking_no, display_tag, reference, request_date) VALUES($args[order_id], '$args[batch_booking_id]', '$args[batch_id]', '$args[batch_no]', '$args[booking_id]', '$args[booking_no]', '$args[display_tag]', '$args[reference]', '$args[request_date]')";
	
	if($wpdb->query($sqlStatement)){
		$success=true;
	}
	
	return $success;
}

function bykea_get_delivery_requests($args=array()){
	global $wpdb;
	
	$sqlStatement="SELECT * FROM ".$wpdb->prefix."bykea_delivery_requests WHERE ID>0";
	
	if(isset($args["ID"])){
		$sqlStatement.=" AND ID=$args[ID]";
	}
	if(isset($args["order_id"])){
		$sqlStatement.=" AND order_id=$args[order_id]";
	}
	if(isset($args["batch_booking_id"])){
		$sqlStatement.=" AND batch_booking_id='$args[batch_booking_id]'";
	}
	if(isset($args["batch_id"])){
		$sqlStatement.=" AND batch_id='$args[batch_id]'";
	}
	if(isset($args["batch_no"])){
		$sqlStatement.=" AND batch_no='$args[batch_no]'";
	}
	if(isset($args["booking_id"])){
		$sqlStatement.=" AND booking_id='$args[booking_id]'";
	}
	if(isset($args["booking_no"])){
		$sqlStatement.=" AND booking_no='$args[booking_no]'";
	}
	if(isset($args["display_tag"])){
		$sqlStatement.=" AND display_tag='$args[display_tag]'";
	}
	if(isset($args["reference"])){
		$sqlStatement.=" AND reference='$args[reference]'";
	}
	if(isset($args["request_date"])){
		$sqlStatement.=" AND request_date='$args[request_date]'";
	}
	if(isset($args["gt_request_date"])){
		$sqlStatement.=" AND request_date>'$args[gt_request_date]'";
	}
	if(isset($args["lt_request_date"])){
		$sqlStatement.=" AND request_date<'$args[lt_request_date]'";
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

function bykea_update_delivery_requests($args=array(), $where=array()){
    global $wpdb;
    
    $success=false;
    
    if($wpdb->update(
		$wpdb->prefix."bykea_delivery_requests",
		$args,
		$where
	)){
		$success=true;
	}
    
    return $success;
}


function bykea_delete_delivery_requests($args=array()){
    global $wpdb;
    
    $success=false;
    
   $sqlStatement="DELETE FROM ".$wpdb->prefix."bykea_delivery_requests WHERE ID>0";
   $where="";
	
	if(isset($args["ID"])){
		$where.=" AND ID=$args[ID]";
	}
	if(isset($args["order_id"])){
		$where.=" AND order_id=$args[order_id]";
	}
	if(isset($args["batch_booking_id"])){
		$where.=" AND batch_booking_id='$args[batch_booking_id]'";
	}
	if(isset($args["batch_id"])){
		$where.=" AND batch_id='$args[batch_id]'";
	}
	if(isset($args["batch_no"])){
		$where.=" AND batch_no='$args[batch_no]'";
	}
	if(isset($args["booking_id"])){
		$where.=" AND booking_id='$args[booking_id]'";
	}
	if(isset($args["booking_no"])){
		$where.=" AND booking_no='$args[booking_no]'";
	}
	if(isset($args["display_tag"])){
		$where.=" AND display_tag='$args[display_tag]'";
	}
	if(isset($args["reference"])){
		$where.=" AND reference='$args[reference]'";
	}
	if(isset($args["request_date"])){
		$where.=" AND request_date='$args[request_date]'";
	}
	if(isset($args["gt_request_date"])){
		$where.=" AND request_date>'$args[gt_request_date]'";
	}
	if(isset($args["lt_request_date"])){
		$where.=" AND request_date<'$args[lt_request_date]'";
	}
    
	if(!empty($where)){
		if($wpdb->query($sqlStatement.$where)){
			$success=true;
		}
    }
    
    return $success;
}