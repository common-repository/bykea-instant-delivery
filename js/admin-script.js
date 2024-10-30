jQuery(document).ready(function(){ 
	jQuery(".woocommerce form#mainform").submit(function(){
		if(jQuery("#wc_bykea_pickup_addresses_contact_mobile").length>0){
			var Phone=jQuery("#wc_bykea_pickup_addresses_contact_mobile").val();
			
			jQuery("#phone-format-error").html('');
			
			if(!/^923\d{9}$/.test(Phone)){
				event.preventDefault();
				
				if(jQuery("#phone-format-error").length==0){
					jQuery("#wc_bykea_pickup_addresses_contact_mobile").before('<span id="phone-format-error" ></span>');
				}
				
				jQuery("#phone-format-error").html('<p style="color:#f30;"><i>Phone number does not match expected format. <b>( 923444555666 )</b></i></p>');
			}
		}
	});
});