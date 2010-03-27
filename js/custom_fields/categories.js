jQuery().ready(function() {
  
  if(mf_categories.length == 1 && mf_categories[0] == "" ){
    
  }else{
    jQuery('#categorychecklist  input:checked').each(function(){
      jQuery(this).removeAttr('checked');
    });
  
    jQuery.each(mf_categories, function(key,value) {
      jQuery("#in-category-"+value).attr('checked','checked');
    });
  }
  
});