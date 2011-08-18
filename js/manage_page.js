// change url for button new in manage page
function change_button_new(url_type, add_url,write_panel){
  tmp_url = jQuery(".wrap").children('h2').children('a').attr('href');
  if(tmp_url == url_type){
    jQuery(".wrap").children('h2').children('a').attr('href',tmp_url+add_url);

    jQuery('li.wp-has-current-submenu > a.wp-has-current-submenu').removeClass('wp-has-current-submenu');
    jQuery('li.wp-has-current-submenu').removeClass('wp-has-current-submenu');
    
    jQuery('li#mf-menu-'+write_panel).addClass('wp-has-current-submenu');
    jQuery('li#mf-menu-'+write_panel+' > a.wp-has-submenu').addClass('wp-has-current-submenu');
  }
}

function unlink_write_panel(){
  
  if( jQuery('span.unlink-writepanel').length == 0){
   jQuery('div.row-actions').each(function(){
    
    id = jQuery(this).parent().parent().attr('id');
    page = escape(document.location.href);
     jQuery(this).append(' | <span class="unlink-writepanel"><a title="unlink this post fo write panel" onclick="return confirm_unlink_write_panel();" href="?page=MagicFieldsMenu&mf_action=unlink-write-panel&post-id='+id+'&dashboard='+page+'">Unlink Post of write panel</a></span>');    
   });
  
 
  }
}

function confirm_unlink_write_panel(){
  var answer = confirm("Unlink write panel delete all data of custom fields (include  images), are you sure?");
	if (answer){
    return true;
  }else{
    return false;
  }
}

function change_highlight_custom_panel(write_panel){
  jQuery('li.wp-has-current-submenu > a.wp-has-current-submenu').removeClass('wp-has-current-submenu');
  jQuery('li.wp-has-current-submenu').removeClass('wp-has-current-submenu');
  
  jQuery('li#mf-menu-'+write_panel).addClass('wp-has-current-submenu');
  jQuery('li#mf-menu-'+write_panel+' > a.wp-has-submenu').addClass('wp-has-current-submenu');
}

function change_number_manage_wp30(all, published, pending, draft, private_,trash,scheduled,page,add_url){
  jQuery('.subsubsub li > a').each(function(){
    element = jQuery(this).text().split(" (");
    url = jQuery(this).attr('href');
    switch(element[0]) {
      case "All":
        if(url == 'edit.php?post_type='+page){
          jQuery(this).attr('href',url+'&'+add_url);
          jQuery(this).children('span').text(all);
        }
        break;
      case "Published":
        if(url == 'edit.php?post_status=publish&post_type='+page){
          jQuery(this).attr('href',url+'&'+add_url);
          jQuery(this).children('span').text(published);
        }
        break;
      case "Pending Review":
        if(url == 'edit.php?post_status=pending&post_type='+page){
          jQuery(this).attr('href',url+'&'+add_url);
          jQuery(this).children('span').text(pending);
        }
        break;
      case "Pending":
        if(url == 'edit.php?post_status=pending&post_type='+page){
          jQuery(this).attr('href',url+'&'+add_url);
          jQuery(this).children('span').text(pending);
        }
        break;
      case "Drafts":
        if(url == 'edit.php?post_status=draft&post_type='+page){
          jQuery(this).attr('href',url+'&'+add_url);
          jQuery(this).children('span').text(draft);
        }
        break;
      case "Draft":
        if(url == 'edit.php?post_status=draft&post_type='+page){
          jQuery(this).attr('href',url+'&'+add_url);
          jQuery(this).children('span').text(draft);
        }
        break;
      case "Private":
        if(url == 'edit.php?post_status=private&post_type='+page){
          jQuery(this).attr('href',url+'&'+add_url);
          jQuery(this).children('span').text(private_);
        }
        break;
      case "Trash":
        if(url == 'edit.php?post_status=trash&post_type='+page){
          jQuery(this).attr('href',url+'&'+add_url);
          jQuery(this).children('span').text(trash);
        }
        break;
      case "Scheduled":
        if(url == 'edit.php?post_status=future&post_type='+page) {
          jQuery(this).attr('href',url+'&'+add_url);
          jQuery(this).children('span').text(scheduled);
        }
      }
  });
}

