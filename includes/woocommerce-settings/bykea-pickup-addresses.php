<?php
function bykea_pickup_addresses_tab() {
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

#select-pickup-location-map,
#pickup-location-map{ 
	width:100%;
	height: 300px; 
}
</style>

<div class="wrap">
    <div id="poststuff">
        <div id="postbox-container" class="postbox-container">
            <div class="meta-box-sortables ui-sortable" id="normal-sortables">
                <div class="postbox " id="postbox-pickup-addresses">
                    <!--<button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: At a Glance</span><span class="toggle-indicator" aria-hidden="true"></span></button>-->
					<h3 class="hndle"><span>Pickup Addresses</span></h3>
                    <div class="inside">
						<div class="bd-table-container">
							<table class="bd-table" width="100%" cellpadding="0" cellspacing="0" >
								<tr class="tr-head" >
									<td width="15" align="left" valign="top" >#</td>
									<td width="" align="left" valign="top" >Location</td>
									<td width="28%" align="left" valign="top" >Pickup Name</td>
									<td width="20%" align="left" valign="top" >Pickup Phone</td>
									<td width="16%" align="left" valign="top" >Action</td>
								</tr>
<?php
$pickup_addresses=bykea_get_pickup_addresses();

$default_address_ID=bykea_pickup_address("default_pickup_address");

if(empty($default_address_ID)){
	$default_address_ID=0;
}

$address_num=1;
foreach($pickup_addresses as $key=>$pickup_address){
	$tr_class="tr-odd";
	
	if(($address_num%2)==0){
		$tr_class="tr-even";
	}
	
	_e('<tr class="'.$tr_class.'" >
		<td width="" align="left" valign="top" >'.$address_num.'</td>
		<td width="" align="left" valign="top" >'.$pickup_address["location_name"].'</td>
		<td width="" align="left" valign="top" >'.$pickup_address["contact_name"].'</td>
		<td width="" align="left" valign="top" >'.$pickup_address["contact_mobile"].'</td>
		<td width="" align="left" valign="top" >
			<a href="http://www.google.com/maps/place/'.$pickup_address["latitude"].','.$pickup_address["longitude"].'/@'.$pickup_address["latitude"].','.$pickup_address["longitude"].',17z" target="_blank" >View</a>
			&nbsp;|&nbsp;
			<a href="javascript:void(0)" onclick="bydiz_updatePickupLocation('.$key.');" style="color:#0b6;" >Edit</a>
			&nbsp;|&nbsp;
			<a href="javascript:void(0)" onclick="bydiz_deletePickupLocation('.$key.');" style="color:#f30;" >Delete</a>
		</td>
	</tr>');
	
	$address_num++;
}
?>
							</table>
						</div>
                    </div>
                </div>
            </div>
		</div>
	</div>
</div>

<?php
woocommerce_admin_fields( bykea_pickup_addresses_form() );
?>

<script>
var pickup_addresses=<?php echo json_encode($pickup_addresses); ?>;
var default_address_ID=<?php echo $default_address_ID; ?>;

jQuery(document).ready(function(){
	jQuery("#wc_bykea_pickup_addresses_location_name").before('<input type="hidden" name="save_pickup_address_location" id="save_pickup_address_location" value="" />');
	
	jQuery(".woocommerce-save-button").after('<button type="reset" class="button button-default" style="margin:0px 0px 0px 10px;" >Reset</button>');
	
	jQuery(".woocommerce #mainform").after('<div class="bykea-popup bykea-popup-delete-pickup-location" ><div class="bykea-popup-container" ><div class="bykea-popup-content" ><form action="" method="post" ><input type="hidden" name="delete_pickup_location" id="delete_pickup_location" /><div class="bykea-popup-row bykea-popup-title" ><div class="bykea-popup-close" onclick="bydiz_closePopup()" >x</div>Delete Location</div><div class="bykea-popup-row" >Are you sure you want to delete <b><span id="location-name" ></span></b> ?</div><div class="bykea-popup-row" ><input type="submit" name="submit_delete_pickup_location" class="button button-primary" value="Delete" /></div></form></div></div></div>');
});

function bydiz_deletePickupLocation(locationIndex){
	jQuery("#delete_pickup_location").val(pickup_addresses[locationIndex].address_ID);
	
	jQuery(".bykea-popup-delete-pickup-location #location-name").html(pickup_addresses[locationIndex].location_name);
	
	bydiz_openBykeaPopup('delete-pickup-location');
}

function bydiz_updatePickupLocation(locationIndex){
	jQuery("#save_pickup_address_location").val(pickup_addresses[locationIndex].address_ID);
	jQuery("#wc_bykea_pickup_addresses_location_name").val(pickup_addresses[locationIndex].location_name);
	jQuery("#wc_bykea_pickup_addresses_contact_name").val(pickup_addresses[locationIndex].contact_name);
	jQuery("#wc_bykea_pickup_addresses_contact_mobile").val(pickup_addresses[locationIndex].contact_mobile);
	jQuery("#wc_bykea_pickup_addresses_address").val(pickup_addresses[locationIndex].address);
	jQuery("#wc_bykea_pickup_addresses_gps_address").val(pickup_addresses[locationIndex].gps_address);
	jQuery("#wc_bykea_pickup_addresses_latitude").val(pickup_addresses[locationIndex].latitude);
	jQuery("#wc_bykea_pickup_addresses_longitude").val(pickup_addresses[locationIndex].longitude);
	
	jQuery("#wc_bykea_pickup_addresses_is_default").removeAttr("checked");
	
	if(default_address_ID==pickup_addresses[locationIndex].address_ID){
		jQuery("#wc_bykea_pickup_addresses_is_default").attr("checked", "checked");
	}
}

function bydiz_openBykeaPopup(popup_name){
	jQuery(".bykea-popup-"+popup_name).fadeIn(300);
}

function bydiz_closePopup(){
	jQuery(".bykea-popup").css("display", "none");
}
</script>
<?php
}

