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
	
	// traversal: setup magic fields options panel
  
  function mfCustomWritePanelChange() {
    var sel = jQuery('#rc-cwp-change-custom-write-panel-id');
    var info = mf_panel_items[sel.val()];

    jQuery('#rc-cwp-set-page-template').removeAttr("disabled").removeClass("disabled").removeAttr("title");
    jQuery('#rc-cwp-set-page-parent').removeAttr("disabled").removeClass("disabled").removeAttr("title");
    
    
    jQuery('#mf-page-template-display').html(info.template_name || '<span class="none">(none)</span>');
    jQuery('#mf-page-parent-display').html(info.parent_page_title || '<span class="none">(none)</span>');
    
    if (info.parent_page == '') {
      jQuery('#rc-cwp-set-page-parent').attr("disabled", "disabled").addClass("disabled").attr("title", "The selected write panel has no default page parent");
    }

    if (info.panel_theme == '') {
        jQuery('#rc-cwp-set-page-template').attr("disabled", "disabled").addClass("disabled").attr("title", "The selected write panel has no default page template");
    }
  }
  
	jQuery('#rc-cwp-change-custom-write-panel-id').change( mfCustomWritePanelChange );
	
	mfCustomWritePanelChange();
	
	jQuery('#rc-cwp-set-page-template').click( function() {
	  var sel = jQuery('#rc-cwp-change-custom-write-panel-id');
    var info = mf_panel_items[sel.val()];
	  jQuery('#page_template').val(info.panel_theme);
    return false;
  });

	jQuery('#rc-cwp-set-page-parent').click( function() {
	  var sel = jQuery('#rc-cwp-change-custom-write-panel-id');
    var info = mf_panel_items[sel.val()];
	  jQuery('#parent_id').val(info.parent_page);
    return false;
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




