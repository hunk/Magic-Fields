<?php
require( dirname(__FILE__) . '/../../../wp-config.php' );

// Start saving data
global $wpdb;

$field_value = urldecode($_POST['field_value']);
$field_type = $wpdb->escape($_POST['field_type']);
$meta_id = $wpdb->escape($_POST['meta_id']);

//@todo Sanitize this  query
$wpdb->query("UPDATE $wpdb->postmeta SET meta_value = '{$field_value}' WHERE meta_id = '{$meta_id}'");