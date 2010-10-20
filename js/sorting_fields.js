jQuery(function($) {  
  $('.sortable').sortable({
    handle: '.handler',
    update: function(event,ui) {
      update_order();
    }
  });
});

update_order = function(){
  //for each group
  jQuery('.sortable').each(function() { 
    //for each field in the group
    //is reorder the items
    index = 0;
    jQuery('input[name*=mf_order]',this).each(function() {
      jQuery(this).val(index);
      index++;
    });
  });
}
