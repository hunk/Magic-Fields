jQuery().ready(function() {
	jQuery("#publish").live('click',check_textarea);
	//set config for editor
  tinyMCE.init(
    jQuery.extend(true, {}, tinyMCEPreInit.mceInit, { editor_selector: "pre_editor" })
  );
  //set editor for textarea
	jQuery(":input[type='textarea'].mf_editor").each( function(inputField){
		var editor_text = jQuery(this).attr('id');
		tinyMCE.execCommand('mceAddControl', true, editor_text);
		jQuery('#'+editor_text).removeClass('pre_editor');
	});
});
// this function update textarea with value the editor  for validation
check_textarea = function(){
     jQuery(":input[type='textarea'].field_required").each(
		function(inputField){ 
			var editor_text = jQuery(this).attr('id');
			jQuery(jQuery('#'+editor_text)).attr('value', tinyMCE.get(editor_text).getContent());
		});
}
// Add the editor (button)
function add_editor(id){
	tinyMCE.execCommand('mceAddControl', false, id);
}
// Remove the editor (button)
function del_editor(id){
	tinyMCE.execCommand('mceRemoveControl', false, id);
}
