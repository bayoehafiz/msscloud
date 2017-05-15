jQuery(document).ready(function() {
	
	var loading;
	var results;
	var display;
	var action;
		jQuery("div[id='domain-form']").on("submit", function(){
		var form = this;
		var z = jQuery(".wdc",form).attr('id');
		
			if(jQuery("input[name='domain']",form).val() == "")
				{alert('please enter your domain');return false;}
				var enable = jQuery('.wdc', form).length;
				
				if(enable > 0){
					action = 'wdc_recaptcha';
					var recaptcha_response = eval('grecaptcha.getResponse(wdcs.' + z + ');');
					if(recaptcha_response == false){
					jQuery("div[id='results']",form).html(unescape('<div class="callout callout-warning alert-warning clearfix"><div class="col-xs-10" style="padding-left:1px;text-align:left;"><i class="glyphicon glyphicon-remove" style="margin-right:10px;"></i> Please verify that you are not a robot.</div></div>'));
					
					return false;
					}
				}else if(enable == 0){
					action = 'wdc_recaptcha_dis';
					recaptcha_response = 0;
				}
				
				var recaptcha_data = {
		      		'action': action,
		      		'response': recaptcha_response,
		      		'security' : wdc_ajax.wdc_nonce
		    		};
				jQuery("div[id='results']",form).css('display','none');
				jQuery("div[id='loading']",form).css('display','inline');

				jQuery.ajax({
				type: "POST",
				url: wdc_ajax.ajaxurl,
				action: action,
				data: recaptcha_data,
				success: function(response_y){
				var y = JSON.parse(response_y);
				
				if(y.success == false && y["error-codes"] == ''){
					jQuery("div[id='loading']",form).css('display','none');
					jQuery("div[id='results']",form).html(unescape('<div class="callout callout-warning alert-warning clearfix"><div class="col-xs-10" style="padding-left:1px;text-align:left;"><i class="glyphicon glyphicon-remove" style="margin-right:10px;"></i> Please verify that you are not a robot.</div></div>'));
					eval('grecaptcha.reset(wdcs.' + z + ');');
					return false;
				}else if(y.success == false && y["error-codes"] == 'missing-input-secret'){
					jQuery("div[id='loading']",form).css('display','none');
					jQuery("div[id='results']",form).html(unescape('<p class="not-available">The secret parameter is missing.</p>'));
					eval('grecaptcha.reset(wdcs.' + z + ');');
					return false;
				}else if(y.success == false && y["error-codes"] == 'invalid-input-secret'){
					jQuery("div[id='loading']",form).css('display','none');
					jQuery("div[id='results']",form).html(unescape('<p class="not-available">The secret parameter is invalid or malformed.</p>'));
					eval('grecaptcha.reset(wdcs.' + z + ');');
					return false;
				}else if(y.success == false && y["error-codes"] == 'missing-input-response'){
					jQuery("div[id='loading']",form).css('display','none');
					jQuery("div[id='results']",form).html(unescape('<p class="not-available">The response parameter is missing.</p>'));
					eval('grecaptcha.reset(wdcs.' + z + ');');
					return false;
				}else if(y.success == false && y["error-codes"] == 'invalid-input-response'){
					jQuery("div[id='loading']",form).css('display','none');
					jQuery("div[id='results']",form).html(unescape('<p class="not-available">The response parameter is invalid or malformed.</p>'));
					eval('grecaptcha.reset(wdcs.' + z + ');');
					return false;
				}
				if(enable > 0){
				eval('grecaptcha.reset(wdcs.' + z + ');');
				}
				var domain = jQuery("input[name='domain']",form).val();
				var item_id = jQuery("input[name='item_id']",form).val();
				var tld = jQuery("input[name='tld']",form).val();

				jQuery("div[id='results']",form).css('display','none');
				jQuery("div[id='results']",form).html('');
				jQuery("div[id='loading']",form).css('display','inline');
				jQuery("#Submit",form).prop("disabled", true);
				var data = {
			      		'action': 'wdc_display',
			      		'domain': domain,
			      		'item_id': item_id,
			      		'tld': tld,
			      		'security' : wdc_ajax.wdc_nonce
			    		};
			    
				jQuery.post(wdc_ajax.ajaxurl, data, function(response) {
				//var x = JSON.parse(response);
				
				jQuery("div[id='results']",form).css('display','block');
				jQuery("div[id='loading']",form).css('display','none');
				jQuery("#Submit",form).removeAttr('disabled');
				jQuery("div[id='results']",form).html(unescape(response));

			});
			return false;
				}
				});

			return false;
	});
	
});	     

jQuery('#Search').keypress(function (e) {
	if(e.keyCode === 8){
   	return true;
   	}
    var regex = new RegExp("^[a-zA-Z0-9\-\.]+$");
    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
    if (regex.test(str)) {
        return true;
    }
     if(e.which == 13) {
     	jQuery(this).submit();
     	return false;
     }

    e.preventDefault();
    return false;
}); 