$(document).ready(function(){

	var root = $('#notetoself');

	var updateURL = root.attr('data-update-controller-url'); 
	var idProduct = root.attr('data-id-product');

	var previousText;

	$('#notetoself-notes').on('keyup', function() {
		var text = $(this).val();
		if (text !== previousText) {
			previousText = text;
			$.post(updateURL, {
				id_product: idProduct,
				notes: text
			});
		}
	});
});