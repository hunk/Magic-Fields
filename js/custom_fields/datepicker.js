jQuery(document).ready(function(){
    
    //Adding the datepicker event to the fields
	jQuery('.datebotton_mf').live('click',function(){
	    	    
        the_id = jQuery(this).attr('id');
        picker = the_id.replace(/pick_/,'');
        format = jQuery('#format_date_field_'+picker).text();
        format = switch_formats(format);
        picker = 'display_date_field_' + picker;
        
        
        jQuery('#'+picker).datepicker({
            changeYear: true,
            dateFormat: format,
            showOn:'focus',
            onClose: function(){
                input = jQuery(this);
                date = input.val();
                id = input.attr('id').replace(/display_/,'');
                jQuery('#'+id).val(date);
                
                //unbind the event
                jQuery(this).datepicker('destroy');
            }
        }).focus();
	});
	
	//TODAY Botton
	jQuery('.todaybotton_mf').live('click',function(){
	    the_id = jQuery(this).attr('id');
	    picker = the_id.replace(/today_/,'');
	    today = 'tt_' + picker;    
	    today = jQuery('#'+today);
	    date = today.val();
	    
        jQuery('#display_date_field_'+picker).val(date);
	    input = picker.replace(/display_/,'');
	    input = jQuery('#'+input);
	    input.val(date)
	});
});

//From php date format to jqueyr datepicker format
switch_formats = function(date){

    if(date == "m/d/Y"){
        return "mm/dd/yy";
    }

    if(date == "l, F d, Y"){
        return "DD, MM dd, yy"; 
    }
    
    if(date == "F d, Y"){
        return "MM dd, yy"
    }
    
    if(date == "m/d/y"){
        return "mm/dd/y";
    }
    
    if(date == "Y-d-m"){
        return "yy-dd-mm";
    }
    
    if(date == "d-M-y"){
        return "dd-M-y";
    }
    
    if(date == "m.d.Y"){
        return "mm.dd.yy";
    }
    
    if(date == "m.d.y"){
        return "mm.dd.y";
    }
}
