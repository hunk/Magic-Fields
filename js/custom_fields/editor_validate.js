jQuery().ready(function() {
	jQuery("#publish").live('click',check_textarea);
	//set config for editor
	tinyMCE.init({
		mode:"specific_textareas", editor_selector:"pre_editor", width:"100%", theme:"advanced", skin:"wp_theme", theme_advanced_buttons1:"bold,italic,strikethrough,|,bullist,numlist,blockquote,|,justifyleft,justifycenter,justifyright,|,link,unlink,wp_more,|,spellchecker,fullscreen,wp_adv,|,add_image,add_video,add_audio,add_media", theme_advanced_buttons2:"formatselect,underline,justifyfull,forecolor,|,pastetext,pasteword,removeformat,|,media,charmap,|,outdent,indent,|,undo,redo,wp_help", theme_advanced_buttons3:"", theme_advanced_buttons4:"", language: lan_editor, spellchecker_languages:"+English=en,Danish=da,Dutch=nl,Finnish=fi,French=fr,German=de,Italian=it,Polish=pl,Portuguese=pt,Spanish=es,Swedish=sv", theme_advanced_toolbar_location:"top", theme_advanced_toolbar_align:"left", theme_advanced_statusbar_location:"bottom", theme_advanced_resizing:"1", theme_advanced_resize_horizontal:"", dialog_type:"modal", relative_urls:"", remove_script_host:"", convert_urls:"", apply_source_formatting:"", remove_linebreaks:"1", gecko_spellcheck:"1", entities:"38,amp,60,lt,62,gt", accessibility_focus:"1", tabfocus_elements:"major-publishing-actions", media_strict:"", wpeditimage_disable_captions:"", plugins:"safari,inlinepopups,spellchecker,paste,wordpress,media,fullscreen,wpeditimage,wpgallery,tabfocus"
	});
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
