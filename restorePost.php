<?php

$id=$_GET['id'];
global $wpdb;
$table_posts=$wpdb->prefix ."posts";
$table_checkLogs=$wpdb->prefix ."check_logs";
$wpdb->update($table_posts,array('post_status'=>"publish"),array("ID"=>$id));
$wpdb->delete($table_checkLogs,array('id_post'=>$id,'user_action'=>'delete'));
$url="admin.php?page=checkLogin";
header("Location: $url", true, 301);
exit;

?>