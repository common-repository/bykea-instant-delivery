<?php
function bykea_new_pickup_address($args=array()){
	global $wpdb;
	
	$success=false;
	
	if(!isset($args["location_name"])){
		$args["location_name"]="";
	}
	if(!isset($args["contact_name"])){
		$args["contact_name"]="";
	}
	if(!isset($args["contact_mobile"])){
		$args["contact_mobile"]="";
	}
	if(!isset($args["address"])){
		$args["address"]="";
	}
	if(!isset($args["gps_address"])){
		$args["gps_address"]="";
	}
	if(!isset($args["latitude"])){
		$args["latitude"]="";
	}
	if(!isset($args["longitude"])){
		$args["longitude"]="";
	}

	$sqlStatement="INSERT INTO ".$wpdb->prefix."bykea_pickup_addresses(location_name, contact_name, contact_mobile, address, gps_address, latitude, longitude) VALUES('$args[location_name]', '$args[contact_name]', '$args[contact_mobile]', '$args[address]', '$args[gps_address]', '$args[latitude]', '$args[longitude]')";
	
	if($wpdb->query($sqlStatement)){
		$success=true;
	}
	
	return $success;
}

function bykea_get_pickup_addresses($args=array()){
	global $wpdb;
	
	$sqlStatement="SELECT * FROM ".$wpdb->prefix."bykea_pickup_addresses WHERE address_ID>0";
	
	if(isset($args["address_ID"])){
		$sqlStatement.=" AND address_ID=$args[address_ID]";
	}
	if(isset($args["location_name"])){
		$sqlStatement.=" AND location_name='$args[location_name]'";
	}
	if(isset($args["contact_name"])){
		$sqlStatement.=" AND contact_name='$args[contact_name]'";
	}
	if(isset($args["contact_mobile"])){
		$sqlStatement.=" AND contact_mobile='$args[contact_mobile]'";
	}
	if(isset($args["address"])){
		$sqlStatement.=" AND address LIKE '%$args[address]%'";
	}
	if(isset($args["gps_address"])){
		$sqlStatement.=" AND gps_address LIKE '%$args[gps_address]%'";
	}
	if(isset($args["latitude"])){
		$sqlStatement.=" AND latitude='$args[latitude]'";
	}
	if(isset($args["longitude"])){
		$sqlStatement.=" AND longitude='$args[longitude]'";
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

function bykea_update_pickup_addresses($args=array(), $where=array()){
    global $wpdb;
    
    $success=false;
    
    if($wpdb->update(
		$wpdb->prefix."bykea_pickup_addresses",
		$args,
		$where
	)){
		$success=true;
	}
    
    return $success;
}

function bykea_delete_pickup_addresses($args=array()){
    global $wpdb;
    
    $success=false;
    
    $sqlStatement="DELETE FROM ".$wpdb->prefix."bykea_pickup_addresses WHERE address_ID>0";
	$where="";
    
   if(isset($args["address_ID"])){
		$where.=" AND address_ID=$args[address_ID]";
	}
	if(isset($args["location_name"])){
		$where.=" AND location_name='$args[location_name]'";
	}
	if(isset($args["contact_name"])){
		$where.=" AND contact_name='$args[contact_name]'";
	}
	if(isset($args["contact_mobile"])){
		$where.=" AND contact_mobile='$args[contact_mobile]'";
	}
	if(isset($args["address"])){
		$where.=" AND address LIKE '%$args[address]%'";
	}
	if(isset($args["gps_address"])){
		$where.=" AND gps_address LIKE '%$args[gps_address]%'";
	}
	if(isset($args["latitude"])){
		$where.=" AND latitude='$args[latitude]'";
	}
	if(isset($args["longitude"])){
		$where.=" AND longitude='$args[longitude]'";
	}
    
	if(!empty($where)){
		if($wpdb->query($sqlStatement.$where)){
			$success=true;
		}
    }
    
    return $success;
}