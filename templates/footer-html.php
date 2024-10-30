<?php
global $bykea_order_ids, $bykea_orders;
?>
<style>
.bykea-popup{
	position:fixed;
	top:0px;
	left:0px;
	bottom:0px;
	right:0px;
	z-index:10000;
	background:rgba(0, 0, 0, 0.4);
	box-sizing: border-box;
	display:none;
	overflow:auto;
}

.bykea-popup-container{
	width:90%;
	max-width:600px;
	height:auto;
	margin:0px auto;
	padding:5% 0px 0px 0px;
	border:0px solid #f30;
	box-sizing: border-box;
}

.bykea-popup-content{
	width:100%;
	height:auto;
	padding:15px 15px 10px 15px;
	background:#fff;
	box-sizing: border-box;
	float:left;
}

.bykea-popup-row{
	width:100%;
	height:auto;
	margin:0px 0px 20px 0px;
	padding:0px;
	float:left;
}

.bykea-popup-title{
	font-weight:bold;
}
.bykea-popup-close{
	font-weight:bold;
	float:right;
	background:#000;
	color:#fff;
	border:0px;
	border-radius:3px;
	width:23px;
	height:23px;
	padding:0px;
	text-align:center;
	cursor:pointer;
}

.bykea-popup-table{
	border-top:0px solid #ccc;
}
.bykea-popup-table tr{
}
.bykea-popup-table tr td{
	padding:8px 3px;
	border-bottom:1px solid #ccc;
}
.bykea-popup .tr-header{
	font-weight:bold;
	background:#09c;
	color:#fff;
}
.bykea-popup .tr-header td{
	border-bottom:0px solid #ccc;
}
.bykea-popup .tr-even{
	background:#eee;
}
</style>
<div class="bykea-popup bykea-popup-ship-with-bykea" >
	<div class="bykea-popup-container" >
		<div class="bykea-popup-content" >
			<form action="" method="post" onsubmit="bydiz_validateShipWithBykea(event)" >
				<input type="hidden" name="request_ship_with_bykea" value="" />
				<input type="hidden" name="order_ids" id="order_ids" value="" />
				<input type="hidden" name="delivery_addresses_values" id="delivery_addresses_values" value="" />
				<input type="hidden" name="CODsValues" id="CODsValues" value="" />
				<div class="bykea-popup-row bykea-popup-title" >
					<div class="bykea-popup-close" onclick="bydiz_closePopup()" >x</div>
					Select Pickup Location
				</div>
				<div class="bykea-popup-row" >
					<table class="bykea-popup-table" width="100%" cellpadding="0" cellspacing="0" >
						<tr class="tr-header" >
							<td width="" align="left" valign="top" >
								&nbsp;
							</td>
							<td width="" align="left" valign="top" >Location</td>
							<td width="" align="left" valign="top" >Pickup Name</td>
							<td width="" align="left" valign="top" >Pickup Phone</td>
						</tr>
<?php
$pickup_addresses=bykea_get_pickup_addresses();

$default_address_ID=bykea_pickup_address("default_pickup_address");

if(empty($default_address_ID)){
	$default_address_ID=0;
}

$default_address=bykea_get_pickup_addresses(array("address_ID"=>$default_address_ID));

$address_num=1;