function bykea_pickup_address($key=""){
	$value="";
	
	if(!empty($key)){
		$value=get_option("wc_bykea_pickup_addresses_".$key);
	}
	
	return $value;
}

function bykea_update_pickup_address($key, $value){
	update_option("wc_bykea_pickup_addresses_".$key, $value);
}

function bykea_pickup_addresses_form(){
    $settings = array(
        'section_title' => array(
            'name'     => __( 'Pickup Addresses', 'woocommerce-pickup-address' ),
            'type'     => 'title',
            'desc'     => '',
            'id'       => 'wc_bykea_pickup_addresses_section_title'
        ),
        'bykea_location_name' => array(
            'name' => __( 'Location Name', 'woocommerce-pickup-address' ),
            'type' => 'text',
            'desc' => '',
            'custom_attributes' => array(
				'required' => 'required'
			),
            'id'   => 'wc_bykea_pickup_addresses_location_name'
        ),
        'bykea_contact_name' => array(
            'name' => __( 'Pickup Contact Name', 'woocommerce-pickup-address' ),
            'type' => 'text',
            'desc' => '',
            'custom_attributes' => array(
				'required' => 'required'
			),
            'id'   => 'wc_bykea_pickup_addresses_contact_name'
        ),
        'bykea_contact_mobile' => array(
            'name' => __( 'Pickup Contact Mobile', 'woocommerce-pickup-address' ),
            'type' => 'text',
            'desc' => '',
            'custom_attributes' => array(
				'required' => 'required'
			),
            'id'   => 'wc_bykea_pickup_addresses_contact_mobile'
        ),
        'bykea_pickup_address' => array(
            'name' => __( 'Pickup Address', 'woocommerce-pickup-address' ),
            'type' => 'text',
            'desc' => '',
            'custom_attributes' => array(
				'required' => 'required'
			),
            'id'   => 'wc_bykea_pickup_addresses_address'
        ),
        'bykea_pickup_gps_address' => array(
            'name' => __( 'Pickup GPS Address', 'woocommerce-pickup-address' ),
            'type' => 'text',
            'desc' => '',
            'id'   => 'wc_bykea_pickup_addresses_gps_address'
        ),
        'bykea_pickup_latitude' => array(
            'name' => __( 'Pickup Latitude', 'woocommerce-pickup-address' ),
            'type' => 'text',
            'desc' => '',
            'custom_attributes' => array(
				'required' => 'required'
			),
            'id'   => 'wc_bykea_pickup_addresses_latitude'
		),
		'bykea_pickup_longitude' => array(
            'name' => __( 'Pickup Longitude', 'woocommerce-pickup-address' ),
            'type' => 'text',
            'desc' => '',
            'custom_attributes' => array(
				'required' => 'required'
			),
            'id'   => 'wc_bykea_pickup_addresses_longitude'
        ),
		'bykea_is_default' => array(
            'name' => __( 'Set As Default', 'woocommerce-pickup-address' ),
            'type' => 'checkbox',
            'desc' => 'Save this address as the default pickup address.',
            'id'   => 'wc_bykea_pickup_addresses_is_default'
        ),
        'section_end' => array(
             'type' => 'sectionend',
             'id' => 'wc_bykea_api_setings_section_end'
        )
    );
	
    return apply_filters( 'wc_bykea_api_setings', $settings );
}
