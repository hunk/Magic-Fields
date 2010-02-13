/**
 * Edit In place
 *
 */
mf_content = "";
mf_type = "";
mf_meta_id = "";
mf_panel = "";
var mf_niceditor;

jQuery(document).ready(function(){
    jQuery('.EIP_textbox').click(add_editor);
    jQuery('.EIP_mulittextbox').click(add_editor);
    jQuery('.mfceip').live('click',cancel_editor);
    jQuery('.mfseip').live('click',save_editor);
});

save_editor = function(){
    
    if(mf_type =="textbox"){
        //Putting the new content in the mf_Content var
        mf_content = jQuery('#FEIP_textbox_'+mf_meta_id).val();
    }else{
        mf_content = jQuery('#FEIP_mulittextbox_'+mf_meta_id).html();
    }
    

    //saving the post
    values = "meta_id=" + escape(encodeURI(mf_meta_id)) +
	"&field_value=" + escape(encodeURI(mf_content )) + 
	"&field_type=" + escape(encodeURI(mf_type));

	jQuery.ajax({
	    type: "POST",
        url: JS_MF_URI + 'RCCWP_EditnPlaceResponse.php',
        data: values,
        success: function(msg){
            cancel_editor();
        }
	});
}

cancel_editor = function(){
    
    if(mf_type =="textbox"){
        //Remove the  text input
        jQuery('#FEIP_textbox_'+mf_meta_id).remove();

        //putting the original content in the div
        jQuery('#mfeip_'+mf_meta_id).html(mf_content);
    
        //restoring the onclick event to the field
        jQuery('#mfeip_'+mf_meta_id).bind('click',add_editor);
    
        //removing the "savecancel" bar
        jQuery('#save_cancel_field').remove();
    
        //Done
    }else{
        //removing the editor
        mf_niceditor.removeInstance('FEIP_mulittextbox_'+mf_meta_id);
        //removing the panel
        mf_niceditor.removePanel('panel_'+mf_meta_id);
        
        //removing the EIP content
        jQuery('#FEIP_mulittextbox_'+mf_meta_id).remove();
        
        //restoring the original content in the div
        jQuery('#mfmueip_'+mf_meta_id).html(mf_content);
        
        //restoring the onclick event to the field
        jQuery('#mfmueip_'+mf_meta_id).bind('click',add_editor);
        
        //removing the "savecancel" bar
        jQuery('#save_cancel_field').remove();
    }
    
}

add_editor = function(){
    //Getting the classes of the div
    element_class  = jQuery(this).attr('class');
    element_id      = jQuery(this).attr('id');
    
    //Getting the type of field (inputtext or multiline)
    mf_type = element_class.split("_")[1];

    //Getting the meta id
    mf_meta_id = element_id.split("_")[1];
    
    //avoid to duplicate the field
    jQuery(this).unbind('click');
    
    // Create save/cancel buttons
    saveCancel =    '<div id="save_cancel_field" class="EIPSaveCancel" style="display:block;">'+
                        '<div id="savingDiv" style="display:none">saving ...</div>'+
                        '<div id="saveButton">'+
                            '<input type="button" value="Save" id="mfseip_'+mf_meta_id+'" class="mfseip" /> Or'+ 
                            '<input type="button" value="Cancel" id="mfceip_'+mf_meta_id+'" class="mfceip" />'+
                        '</div>'+
                    '</div>';
   	jQuery(document.body).prepend(saveCancel);
    
    //Getting the original value of the content
    mf_content = jQuery(this).html();
    
    
    jQuery(this).empty();
    jQuery(this).html('');
    if(mf_type == "textbox"){
        //Creating the input field for put the new content
        jQuery(this).html('<input  type="text" value="'+mf_content+'" class="FEIP_textbox" id="FEIP_textbox_'+mf_meta_id+'"/>');
    }else{
        //if is multiline
        jQuery(this).html('<div style="background-color: #ccc; padding: 3px; width: 100%;" class="FEIP_mulittextbox" id="FEIP_mulittextbox_'+mf_meta_id+'">'+mf_content+'</div>');
        
        //creating a div for put the pannel        
        mf_niceditor = new nicEditor({iconsPath : JS_MF_URI + 'js/nicEditorIcons.gif',buttonList : ['bold','italic','underline','ol','ul','link','unlink']});
        mf_niceditor.addInstance('FEIP_mulittextbox_'+mf_meta_id);
        // Creat nicEditor panel
        mf_panel = "<div id='panel_" + mf_meta_id+"' class='EIPnicPanelDiv'></div>"; 
        jQuery('#mfmueip_'+mf_meta_id).prepend(mf_panel);
        
        mf_niceditor.setPanel('panel_'+mf_meta_id);
    }
}