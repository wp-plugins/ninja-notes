<?
/*
Plugin Name: Ninja Notes
version: 1.1
Plugin URI: http://www.code-ninja.co.uk/
Description: NOTES App for keeping track of various things
Author: Code Ninja
Author URI: http://www.code-ninja.co.uk/
*/

//Variables
global $nn_db_version;
$nn_db_version = "1.0";

//Install
function nn_install() {
	global $wpdb;
	global $nn_db_version;
	$table_name = $wpdb->prefix . "ninjanotes";
	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		name tinytext NOT NULL,
		notes text NOT NULL,
		UNIQUE KEY id (id)
	);";
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql); 
	add_option("nn_db_version", $nn_db_version);
}

function nn_install_data() {
	global $wpdb;
	$nn_name="Welcome";
	$nn_note="Thank you for using Ninja Notes.\nWhen A Ninja handles your Notes, you know they are safe.\n\nCode-Ninja";
	$addNote = $wpdb->insert( $wpdb->prefix."ninjanotes",array('name' => $nn_name, 'notes' => $nn_note));
}

//Tags & hooks
register_activation_hook(__FILE__,'nn_install');
register_activation_hook(__FILE__,'nn_install_data');
add_action( 'admin_menu', 'nn_add_post_box' );
add_action('admin_menu', 'ninjanotes_menu');
add_action("save_post", "nn_save_details");


//This function adds the  meta baox to the Write Post Screen
function nn_add_post_box() {
	add_meta_box('nn_options','Ninja Notes','nn_post_box_content','post','normal','high' );
	add_meta_box('nn_options','Ninja Notes','nn_post_box_content','page','normal','high' );
}

//Save Notes
function nn_save_details($post_id){
global $wpdb;
  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
  if ( 'page' == $_POST['post_type'] )
  {
    if ( !current_user_can( 'edit_page', $post_id ) )
        return $post_id;
  }
  else
  {
    if ( !current_user_can( 'edit_post', $post_id ) )
        return $post_id;
  };
  $wpdb->update($wpdb->prefix."ninjanotes", array('notes' => $_POST['nnnotes']), array('id' => $_POST['nnselect']),array('%s'));

}

//metaBox for posts/pages
function nn_post_box_content() {
global $wpdb;
?>
<script type="text/javascript">
jQuery(document).ready(function() {
        jQuery("#nnselect").change(function() {
                var selected = jQuery("#nnselect").val();
                jQuery.ajax({
                        type: "POST",
                        data: "&id=" + selected,
                        url: "<? echo plugins_url('ninja-notes/notes.php');?>",
                        success: function(msg){
                                        document.getElementById("nnnotes").value = msg;
                        }
                });
        });
        var selected = jQuery("#nnselect").val();
        jQuery.ajax({
                type: "POST",
                data: "&id=" + selected,
                url: "<? echo plugins_url('ninja-notes/notes.php');?>",
                success: function(msg){
                        document.getElementById("nnnotes").value = msg;
                }
        });
});
</script>
<select name="nnselect" id="nnselect">
<?php
$res = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ninjanotes order by `name`");
foreach($res as $row){
echo("<option value='".$row->id."'>".$row->name."</option>");
}
?>
</select><br/>
<div id="wp-content-editor-container" class="wp-editor-container">
<textarea rows="15" cols="100"  name="nnnotes" id="nnnotes" class="wp-editor-area">
</textarea></div>
<?php
}


//Menu
function ninjanotes_menu() {
	add_menu_page( 'Notes', 'Notes', 'manage_options', 'ninjanotes-notepage', 'ninjanotes_notepage_callback', plugins_url('ninja-notes/images/icon.png') ,40 );
	add_submenu_page( 'ninjanotes-notepage', 'Information', 'Information', 'manage_options', 'ninjanotes-infopage', 'ninjanotes_infopage_callback' );
}

function ninjanotes_notepage_callback() {
global $wpdb;
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery("#nnselect").change(function() {
		var selected = jQuery("#nnselect").val();
	        jQuery.ajax({
	        	type: "POST",
        		data: "&id=" + selected,
			url: "<? echo plugins_url('ninja-notes/notes.php');?>",
            		success: function(msg){
                    			document.getElementById("nnnotes").value = msg;
			}			
		});
	});
	var selected = jQuery("#nnselect").val();
	jQuery.ajax({
		type: "POST",
		data: "&id=" + selected,
		url: "<? echo plugins_url('ninja-notes/notes.php');?>",
		success: function(msg){
			document.getElementById("nnnotes").value = msg;
		}
	});
});
</script>

<div class="wrap">
<h2>Ninja Notes by <a href="http://code-ninja.co.uk">CodeNinja</a></h2>
<hr/>
<form method="post" action="<? echo plugins_url('ninja-notes/notes.php');?>">
<select name="nnselect" id="nnselect">
<?php
$res = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ninjanotes order by `name`");
foreach($res as $row){
echo("<option value='".$row->id."'>".$row->name."</option>");
}
?>
</select><br/>
<textarea rows="25" cols="140"  name="nnnotes" id="nnnotes">
Please Select a Note from the dropdown
If there are no notes listed in the dropdown then create a new one.
</textarea>
<input type="hidden" name="page_options" value="nnnotes,nnselect" />
<input type="hidden" name="action" value="update" />
<p>
<input type="submit" value="Save" name="submit"/><input type="submit" value="Delete" name="submit"/>
<input type="text" name="nnname"><input type="submit" value="New" name="submit"/>
</p>
</form>
</div>
<?
}

function ninjanotes_infopage_callback() {
?>
<div class="wrap">
<h2>Ninja Notes by <a href="http://code-ninja.co.uk">CodeNinja</a></h2>
<hr/>
<p>
<b>Ninja Notes</b> is a simple notepad system for Wordpress. 
</p>
<p>
I needed a place to keep track of Blog Series, Peoples Aliases, and other small bits of information I used on my blog. I tried several notes plugins, but none did exactly as I wanted. So I made my own simple notepad that can keep track of as many sets of notes as you need. It's like a whole stack of handy post it notes.
</p>
<p>
If you find this Plugin as useful as I do, Please consider a small donation to keep me in Beer & Developing.
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
<input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal  The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
</form>
</p>
</div>
<?
}

?>
