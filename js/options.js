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
                                " 'Hide non-standart content in Post Panel' option?";
        
                if(confirm(uncompatible)){
                    jQuery("#condense-menu").removeAttr("checked");
                }else{
                    jQuery(this).removeAttr("checked");
                }
            }
        }
    });
});