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

	function FilterCustomPostsWhere($where)
	{
		global $wpdb;
		global $curr_qs_vars; 
		
		foreach ($curr_qs_vars as $queryVarKey => $queryVarValue){
			if (substr($queryVarKey, 0, strlen(RC_CWP_QUERY_PERFIX)) == RC_CWP_QUERY_PERFIX){	
				$customKey = substr($queryVarKey, strlen(RC_CWP_QUERY_PERFIX));
				$customVal = $queryVarValue;
				$where = $where . " AND 0 < (SELECT count($wpdb->postmeta.meta_value)
						FROM $wpdb->postmeta
						WHERE $wpdb->postmeta.post_id = $wpdb->posts.ID and $wpdb->postmeta.meta_key = '$customKey' and $wpdb->postmeta.meta_value = '$customVal') ";
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
?>