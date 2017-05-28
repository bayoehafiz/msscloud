(function ($) {
	$(document).on('ready', function () {
		$('.wca_dropdown_toggle').on('click', function (e) {
			e.preventDefault();
			$(this).parent('.wca_dropdown_outer').toggleClass('active');
		});
		$('.wca_modal_container').each(function wca_modal() {
			var $container = $(this);
			var modal = $container.find('.wca_modal');
			var content = $container.find('.wca_modal_content');
			var opener = $container.find('.wca_modal_opener');
			var close = $container.find('.wca_modal_close');


			opener.on('click', function () {
				modal.show(0, function () {
					content.animate({
						top: "0",
					}, 500);
				});
			});
			close.on('click', function () {
				content.animate({
					top: "-1000px",
				}, 500, function () {
					modal.hide(0)
				});
			});
			modal.on('click', function () {
				content.animate({
					top: "-1000px",
				}, 500, function () {
					modal.hide(0)
				});
			});
			content.on('click', function (e) {
				e.stopPropagation();
			});
		});
		$('.wca_login_form').removeClass('whmpress').addClass('wca_container');
	});
}(jQuery));

function is_json(str) {
	str = jQuery.trim(str);
	if (str == "") return false;
	try {
		JSON.parse(str);
	} catch (e) {
		return false;
	}
	return true;
}

