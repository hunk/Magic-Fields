jQuery(document).ready(function(){
    jQuery('#condense-menu').click(function(){
        if(jQuery(this).attr("checked")){
            if(jQuery('#hide-non-standart-content').attr("checked")){
                uncompatible =  "This option is uncompatible with 'Hide"+ 
                                " non-standart content in Post Panel' option"+
                                " do you want disable this option and active"+
                                " 'Condensing Menu' option?";
                                
                if(confirm(uncompatible)){
                    jQuery("#hide-non-standart-content").removeAttr("checked");
                }else{
                    jQuery(this).removeAttr("checked");
                }
            }
        }
    });
    
    jQuery('#hide-non-standart-content').click(function(){
        if(jQuery(this).attr("checked")){
            if(jQuery('#condense-menu').attr("checked")){
                uncompatible =  "This option is uncompatible with '"+ 
                                "Condense Menu' option"+
                                " do you want disable this option and active"+
                                " 'Hide non-standard content in Post Panel' option?";
        
                if(confirm(uncompatible)){
                    jQuery("#condense-menu").removeAttr("checked");
                }else{
                    jQuery(this).removeAttr("checked");
                }
            }
        }
    });

	jQuery('input[name=is_public]').click(function(){
		jQuery('.is_public_options').toggle("slow");	
	});

	jQuery('input[name=supports]').click(function(){
		jQuery('.supports_options').toggle("slow");	
	});
	
	
	/* Addition to allow suggestion of a field name based on the label */
	var string_prefix = "";
	if (typeof mf_group_info !== 'undefined' && mf_group_info && mf_group_info.safe_name && mf_group_info.safe_name != ""  && mf_group_info.safe_name != "__default") {
		string_prefix = mf_group_info.name + ' ';
		if (string_prefix == " ") {
			string_prefix = "";
		}			
	}	
	
	jQuery('#custom-field-description').focus(function(event) {
	  if (mf_create_field) { // only suggest names if user is CREATING the field
	    jQuery("#custom-field-description").stringToSlug({space:'_', getPut:'#custom-field-name', prefix:string_prefix, replace:/\s?\([^\)]*\)/gi});
	  }
  });
  
	jQuery('#bt-custom-field-name-suggest').click( function() {	  
	  if (jQuery.trim(jQuery('#custom-field-description').val()) != "") { 
	    jQuery("#custom-field-description").stringToSlug({space:'_', getPut:'#custom-field-name', prefix:string_prefix, replace:/\s?\([^\)]*\)/gi});
		jQuery("#custom-field-description").trigger('blur');
    } else {
      alert('Please enter a field label first!');
    }
	  return false;
  });
  
});
