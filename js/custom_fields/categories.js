jQuery().ready(function() {
  
  jQuery('#categorychecklist  input:checked').each(function(){
    jQuery(this).removeAttr('checked');
  });
  
  jQuery.each(mf_categories, function(key,value) {
    jQuery("#in-category-"+value).attr('checked','checked');
  });
  
});