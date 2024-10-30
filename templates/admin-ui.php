<style>
.bd-wrap-content{
	width:98%;
	height:auto;
	margin:0px auto;
	padding:20px 0px;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}
.bd-wrap-content a{
	text-decoration:none;
}
.bd-wrap-content h3{
	margin:0px 0px 10px 0px;
	padding:0px;
}

.bd-wrap-content .text-right{
	text-align:right;
}

.bd-wrap-content form{
	width:100%;
	max-width:600px;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	padding:0px 10px;
}
.bd-wrap-content form input[type=text],
.bd-wrap-content form input[type=password]{
	margin:5px 0px 20px 0px;
}

.bd-row{
	width:100%;
	height:auto;
	padding:0px;
	margin:0px 0px 20px 0px;
	float:left;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}
.bd-table-container{
	width:100%;
	height:auto;
	overflow:auto;
	padding:5px 0px;
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
.bd-table .tr-odd{
	background:#fff;
}
.bd-table .tr-even{
	background:#eee;
}
</style>

<div class="wrap bd-row">
	<div class="bd-wrap-content">
		<div class="bd-row" >
			<div class="nav-tab-wrapper">
				<a href="<?php echo admin_url("admin.php?page=bykea-delivery#pickup-locations"); ?>" id="tab-pickup-locations" class="nav-tab nav-tab-active" >Pickup Locations</a>
				<a href="<?php echo admin_url("admin.php?page=bykea-delivery#api-settings"); ?>" id="tab-api-settings" class="nav-tab" >API Settings</a>
			</div>
		</div>
		
		<div class="bd-row" >
			<div class="bd-row bykea-admin-pages pickup-locations" >
				<h3>Pickup Locations</h3>
				<div class="bd-row bd-table-container" >
					<table class="bd-table" width="100%" cellpadding="3" cellspacing="3" >
						<tr class="tr-head" >
							<td width="" align="left" valign="top" >Location</td>
							<td width="25%" align="left" valign="top" >Pickup Name</td>
							<td width="18%" align="left" valign="top" >Pickup Phone</td>
							<td width="15%" align="left" valign="top" >Actions</td>
						</tr>
						<tr class="tr-odd" >
							<td width="" align="left" valign="top" >Sadder</td>
							<td width="" align="left" valign="top" >Abdul Mannan</td>
							<td width="" align="left" valign="top" >0333543542</td>
							<td width="" align="left" valign="top" >
								<a href="#" >View</a>
								&nbsp;|&nbsp;
								<a href="#" style="color:#0a6;" >Edit</a>
								&nbsp;|&nbsp;
								<a href="#" style="color:#f30;" >Delete</a>
							</td>
						</tr>
						<tr class="tr-even" >
							<td width="" align="left" valign="top" >Sadder</td>
							<td width="" align="left" valign="top" >Abdul Mannan</td>
							<td width="" align="left" valign="top" >0333543542</td>
							<td width="" align="left" valign="top" >
								<a href="#" >View</a>
								&nbsp;|&nbsp;
								<a href="#" style="color:#0a6;" >Edit</a>
								&nbsp;|&nbsp;
								<a href="#" style="color:#f30;" >Delete</a>
							</td>
						</tr>
						<tr class="tr-odd" >
							<td width="" align="left" valign="top" >Sadder</td>
							<td width="" align="left" valign="top" >Abdul Mannan</td>
							<td width="" align="left" valign="top" >0333543542</td>
							<td width="" align="left" valign="top" >
								<a href="#" >View</a>
								&nbsp;|&nbsp;
								<a href="#" style="color:#0a6;" >Edit</a>
								&nbsp;|&nbsp;
								<a href="#" style="color:#f30;" >Delete</a>
							</td>
						</tr>
						<tr class="tr-even" >
							<td width="" align="left" valign="top" >Sadder</td>
							<td width="" align="left" valign="top" >Abdul Mannan</td>
							<td width="" align="left" valign="top" >0333543542</td>
							<td width="" align="left" valign="top" >
								<a href="#" >View</a>
								&nbsp;|&nbsp;
								<a href="#" style="color:#0a6;" >Edit</a>
								&nbsp;|&nbsp;
								<a href="#" style="color:#f30;" >Delete</a>
							</td>
						</tr>
					</table>
				</div>
				<div class="bd-row" >
					<button class="button button-primary" >+ Add Location</button>
				</div>
			</div>
			
			<div class="bd-row bykea-admin-pages api-settings" >
				<form action="" method="post" >
					<h3>Bykea API Settings</h3>
					
					<input type="hidden" name="bykea_api_settings" />
					
					<label>Login</label>
					<input class="widefat" type="text" name="username" id="username" value="" />
					
					<label>Password</label>
					<input class="widefat" type="password" name="password" id="password" value="" />
					
					<label>Authentication URL</label>
					<input class="widefat" type="text" name="auth_host_url" id="auth_host_url" value="" />
					
					<label>Token</label>
					<input class="widefat" type="text" name="token" id="token" value="" />
					
					<label>Delivery URL</label>
					<input class="widefat" type="text" name="delivery_url" id="token" value="" />
					
					<input class="button button-primary" type="submit" name="submit_bykea_api_settings" value="Submit" />
				</form>
			</div>
		</div>
	</div>
</div>

<script>
var current_data_page="";

jQuery(document).ready(function(){
	bydiz_loadCurrentPage();
});

function bydiz_checkCurrentPage(){
	var current_page = window.location.hash.substr(1);
	if(current_page==""){
		current_page="pickup-locations";
	}
	
	return current_page;
}

function bydiz_loadCurrentPage(){
	var current_page = bydiz_checkCurrentPage();
	
	if(current_data_page!=current_page){
		current_data_page=current_page;
		jQuery(".bykea-admin-pages").css("display", "none");
		jQuery("."+current_page).fadeIn(300);
		
		jQuery(".nav-tab-active").attr("class", "nav-tab");
		jQuery("#tab-"+current_page).attr("class", "nav-tab nav-tab-active");
	}
	
	setTimeout(function(){ bydiz_loadCurrentPage(); }, 300);
}

</script>