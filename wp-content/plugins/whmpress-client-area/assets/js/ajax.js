// WHMPress ClientArea

function WCA_FORM_(rand) {
	jQuery('.wca_login_spinner').removeClass('error').show().html("Logging in .....");
	jQuery.post(wca_ajax.ajax_url, jQuery('#form_' + rand).serialize(), function (data) {
		jQuery('.wca_login_spinner').removeClass('error').show().html("Logging in .....");
		if (is_json(data)) {
			data = JSON.parse(data);
			if (data.action == "redirect") window.location.href = data.goto;
		} else {
			jQuery('.wca_login_spinner').addClass('error').html(data);
		}
	});
	return false;
}

if (wca_ajax.ajax_url) {
	jQuery(function () {
		if (jQuery("#loginform").length == "1") {
			jQuery("#loginform").append("<input type='hidden' name='action' value='wca_login'>");
			jQuery("<div style='display: none;' id='wca_login_spinner'>Logging in .....</div>").insertBefore(".forgetmenot");
		}
		jQuery(document).on("submit", "#loginform", function (e) {
			jQuery('#wca_login_spinner, .wca_login_spinner').removeClass('error').show().html("Logging in .....");
			e.preventDefault();
			jQuery.post(wca_ajax.ajax_url, jQuery('#loginform').serialize(), function (data) {
				if (is_json(data)) {
					data = JSON.parse(data);
					if (data.action == "redirect") {
						jQuery('#wca_login_spinner, .wca_login_spinner').hide();
						window.location.href = data.goto;
					}
				} else {
					jQuery('#wca_login_spinner, .wca_login_spinner').addClass('error').html(data);
				}
			});
			return false;
		});
	});
}