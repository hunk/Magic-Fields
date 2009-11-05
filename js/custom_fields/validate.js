jQuery.metadata.setType("attr", "validate");
jQuery().ready(function() {
	jQuery("#post").validate({
			errorClass: "error_magicfields"
		});
});