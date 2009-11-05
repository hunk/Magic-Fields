jQuery().ready(function() {
	jQuery("#publish").live('click',check_textarea);
});

check_textarea = function(){
     jQuery(":input[type='textarea'].field_required").each(
		function(inputField){ 
			jQuery(jQuery('#'+jQuery(this).attr('id'))).attr('value', tinyMCE.get(jQuery(this).attr('id')).getContent());
		});
}