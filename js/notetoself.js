$(document).ready(function(){

	var $root = $('#notetoself');
	var $status = $('#notetoself-status');

	var updateURL = $root.attr('data-update-controller-url'); 
	var idProduct = $root.attr('data-id-product');

	var previousText;
	var throttle = 300;
	var timeOut;

	var messages = $root.data('messages');

	function update(text) {
		$.post(updateURL, {
			id_product: idProduct,
			notes: text
		}).done(function(resp) {
			if (resp.success) {
				$status.html('<span class="status success">'+messages['saved']+'</span>');
			} else {
				$status.html('<span class="status error">'+messages['oops']+'</span>');
			}
		})
	}

	function scheduleUpdate(text) {

		if (timeOut) {
			window.clearTimeout(timeOut);
		}

		timeOut = window.setTimeout(function() {
			update(text);
		}, throttle);
	}

	$('#notetoself-notes').on('keyup', function() {
		var text = $(this).val();
		if (text !== previousText) {
			previousText = text;

			$status.html('<span class="status">'+messages['saving']+'</span>');

			scheduleUpdate(text);
		}
	});
});