<?php

define('RC_CWP_QUERY_PERFIX', 'x_');
define('RC_CWP_QUERY_ORDERBY', 'customorderby');

class RCCWP_Query
{

	function FilterPrepare(&$qs)
	{
		global $curr_qs_vars;
		$curr_qs_vars = $qs->query_vars;
		return $qs;
	}

	/**
	 *  Add a new column with the name of the  write
	 *	panel was used for create the post/page, 
	 *	this is executed if the "condese menu" option is active
	 */
	function ColumnWritePanel($defaults){
		//put the write panel column in the third place of the table
		$counter = 0;
		$temp = array();
		foreach($defaults as $key => $element){
			if($counter == 2){
				$temp['WritePanel'] = "Panel name";
			}
			$temp[$key] = $element;
			$counter++;
		}

		$defaults = $temp;
		
		return $defaults;
	}
	
	
	/**
	 *  Fill the new column Panel name
	 *	This is executed if the "condense menu" option is active
	 */
	function ColumnWritePanelData($column_name){
		global $post;
		
		if($column_name == "WritePanel"){
			$name = RCCWP_CustomWritePanel::GetWritePanelName($post->ID);
			
			if(!$name){
				echo " - ";
			}else{
				echo $name;
			}
		}
	}
	

	/**
	 *  Filter all the posts in POST -> Edit  for doesn't display 
	 *  the posts created using some write panel.
	 */
	function ExcludeWritepanelsPosts($where){
		global $wpdb, $parent_file;
		$types = array('edit.php','edit-pages.php','edit.php?post_type=page');
		if( !in_array($parent_file, $types) ) return $where;
	
		require_once ('RCCWP_Options.php');
		$exclude = RCCWP_Options::Get('hide-non-standart-content');

		if($exclude == false){
			return $where;
		}

		if (empty($_GET['filter-posts'])){
			$where = $where . " AND 0 = (SELECT count($wpdb->postmeta.meta_value) FROM $wpdb->postmeta WHERE $wpdb->postmeta.post_id = $wpdb->posts.ID and $wpdb->postmeta.meta_key = '_mf_write_panel_id')";
		}
		return $where;
	}

	function FilterCustomPostsWhere($where)
	{
		global $wpdb;
		global $curr_qs_vars; 
		if( is_array($curr_qs_vars) ){
                  foreach ($curr_qs_vars as $queryVarKey => $queryVarValue){
                    if (substr($queryVarKey, 0, strlen(RC_CWP_QUERY_PERFIX)) == RC_CWP_QUERY_PERFIX){	
                      $customKey = substr($queryVarKey, strlen(RC_CWP_QUERY_PERFIX));
                      $customVal = $queryVarValue;
                      $where = $where . " AND 0 < (SELECT count($wpdb->postmeta.meta_value)
						FROM $wpdb->postmeta
						WHERE $wpdb->postmeta.post_id = $wpdb->posts.ID and $wpdb->postmeta.meta_key = '$customKey' and $wpdb->postmeta.meta_value = '$customVal') ";
                    }
                  }
                }
		//Add orderby 
		if (get_query_var(RC_CWP_QUERY_ORDERBY)){
			$newOrderby = get_query_var(RC_CWP_QUERY_ORDERBY);
			$newOrderbyFieldName = 	substr($newOrderby, strlen(RC_CWP_QUERY_PERFIX));
			$where = $where . " AND pmeta.meta_key = '$newOrderbyFieldName' ";
		}
		
		return $where;
	}

	function FilterCustomPostsOrderby($orderby)
	{
		global $wpdb;
		
		if (get_query_var(RC_CWP_QUERY_ORDERBY)){
			$newOrderby = get_query_var(RC_CWP_QUERY_ORDERBY);
			$newOrderbyFieldName = 	substr($newOrderby, strlen(RC_CWP_QUERY_PERFIX));
			$orderby = "pmeta.meta_value ".get_query_var('order');
		}
		
		return $orderby;

	}

	function FilterCustomPostsFields($fields) {
		global $wpdb;
		if (get_query_var(RC_CWP_QUERY_ORDERBY)){
			$newOrderby = get_query_var(RC_CWP_QUERY_ORDERBY);
			$newOrderbyFieldName = 	substr($newOrderby, strlen(RC_CWP_QUERY_PERFIX));
			$fields = $fields. " , pmeta.meta_value ";
		}
		
		return $fields;
	}

	function FilterCustomPostsJoin($join) {
		global $wpdb;

		if (get_query_var(RC_CWP_QUERY_ORDERBY)){
			$join = $join . " INNER JOIN $wpdb->postmeta pmeta ON $wpdb->posts.ID = pmeta.post_id "; 
			
		}
		
		return $join;
	}

}
