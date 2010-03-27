jQuery().ready(function() {
  
  if(mf_parent != ''){
    jQuery("#parent_id option[value='"+mf_parent+"']").attr('selected', 'selected');
  }
  
  if(mf_theme != ''){
    jQuery("#page_template option[value='"+mf_theme+"']").attr('selected', 'selected');
  }
  
});