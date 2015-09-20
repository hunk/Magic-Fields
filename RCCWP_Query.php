<?php

define('RC_CWP_QUERY_PERFIX', 'x_');
define('RC_CWP_QUERY_ORDERBY', 'customorderby');

class RCCWP_Query
{

	public static function FilterPrepare(&$qs)
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
	public static function ExcludeWritepanelsPosts($where){
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

		//is search
		if (isset($_GET['s']) && isset($_GET['filter-posts']) ) {
			$remove = "/and wp_postmeta.meta_key = '_mf_write_panel_id' and wp_postmeta.meta_value = '(\w)'/";
			$where = preg_replace($remove,"",$where);

			$sql = $wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_mf_write_panel_id' AND meta_value = '%s'",$_GET['custom-write-panel-id']);
			$results = $wpdb->get_results($sql);

			if (count($results) == 0){
				return $where;
			}

			$postIDs = array();
			foreach ($results as $result) {
				$postIDs[] = $result->post_id;
			}

			if ( count($postIDs) == 1 ) {
				$postIDs[] = $postIDs[0];
			}
			$where .= sprintf(" AND $wpdb->posts.ID IN (%s)",implode(",", $postIDs));
		}

		return $where;
	}

	public static function FilterCustomPostsWhere($where)
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

	public static function FilterCustomPostsOrderby($orderby)
	{
		global $wpdb;
		
		if (get_query_var(RC_CWP_QUERY_ORDERBY)){
			$newOrderby = get_query_var(RC_CWP_QUERY_ORDERBY);
			$newOrderbyFieldName = 	substr($newOrderby, strlen(RC_CWP_QUERY_PERFIX));
			$orderby = "pmeta.meta_value ".get_query_var('order');
		}
		
		return $orderby;

	}

	public static function FilterCustomPostsFields($fields) {
		global $wpdb;
		if (get_query_var(RC_CWP_QUERY_ORDERBY)){
			$newOrderby = get_query_var(RC_CWP_QUERY_ORDERBY);
			$newOrderbyFieldName = 	substr($newOrderby, strlen(RC_CWP_QUERY_PERFIX));
			$fields = $fields. " , pmeta.meta_value ";
		}
		
		return $fields;
	}

	public static function FilterCustomPostsJoin($join) {
		global $wpdb;

		if (get_query_var(RC_CWP_QUERY_ORDERBY)){
			$join = $join . " INNER JOIN $wpdb->postmeta pmeta ON $wpdb->posts.ID = pmeta.post_id "; 
			
		}
		
		return $join;
	}

	public static function AddConditionForSearchInPostmeta($where){
		if( is_search() ) {
		
			global $wpdb, $wp;
		
			$where = preg_replace(
				"/($wpdb->posts.post_title (LIKE '%{$wp->query_vars['s']}%'))/i",
				"$0 OR ( $wpdb->postmeta.meta_value LIKE '%{$wp->query_vars['s']}%' )",
				$where
				);
			
			add_filter( 'posts_join_request', array('RCCWP_Query','JoinForSearchPostMeta' ));
			add_filter( 'posts_distinct_request', array('RCCWP_Query', 'DistinctForSearchPostMeta' ));
		}
	
		return $where;
	}

	public static function JoinForSearchPostMeta( $join ) {
		global $wpdb;
		$pattern = '/'.$wpdb->postmeta.'/';
		preg_match($pattern, $join, $matches);
		if (count($matches) == 0) {
			return $join .= " LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id) ";
		}

		return $join;
	}

	public static function DistinctForSearchPostMeta( $distinct ) {
		return 'DISTINCT';
	}

}
