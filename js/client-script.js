jQuery(document).ready(function(){
	if(jQuery("label[for=billing_phone]").length>0){
		var labelHTML=jQuery("label[for=billing_phone]").html();
		
		jQuery("label[for=billing_phone]").html(labelHTML+'&nbsp;&nbsp;( <i>Example: <b>923444555666</b></i> )');
	}
});