if(isset($default_address[0])){
	$default_address=$default_address[0];
	
	_e('<tr class="tr-odd" >
		<td width="" align="left" valign="top" ><input type="radio" name="delivery_locations" class="delivery_locations" value="'.$default_address["address_ID"].'" checked="checked" /></td>
		<td width="" align="left" valign="top" >'.$default_address["location_name"].'</td>
		<td width="" align="left" valign="top" >'.$default_address["contact_name"].'</td>
		<td width="" align="left" valign="top" >'.$default_address["contact_mobile"].'</td>
	</tr>');
	
	$address_num++;
}

foreach($pickup_addresses as $key=>$pickup_address){
	if($default_address_ID!=$pickup_address["address_ID"]){
		$tr_class="tr-odd";
		
		if(($address_num%2)==0){
			$tr_class="tr-even";
		}
		
		$checked="";
		
		if($address_num==1){
			$checked='checked="checked"';
		}
		
		_e('<tr class="'.$tr_class.'" >
			<td width="" align="left" valign="top" ><input type="radio" name="delivery_locations" class="delivery_locations" value="'.$pickup_address["address_ID"].'" '.$checked.' /></td>
			<td width="" align="left" valign="top" >'.$pickup_address["location_name"].'</td>
			<td width="" align="left" valign="top" >'.$pickup_address["contact_name"].'</td>
			<td width="" align="left" valign="top" >'.$pickup_address["contact_mobile"].'</td>
		</tr>');
		
		$address_num++;
	}
}
?>
					</table>
				</div>
				<div class="bykea-popup-row" >
					<table class="bykea-popup-table bykea-change-order-details" width="100%" cellpadding="0" cellspacing="0" >
						<thead>
							<tr class="tr-header" >
								<td width="20%" align="left" valign="top" >Order #</td>
								<td width="30%" align="left" valign="top" >Name</td>
								<td width="" align="left" valign="top" >Delivery Address</td>
								<td width="15%" align="left" valign="top" >COD</td>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
				<div class="bykea-popup-row" >
					<table class="" width="100%" cellpadding="3" cellspacing="0" >
						<tr class="" >
							<td width="" align="left" valign="top" >&nbsp;</td>
							<td width="100" align="right" valign="top" >
								<input type="submit" class="button button-primary" value="Submit" />
							</td>
						</tr>
					</table>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
var bykea_orders=<?php echo json_encode($bykea_orders); ?>;
var bykea_order_ids=<?php echo json_encode($bykea_order_ids); ?>;

jQuery(document).ready(function(){
	
	jQuery("#posts-filter").submit(function(){
		bydiz_shipWithByKeaBulk();
	});
	
	jQuery('.order-preview').click(function(){
		var selected_order_id=jQuery(this).attr("data-order-id");
			
		var booking_no="";
		var batch_no="";
		
		bykea_orders.forEach(function(bykea_order){
			if(bykea_order.order_id==selected_order_id){
				booking_no=bykea_order.booking_no;
				batch_no=bykea_order.batch_no;
			}
		});
		
		bydiz_insert_booking_no(booking_no, batch_no);
	});
});

function bydiz_insert_booking_no(booking_no, batch_no){
	if(booking_no!=""){
		if(jQuery('.wc-backbone-modal-header h1').length>0){
			booking_no='<span style="float:right; margin:0px 30px 0px 0px;" ><b>Booking No:</b> '+booking_no+'</span>';
			
			jQuery('.wc-backbone-modal-header h1').before(booking_no);
		}
		else{
			setTimeout(function(){
				bydiz_insert_booking_no(booking_no, batch_no);
			}, 300);
		}
	}
}

function bydiz_shipWithByKeaBulk(){
	var search_input=jQuery("#post-search-input").val();
	var bulk_action_top=jQuery("#bulk-action-selector-top").val();
	var bulk_action_bottom=jQuery("#bulk-action-selector-bottom").val();
	
	if((bulk_action_top=="bykea_request_bulk_delivery" || bulk_action_bottom=="bykea_request_bulk_delivery") && search_input==""){
		event.preventDefault();
		
		if(jQuery("#posts-filter table.wp-list-table tbody input[type=checkbox]:checked").length>0){
			var order_IDs="";
			jQuery("#posts-filter table.wp-list-table tbody input[type=checkbox]:checked").each(function(){
				if(order_IDs!=""){
					order_IDs+=",";
				}
				
				order_IDs+=jQuery(this).val();
			});
			
			jQuery("#order_ids").val(order_IDs);
			
			bydiz_addOrderDetailsFields(order_IDs);
			
			bydiz_openBykeaPopup('ship-with-bykea');
		}
	}
}

function bydiz_addOrderDetailsFields(order_IDs){
	jQuery(".bykea-change-order-details tbody").html('<tr class="tr-odd" ><td width="" align="center" valign="top" colspan="10" ><i>Loading..</i></td></tr>');
	
	bydiz_disableSubmitBtn();
	
	jQuery.ajax({
		data:{get_order_fields_details:1, order_ids: order_IDs},
		type: 'post',
		dataType: 'json',
		url: '<?php echo site_url("ajax"); ?>',
		error: function(request, status, error){
		},
		success: function(results){
			if(results.order_details.length>0){
				var order_details_html='';
				var CODsValues=[], delivery_addresses_values=[];
				
				results.order_details.forEach(function(order_detail){
					if(bykea_order_ids.indexOf((order_detail.order_id*1))==-1){
						order_details_html+='<tr class="tr-odd" ><td width="" align="left" valign="top" >'+order_detail.order_id+'</td><td width="" align="left" valign="top" >'+order_detail.customer_name+'</td><td width="" align="left" valign="top" ><input type="text" name="delivery_addresses[]" class="delivery_addresses widefat" value="'+order_detail.shipping_address+'" style="width:90%;" /></td><td width="" align="left" valign="top" ><input type="text" name="CODs[]" class="CODs widefat" value="'+order_detail.order_total+'" style="width:90%;" /></td></tr>';
						
						CODsValues.push(order_detail.order_total);
						delivery_addresses_values.push(order_detail.shipping_address);
						
						bydiz_enableSubmitBtn();
					}
				});
				
				jQuery(".bykea-change-order-details tbody").html(order_details_html);
				
				jQuery("#delivery_addresses_values").val(delivery_addresses_values.join("=="));
				jQuery("#CODsValues").val(CODsValues.join("=="));
			}
		}
	});
}

function bydiz_validateShipWithBykea(event){
	bydiz_disableSubmitBtn();
	if(jQuery("input.delivery_locations:checked").length==0){
		event.preventDefault();
		bydiz_enableSubmitBtn();
	}
}

function bydiz_shipWithByKeaSingle(order_ID){
	jQuery("#order_ids").val(order_ID);
	
	bydiz_addOrderDetailsFields(order_ID);
	
	bydiz_openBykeaPopup('ship-with-bykea');
}

function bydiz_enableSubmitBtn(){
	jQuery(".bykea-popup-ship-with-bykea input[type=submit]").removeAttr("disabled");
}
function bydiz_disableSubmitBtn(){
	jQuery(".bykea-popup-ship-with-bykea input[type=submit]").attr("disabled", "disabled");
}

function bydiz_openBykeaPopup(popup_name){
	jQuery(".bykea-popup-"+popup_name).fadeIn(300);
}

function bydiz_closePopup(){
	jQuery(".bykea-popup").css("display", "none");
}
</script>