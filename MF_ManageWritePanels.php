<?php

//enqueue js file
 add_action('admin_init', 'manage_js');
 
function manage_js(){
  wp_enqueue_script('manage_page_js',
					MF_URI.'js/manage_page.js',
					array('jquery'),
          '0.1',
          TRUE
				);

    //load jquery stringToSlug plugin (special chars power convert)
		wp_enqueue_script(	'jqueryStringToSlug', 
							MF_URI.'js/jquery.stringToSlug.min.js'
						);


}


// filter for boton new
add_filter('manage_posts_columns','change_botton_new_in_manage');
add_filter('manage_pages_columns','change_botton_new_in_manage');

function change_botton_new_in_manage($where){
  global $parent_file;

  $types = array('edit.php','edit-pages.php','edit.php?post_type=page');
  $type_add_new = array('edit.php' => 'post-new.php' ,'edit-pages.php' => 'page-new.php','edit.php?post_type=page' => 'post-new.php?post_type=page');
  $contact = '?';
  if(strpos($parent_file, '?')) $contact = '&';
  
  if( !in_array($parent_file, $types) ) return $where;
  
  if( isset($_GET['custom-write-panel-id']) ){
    printf("
      <script type=\"text/javascript\">
      //<![CDATA[
        jQuery().ready(function() {
          change_button_new('%s','%scustom-write-panel-id=%s','%s');
					unlink_write_panel();
        });
      //]]>
      </script>",$type_add_new[$parent_file],$contact,$_GET['custom-write-panel-id'],$_GET['custom-write-panel-id']);
  }
  return $where;
}


// filter for numbers in manage
add_filter('manage_posts_columns','change_number_manage');
add_filter('manage_pages_columns','change_number_manage');

function change_number_manage($where){
  global $wpdb, $parent_file;
  
  $types = array('edit.php','edit-pages.php','edit.php?post_type=page');
  if( !in_array($parent_file, $types) ) return $where;
  
  if(isset($_GET['custom-write-panel-id'])){
    
		if(is_wp30()){
			$ver = '30';
			$post_type = 'post';
			if( isset($_GET['post_type']) ) $post_type = $_GET['post_type'];
		}else{
			$ver = '29';
			$post_type = $parent_file;
		}
    $num_posts_mf = RCCWP_CustomWritePanel::GetCountPstWritePanel($_GET['custom-write-panel-id']);
    printf("
      <script type=\"text/javascript\">
      //<![CDATA[
        jQuery().ready(function() {
          change_number_manage_wp%s('(%s)','(%s)','(%s)','(%s)','(%s)','(%s)','(%s)','%s','filter-posts=1&custom-write-panel-id=%s');
        });
      //]]>
      </script>",
			$ver,
      array_sum( (array) $num_posts_mf ) - $num_posts_mf->trash,
      $num_posts_mf->publish,
      $num_posts_mf->pending,
      $num_posts_mf->draft,
      $num_posts_mf->private,
      $num_posts_mf->trash,
      $num_posts_mf->future,
      $post_type ,
      $_GET['custom-write-panel-id']
    );
  }
  return $where;
}

// change the title manage page
add_filter('manage_posts_columns','change_title_manage');
add_filter('manage_pages_columns','change_title_manage');

function change_title_manage($where){
  if(isset($_GET['custom-write-panel-id'])){
    
    $write_panel = RCCWP_CustomWritePanel::Get($_GET['custom-write-panel-id']);
    printf("
      <script type=\"text/javascript\">
      //<![CDATA[
        jQuery().ready(function() {
          change_title_manage('%s');
        });
      //]]>
      </script>",
    $write_panel->name
    );
  }
    
  return $where;
}

// add input for search in manage page for write panels
add_filter('manage_posts_columns','add_input_search_manage');
add_filter('manage_pages_columns','add_input_search_manage');

function add_input_search_manage($where){
  if(isset($_GET['custom-write-panel-id'])){
    
    $write_panel = RCCWP_CustomWritePanel::Get($_GET['custom-write-panel-id']);
    printf("
      <script type=\"text/javascript\">
      //<![CDATA[
        jQuery().ready(function() {
          add_input_search_manage('%s');
        });
      //]]>
      </script>",
      $_GET['custom-write-panel-id']
    );
  }
  return $where;
}

// change the number for edit in post or page when hide post with write panel
add_filter('manage_posts_columns','change_number_not_write_panel_manage');
add_filter('manage_pages_columns','change_number_not_write_panel_manage');

function change_number_not_write_panel_manage($where){
  global $parent_file;
  
  $types = array('edit.php','edit-pages.php','edit.php?post_type=page');
  
  if( !in_array($parent_file, $types) ) return $where;
  if(isset($_GET['custom-write-panel-id'])) return $where;
  if( !RCCWP_Options::Get('hide-non-standart-content') ) return $where;

  $type = 'post';
  if(is_wp30()){
    if($parent_file == 'edit.php?post_type=page') $type = 'page';
  }else{
    if($parent_file == 'edit-pages.php') $type = 'page';
  }

  $num_posts_mf = RCCWP_CustomWritePanel::GetCountPostNotWritePanel($type);
  printf("
    <script type=\"text/javascript\">
    //<![CDATA[
      jQuery().ready(function() {
        change_number_manage_not_write_panel('(%s)','(%s)','(%s)','(%s)','(%s)','(%s)','(%s)');
      });
    //]]>
    </script>",
    array_sum( (array) $num_posts_mf ) - $num_posts_mf->trash,
    $num_posts_mf->publish,
    $num_posts_mf->pending,
    $num_posts_mf->draft,
    $num_posts_mf->private,
    $num_posts_mf->trash,
    $num_posts_mf->future
  );
  
  return $where;
}
