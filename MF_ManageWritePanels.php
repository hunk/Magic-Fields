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
          change_button_new('%s','%scustom-write-panel-id=%s');
        });
      //]]>
      </script>",$type_add_new[$parent_file],$contact,$_GET['custom-write-panel-id']);
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
          change_number_manage_wp%s('(%s)','(%s)','(%s)','(%s)','(%s)','(%s)','%s','filter-posts=1&custom-write-panel-id=%s');
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
      $post_type ,
      $_GET['custom-write-panel-id']
    );
  }
  return $where;
}