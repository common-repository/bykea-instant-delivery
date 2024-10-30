<?php
function bykea_order_delivery_logs_meta_box(){
	add_meta_box(
		'bykea_order_delivery_logs',
		'Bykea Status History',
		'bykea_order_delivery_logs_table',
		'shop_order',
		'normal',
		'low'
	);
}
add_action('add_meta_boxes', 'bykea_order_delivery_logs_meta_box');


function bykea_order_delivery_logs_table(){
	global $post;
	
	$batch_no = "";
	$booking_no = "";
	$trackinglink = "";
	$delivery_log= array();
	
	$DeliveryInfo=bykea_getDeliveryInfo($post->ID);
	
	if(isset($DeliveryInfo["batch_no"])){
		$batch_no=$DeliveryInfo["batch_no"];
		$booking_no=$DeliveryInfo["booking_no"];
		
		$delivery_log=bykea_getWebhookInfo($DeliveryInfo["booking_id"]);
		
		if(!empty($delivery_log)){
			foreach($delivery_log as $info){
				if($info["event"]=="trackinglink"){
					$trackinglink=$info["tracking_link"];
				}
			}
		}
	}
?>
<style>
.bd-table-container{
	width:100%;
	height:auto;
	overflow:auto;
	padding:5px 0px;
}
.bd-table-container a{
	text-decoration:none;
}
.bd-table{
	width:100%;
}
.bd-table tr td{
	padding:6px 5px;
	word-wrap: break-word;
}
.bd-table tbody tr td{
	border-bottom:1px solid #ccc;
}
.bd-table .tr-head{
	background:#09c;
	color:#fff;
}
.bd-table .tr-head td{
	border-bottom:0px solid #ccc;
}
.bd-table .tr-odd{
	background:#fff;
}
.bd-table .tr-even{
	background:#eee;
}
</style>

<div class="bd-row bd-table-container" >
	<table class="bd-table" width="100%" cellpadding="0" cellspacing="0" >
		<tr class="tr-head" >
			<td width="20%" align="left" valign="top" >Status</td>
			<td width="" align="left" valign="top" >Date &amp; Time</td>
			<td width="22%" align="left" valign="top" >Driver</td>
			<td width="20%" align="left" valign="top" >Booking No</td>
			<td width="10%" align="left" valign="top" >View</td>
		</tr>
<?php
if(!empty($delivery_log)){
	$trnum=1;
	$tracking_url="";
	$driver="";
	$lat="24.8649064";
	$lng="67.0814624";
	
	foreach($delivery_log as $log){
		$event=ucfirst($log["event"]);
		if(strtolower($event)=="partner"){
			$event="Feedback";
		}
		
		if(!empty($log["tracking_link"])){
			$tracking_url=$log["tracking_link"];
		}
		
		if(!empty($tracking_url)){
			$event='<a href="'.$tracking_url.'" target="_blank" >'.$event.'</a>';
		}
		
		if(!empty($log["partner_name"])){
			$driver=$log["partner_name"];
		}
		
		if(!empty($log["lat"]) && !empty($log["lng"])){
			$lat=$log["lat"];
			$lng=$log["lng"];
		}
		
		$tracking_link='<a href="http://www.google.com/maps/place/'.$lat.','.$lng.'/@'.$lat.','.$lng.',17z" target="_blank" >View</a>';
		
		if(strtolower($log["event"])=="created" || strtolower($log["event"])=="trackinglink"){
			$tracking_link="";
		}
		
		$tr_class="tr-odd";
		
		if(($trnum%2)==0){
			$tr_class="tr-even";
		}
		
		$event_time=date('Y-m-d H:i', $log["event_time"]/1000);
		
		$GMT = new DateTimeZone("GMT");
		$date = new DateTime( $event_time, $GMT );
		$date->setTimezone(new DateTimeZone('Asia/Karachi'));
		$event_time=$date->format('Y-m-d H:i');
		
		_e('<tr class="'.$tr_class.'" >
			<td width="" align="left" valign="top" >'.$event.'</td>
			<td width="" align="left" valign="top" >'.$event_time.'</td>
			<td width="" align="left" valign="top" >'.$driver.'</td>
			<td width="" align="left" valign="top" >'.$booking_no.'</td>
			<td width="" align="left" valign="top" >'.$tracking_link.'</td>
		</tr>');
		
		$trnum++;
	}
}
	
if(!empty($batch_no)){
	$batch_no='<b>Batch No:</b> '.$batch_no;
}


if(!empty($booking_no)){
	if(!empty($trackinglink)){
		$booking_no='<a href="'.$trackinglink.'" target="_blank" >'.$booking_no.'</a>';
	}
	
	$booking_no='<b>Booking No:</b> '.$booking_no;
}

?>
	</table>
</div>
<script>
var order_delivery_info_html='<div style="float:right; text-align:right;" ><span style="margin:0px 20px 0px 0px;"><?php echo $batch_no; ?></span><span style="margin:0px 0px;"><?php echo $booking_no; ?></span></div>';

jQuery(".woocommerce-order-data__heading").before(order_delivery_info_html);

jQuery(document).ready(function(){
});
</script>
<?php
}

function bykea_save_order_delivery_logs_meta_box(){
	global $post;
	
}
add_action('save_post', 'bykea_save_order_delivery_logs_meta_box');