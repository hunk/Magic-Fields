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
	
	var suggestCustomFieldName = function() {
	  var desc = jQuery('#custom-field-description').val();
	  
	  // first, try to extract bracketed items OUT of the field name 
	  // so a field labelled "Image File (640 X 480)" would have the extra
	  // info removed from the suggestion
	  
    desc = desc.replace(/\s?\([^\)]*\)/gi, "");   

	  var nv = jQuery.slug(desc, { sep: "_" });
	  
	  
    if (mf_group_info && mf_group_info.safe_name && mf_group_info.safe_name != ""  && mf_group_info.safe_name != "__default") {
      var prefix = jQuery.slug(mf_group_info.singular_safe_name, { sep: "_" });
      
      if (prefix != "" && prefix != "_") {
        nv = prefix + "_" + nv;
      }
    }

	  jQuery('#custom-field-name').val(nv);

  };
  
	jQuery('#custom-field-description').change(function(event) {
	  if (mf_create_field) { // only suggest names if user is CREATING the field
	    suggestCustomFieldName();
	  }
  });
  
	jQuery('#bt-custom-field-name-suggest').click( function() {
	  
	  if (jQuery.trim(jQuery('#custom-field-description').val()) != "") { 
	    suggestCustomFieldName();
    } else {
      alert('Please enter a field label first!');
    }
	  return false;
  });
  
});
