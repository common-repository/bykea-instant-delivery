<?php
function bykea_valued($key, $value){
	if(isset($value[$key])){
		return $value[$key];
	}
	else{
		return "";
	}
}

function bykea_url_parts(){
	$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
	
	$actual_link=str_replace(site_url("/"), "", $actual_link);
	
	$actual_link=explode("/", $actual_link);
	
	$parts_count=count($actual_link);
	$parts_index=0;
	
	$url_parts=array();
	
	while($parts_index<$parts_count){
		if(!empty($actual_link[$parts_index])){
			$url_parts[]=$actual_link[$parts_index];
		}
		
		$parts_index++;
	}
	
	return $url_parts;
}

function bykea_week_dates($weeks=0){
	$today=strtolower(date("l"));
	$current_date=date("Y-m-d");
	$weekdays=array("monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday");
	$daypos=array_search($today, $weekdays);
	$weeksintreval=$weeks*7;
	
	if($weeksintreval>0){
		$current_date=plus_days($weeksintreval, $current_date);
	}
	if($weeksintreval<0){
		$weeksintreval*=-1;
		$current_date=minus_days($weeksintreval, $current_date);
	}
	
	$weekdates=array();
	
	$day_index=$daypos;
	
	while($day_index>=0){
		$minusdays=$daypos-$day_index;
		
		$date=minus_days($minusdays, $current_date);
		$date2=date("jS M", strtotime($date));
		
		$weekdates[$day_index]=array(
			"day"=>$weekdays[$day_index],
			"date"=>$date,
			"date2"=>$date2
		);
		
		$day_index--;
	}
	
	$day_index=6;
	
	while($day_index>$daypos){
		$plusdays=$day_index-$daypos;
		
		$date=plus_days($plusdays, $current_date);
		$date2=date("jS M", strtotime($date));
		
		$weekdates[$day_index]=array(
			"day"=>$weekdays[$day_index],
			"date"=>$date,
			"date2"=>$date2
		);
		
		$day_index--;
	}
	
	return $weekdates;
}

function bykea_minus_days($days, $from_date="", $output_format="Y-m-d"){
	if(empty($from_date)){
		$from_date=date($output_format);
	}
	
	$new_date=$from_date;
	
	if($days>0){
		$minusdays="1 day";
		
		if($days>1){
			$minusdays=$days." days";
		}
		
		$date = date_create($from_date);
		date_sub($date, date_interval_create_from_date_string($minusdays));
		
		$new_date=date_format($date, $output_format);
	}
	
	return $new_date;
}

function bykea_plus_days($days, $from_date="", $output_format="Y-m-d"){
	if(empty($from_date)){
		$from_date=date($output_format);
	}
	
	$new_date=$from_date;
	
	if($days>0){
		$plusdays="1 day";
		
		if($days>1){
			$plusdays=$days." days";
		}
		$date = date_create($from_date);
		date_add($date, date_interval_create_from_date_string($plusdays));
		
		$new_date=date_format($date, $output_format);
	}
	
	return $new_date;
}

function format_date($origDate, $format){
	$newDate = date($format, strtotime($origDate));
	
	return $newDate;
}

function bykea_format_am_pm($origTime){
	$newDate = date("g:i a", strtotime($origTime));
	
	return $newDate;
}

function bykea_time_defference($from_time, $to_time){
	$start_date = new DateTime($from_time);
	$since_start = $start_date->diff(new DateTime($to_time));
	
	$time_deff["days_total"]=$since_start->days;
	$time_deff["years"]=$since_start->y;
	$time_deff["months"]=$since_start->m;
	$time_deff["days"]=$since_start->d;
	$time_deff["hours"]=$since_start->h;
	$time_deff["minutes"]=$since_start->i;
	$time_deff["seconds"]=$since_start->s;
	
	$months_total = $since_start->y * 12;
	$months_total += $since_start->m;
	$time_deff["months_total"]=$months_total;
	
	$hours_total = $since_start->days * 24;
	$hours_total += $since_start->h;
	$time_deff["hours_total"]=$hours_total;
	
	$minutes_total = $since_start->days * 24 * 60;
	$minutes_total += $since_start->h * 60;
	$minutes_total += $since_start->i;
	$time_deff["minutes_total"]=$minutes_total;
	
	$seconds_total = $since_start->days * 24 * 60;
	$seconds_total += $since_start->h * 60;
	$seconds_total += $since_start->i * 60;
	$seconds_total += $since_start->s;
	$time_deff["seconds_total"]=$seconds_total;
	
	return $time_deff;
}