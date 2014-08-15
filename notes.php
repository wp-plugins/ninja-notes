<?php
$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
require_once( $parse_uri[0] . 'wp-load.php' );
global $wpdb;
if($_POST['submit']=="Save"){
	$wpdb->update($wpdb->prefix."ninjanotes", array('notes' => $_POST['nnnotes']), array('id' => $_POST['nnselect']),array('%s'));
	header("Location: ". $_SERVER["HTTP_REFERER"]);
}elseif($_POST['submit']=="Delete"){
	$wpdb->query("delete from ".$wpdb->prefix."ninjanotes where `id`='".$_POST['nnselect']."'");
	header("Location: ". $_SERVER["HTTP_REFERER"]);
}elseif($_POST['submit']=="New"){
	if($_POST['nnname']!=''){
		$wpdb->insert( $wpdb->prefix."ninjanotes",array('name' => $_POST['nnname']),array('%s'));
	}
	header("Location: ". $_SERVER["HTTP_REFERER"]);
}else{
	$res = $wpdb->get_var("SELECT `notes` FROM ".$wpdb->prefix."ninjanotes where `id`='".$_POST['id']."'");
	echo($res);
}
?>