function change_number_manage_wp29(all, published, pending, draft, private_,trash,scheduled,page,add_url){

  jQuery('.subsubsub li > a').each(function(){
    element = jQuery(this).text().split(" (");
    url = jQuery(this).attr('href');
    switch(element[0]) {
      case "All":
        if(url == page){
          jQuery(this).attr('href',url+'?'+add_url);
          jQuery(this).children('span').text(all);
        }
        break;
      case "Published":
        if(url == page+'?post_status=publish'){
          jQuery(this).attr('href',url+'&'+add_url);
          jQuery(this).children('span').text(published);
        }
        break;
      case "Pending Review":
        if(url == page+'?post_status=pending'){
          jQuery(this).attr('href',url+'&'+add_url);
          jQuery(this).children('span').text(pending);
        }
        break;
      case "Pending":
        if(url == page+'?post_status=pending'){
          jQuery(this).attr('href',url+'&'+add_url);
          jQuery(this).children('span').text(pending);
        }
        break;
      case "Drafts":
        if(url == page+'?post_status=draft'){
          jQuery(this).attr('href',url+'&'+add_url);
          jQuery(this).children('span').text(draft);
        }
        break;
      case "Draft":
        if(url == page+'?post_status=draft'){
          jQuery(this).attr('href',url+'&'+add_url);
          jQuery(this).children('span').text(draft);
        }
        break;
      case "Private":
        if(url == page+'?post_status=private'){
          jQuery(this).attr('href',url+'&'+add_url);
          jQuery(this).children('span').text(private_);
        }
        break;
      case "Trash":
        if(url == page+'?post_status=trash'){
          jQuery(this).attr('href',url+'&'+add_url);
          jQuery(this).children('span').text(trash);
        }
        break;
      case "Scheduled":
        if(url == page+'?post_status=future') {
          jQuery(this).attr('href',url+'&'+add_url);
          jQuery(this).children('span').text(scheduled);
        }
      }
  });
}

function change_title_manage(write_panel){
  
  tmp = jQuery(".wrap").children('h2').html().split(' <');
  check = jQuery(".wrap").children('h2').text().split(">>");
  if(check.length == 1){
    jQuery(".wrap").children('h2').html('Manage >> ' + write_panel + '<' +tmp[1]);
  }
}

function add_input_search_manage(write_panel){
  check = jQuery('#input_mf_write_panel');
  if(check.length == 0){
    add_input  = '<input type="hidden" value="1"  id="input_mf_write_panel" name="filter-posts">';
    add_input += '<input type="hidden" value="'+write_panel+'"  name="custom-write-panel-id">';
    jQuery("#posts-filter").append(add_input);
  }
}

function change_number_manage_not_write_panel(all, published, pending, draft, private_,trash,scheduled){
  jQuery('.subsubsub li > a').each(function(){
    element = jQuery(this).text().split(" (");

    switch(element[0]) {
      case "All":
        jQuery(this).children('span').text(all);
        break;
      case "Published":
        jQuery(this).children('span').text(published);
        break;
      case "Pending Review":
        jQuery(this).children('span').text(pending);
        break;
      case "Pending":
        jQuery(this).children('span').text(pending);
        break;
      case "Drafts":
        jQuery(this).children('span').text(draft);
        break;
      case "Draft":
        jQuery(this).children('span').text(draft);
        break;
      case "Private":
        jQuery(this).children('span').text(private_);
        break;
      case "Trash":
        jQuery(this).children('span').text(trash);
        break;
      case "Scheduled":
        jQuery(this).children('span').text(scheduled);
      }
  });
}
