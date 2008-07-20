<?php
/*
Plugin Name: DMSGuestbook
Plugin URI: http://danielschurter.net/
Description: The administration panel is found on the top of this site.
Version: 1.12.0
Author: Daniel M. Schurter
Author URI: http://danielschurter.net/
*/

define('DMSGUESTBOOKVERSION', "1.12.0");

	/* menu (DMSGuestbook, Manage) */
	add_action('admin_menu', 'add_dmsguestbook');


	function add_dmsguestbook() {
		add_menu_page(__('Options', 'dmsguestbook'), __('DMSGuestbook', 'dmsguestbook'),
		'edit_others_posts', 	'dmsguestbook', 'dmsguestbook_meta_description_option_page');

		add_submenu_page( 'dmsguestbook' , __('Manage', 'dmsguestbook'), __('Manage', 'dmsguestbook'), 'edit_others_posts',
		'Manage', 'dmsguestbook2_meta_description_option_page');

		add_submenu_page( 'dmsguestbook' , __('FAQ', 'dmsguestbook'), __('FAQ', 'dmsguestbook'), 'edit_others_posts',
		'FAQ', 'dmsguestbook4_meta_description_option_page');

	  /* if role is administrator */
	  global $userdata;
	  if($userdata->user_level==10) {
		add_submenu_page( 'dmsguestbook' , __('phpinfo', 'dmsguestbook'), __('phpinfo', 'dmsguestbook'), 'edit_others_posts',
		'phpinfo', 'dmsguestbook3_meta_description_option_page');
	  }
	}

	/* create db while the activation process */
	add_action('activate_dmsguestbook/admin.php', 'dmsguestbook_install');


	/* version */
	add_action('wp_head', 'addversion');
	function addversion() {
		echo "<meta name='DMSGuestbook' content='".DMSGUESTBOOKVERSION."' />\n";
	}


	/* backup options */
	if(isset($_REQUEST[backup_options])) {
	$filename = "DMSGuestbook_options_" . date("d-m-Y") . ".txt";
	header("Content-Type: text/plain");
	header("Content-Disposition: attachment; filename=$filename");
	echo get_option("DMSGuestbook_options");
	exit;
	}

	/* restore options*/
	if($_REQUEST[restore_options]==1 && $_REQUEST[restore_data]!="") {
	$restore = str_replace("\r\n", "[br]", $_REQUEST[restore_data]);
	update_option("DMSGuestbook_options", $restore);
	message("<b>Options were saved...</b>", 300, 800);
	}

	if($_REQUEST[restore_options]==1 && $_REQUEST[restore_data]=="") {
	message("<b>Options were not saved, textfield is empty...</b>", 300, 800);
	}


# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

/* DMSGuestbook adminpage main function */

function dmsguestbook_meta_description_option_page() {

	$url=get_bloginfo('wpurl');

	/* initialize */
	$options 			= create_options();
	$options_name 		= default_options_array();
	version_control();

	/* global var for DMSGuestbook and option database */
	global $wpdb;
	global $userdata;

	$table_name = $wpdb->prefix . "dmsguestbook";
	$table_option = $wpdb->prefix . "options";
	update_db_fields();
?>

	<!-- color picker -->
	<script language="javascript">
	var which_input;
	// The function name fillColorValue must be used
	// to get the color value. This function takes a string
	// as the color value in a hexidacimal format (e.g. FFFFFF)
	// the function code can be anything the application need.
	function fillColorValue(color){
		document.getElementById(which_input).value = color;
		document.getElementById(which_input+"_div").style.backgroundColor = "#"+color;
	}

	function show_picker(ID, Current_Color, Previous_Color){
		which_input = ID;
		var lnk = "../wp-content/plugins/dmsguestbook/js/color_picker/color_picker_files/color_picker_interface.html\
		?cur_color="+Current_Color+"&pre_color="+Previous_Color;
		window.open(lnk, "", "width=465, height=350");
	}
	</script>


<?php
/* only valid for administrator role (10) */
/* Administrator = 10, Editor = 7*/
//get_currentuserinfo();
if($userdata->user_level==7) {
echo "<br /><br /><div class='wrap'>Edit guestbook entries under \"Manage\"<br />";
echo "If you want change guestbook settings, ask your site administrator for permission.</div>";
}

if($userdata->user_level==10) {

?>

	<!-- header -->
	<div class="wrap">
    <h2>DMSGuestbook Option</h2>
    <ul>
    <li>1.) Create a page where you want to display the DMSGuestbook.</li>
    <li>2.) Save the page and set the page id number value in the red "Page ID" field under "Guestbook settings" -> "Basic" .</li>
    <li>3.) Customize the guestbook to your desire!</li>
    </ul>
	<br />

<?php

	/* if option(s) are missing */
	if(strlen($_SESSION["missing_options"])>0) {
		$_SESSION[fixed_update] = get_option("DMSGuestbook_options") . $_SESSION["missing_options_fixed_update"];
			echo "<b style='width:100%;color:#cc0000;'>One or more options are missing.</b><br />";
			echo "<form name='form0' method='post' action='$location'>
  			<input name='action' value='fix_update' type='hidden' />
  			<input name='fixed' value='$fixed_update' type='hidden' />
  			<input style='font-weight:bold; margin:10px 0px; width:250px;' type='submit' value='Update options database' />
			</form>";
	missing_options();
	unset($_SESSION["missing_options"]);
	}

	/* save the fixed options */
	if($_REQUEST[action]=="fix_update") {
	$restore = str_replace("\r\n", "[br]", $_SESSION[fixed_update]);
	update_option("DMSGuestbook_options", $restore);
	message("<b>Update database...</b>", 300, 800);
	echo "<meta http-equiv='refresh' content='0; URL=$location'>";
	}



	/* user can create new DMSGuestbook database if these failed during the installation. */
    if($_REQUEST[action]=="createnew") {
		$sql = $wpdb->query("CREATE TABLE " . $table_name . " (
	  	id mediumint(9) NOT NULL AUTO_INCREMENT,
	  	name varchar(50) DEFAULT '' NOT NULL,
	  	email varchar(50) DEFAULT '' NOT NULL,
	  	gravatar varchar(32) DEFAULT '' NOT NULL,
	  	url varchar(50) DEFAULT '' NOT NULL,
	  	date int(10) NOT NULL,
	  	ip varchar(15) DEFAULT '' NOT NULL,
	  	message longtext NOT NULL,
	  	flag int(2) NOT NULL,
	  	UNIQUE KEY id (id)
	  	)" . mysql_real_escape_string($_REQUEST[collate]) . "");
	  	$abspath = str_replace("\\","/", ABSPATH);
	  	require_once($abspath . 'wp-admin/upgrade-functions.php');
	  	dbDelta($sql);
	  	message("<b>$table_name was created...</b>", 300, 800);
	}

	/* user can delete DMSGuestbook database after the confirmation */
	if($_REQUEST[action]=="delete" && $_REQUEST[delete]=="yes, i am sure") {
		$wpdb->query('DROP TABLE IF EXISTS ' . $table_name);
		$abspath = str_replace("\\","/", ABSPATH);
	  	require_once($abspath . 'wp-admin/upgrade-functions.php');
	  	message("<b>$table_name was deleted...</b>",300,800);
	}

	/* user can create DMSGuestbook option if the failed during the installation. */
	if($_REQUEST[action]=="createoption") {
		initialize_option();
	  	message("<b>DMSGuestbook options <br />were created...</b><br />Don't forget to set the page id.",260,800);
		echo "<meta http-equiv='refresh' content='0; URL=$location'>";
	}

	/* user can delete all DMSGuestbook_ entries in DMSGuestbook option after confirmation. */
    if($_REQUEST[action]=="deleteoption" && $_REQUEST[confirm_delete_option]=="delete") {
		$wpdb->query('DELETE FROM ' . $table_option . ' WHERE option_name LIKE "DMSGuestbook_%"');
	  	$abspath = str_replace("\\","/", ABSPATH);
	  	require_once($abspath . 'wp-admin/upgrade-functions.php');
	  	message("<b>All DMSGuestbook options were deleted...</b>",300,800);
	}
	?>





<!-- dbx nice & fancy menu box -->
<link rel='stylesheet' href='../wp-content/plugins/dmsguestbook/js/dbx/dbx.css' type='text/css' />
<script type="text/javascript">
//<![CDATA[
function addLoadEvent(func) {if ( typeof wpOnload!='function'){wpOnload=func;}else{ var 						oldonload=wpOnload;wpOnload=function(){oldonload();func();}}}
//]]>
</script>
<script type='text/javascript' src='../wp-content/plugins/dmsguestbook/js/dbx/dbx.js'></script>
<script type='text/javascript' src='../wp-content/plugins/dmsguestbook/js/dbx/dbx-key.js'></script>
<script type="text/javascript" src="../wp-content/plugins/dmsguestbook/js/tooltip/wz_tooltip.js"></script>

<?php
$collaps_dbs="<a href='admin.php?page=dmsguestbook&dbs=1'>
<img src='../wp-content/plugins/dmsguestbook/img/server.png'><b>Database settings</b></a>";
$collaps_basic="<a href='admin.php?page=dmsguestbook&basic=1'>
<img src='../wp-content/plugins/dmsguestbook/img/basic.png'><b>Guestbook settings</b></a>";
$collaps_advanced="<a href='admin.php?page=dmsguestbook&advanced=1'>
<img src='../wp-content/plugins/dmsguestbook/img/language.png'><b>Language settings</b></a>";
?>

<!-- table for DMSGuestbook and DMSGuestbook option environment-->
<table style="width:100%;">
		<tr>
			<td><?php echo $collaps_dbs;?></td>
			<td><?php echo $collaps_basic?></td>
			<td><?php echo $collaps_advanced?></td>
		</tr>
</table>
<br /><br /><br />

<?php
if($_REQUEST[dbs]==1)
{

/* dmsguestbook datatbase*/
		// search prefix_dmsguestbook
        $result = $wpdb->query("SHOW TABLES LIKE '$table_name'");
		if ($result > 0) {
			/* if prefix_dmsguestbook is exist */
			$return_dmsguestbook_database = "<b style='color:#00bb00;'>[Status OK] $table_name is exist.</b><br /><br />
  			Type \"yes, i am sure\" in this textfield if you want delete $table_name.<br />
  			<b>All guestbook data will be lost!</b><br />
  			<form name='form0' method='post' action='$location'>
  			<input type='text' name='delete' value='' /><br />
  			<input name='action' value='delete' type='hidden' />
  			<input style='font-weight:bold; margin:10px 0px; width:250px;' type='submit' value='delete $table_name' />
			</form>";
		} else {
		    /* if prefix_dmsguestbook isn't exist */
			$return_dmsguestbook_database = "<b style='color:#bb0000;padding:5px;'>$table_name isn't exist.</b><br /><br />
			<form name='form0' method='post' action='$location'>
				  <select name='collate'>
				  	<option value='DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'>utf8_unicode_ci</option>
					<option value='DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci'>utf8_general_ci</option>
					<option value=''>if you use mySQL 4.0.xx or lower</option>
				</select><br />
				<input name='action' value='createnew' type='hidden' />
				<input style='font-weight:bold; margin:10px 0px; width:300px;' type='submit' value='create $table_name' />
			</form>
			If you want use char like &auml;,&uuml;,&ouml;... and your mysql version is lower than 4.1, be sure the language
			setting is e.g. \"de-iso-8859-1\" or similar. Check this with your mysql graphical frontenend like phpmyadmin.<br />";
		}

	$return_dmsguestbook_database_error = "<br /><a href='../wp-content/plugins/dmsguestbook/default_sql.txt' target='_blank'>Is something wrong with my $table_name table?</a>";


/* dmsguestbook options*/
	/* search all DMSGuestbook option (inform the user about the old dmsguestbook entries) */
	$query_options = $wpdb->get_results("SELECT * FROM $table_option WHERE option_name LIKE 'DMSGuestbook_%'");
	//$num_rows_option = mysql_affected_rows();
	$num_rows_option = $wpdb->num_rows;

	/* search to DMSGuestbook_options */
	$query_options1 = $wpdb->get_results("SELECT * FROM $table_option WHERE option_name LIKE 'DMSGuestbook_options'");
	$num_rows_option1 = $wpdb->num_rows;

		if($num_rows_option1==1) {
		$return_dmsguestbook_options = "<b style='color:#00bb00'>[Status OK] \"DMSGuestbook_options\" found in $table_option.</b><br />";
		}

		if($num_rows_option1==0) {
		$return_dmsguestbook_options = "<b style='color:#bb0000'>No \"DMSGuestbook_options\" found in $table_option.</b><br />";
		}

		if($num_rows_option >= 2) {
		$return_dmsguestbook_options = "<b style='color:#bb0000'>Notice: You have some old \"DMSGuestbook_xxxx\" rows in your $table_option, but this have no functionality impact.</b>";
		}

		$return_dmsguestbook_options .= "<form name='form0' method='post' action='$location'
			<input name='action' value='createoption' type='hidden' />
			<input style='font-weight:bold; margin:10px 0px; width:400px;' type='submit' value='Create new DMSGuestbook options' />
		</form>
		<br /><br />
		<form name='form0' method='post' action='$location'>
				Type \"delete\" to remove all DMSGuestbook option entries from the $table_option table.<br />
				<input type='text' name='confirm_delete_option' value='' /><br />
				<input name='action' value='deleteoption' type='hidden' />
				<input style='font-weight:bold; margin:10px 0px; width:400px;' type='submit' value='Delete DMSGuestbook options fom the database' />
			</form>
	<br /><a href='../wp-content/plugins/dmsguestbook/default_options.txt' target='_blank'>Is something wrong with my DMSGuestbook_options in $table_option?</a>";

/* backup */
		$return_dmsguestbook_options_backup = "<a href='$location?backup_options'>[backup DMSGuestbook_options]</a>
		<br />
		<br />
		Restore DMSGuestbook_options:<br />
		Open a DMSGuestbook_options_DATE.txt file, copy the whole content and put these to the textfield below.<br />
		All data will be overwrite.
		<form action='$location' method='post'>
		<textarea style='width:450px; height:200px;' name='restore_data'></textarea><br />
		<input type='hidden' name='restore_options' value='1' />
		<input type='submit' value='restore' onclick=\"return confirm('Would you really to restore all data?');\" />
		</form>";



echo "<b style='font-size:20px;'>Database settings</b><br />";
echo "<table width='100%' border='0'>";
echo "<tr><td>";

echo "<div id='outer'>
		<div class='dbx-group' id='dmsguestbook'>";

	echo "<div class='dbx-box'>
				<div class='dbx-handle' title='DMSGuestbook Database'><b style='color:#eee;'>DMSGuestbook Database</b></div>
				<ul class='dbx-content'>
				<li>&nbsp;</li>
				<li>$return_dmsguestbook_database</li>
				<li>$return_dmsguestbook_database_error</li>
				</ul>
			</div>";

	echo "<div class='dbx-box'>
				<div class='dbx-handle' title='DMSGuestbook options'><b style='color:#eee;'>DMSGuestbook options</b></div>
				<ul class='dbx-content'>
				<li>&nbsp;</li>
				<li>$return_dmsguestbook_options</li>
				</ul>
			</div>";

	echo "<div class='dbx-box'>
				<div class='dbx-handle' title='DMSGuestbook options backup'><b style='color:#eee;'>DMSGuestbook options backup</b></div>
				<ul class='dbx-content'>
				<li>&nbsp;</li>
				<li>$return_dmsguestbook_options_backup<li>
				<li>&nbsp;<li>
				</ul>
			</div>";

echo   "</div>
	</div>";
echo "</td></tr></table>";

?>
<script type="text/javascript">if(typeof wpOnload=='function')wpOnload();</script>
<?php
}
?>
<!-- end table for DMSGuestbook and DMSGuestbook option environment -->

<!-- main table with all DMSGuestbook options -->
<?php
		$submitbutton = "<input style='font-weight:bold;margin:10px 0px; width:300px;'
		type='submit' value='Save' name='csssave' onclick=\"document.getElementById('save')\" />";

	if($num_rows_option==$dmsguestbook_options)
	{
if($_REQUEST[basic]==1)
{
reset($options);
while (list($key, $val) = each($options)) {

	if($key == "page_id") {
		$label = "Page ID:";
		$entries = 0;
		$value = $options["page_id"];
		$char_lenght = "";
		$additional = "";
		$style = "width:50px;background-color:#cc0000;";
		$tooltip = "Put the guestbook page id number here<br /><br />If you have Wordpress 2.3 or lower:<br /><img src=\'../wp-content/plugins/dmsguestbook/img/wp2-x_id.png\' /><br />Goto \'Manage -> Pages\' and insert your guestbook ID on \'Page ID\'<br /><br /><br />If you have Wordpress 2.5:<br /><img src=\'../wp-content/plugins/dmsguestbook/img/wp2-5_id.png\' /><br /><br />Goto \'Manage -> Pages\' and hover the mouse over your guestbook page. The ID will be shown on the bottom of page.<br /><br />";
		$return_page_id = OneInput($key, $label, $entries, $value, $char_lenght, $additional, $style, $tooltip);
	}

	if($key == "step") {
		$label = "Post per page:";
		$option = "1@3@5@10@15@20@25@30@35@40@45@50@60@70@80@90@100@";
		$value = $options["step"];
		$additional = "";
		$style = "";
		$tooltip = "Number of entry in each page";
  		$return_step = SelectBox($key, $label, $option, $value, $additional, $style, $tooltip);
	}

	if($key == "width1") {
		$label = "Guestbook width:";
		$entries = 0;
		$value = $options["width1"];
		$char_lenght = "";
		$additional = "%";
		$style = "width:50px;";
		$tooltip = "Guestbook width in percent<br /><br />Variable: {width1}";
		$return_width1 = OneInput($key, $label, $entries, $value, $char_lenght, $additional, $style, $tooltip);
	}

	if($key == "width2") {
		$label = "Separator width:";
		$entries = 0;
		$value = $options["width2"];
		$char_lenght = "";
		$additional = "%";
		$style = "width:50px;";
		$tooltip = "Separator width in percent<br /><br />Variable: {width2}";
		$return_width2 = OneInput($key, $label, $entries, $value, $char_lenght, $additional, $style, $tooltip);
	}

	if($key == "position1") {
		$label = "Guestbook position (x-axis):";
		$entries = 0;
		$value = $options["position1"];
		$char_lenght = "";
		$additional = "px";
		$style = "width:50px;";
		$tooltip = "Absolute guestbook position in pixel horizontal (x-axis)<br /><br />Variable: {position1}";
		$return_position1 = OneInput($key, $label, $entries, $value, $char_lenght, $additional, $style, $tooltip);
	}

	if($key == "position2") {
		$label = "Guestbook position (y-axis):";
		$entries = 0;
		$value = $options["position2"];
		$char_lenght = "";
		$additional = "px";
		$style = "width:50px;";
		$tooltip = "Absolute guestbook position in pixel vertical (y-axis)<br /><br />Variable: {position2}";
		$return_position2 = OneInput($key, $label, $entries, $value, $char_lenght, $additional, $style, $tooltip);
	}

	if($key == "forwardchar") {
		$tooltip ="Navigation char style<br /><br />E.g. < >";
		$showtooltip="<b style='font-weight:normal;background-color:#bb1100;color:#fff;padding:3px;' onmouseover=\"Tip('$tooltip')\" onclick=\"UnTip()\">?</b>";
		$return_forwardchar = "<li><table style='width:95%;' border='0'><colgroup><col width='40%'><col width='55%'><col width='5%'><colgroup><tr><td>Navigation char style:</td>
		<td><input style='width:50px;' type='text' name='backwardchar' value='$options[backwardchar]' />
		<input style='width:50px;' type='text' name='forwardchar' value='$options[forwardchar]' /></td>
		<td style='text-align:right;'>$showtooltip</td></tr></table></li>";
	}

	if($key == "navigationsize") {
		$label = "Navigation char size:";
		$entries = 0;
		$value = $options["navigationsize"];
		$char_lenght = "";
		$additional = "px";
		$style = "width:50px;";
		$tooltip = "Navigation font size in pixel<br /><br />Variable: {navigationsize}";
		$return_navigationsize = OneInput($key, $label, $entries, $value, $char_lenght, $additional, $style, $tooltip);
	}

	if($key == "language") {
		unset($tmp);
		$abspath = str_replace("\\","/", ABSPATH);
				if ($handle = opendir($abspath . 'wp-content/plugins/dmsguestbook/language/')) {
    				while (false !== ($file = readdir($handle))) {
        				if ($file != "." && $file != ".." && $file != "README.txt") {
           				$tmp .= "$file" . "@";
        				}
    				}
    			closedir($handle);
				}
		$label = "Language:";
		$option = $tmp;
		$value = $options["language"];
		$additional = "";
		$style = "";
		$tooltip = "Edit languages under \'Language settings\'";
  		$return_language = SelectBox($key, $label, $option, $value, $additional, $style, $tooltip);
	}

	if($key == "formpos") {
		$label = "Guestbook form position:";
		$option = "top@bottom@";
		$value = $options["formpos"];
		$additional = "";
		$style = "";
		$tooltip = "Visible the guestbook input form on top or bottom";
  		$return_formpos = SelectBox($key, $label, $option, $value, $additional, $style, $tooltip);
	}

	if($key == "formposlink") {
		$label = "Link text:";
		$entries = 0;
		$value = $options["formposlink"];
		$char_lenght = "";
		$additional = "";
		$style = "width:150px;";
		$tooltip = "Define a link text if you selected \'bottom\'";
		$return_formposlink = OneInput($key, $label, $entries, $value, $char_lenght, $additional, $style, $tooltip);
	}

	if($key == "sortitem") {
		$label = "Sort guestbook items:";
		$option = "ASC@DESC@";
		$value = $options["sortitem"];
		$additional = "";
		$style = "";
  		$tooltip = "DESC = Newer post first<br />ASC = Older post first";
  		$return_sortitem = SelectBox($key, $label, $option, $value, $additional, $style, $tooltip);
	}

  	if($key == "dbid") {
  		$label = "Database id:";
		$entries = "0";
		$value = $options["dbid"];
		$additional = "";
		$style = "";
		$tooltip = "Use the database id to consecutively numbered each guestbook entry";
  		$return_dbid = CheckBoxes($key, $label, $value, $entries, $additional, $style, $tooltip);
	}

	if($key == "form_template") {
		unset($tmp);
		$abspath = str_replace("\\","/", ABSPATH);
				if ($handle = opendir($abspath . 'wp-content/plugins/dmsguestbook/template/form/')) {
    				while (false !== ($file = readdir($handle))) {
        				if ($file != "." && $file != "..") {
           				$tmp .= "$file" . "@";
        				}
    				}
    			closedir($handle);
				}
		$label = "Form template:";
		$option = $tmp;
		$value = $options["form_template"];
		$additional = "";
		$style = "";
		$tooltip = "Create your own input form template and use is it on your guestbook site<br /><br />See an examle in /template/form/default.tpl";
  		$return_form_template = SelectBox($key, $label, $option, $value, $additional, $style, $tooltip);
	}

	if($key == "post_template") {
		unset($tmp);
		$abspath = str_replace("\\","/", ABSPATH);
				if ($handle = opendir($abspath . 'wp-content/plugins/dmsguestbook/template/post/')) {
    				while (false !== ($file = readdir($handle))) {
        				if ($file != "." && $file != "..") {
           				$tmp .= "$file" . "@";
        				}
    				}
    			closedir($handle);
				}
		$label = "Post template:";
		$option = $tmp;
		$value = $options["post_template"];
		$additional = "";
		$style = "";
		$tooltip = "Create your own guestbook post template and use is it on your guestbook site<br /><br />See an examle in /template/post/default.tpl";
  		$return_post_template = SelectBox($key, $label, $option, $value, $additional, $style, $tooltip);
	}

	if($key == "nofollow") {
  		$label = "rel=\"nofollow\" tag for posted url's:";
		$entries = "0";
		$value = $options["nofollow"];
		$additional = "";
		$style = "";
		$tooltip = "Activate the nofollow tag for posted url\'s<br /><a href=\'http://en.wikipedia.org/wiki/Nofollow\' target=\'_blank\'>http://en.wikipedia.org/wiki/Nofollow</a>";
  		$return_nofollow = CheckBoxes($key, $label, $value, $entries, $additional, $style, $tooltip);
	}

	if($key == "separatorcolor") {
		$label = "Separator color:";
		$value = $options["separatorcolor"];
		$char_lenght = 6;
		$id = 1;
		$additional = "";
		$style = "width:150px;";
		$tooltip = "Separator between header and body in each entry<br /><br />Variable: {separatorcolor}";
		$return_separatorcolor = ColorInput($key, $label, $id, $value, $additional, $style, $tooltip);
	}

	if($key == "bordercolor1") {
		$label = "Outside border color:";
		$value = $options["bordercolor1"];
		$char_lenght = 6;
		$id = 2;
		$additional = "";
		$style = "width:150px;";
		$tooltip = "Color of the outside box<br /><br />Variable: {bordercolor1}";
		$return_bordercolor1 = ColorInput($key, $label, $id, $value, $additional, $style, $tooltip);
	}

	if($key == "bordercolor2") {
		$label = "Textfield border color:";
		$value = $options["bordercolor2"];
		$char_lenght = 6;
		$id = 3;
		$additional = "";
		$style = "width:150px;";
		$tooltip = "Color of all textfield borders<br /><br />Variable: {bordercolor2}";
		$return_bordercolor2 = ColorInput($key, $label, $id, $value, $additional, $style, $tooltip);
	}

	if($key == "navigationcolor") {
		$label = "Navigation char color:";
		$value = $options["navigationcolor"];
		$char_lenght = 6;
		$id = 4;
		$additional = "";
		$style = "width:150px;";
		$tooltip = "Define the navigation color<br /><br />Variable: {navigationcolor}";
		$return_navigationcolor = ColorInput($key, $label, $id, $value, $additional, $style, $tooltip);
	}

	if($key == "fontcolor1") {
		$label = "Font color:";
		$value = $options["fontcolor1"];
		$char_lenght = 6;
		$id = 5;
		$additional = "";
		$style = "width:150px;";
		$tooltip = "Overall font color<br /><br />Variable: {fontcolor1}";
		$return_fontcolor1 = ColorInput($key, $label, $id, $value, $additional, $style, $tooltip);
	}

	if($key == "captcha_color") {
		$label = "Antispam image text color:";
		$value = $options["captcha_color"];
		$char_lenght = 6;
		$id = 6;
		$additional = "";
		$style = "width:150px;";
		$tooltip = "Antispam image text color<br /><br />Variable: {captcha_color}";
		$return_captcha_color = ColorInput($key, $label, $id, $value, $additional, $style, $tooltip);
	}

	if($key == "dateformat") {
		$label = "Date / Time format:";
		$entries = 0;
		$value = $options["dateformat"];
		$char_lenght = "";
		$additional = "";
		$style = "width:200px;";
		$tooltip = "Set the date and time format<br />More infos: <a href=\'http://www.php.net/manual/en/function.strftime.php\' target=\'_blank\'>http://www.php.net/manual/en/function.strftime.php</a>";
		$return_dateformat = OneInput($key, $label, $entries, $value, $char_lenght, $additional, $style, $tooltip);
	}

	if($key == "setlocale") {
		$label = "Setlocale:";
		$entries = 0;
		$value = $options["setlocale"];
		$char_lenght = "";
		$additional = "";
		$style = "width:50px;";
		$tooltip = "Set your language: e.g. en_EN, de_DE, fr_FR, it_IT, de, ge ...<br />(must be installed on your system)";
		$return_setlocale = OneInput($key, $label, $entries, $value, $char_lenght, $additional, $style, $tooltip);
	}

	if($key == "offset") {
		$label = "Offset:";
		$option = "-12@-11@-10@-9@-8@-7@-6@-5@-4@-3@-2@-1@0@+1@+2@+3@+4@+5@+6@+7@+8@+9@+10@+11@+12@";
		$value = $options["offset"];
		$additional = "";
		$style = "";
		$tooltip = "Time offset: Use this offset if you Wordpress installation is not in the same country where you live.<br />e.g: You live in London and the Wordpress installation is on a server in Chicago.<br />You want to show the date in GMT (Greenwich Mean Time), set the offset -6 and check the correct time below.<br /><br /> Notice: don\'t use the %z or %Z parameter if you offset is not 0";
  		$return_offset = SelectBox($key, $label, $option, $value, $additional, $style, $tooltip);
	}

	if($key == "send_mail") {
  		$label = "Send a mail:";
		$entries = "0";
		$value = $options["send_mail"];
		$additional = "";
		$style = "";
		$tooltip = "Receive a notification email when user write an new guestbook post";
  		$return_send_mail = CheckBoxes($key, $label, $value, $entries, $additional, $style, $tooltip);
	}

	if($key == "mail_adress") {
		$label = "Email adress:";
		$entries = 0;
		$value = $options["mail_adress"];
		$char_lenght = "";
		$additional = "";
		$style = "width:150px;";
		$tooltip = "The email address where the message to be sent is";
		$return_mail_adress = OneInput($key, $label, $entries, $value, $char_lenght, $additional, $style, $tooltip);
	}

	if($key == "require_antispam") {
			if (ImageTypes() & IMG_PNG) {
    		$pngsupport = "[PNG support is available]"; }
			$array = gd_info();
	 		$requirement1 ="Requirement: GD 2.0.1 or above -> " . $array["GD Version"];
	 		if($array["FreeType Support"]==1) {
	 		$requirement2 = "[FreeType support enabled]";}

  		$label = "Antispam off:@Antispam image:@Antispam mathematic figures:@";
		$entries = "2";
		$value = $options["require_antispam"];
		$additional = "";
		$style = "";
		$tooltip = "Image:<br /><img src=\'../wp-content/plugins/dmsguestbook/captcha/captcha.php\' /><br />If you don\'t see the image here, check the xfiles.ttf and captcha.png permission in your captcha folder<br /><br />$pngsupport<br />$requirement1<br />Requirement: FreeType support -> $requirement2<br /><br />Mathematic figures:<br />4 + 9 = <input style=\'width:15px;\' type=\'text\' name=\'\' value=\'13\' />";
  		$return_require_antispam = RadioBoxes($key, $label, $value, $entries, $additional, $style, $tooltip);
	}

	if($key == "antispam_key") {
		$label = "Antispam key:";
		$entries = 0;
		$value = $options["antispam_key"];
		$char_lenght = 20;
		$additional = "";
		$style = "width:180px;";
		$tooltip = "Set a random key to prevent spam.<br />If your key is shorter than 20 characters, the systen will be create a new one.";
		$return_antispam_key = OneInput($key, $label, $entries, $value, $char_lenght, $additional, $style, $tooltip);
	}

	if($key == "require_email") {
  		$label = "Email:";
		$entries = "0";
		$value = $options["require_email"];
		$additional = "";
		$style = "";
  		$tooltip = "User must fill out the email text field";
  		$return_require_email = CheckBoxes($key, $label, $value, $entries, $additional, $style, $tooltip);
	}

	if($key == "require_url") {
  		$label = "Website:";
		$entries = "0";
		$value = $options["require_url"];
		$additional = "";
		$style = "";
		$tooltip = "User must fill out the website text field";
  		$return_require_url = CheckBoxes($key, $label, $value, $entries, $additional, $style, $tooltip);
	}

	if($key == "mandatory_char") {
		$label = "Mandatory char:";
		$entries = 0;
		$value = $options["mandatory_char"];
		$char_lenght = 1;
		$additional = "";
		$style = "width:20px;";
		$tooltip = "Mandatory char were to display on guestbook input form";
		$return_mandatory_char = OneInput($key, $label, $entries, $value, $char_lenght, $additional, $style, $tooltip);
	}

	if($key == "show_email") {
  		$label = "Show email:";
		$entries = "0";
		$value = $options["show_email"];
		$additional = "";
		$style = "";
		$tooltip = "Visible email for everyone in each post";
  		$return_show_email = CheckBoxes($key, $label, $value, $entries, $additional, $style, $tooltip);
	}

	if($key == "show_url") {
  		$label = "Show website:";
		$entries = "0";
		$value = $options["show_url"];
		$additional = "";
		$style = "";
		$tooltip = "Visible website for everyone in each post";
  		$return_show_url = CheckBoxes($key, $label, $value, $entries, $additional, $style, $tooltip);
	}

	if($key == "show_ip") {
  		$label = "Show ip adress:";
		$entries = "0";
		$value = $options["show_ip"];
		$additional = "";
		$style = "";
		$tooltip = "Visible ip for everyone in each post";
  		$return_show_ip = CheckBoxes($key, $label, $value, $entries, $additional, $style, $tooltip);
	}

	if($key == "ip_mask") {
		$label = "Mask ip adress:";
		$option = "*.123.123.123@*.*.123.123@*.*.*.123@123.123.123*@123.123.*.*@123.*.*.*@";
		$value = $options["ip_mask"];
		$additional = "";
		$style = "";
  		$tooltip = "Mask ip adress if this is visible";
  		$return_ip_mask = SelectBox($key, $label, $option, $value, $additional, $style, $tooltip);
	}

	if($key == "email_image_path") {
			$part1=explode("/", $options["email_image_path"]);
			$image=end($part1);
		$label = "Email image path:";
		$entries = 0;
		$value = $options["email_image_path"];
		$char_lenght = "";
		$additional = "";
		$style = "width:200px;";
		$tooltip = "Email image path:<br /><a href=\'$options[email_image_path]\' target=\'_blank\'>$options[email_image_path]</a><br /><br />Actually image: <img src=\'$options[email_image_path]\'>";
		$return_email_image_path = OneInput($key, $label, $entries, $value, $char_lenght, $additional, $style, $tooltip);
	}

	if($key == "website_image_path") {
			$part1=explode("/", $options["website_image_path"]);
			$image=end($part1);
		$label = "Website image path:";
		$entries = 0;
		$value = $options["website_image_path"];
		$char_lenght = "";
		$additional = "";
		$style = "width:200px;";
		$tooltip = "Website image path:<br /><a href=\'$options[website_image_path]\' target=\'_blank\'>$options[website_image_path]</a><br /><br />Actually image: <img src=\'$options[website_image_path]\'>";
		$return_website_image_path = OneInput($key, $label, $entries, $value, $char_lenght, $additional, $style, $tooltip);
	}

	if($key == "admin_review") {
  		$label = "Admin must every post review:";
		$entries = "0";
		$value = $options["admin_review"];
		$additional = "";
		$style = "";
		$tooltip = "Admin must review every post before this can display on the page.<br />You can edit the guestbook review status under \'Manage\'";
  		$return_admin_review = CheckBoxes($key, $label, $value, $entries, $additional, $style, $tooltip);
	}

	if($key == "url_overruled") {
		$label = "URL overrule:";
		$entries = 0;
		$value = $options["url_overruled"];
		$additional = "";
		$style = "width:200px;";
		$tooltip = "You can overrule this link if you have trouble with the guestbook form submit.<br /><br />Examples:<br />$url/?p=$options[page_id]<br />$url/$options[page_id]/<br />$url/YourGuestBookName";
		$return_url_overruled = OneInput($key, $label, $entries, $value, $char_lenght, $additional, $style, $tooltip);
	}

	if($key == "gravatar") {
  		$label = "User can use Gravatar:";
		$entries = "0";
		$value = $options["gravatar"];
		$additional = "";
		$style = "";
		$tooltip = "More infos: <a href=\'http://en.gravatar.com\' target=\'_blank\'>http://en.gravatar.com</a>";
  		$return_gravatar = CheckBoxes($key, $label, $value, $entries, $additional, $style, $tooltip);
	}

	if($key == "gravatar_rating") {
		$label = "Gravatar rating:";
		$option = "G@PG@R@X@";
		$value = $options["gravatar_rating"];
		$additional = "";
		$style = "";
  		$tooltip = "You can specify a rating of G, PG, R, or X.<br />[G] A G rated gravatar is suitable for display on all websites with any audience type.<br />[PG] PG rated gravatars may contain rude gestures, provocatively dressed individuals, the lesser swear words, or mild violence.<br />[R] R rated gravatars may contain such things as harsh profanity, intense violence, nudity, or hard drug use.<br />[X] X rated gravatars may contain hardcore sexual imagery or extremely disturbing violence.";
  		$return_gravatar_rating = SelectBox($key, $label, $option, $value, $additional, $style, $tooltip);
	}

	if($key == "gravatar_size") {
		$label = "Gravatar size:";
		$entries = 0;
		$char_lenght = 3;
		$value = $options["gravatar_size"];
		$additional = "px";
		$style = "width:30px;";
		$tooltip = "Image size in pixel";
		$return_gravatar_size = OneInput($key, $label, $entries, $value, $char_lenght, $additional, $style, $tooltip);
	}

	if($key == "css") {
		$part1 = explode("@", $options["css"]);

		$options["css"] = str_replace("[br]", "\r\n", $options["css"]);
		$part1 = explode("@", $options["css"]);

		$part11 = explode("@", $_SESSION[csscontainer]);

		if(count($part11) > count($part1)) {
		$newone="<b style='color:#bb1100;'>NEW!</b><br />";
		echo "<b style='font-size:12px;color:#bb1100;'>
		--------------------------------------------------<br />";
		echo "New CSS entries found, press the save button to save it.<br />";
		echo "--------------------------------------------------</b><br /><br />";
		}

		if(count($part11) < count($part1)) {
		$restore_css = 1;
		echo "<b style='font-size:12px;color:#bb1100;'>
		--------------------------------------------------<br />";
		echo "Some CSS entries are missing!<br />Press the save button to restore CSS settings.<br />";
		echo "--------------------------------------------------</b><br /><br />";
		}

	   $tooltip = "{width1} = Guestbook width<br />{width2} = Separator width<br />{position1} = Relative guestbook position (left to right)<br />{separatorcolor} = Separator between header and body in each entry<br />{bordercolor1} = Border of the outside box<br />{bordercolor2} = Color of all textfield border<br />{navigationcolor} = Define the navigation color<br />{fontcolor1} = Overall font color<br />{navigationsize} = Size of both navigation chars<br />{captcha_color} = Antispam image text color<br /><br />Stylesheet (CSS) Help & Tutorials:<br />English: http://www.html.net/tutorials/css/<br />German: http://www.css4you.de/<br />Or ask Google and friends :-)";


			$return_css .= "<table border='0'><colgroup><col width='50'><col width='210'><col width='50'><colgroup>";
					$xx = 0;
					for($x=0; $x<count($part11)-1; $x++) {
    				$part2 = explode("|", $part1[$xx]);
    				$part22 = explode("|", $part11[$x]);

    					if(trim($part2[1]) == trim($part22[1])) {
    					$yxc[$x] = "<div style='font-size:0.9em;'>Description: $part2[0]<br />
    					CSS class: $part2[1]</div><input type='hidden' name='cssdescription$x' value='$part2[0]' />
    					<input type='hidden' name='cssname$x' value='$part2[1]' />";
						$xx++;
						}
						else
							{
								if($restore_css!=1) {
								$yxc[$x] = "<div style='font-size:0.9em;'>Description: $part22[0]<br />
    							CSS class: $part22[1]</div><input type='hidden' name='cssdescription$x' value='$part22[0]' />
    							<input type='hidden' name='cssname$x' value='$part22[1]' />";
								$xx -1;
								$t[]=$x;
								}
							}
					}

					$xx=0;
					for($x=0; $x<count($part11)-1; $x++) {
					$part2 = explode("|", $part1[$xx]);
					$part22 = explode("|", $part11[$x]);
					$y=$x+1;

			$showtooltip="<b style='font-weight:bold;background-color:#bb1100;color:#fff;padding:3px;' onmouseover=\"Tip('$tooltip')\" onclick=\"UnTip()\">?</b>";

			if(!$t) {$t[]=99999;}
			if(!in_array($x, $t) && $restore_css!=1) {
    		$return_css	.= "<tr><td style='background-color:#fff;text-align:center;'>($y)</td>
    		<td>$yxc[$x]<textarea name='css$x' cols='50' rows='5' >$part2[2]</textarea></td><td>$showtooltip</td></tr>";

						unset($css_submitbutton);
						if($x==4 OR $x==9 OR $x==14 OR $x==19 OR $x==24 OR $x==29) {
						$css_submitbutton = "<br /><br />" . $submitbutton . "<br /><br />";
						}

			$return_css .= "<tr><td></td><td>$css_submitbutton</td></tr>";
			$xx++;
			}
			else {
			     	if($restore_css!=1) {
				 	$return_css .= "<tr><td style='text-align:center;'><b style='color:#bb1100;'>NEW!</b><br />($y)</td>
    			 	<td>$yxc[$x]<textarea name='css$x' cols='50' rows='5' >$part22[2]</textarea></td><td>$showtooltip</td></tr>";

						unset($css_submitbutton);
						if($x==4 OR $x==9 OR $x==14 OR $x==19 OR $x==24 OR $x==29) {
						$css_submitbutton = "<br /><br />" . $submitbutton . "<br /><br />";
						}

				 	$return_css .= "<tr><td></td><td>$css_submitbutton</td></tr>";
			     	$xx-1;
			     	}
			     }


			if($restore_css==1) {
			$return_css .= "<input type='hidden' name='cssdescription$x' value='$part22[0]' />";
			$return_css .= "<input type='hidden' name='cssname$x' value='$part22[1]' />";
			$return_css .= "<input type='hidden' name='css$x' value='$part22[2]' />";
			}


					}
			$return_css .= "</table>";
	}

	if($key == "css_customize") {
			$options["css_customize"] = str_replace("[br]", "\r\n", $options["css_customize"]);

			$return_css_customize = "<table style='width:95%;' border='0'>";
    				$yxc = "<div style='font-size:0.9em;'>Custom CSS:</div>";

			$tooltip = "Class heredity:<br /><br />E.g.<br /><b>a.</b>css_navigation_char<b>:hover</b> {color:#ff0000;}<br />All url link with css_navigation_char (navigation link)<br />become hover color red when user drag over it<br /><br /><b>td</b>.css_guestbook_message_nr_name {background-color:#00ff00;}<br />All td with css_guestbook_message_nr_name (guestbook name & id)<br />become background color green<br /><br />";
			$showtooltip="<b style='font-weight:bold;background-color:#bb1100;color:#fff;padding:3px;' onmouseover=\"Tip('$tooltip')\" onclick=\"UnTip()\">?</b>";

    		$return_css_customize .= "<tr><td>$yxc <textarea name='css_customize' cols='55' rows='15' >$options[css_customize]</textarea></td><td>$showtooltip</td></tr>";
			$return_css_customize .= "</table>";
	}
}

echo "<b style='font-size:20px;'>Guestbook settings</b><br />";
echo "<table width='100%' border='0'>";
echo "<tr><td>";

echo "<form name='form1' method='post' action='$location'>";
echo $submitbutton;
echo "<div id='outer'>
		<div class='dbx-group' id='dmsguestbook'>";

	echo "<div class='dbx-box'>
				<div class='dbx-handle' title='Basic'><b style='color:#eee;'>Basic</b></div>
				<ul class='dbx-content'>
				<li>&nbsp;</li>
				<li>$return_page_id</li>
				<li>$return_step</li>
				<li>$return_language</li>
				<li>&nbsp;</li>
				<li>$return_formpos</li>
				<li>$return_formposlink</li>
				</ul>
			</div>";

	echo "<div class='dbx-box'>
				<div class='dbx-handle' title='Extended'><b style='color:#eee;'>Extended</b></div>
				<ul class='dbx-content'>
				<li>&nbsp;</li>
				<li>$return_position1</li>
				<li>$return_position2</li>
				<li>$return_width1</li>
				<li>$return_width2</li>
				<li>$return_forwardchar</li>
				<li>$return_navigationsize</li>
				<li>$return_show_email</li>
				<li>$return_show_url</li>
				<li>$return_show_ip</li>
				<li>$return_ip_mask</li>
				<li>&nbsp;</li>
				<li>$return_sortitem</li>
				<li>$return_dbid</li>
				<li>$return_form_template</li>
				<li>$return_post_template</li>
				<li>$return_nofollow</li>
				</ul>
			</div>";

	echo "<div class='dbx-box'>
				<div class='dbx-handle' title='Color'><b style='color:#eee;'>Color</b></div>
				<ul class='dbx-content'>
				<li>&nbsp;</li>
				<li>$return_bordercolor1</li>
				<li>$return_bordercolor2</li>
				<li>$return_navigationcolor</li>
				<li>$return_separatorcolor</li>
				<li>$return_fontcolor1</li>
				<li>$return_captcha_color</li>
				<li>&nbsp;</li>
				</ul>
			</div>";

			setlocale(LC_TIME, $options[20]);
			$offset = mktime(date("H")+$options[22], date("i"), date("s"), date("m")  , date("d"), date("Y"));
     		$time_example = htmlentities(strftime($options[20], $offset), ENT_QUOTES);

			echo "<div class='dbx-box'>
				<div class='dbx-handle' title='Time / Date'><b style='color:#eee;'>Time / Date</b></div>
				<ul class='dbx-content'>
				<li>&nbsp;</li>
				<li>$return_dateformat</li>
				<li>$return_setlocale</li>
				<li>$return_offset</li>
				<li>$time_example</li>
				<li>&nbsp;</li>
				</ul>
			</div>";

			echo "<div class='dbx-box'>
				<div class='dbx-handle' title='Email Notification'><b style='color:#eee;'>Email Notification</b></div>
				<ul class='dbx-content'>
				<li>&nbsp;</li>
				<li>$return_send_mail</li>
				<li>$return_mail_adress</li>
				<li>&nbsp;</li>
				</ul>
			</div>";

			echo "<div class='dbx-box'>
				<div class='dbx-handle' title='Antispam / Captcha'><b style='color:#eee;'>Antispam / Captcha</b></div>
				<ul class='dbx-content'>
				<li>&nbsp;</li>
				<li>$return_require_antispam</li>
				<li>$return_antispam_key</li>
				<li>&nbsp;</li>
				</ul>
			</div>";

			/* antispam key random key */
			$random_key = RandomAntispamKey();
			echo "<script type=\"text/javascript\">
				if(document.form1.antispam_key.value.length < 20)
				{
				document.form1.antispam_key.value = \"$random_key\"
				}
			</script>";

			echo "<div class='dbx-box'>
				<div class='dbx-handle' title='Mandatory'><b style='color:#eee;'>Mandatory</b></div>
				<ul class='dbx-content'>
				<li>&nbsp;</li>
				<li><table style='width:95%;' border='0'><colgroup><col width='40%'><col width='55%'><col width='5%'><colgroup><tr><td>Name:</td>
				<td><input type='checkbox' checked disabled></td><td style='text-align:right;'><b style='font-weight:bold;background-color:#bb1100;color:#fff;padding:3px;' onmouseover=\"Tip('User must fill out name text field')\" onclick=\"UnTip()\">?</b></td></tr></li>
				<li><table style='width:95%;' border='0'><colgroup><col width='40%'><col width='55%'><col width='5%'><colgroup><tr><td>Message:</td>
				<td><input type='checkbox' checked disabled></td><td style='text-align:right;'><b style='font-weight:bold;background-color:#bb1100;color:#fff;padding:3px;' onmouseover=\"Tip('User must fill out message text field')\" onclick=\"UnTip()\">?</b></td></tr></li>
				<li>$return_require_email</li>
				<li>$return_require_url</li>
				<li>$return_mandatory_char</li>
				<li>&nbsp;</li>
				</ul>
			</div>";

			echo "<div class='dbx-box'>
				<div class='dbx-handle' title='Gravatar'><b style='color:#eee;'>Gravatar</b></div>
				<ul class='dbx-content'>
				<li>&nbsp;</li>
				<li>$return_gravatar</li>
				<li>$return_gravatar_rating</li>
				<li>$return_gravatar_size</li>
				<li>&nbsp;</li>
				</ul>
			</div>";

			echo "<div class='dbx-box'>
				<div class='dbx-handle' title='Miscellaneous'><b style='color:#eee;'>Miscellaneous</b></div>
				<ul class='dbx-content'>
				<li>&nbsp;</li>
				<li>$return_email_image_path</li>
				<li>$return_website_image_path</li>
				<li>$return_admin_review</li>
				<li>$return_url_overruled</li>
				<li>&nbsp;</li>
				</ul>
			</div>";

			$abspath = str_replace("\\","/", ABSPATH);
			if(is_writable($abspath . "wp-content/plugins/dmsguestbook/dmsguestbook.css")) {
			$css_notice = "<b>dmsguestbook.css</b><i style='color:#00bb00;font-style:normal;'> is writable.</i>";
			} else  {
					$css_notice = "<b>dmsguestbook.css</b><i style='color:#bb0000;font-style:normal;'> is not writable or doesn't exist.</i>";
					}

			echo "<div class='dbx-box'>
				<div class='dbx-handle' title='CSS'><b style='color:#eee;'>CSS</b></div>
				<ul class='dbx-content'>
				<li>&nbsp;</li>
				<li>If dmsguestbook.css is exist or writable, all CSS settings will be read from it.<br />
				Otherwise these settings will be load from the database.<br />
				$css_notice<br /><br /></li>
				<li>$return_css</li>
				<li>$return_css_customize</li>
				<li>
				<b>Settings for custom CSS</b><br />
				<br />
				<i>Mouse hover color</i><br />
				a.css_navigation_char:hover {text-decoration:none; color:#{navigationcolor};}<br />
				a.css_navigation_select:hover {text-decoration:none; color:#bb1100;}<br />
				a.css_navigation_notselect:hover {text-decoration:none; color:#000000;}<br />
				<br />
				<i>Posting email and url image properties</i><br />
				img.css_post_url_image {border:0px;}<br />
				img.css_post_email_image {border:0px;}<br />
				</li>
				<li>&nbsp;</li>
				</ul>
			</div>";

echo   "</div>
	</div>";
echo "</td></tr></table>";


echo "<table border='0'><colgroup><col width='100%' span='2'></colgroup><tr>";
echo "<td><input id='save' name='action' value='insert' type='hidden' />";
echo "<input style='font-weight:bold; margin:10px 0px; width:300px;' type='submit' value='Save' />";
echo "</form></td>";

	 	#restore default settings button -->
		echo "<td><form name='form3' method='post' action='$location'>
		<input name='action2' value='default_settings' type='hidden' />
		<input style='font-weight:bold; margin:10px 0px;' type='submit'
		value='Restore default settings - All data will be replaced' onclick=\"return confirm('Would you really to restore all data?');\" />
     	</form></td>";

echo "</tr></table>";
?>
<script type="text/javascript">if(typeof wpOnload=='function')wpOnload();</script>
<?php
}
?>

<!-- language -->
<?php
if($_REQUEST[advanced]==1)
{
	clearstatcache();
	$color3=settablecolor(3,0);
	unset($buffer);
	echo "<b style='font-size:20px;'>Language settings</b><br />";
	$abspath = str_replace("\\","/", ABSPATH);

		if ($handle = opendir($abspath . 'wp-content/plugins/dmsguestbook/language/')) {
    		/* language */
    		while (false !== ($file = readdir($handle))) {
        		if ($file != "." && $file != "..") {
        			if($file=="README.txt") {
           			echo "<a style='color:#bb0000;' href='admin.php?page=dmsguestbook&advanced=1&folder=language/&file=$file'>$file</a>, ";
        			}
        			else 	{
        					echo "<a href='admin.php?page=dmsguestbook&advanced=1&folder=language/&file=$file'>$file</a>, ";
        					}
        		}
    		}
    		echo "<br />";
    		closedir($handle);
		}

if($_REQUEST[file]!="") {

	clearstatcache();

	/* check the file variable for language text file */
	if(preg_match('/^[a-z0-9]+\.+(txt)/i', "$_REQUEST[file]")==1) {
	$file=$_REQUEST[file];

	if(file_exists($abspath . "wp-content/plugins/dmsguestbook/language/" . $file)) {
	$folder="language/";
	$valid_file=1;
	$save_advanced_button = check_writable($folder, $file);
	} else
	  		{
	  		$valid_file=0;
	  		echo "<br /><b>File not found!</b>";
	  		}
	}


	/* error handling */
	if($file=="") {
	echo "<br /><b>Not a valid file, must be a language file with .txt prefix.</b>";
	}


		if ($valid_file==1) {
		$handle = @fopen ($abspath . "wp-content/plugins/dmsguestbook/" . $folder . $file, "r");
			while (!feof($handle)) {
    		$buffer .= fgets($handle, 4096);
			}
		fclose ($handle);
		}


}

	$showfiledata = htmlentities($buffer, ENT_QUOTES);

?>
	<br />
	<table style="border:0px solid #000000; width:100%;background-color:#<?php echo $color3; ?>;" cellspacing="0" cellpadding="0">
	  <tr>
		<form name="form0" method="post" action="<?php echo $location;?>">
		<td><textarea style="width:99%; height:500px;" name="advanced_data"><?php echo $showfiledata;?></textarea></td>
	  </tr>
		<input name="action3" value="save_advanced_data" type="hidden" />
	  	<input name="folder" value="<?php echo $folder; ?>" type="hidden" />
	  	<input name="file" value="<?php echo $file; ?>" type="hidden" />
	  <tr>
		<td style="text-align:center;"><?php echo $save_advanced_button;?></td>
  	  </tr>
  		</form>
	  </tr>
	 </table>
<?php
}
?>
	 </div>
<?php
	}
}	/* end of DMSGuestbook adminpage main function */

} /* end $userdata->user_level, only valid for administrator role (10) */


	/* check the old HTTP_POST_VARS and new $_POST var */
	if(!empty($HTTP_POST_VARS)) {
	$POSTVARIABLE   = $HTTP_POST_VARS;
	}
	else {
		 $POSTVARIABLE = $_POST;
		 }



	/* write DMSGuestbook option in wordpress options database */
	if ('insert' == $POSTVARIABLE['action'])
	{
	$url=get_bloginfo('wpurl');
	$options = create_options();
		$save_options = default_options_array();
		unset($save_to_db);
		unset($save_to_dmsguestbook_css);

		while (list($key, $val) = each($save_options)) {
			if($POSTVARIABLE[$key]==""){$POSTVARIABLE[$key]=0;}

				if($key=="css") {
					$part = explode("@", $_SESSION[csscontainer]);
					for($y=0; $y<count($part)-1; $y++) {
					$POSTVARIABLE["cssdescription$y"] = str_replace("@", "", $POSTVARIABLE["cssdescription$y"]);
					$POSTVARIABLE["cssdescription$y"] = str_replace("|", "", $POSTVARIABLE["cssdescription$y"]);
					$POSTVARIABLE["cssname$y"] = str_replace("@", "", $POSTVARIABLE["cssname$y"]);
					$POSTVARIABLE["cssname$y"] = str_replace("|", "", $POSTVARIABLE["cssname$y"]);
					$POSTVARIABLE["css$y"] = str_replace("@", "", $POSTVARIABLE["css$y"]);
					$POSTVARIABLE["css$y"] = str_replace("|", "", $POSTVARIABLE["css$y"]);
					$cssdata.= $POSTVARIABLE["cssdescription$y"] . "|" . $POSTVARIABLE["cssname$y"] . "|" . $POSTVARIABLE["css$y"] . "@";
					}


     				$cssdata = str_replace("\r\n", "[br]", $cssdata);
				$save_to_db.="<" . $key . ">" . htmlentities($cssdata, ENT_QUOTES) . "</" . $key . ">\r\n";
				}
				elseif($key=="css_customize") {
					$css_customize = str_replace("\r\n", "[br]", $POSTVARIABLE["css_customize"]);
					$save_to_db.="<" . $key . ">" . htmlentities($css_customize, ENT_QUOTES) . "</" . $key . ">\r\n";
				}
				else {
						if($key == "page_id") {
							$save_to_db.="<" . $key . ">" . sprintf("%d",$POSTVARIABLE[$key]) . "</" . $key . ">\r\n";
							}
						elseif ($key == "width1") {
							$save_to_db.="<" . $key . ">" . sprintf("%d",$POSTVARIABLE[$key]) . "</" . $key . ">\r\n";
							}
						elseif ($key == "width2") {
							$save_to_db.="<" . $key . ">" . sprintf("%d",$POSTVARIABLE[$key]) . "</" . $key . ">\r\n";
							}
						elseif ($key == "position1") {
							$save_to_db.="<" . $key . ">" . sprintf("%d",$POSTVARIABLE[$key]) . "</" . $key . ">\r\n";
							}
						elseif ($key == "position2") {
							$save_to_db.="<" . $key . ">" . sprintf("%d",$POSTVARIABLE[$key]) . "</" . $key . ">\r\n";
							}
						elseif ($key == "navigationsize") {
							$save_to_db.="<" . $key . ">" . sprintf("%d",$POSTVARIABLE[$key]) . "</" . $key . ">\r\n";
							}
						else {
				     		$save_to_db.="<" . $key . ">" . htmlentities($POSTVARIABLE[$key], ENT_QUOTES) . "</" . $key . ">\r\n";
				     		}
				     }
		}
		$save_to_db = str_replace("\"", "&amp;quot;", $save_to_db);
		update_option("DMSGuestbook_options", mysql_real_escape_string($save_to_db));
		message("<b>saved...</b>",300,800);

		/* save to dmsguestbook.css if is writable */
		$abspath = str_replace("\\","/", ABSPATH);
			if(is_writable($abspath . "wp-content/plugins/dmsguestbook/dmsguestbook.css")) {
				$notice ="/*\nUse the DMSGuestbook admin interface for change these css settings.\nDon't edit this file direct, your change could be overwrite by the DMSGuestbook admin.\nIf dmsguestbook.css is exist or writable, all CSS settings will be read from it.\nOtherwise these settings will be load from the database.\n*/\n\n";
				$csscode = make_css();
				$handle = fopen($abspath . "wp-content/plugins/dmsguestbook/dmsguestbook.css", "w");
				fwrite($handle, $notice . $csscode);
				fclose($handle);
			}
	}
	/* end of write DMSGuestbook option in wordpress options database */



	/* reset DMSGuestbook */
	if ('default_settings' == $POSTVARIABLE['action2']) {
	default_option();
	}


	/* save advanced */
	if ('save_advanced_data' == $POSTVARIABLE['action3']) {
	$abspath = str_replace("\\","/", ABSPATH);

	/* check the folder variable */
	if($POSTVARIABLE['folder']=="language/"){
	$folder="language/";
	} else {$folder="";}

	/* check the file variable xxxx.txt */
	if(preg_match('/^[a-z0-9]+\.+(txt)/i', $POSTVARIABLE['file'])==1) {
	$file=$POSTVARIABLE['file'];
	} else {$file="";}

		clearstatcache();
		if (file_exists($abspath . "wp-content/plugins/dmsguestbook/" . $folder . $file)) {
		$handle = fopen($abspath . "wp-content/plugins/dmsguestbook/" . $folder . $file, "w");

		$writetofile = str_replace("\\", "", $POSTVARIABLE['advanced_data']);

		fwrite($handle, $writetofile);
		fclose($handle);
		message("<b>saved...</b>",300,800);
		} else {message("<br /><b>File not found</b>",300,800);}
	}



/* manage guestbook entries */
function dmsguestbook2_meta_description_option_page() {
		version_control();
		$options=create_options();

?>
		<div class="wrap">
		<h2>Manage DMSGuestbook</h2>
		<ul>
	 		<li>You can edit all text fields</li>
	 		<li>You can use HTML tags in the name and message box. But, be care with this :-)<br />
	 		Message box: HTML tags must be embedded in [html] [/html] (e.g. [html]&lt;b&gt;text&lt;/b&gt;[/html])<br />
			Name field: Write HTML code direct in this field. (e.g. &lt;b&gt;name&lt;/b&gt;)<br />
			Don't forget: Close all HTML tags!</li>
			<li>If you edit the url field, don't delete the "http(s)://" prefix.</li>
		</ul>
		* If Admin review checkbox is activated, the post will not be shown on the guestbook page.
<?php
		/* maximum guestbook entries were displayed on page */
		$gb_step=$options["step"];

			/* if some option(s) data are missing*/
			if($options[9999]) {
			$gb_step="50";
			$options["sortitem"]="DESC";
			$options["dateformat"]="%a, %e %B %Y %H:%M:%S %z";
			$options["setlocale"]="en_EN";
			}

		/* initialize */
		if($_REQUEST[from]=="") {$_REQUEST[from]=0; $_REQUEST[select]=1;}

		/* global var for DMSGuestbook */
		global $wpdb;
		$table_name = $wpdb->prefix . "dmsguestbook";

		if($_REQUEST[search]!="") {
		$_REQUEST[search] = preg_replace("/[\<\>\"\'\`\´]+/i", "", $_REQUEST[search]);
		$search_param ="WHERE name LIKE '$_REQUEST[search]' OR email LIKE '$_REQUEST[search]' OR url LIKE '$_REQUEST[search]' OR ip LIKE '$_REQUEST[search]' OR message LIKE '$_REQUEST[search]'";
		}
		else
			{$search_param="";}

		/* count all search database entries / mysql_query */
    	$query0 = $wpdb->get_results("SELECT * FROM  $table_name $search_param");
    	$num_rows0 = $wpdb->num_rows;

		/* read all search guestbook entries */
		$query1 = $wpdb->get_results("SELECT * FROM $table_name $search_param ORDER BY id " . sprintf("%s", $options["sortitem"]) . " LIMIT
		" . sprintf("%d", $_REQUEST[from]) . "," . sprintf("%d", $gb_step) . ";");
		$num_rows1 = $wpdb->num_rows;

?>
		<br />
		<br />
		<table style="border:1px solid #000000; width:400px;">
		<tr>
		<td><form name="search" method="post" action="<?php echo $location ?>">
		<input style="width:250px;" type="text" name="search" value="<?php echo $_REQUEST[search]; ?>" />
		<input style="font-weight:bold;" type="submit" value="search" />
	 	</form></td>
	 	</tr>
	 	<tr>
	 	<td>Search in: Name, Message, IP, Website and Email Fields.<br />Use % to specify search patterns. E.g. %fox% or %fox or fox%</td>
	 	</tr>
	 	</table>


		<br /><br />
		<div style="width:<?php echo $options["width1"] . "%" ;?>; text-align:center;">
		<div style="font-size:11px;">(<?php echo $num_rows0;?>)</div>

<?php

		for($q=0; $q<$num_rows0; ($q=$q+$gb_step))
		{
		$y++;
			if($_REQUEST[select]==$y) {
			echo "<a style='color:#bb1100; text-decoration:none;' href='admin.php?page=Manage&from=$q&select=$y'> $y</a>";
			}
			else {
				 echo "<a style='color:#000000; text-decoration:none;' href='admin.php?page=Manage&from=$q&select=$y'> $y</a>";
				 }
		}
		echo "</div>
		<br /><br />";


		$color1=settablecolor(1,0);
		$color2=settablecolor(2,0);
		$color3=settablecolor(3,0);
		$tbc3="style='background-color:#$color3; text-align:center; height:35px;'";
?>
		<table style="border:1px solid #000000; width:100%;">
		<tr <?php echo $tbc3; ?>>
			<th style="padding:0px 5px 0px 5px;">ID</th>
			<th style="padding:0px 5px 0px 5px;">Admin review *</th>
	 		<th style="padding:0px 5px 0px 5px;">Name</th>
	 		<th style="padding:0px 5px 0px 5px;">Message</th>
	 		<th style="padding:0px 5px 0px 5px;">Header</th>
	 		<th style="padding:0px 5px 0px 5px;"></th>
	 		<th style="padding:0px 5px 0px 5px;"></th>
		</tr>

<?php
			setlocale(LC_TIME, $options["setlocale"]);
			$bgcolor=$color1;
			foreach ($query1 as $result) {
	    		if($bgcolor==$color2) {$bgcolor=$color1;} else {$bgcolor=$color2;}
				if($result->flag==1) {$bgcolor="F2E4E4";}

	 			// build the data / time variable
				$sec=date("s", "$result->date");
				$min=date("i", "$result->date");
				$hour=date("H", "$result->date");
				$day=date("d", "$result->date");
				$month=date("m", "$result->date");
				$year=date("Y", "$result->date");
				$date = strftime ($options["dateformat"], mktime ($hour, $min, $sec, $month, $day, $year));

				$gbname 	= preg_replace("/[\\\\\"=\(\)\{\}]+/i", "", stripslashes($result->name));
				$gbemail 	= preg_replace("/[^a-z-0-9-_\.@]+/i", "", $result->email);
				$gburl 		= preg_replace("/[^a-z-0-9-_,.:?&%=\/]+/i", "", $result->url);
				$gbip 		= preg_replace("/[^0-9\.]+/i", "", $result->ip);
				$gbmsg 		= preg_replace("/(\<\/textarea\>)||(\\\\)/i", "", stripslashes($result->message));
?>
	 			<tr>
	 			<form name="edit_form" method="post" action="<?php echo $location ?>">
	 			<td style="font-size:10px; border:1px solid #eeeeee; background-color:#<?php echo $bgcolor; ?>"><?php echo $result->id;?></td>

				<td style="font-size:10px; border:1px solid #eeeeee; background-color:#<?php echo $bgcolor; ?>;text-align:center;">
				<?php if($result->flag==1) {$check = "checked"; } else {$check="";} ?>
     			<input type="checkbox" name="gb_flag" value="1" <?php echo $check; ?> />
     			</td>

	 			<td style="border:1px solid #eeeeee; background-color:#<?php echo $bgcolor;?>">
	 			<input style="font-size:10px;" type="text" name="gb_name" value="<?php echo $gbname;?>" /></td>
	 			<td style="border:1px solid #eeeeee; background-color:#<?php echo $bgcolor;?>">
	 			<textarea style="height:120px; width:500px;font-size:10px;" name="gb_message"><?php echo $gbmsg;?></textarea></td>
	 			<td style="font-size:10px; border:1px solid #eeeeee; background-color:#<?php echo $bgcolor;?>">

	 			<table border="0">
	 			<tr><td style="font-size:10px;">Date:</td>
	 			<td style="font-size:10px;"><?php echo $date;?><br />
	 			Day.Month.Year,Hour:Minute:Second
	 			<input style="font-size:10px; width:200px;" type="text" name="gb_date" value="<?php echo $date2?>" /><br />
	 			(DD.MM.YYYY,HH:MM:SS)</td></tr>
	 			<input type="hidden" name="hidden_date" value="<?php echo $date;?>" />

				<tr><td style="height:5px;"></td></tr>

	 			<tr><td style="font-size:10px;">IP:</td> <td><input style="font-size:10px; width:200px;"
	 			type="text" name="gb_ip" value="<?php echo $gbip; ?>" maxlength="15" />&nbsp;<a style="font-size:10px;" href="http://www.ripe.net/whois?searchtext=<?php echo $result->ip; ?>" target="_blank">[query]</a></td></tr>
	 			<tr><td style="font-size:10px;">Email: </td> <td><input style="font-size:10px; width:200px;"
	 			type="text" name="gb_email" value="<?php echo $gbemail;?>" /></td></tr>
	 			<tr><td style="font-size:10px;">Website: </td> <td><input style="font-size:10px; width:200px;"
	 			type="text" name="gb_url" value="<?php echo $gburl;?>" /></td></tr>
				</table>

	 			<td style="font-size:10px; border:1px solid #eeeeee; background-color:#<?php echo $bgcolor;?>">
	 			<form name="edit_form" method="post" action="<?php echo $location ?>">
	 			<input name="editdata" value="edit" type="hidden" />
	 			<input name="id" value="<?php echo $result->id;?>" type="hidden" />
	 			<input style="font-weight:bold; color:#0000bb; margin:10px 0px;"
	 			type="submit" value="save" onclick="return confirm('Would you really to edit this dataset?');" />
	 			</form>
	 			</td>

	 			<td style="font-size:10px; border:1px solid #eeeeee; background-color:#<?php echo $bgcolor;?>">
	 			<form name="delete_form" method="post" action="<?php echo $location ?>">
	 			<input name="deletedata" value="delete" type="hidden" />
				<input name="id" value="<?php echo $result->id;?>" type="hidden" />
	 			<input style="font-weight:bold; color:#bb0000; margin:10px 0px;" type="submit"
	 			value="X" onclick="return confirm('Would you really to delete this dataset?');" />
	 			</form>
	 			</td></tr>
<?php			}
?>
		</table>
		</div>
<?php
	} /* end of manage guestbook entries */


	/* edit */
	if ('edit' == $POSTVARIABLE['editdata']) {

	   	/* set http(s):// if not exist */
		if(substr("$_REQUEST[gb_url]", 0, 7) != "http://" && substr("$_REQUEST[gb_url]", 0, 8) != "https://") {
	   	$_REQUEST[gb_url]="http://";
		}

		$table_name = $wpdb->prefix . "dmsguestbook";
		$updatedata = $wpdb->query("UPDATE $table_name SET
		name 		= 	'" . mysql_real_escape_string(addslashes($_REQUEST[gb_name])) . "',
		email 		= 	'" . mysql_real_escape_string($_REQUEST[gb_email]) . "',
		url 		= 	'" . mysql_real_escape_string($_REQUEST[gb_url]) . "',
		ip	 		= 	'" . mysql_real_escape_string($_REQUEST[gb_ip]) . "',
		message 	= 	'" . mysql_real_escape_string(addslashes($_REQUEST[gb_message])) ."',
		flag		=	'" . sprintf("%d", $_REQUEST[gb_flag]) . "'
		WHERE id = '" . sprintf("%d", $_REQUEST[id]) . "'");
  		$update = mysql_query($updatedata);

		if(strlen($_REQUEST[gb_date])!=0) {
		$part0 = explode(",", $_REQUEST[gb_date]);
		$part1 = explode(".", $part0[0]);
		$part2 = explode(":", $part0[1]);

		if(ctype_digit($part2[0])==1) {$part2[0]=substr($part2[0],0,2);} else{$part2[0]=date("H");}
		if(ctype_digit($part2[1])==1) {$part2[1]=substr($part2[1],0,2);} else{$part2[1]=date("i");}
		if(ctype_digit($part2[2])==1) {$part2[2]=substr($part2[2],0,2);} else{$part2[2]=date("s");}
		if(ctype_digit($part1[1])==1) {$part1[1]=substr($part1[1],0,2);} else{$part1[1]=date("m");}
		if(ctype_digit($part1[0])==1) {$part1[0]=substr($part1[0],0,2);} else{$part1[0]=date("d");}
		if(ctype_digit($part1[2])==1) {$part1[2]=substr($part1[2],0,4);} else{$part1[2]=date("Y");}

		$timestamp = mktime($part2[0],$part2[1],$part2[2],$part1[1],$part1[0],$part1[2]);

			$updatedata2 = $wpdb->query("UPDATE $table_name SET
			date 		= 	'$timestamp'
			WHERE id = '" . sprintf("%d", $_REQUEST[id]) . "'");
  			$update2 = mysql_query($updatedata2);
		}

		message("<b>Dataset ($_REQUEST[id]) was saved</b>", 140, 800);
	}

	/* delete */
	if ('delete' == $POSTVARIABLE['deletedata']) {
		$table_name = $wpdb->prefix . "dmsguestbook";
		$deletedata = $wpdb->query("DELETE FROM $table_name WHERE id = '" . sprintf("%d", $_REQUEST[id]) . "'");
		$delete = mysql_query($deletedata);
		message("<b>Dataset ($_REQUEST[id]) was deleted...</b>", 140, 800);
	}
/* end of manage guestbook entries */




	# #	# # # # # - FUNCTIONS - # # # # # # #

	/* DMSGuestbook first time database install */
	function dmsguestbook_install () {
   		global $wpdb;
   		$table_name = $wpdb->prefix . "dmsguestbook";

			if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
	      	$sql = $wpdb->query("CREATE TABLE " . $table_name . " (
	  		id mediumint(9) NOT NULL AUTO_INCREMENT,
	  		name varchar(50) DEFAULT '' NOT NULL,
	  		email varchar(50) DEFAULT '' NOT NULL,
	  		gravatar varchar(32) DEFAULT '' NOT NULL,
	  		url varchar(50) DEFAULT '' NOT NULL,
	  		date int(10) NOT NULL,
	  		ip varchar(15) DEFAULT '' NOT NULL,
	  		message longtext NOT NULL,
	  		flag int(2) NOT NULL,
	  		UNIQUE KEY id (id)
	  		)DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
      		require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
      		dbDelta($sql);
   			}
   			update_db_fields();
  		initialize_option();
		}


   	function update_db_fields() {
   		global $wpdb;
   		$table_name = $wpdb->prefix . "dmsguestbook";
   			/* add field for flags (> 1.8.0) */
   			if($wpdb->get_var("SHOW FIELDS FROM $table_name LIKE 'flag'")=="") {
   			$sql = $wpdb->query("ALTER TABLE " . $table_name . " ADD flag INT(2) NOT NULL");
   			require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
      		dbDelta($sql);
   			}

   			/* add field for flags (> 1.10.0) */
   			if($wpdb->get_var("SHOW FIELDS FROM $table_name LIKE 'gravatar'")=="") {
   			$sql = $wpdb->query("ALTER TABLE " . $table_name . " ADD gravatar varchar(32) NOT NULL");
   			require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
      		dbDelta($sql);
   			}
   	}


	/* DMSGuestbook option first time initialize */
	function initialize_option() {
		$options=default_options_array();
		$count=0;
   		unset($save_options);

		while (list($key, $val) = each($options)) {
		if($key=="antispam_key") {$val = RandomAntispamKey();}
		$save_options.="<" . $key . ">" . $val . "</" . $key . ">\r\n";
		}
		if(!get_option("DMSGuestbook_options")) {update_option("DMSGuestbook_options", $save_options);}
	}


	/* create css entries*/
	function make_css() {
		$options = create_options();
		$options["css_customize"] = str_replace("[br]", "\r\n", $options["css_customize"]);

		$part1 = explode("@", $options["css"]);
		$options["css"] = str_replace("[br]", "\r\n", $options["css"]);
		$part1 = explode("@", $options["css"]);

	   	for($x=0; $x<count($part1)-1; $x++) {
       	$part2 = explode("|", $part1[$x]);
	   	}

	   	unset($csscode1);
	   	for($x=0; $x<count($part1)-1; $x++) {
	   	$part2 = explode("|", $part1[$x]);

	   	$cssvar[]=$part2[1];
	   	$csscode1 .= $part2[1] . " {" . $part2[2] . "}\r\n";
	   	}

	   	$replace_tags = array(
	   	"width1",
	   	"width2",
	   	"position1",
	   	"position2",
	   	"separatorcolor",
	   	"bordercolor1",
	   	"bordercolor2",
	   	"navigationcolor",
	   	"fontcolor1",
	   	"navigationsize",
	   	"captcha_color",
	   	);

		$csscode2 = $options["css_customize"];

	    for($x=0; $x<count($replace_tags); $x++) {
	    $rep="{" . $replace_tags[$x] . "}";
	    $csscode1 = str_replace("$rep", $options["$replace_tags[$x]"], $csscode1);
	    $csscode2 = str_replace("$rep", $options["$replace_tags[$x]"], $csscode2);
	    }

       	$csscode1 = str_replace("css_", ".css_", $csscode1);
       	$csscode= $csscode1 . $csscode2;
       	return $csscode;
	}


	/* add css to header */
	function insert_css() {
	$url=get_bloginfo('wpurl');

		$csscode = make_css();

		$abspath = str_replace("\\","/", ABSPATH);
			if(is_writable($abspath . "wp-content/plugins/dmsguestbook/dmsguestbook.css")) {
				echo '<link rel="stylesheet" href="' . $url . '/wp-content/plugins/dmsguestbook/dmsguestbook.css" type="text/css" media="screen" />';
			} else  {
					echo "<style type='text/css'>";
					echo $csscode;
					echo "</style>";
					}
	}
	add_action('wp_head','insert_css');



	/* display the dmsguestbook.php */
	function DMSGuestBook($content) {
		$options=create_options();
		$page_id=$options["page_id"];
			if(is_page($page_id) AND $page_id!="")
			{
			echo $content;
			include_once ("dmsguestbook.php");
			}
			else	{
					return $content;
					}
		}
	add_action('the_content', 'DMSGuestBook');


/* option array, all options for initialize and reset */
function default_options_array() {

/* css */
$csscontainer = "
position of the guestbook|
css_guestbook_position|
position:relative;
left:{position1}px;
top:{position2}px;@

overall guestbook color|
css_guestbook_font_color|
color:#{fontcolor1};@

Form title property (name, email, url, message)|
css_form_text|
font-weight:normal;@

name text field|
css_form_namefield|
border:1px solid #{bordercolor2};
width:150px;
color:#{fontcolor1};@

email text field|
css_form_emailfield|
border:1px solid #{bordercolor2};
width:150px;
color:#{fontcolor1};@

url text field|
css_form_urlfield|
border:1px solid #{bordercolor2};
width:150px;
color:#{fontcolor1};@

define space between each text fields|
css_form_textfieldspace|
text-align:left;
padding:5px 0px 0px 0px;
margin:0px 0px;@

message text field|
css_form_messagefield|
border:1px solid #{bordercolor2};
width:80%;
height:150px;
color:#{fontcolor1};@

antispam information message|
css_form_antispamtext|
text-align:center;@

antispam image or mathematic figures|
css_form_antispamcontent|
border:1px solid #{bordercolor2};@

antispam image or mathematic figures position|
css_form_antispamcontent_position|
text-align:center;
padding:5px 0px;
margin:0px 0px;@

antispam input text field|
css_form_antispam_inputfield|
width:60px;
border:1px solid #{bordercolor2};
color:#{fontcolor1};@

submit button|
css_form_submit|
color:#{fontcolor1};@

submit button position|
css_form_submit_position|
text-align:center;
padding:20px 0px 10px 0px;@

wrong input text error message|
css_form_errormessage|
color:#bb0000;
font-size: 11px;
text-decoration: none;
font-weight:bold;@

success input text message|
css_form_successmessage|
color:#00bb00;
font-size: 11px;
text-decoration: none;
font-weight:bold;@

visible if the guestbook form is set to 'bottom'|
css_form_link|
font-size:11px;
position:relative;
top:0px;
left:0;@

total guestbook entrys (nr)|
css_navigation_totalcount|
font-size:11px;
left:{position1}px;
width:{width1}%;
text-align:center;
padding:0px 0px 5px 10px;@

guestbook pages (1 2 3 4 [..])|
css_navigation_overview|
left:{position1}px;
width:{width1}%;
text-align:center;
padding:0px 0px 15px 12px;@

selected guestbook page|
css_navigation_select|
color:#bb1100;
text-decoration:none;@

not selected guestbook page|
css_navigation_notselect|
color:#000000;
text-decoration:none;@

navigation char e.g. &lt; &gt;|
css_navigation_char|
color:#{navigationcolor};
font-size:{navigationsize}px;
text-decoration:none;
font-weight:bold;@

navigation char position|
css_navigation_char_position|
left:{position1}px;
width:{width1}%;
padding:0px 0px 0px 10px;
margin:0px 0px 20px 0px;
text-align:center;@

post message number e.g. (24)|
css_post_header1|
font-size:11px;
height:15px;@

post url container|
css_post_header2|
width:20px;
height:15px;@

post email container|
css_post_header3|
width:20px;
height:15px;@

post date & ip address|
css_post_header4|
font-size:11px;
height:15px;@

email image|
css_post_email_image|
height:15px;
width:15px;
border:0px;@

url image|
css_post_url_image|
height:15px;
width:15px;
border:0px;@

guestbook separator (separator between guestbook header and body)|
css_post_separator|
border: 1px solid #{separatorcolor};
height:1px;
width:{width2}%;
text-align:left;
margin:0px 0px 0px 0px;@

content in guestbook body (written text by homepage visitors)|
css_post_message|
font-size:11px;
margin:5px 0px 0px 0px;@

guestbook input data container|
css_form_embedded|
width:{width1}%;
border:1px solid #{bordercolor1};
font-size:12px;
text-align:left;
padding:0px 10px;
margin:0px 0px 0px 0px;
line-height:1.4em;@

guestbook display post container|
css_post_embedded|
width:{width1}%;
border:1px solid #{bordercolor1};
font-size:12px;
text-align:left;
padding:10px 10px;
margin:0px 0px 0px 0px;
line-height:1.4em;@
";
$_SESSION[csscontainer] = $csscontainer;

$url=get_bloginfo('wpurl');

$options = array(
"page_id" => "-",							/* id */
"width1" => "95",							/* guestbook width */
"width2" => "35",							/* separator width */
"step" => "10",								/* step */
"position1" => "0",							/* guestbook position x-axis horizontal */
"position2" => "0",							/* guestbook position y-axis vertical */
"separatorcolor" => "EEEEEE",				/* separator color (separator */
"bordercolor1" => "AAAAAA",					/* outside border color */
"bordercolor2" => "DEDEDE",					/* textfield border color */
"navigationcolor" => "000000",				/* navigation char color*/
"fontcolor1" => "000000",					/* font color */
"forwardchar" => ">",						/* forward char */
"backwardchar" => "<",						/* backward char */
"navigationsize" => "20",					/* forward / backward char size */
"require_email" => "0",						/* require email */
"require_url"=> " 0",						/* require url */
"require_antispam" => "1",					/* require antispam */
"antispam_key" => "0",						/* random key to prevent spam*/
"show_url" => "1",							/* show url */
"show_email" => "1",						/* show email */
"show_ip" => "0",							/* show ip */
"ip_mask" => "123.123.123.*",				/* ip mask */
"captcha_color" => "000000",				/* captcha color */
"dateformat" => "%a, %e %B %Y %H:%M:%S %z",	/* date format */
"setlocale" => "en_EN",						/* setlocale */
"offset" => "0",							/* date offset */
"formpos" => "top",							/* form position */
"formposlink" => "-",						/* form link if is set formpos = bottom */
"send_mail" => "0",							/* notification mail */
"mail_adress" => "name@example.com",		/* notification mail to this adress */
"sortitem" => "DESC",						/* each post sort by*/
"dbid" => "0",								/* show database id instead continous number*/
"language" => "english.txt",				/* language */
"email_image_path" => "$url/wp-content/plugins/dmsguestbook/img/email.gif",	/* email image path */
"website_image_path" => "$url/wp-content/plugins/dmsguestbook/img/website.gif", /* website image path */
"admin_review" => "0",						/* admin must review every post before this can display on page */
"url_overruled" => "0",						/* you can overrule the url if you have trouble with the guestbook form submit */
"gravatar" => "0",							/* gravatar */
"gravatar_rating" => "G",					/* gravatar rating */
"gravatar_size" => "40",					/* gravatar image size in pixel */
"mandatory_char" => "*",					/* mandatory char which you want display on your site */
"form_template" => "default.tpl",			/* form template */
"post_template" => "default.tpl",			/* post template */
"nofollow" => "1",							/* activate the nofollow tag for posted url's */
"css" => "$csscontainer",					/* all css settings */
"css_customize" => "a.css_navigation_char:hover {text-decoration:none; color:#{navigationcolor};}
a.css_navigation_select:hover {text-decoration:none; color:#bb1100;}
a.css_navigation_notselect:hover {text-decoration:none; color:#000000;}
img.css_post_url_image {border:0px;}
img.css_post_email_image {border:0px;}", /* custom css */
);

return $options;
}



	/* reset DMSGuestbook  */
	function default_option() {
		$options=default_options_array();
   		unset($save_options);

		while (list($key, $val) = each($options)) {
		if($key=="antispam_key") {$val = RandomAntispamKey();}
		$save_options.="<" . $key . ">" . $val . "</" . $key . ">\r\n";
		}
		update_option("DMSGuestbook_options", $save_options);
	  	message("<b>Restore default settings...</b> <br />Don't forget to set the page id.", 280, 800);
	}



	/* DMSGuestbook admin message handling */
	function message($message_text, $top, $left) {
		$date=date("H:i:s");
		echo "<div style='position:absolute; top:" . $top . "px; left:" . $left . "px;' id='message' class='updated fade'><p>
		$message_text <br /></p><p style='font-size:10px;'>[$date]</p>
		<img  style='position:absolute; top:-5px; left:5px; height:13px; width:9px;'
		src='../wp-content/plugins/dmsguestbook/img/icon_pin.png'></div>";
	}


	/* show phpinfo() */
	function dmsguestbook3_meta_description_option_page() {
		echo "<div class='wrap'>";
		phpinfo();
		echo "</div>";
	}


	/* show faq */
	function dmsguestbook4_meta_description_option_page() {
		version_control();
		echo "<div class='wrap'>";
			@$file = fopen ("http://danielschurter.net/dmsguestbook/faq.php", "r");
			if (!$file) {
    		echo "<p>File not exist.\n";
    		exit;
			}
				while (!feof ($file)) {
    			$line = @fgets ($file, 1024);
				echo $line;
				}
			@fclose($file);
		echo "</div>";
		}


	/* version control */
	function version_control() {
		@$file = fopen ("http://danielschurter.net/dmsguestbook/release.txt", "r");
			if (!$file) {
    		}
		$line = @fgets ($file, 1024);
		@fclose($file);

		echo "v" . DMSGUESTBOOKVERSION;
		if(DMSGUESTBOOKVERSION < "$line") {echo "<br /><a href='http://wordpress.org/extend/plugins/dmsguestbook' target='_blank'>A new version is available</a>";}
	}


	/* show permission */
	function truetype_permission($file) {
		$abspath = getcwd();
    	$abspath = str_replace("\\","/", $abspath);
    	clearstatcache();
		$fileperms=fileperms("../wp-content/plugins/dmsguestbook/captcha/$file");
		$fileperms = decoct($fileperms);
		echo "<b>" . $file . "</b>" . " have permission: " . substr($fileperms, 2, 6);
	}


	/* options */
	function create_options() {
	$options=default_options_array();
	$stringtext = get_option('DMSGuestbook_options');

			$p=0;
			$c=0;
			reset($options);
			while (list($key, $val) = each($options)) {
			$part1 = explode("<" . $key . ">", $stringtext);
			$part2 = explode("</" . $key . ">", $part1[1]);

				if($part2[0]=="") {
				$missing_entries_for_fixed_update .= "<" . $key . ">" . $val . "</" . $key . ">";
				$missing_entries .= "&lt;" . $key . "&gt;" . $val . "&lt;/" . $key . "&gt;" . "<br />"; $p++;
				}
			$opt["$key"] = html_entity_decode($part2[0], ENT_QUOTES);
			/* cut invalid char XSS prevent */
				/* url overruled, need / */
				if($key=="url_overruled") {
						$opt["$key"] = preg_replace("/[^a-z-0-9-_,.:?&%=\/]+/i", "", html_entity_decode($part2[0]), ENT_QUOTES);
				}
				elseif($key=="email_image_path") {
						$opt["$key"] = preg_replace("/[^a-z-0-9-_,.:?&%=\/]+/i", "", html_entity_decode($part2[0]), ENT_QUOTES);
				}
				elseif($key=="website_image_path") {
						$opt["$key"] = preg_replace("/[^a-z-0-9-_,.:?&%=\/]+/i", "", html_entity_decode($part2[0]), ENT_QUOTES);
				}
				elseif($key=="css") {
					   $opt["$key"] = preg_replace("/[\<\>\"\'\`\´]+/i", "", html_entity_decode($part2[0]), ENT_QUOTES);
					   }
				elseif($key=="css_customize") {
					   $opt["$key"] = preg_replace("/[\<\>\"\'\`\´]+/i", "", html_entity_decode($part2[0]), ENT_QUOTES);
					   }
				elseif($key=="formposlink") {
					   $opt["$key"] = preg_replace("/[\"\'\`\´\/\\\\]/i", "", html_entity_decode($part2[0], ENT_QUOTES));
					   }
				elseif($key=="forwardchar") {
					   $opt["$key"] = preg_replace("/[\"\'\`\´\/\\\\]/i", "", html_entity_decode($part2[0], ENT_QUOTES));
					   }
				elseif($key=="backwardchar") {
					   $opt["$key"] = preg_replace("/[\"\'\`\´\/\\\\]/i", "", html_entity_decode($part2[0], ENT_QUOTES));
					   }
				elseif($key=="dateformat") {
					   $opt["$key"] = preg_replace("/[\"\'\`\´\/\\\\]/i", "", html_entity_decode($part2[0], ENT_QUOTES));
					   }
				elseif($key=="setlocale") {
					   $opt["$key"] = preg_replace("/[\"\'\`\´\/\\\\]/i", "", html_entity_decode($part2[0], ENT_QUOTES));
					   }
				elseif($key=="mail_adress") {
					   $opt["$key"] = preg_replace("/[\"\'\`\´\/\\\\]/i", "", html_entity_decode($part2[0], ENT_QUOTES));
					   }
				elseif($key=="mandatory_char") {
					   $opt["$key"] = preg_replace("/[\"\'\`\´\/\\\\]/i", "", html_entity_decode($part2[0], ENT_QUOTES));
					   }
				elseif($key=="gravatar_size") {
					   $opt["$key"] = preg_replace("/[^0-9]/i", "", html_entity_decode($part2[0], ENT_QUOTES));
					   }
				elseif($key=="antispam_key") {
					   $opt["$key"] = $part2[0];
					   }
						else {
					 		 $opt["$key"] = preg_replace("/[\"\'\`\´\/\\\\]/i", "", $part2[0]);
					 		 }
			$c++;
			}
			if($missing_entries!="") {
			unset($_SESSION["missing_options"]);
			unset($_SESSION["missing_options_fixed"]);
			$_SESSION["missing_options"]=$missing_entries;
			$_SESSION["missing_options_fixed_update"]=$missing_entries_for_fixed_update;
			}

		return $opt;
	}


	function settablecolor($setcolor,$tablecolor) {
	if($setcolor==1) {$colorresult="EDF6FF";}
	if($setcolor==2) {$colorresult="E5F2FF";}
	if($setcolor==3) {$colorresult="CFDBE6";}
	if($tablecolor==1) {$colorresult="style='background-color:#$colorresult; padding:2px 2px;'"; }
	if($tablecolor==2) {$colorresult="style='background-color:#$colorresult; padding:0px 2px; text-align:center;'"; }
	return $colorresult;
	}


	/* advanced file */
	function check_writable($folder, $file) {
	$abspath = str_replace("\\","/", ABSPATH);
		if(is_writable($abspath . "wp-content/plugins/dmsguestbook/" . $folder . $file)) {
		echo "<br />$_REQUEST[file] <font style='color:#00bb00;'>is writable!</font><br />Set $file readonly again when your finished to customize!";
		$save_advanced_button = "<input style='font-weight:bold; margin:10px 0px; width:250px;' type='submit' value='save' />";
		return $save_advanced_button;
		}
		else {
	         echo "<br />$_REQUEST[file] is <font style='color:#bb0000;'>not writable!
	         </font><br />Set the write permission for $_REQUEST[file] to customize this file.";
	         return $save_advanced_button="";
	         }

	}


	/* missing options */
	function missing_options() {
	global $wpdb;
   	$table_name = $wpdb->prefix . "dmsguestbook";
		echo "<br /><br />
		<hr style='width:100%;border:1px solid #cc0000;'></hr>
		<b>This option(s) was not found in " . $table_name . " -> " . $wpdb->prefix . "options -> DMSGuestbook_options:</b><br />
		$_SESSION[missing_options]
		<hr style='width:100%;border:1px solid #cc0000;'></hr>";
	}



	/* generate all options */
	function OneInput($key, $label, $entries, $value, $char_lenght, $additional, $style, $tooltip) {
		$part1 = explode("@", $label);
		$part2 = explode("@", $additional);
		unset($data);
			for($x=0; $x<=$entries; $x++) {
				if($tooltip!=""){$showtooltip="<b style='font-weight:bold;background-color:#bb1100;color:#fff;padding:3px;' onmouseover=\"Tip('$tooltip')\" onclick=\"UnTip()\">?</b>";}
			$data .= "<table style='width:95%;' border='0'><colgroup><col width='40%'><col width='55%'><col width='5%'></colgroup><tr>
			<td>$part1[$x]</td>
			<td><input style='$style;' type='text' name='$key' value='$value' maxlength='$char_lenght' />$part2[$x]</td><td style='text-align:right;'>$showtooltip</td></tr></table>";
			}
		return $data;
	}

	function ColorInput($key, $label, $id, $value, $additional, $style, $tooltip) {
		$part1 = explode("@", $label);
		$part2 = explode("@", $additional);
		unset($data);
		if($tooltip!=""){$showtooltip="<b style='font-weight:bold;background-color:#bb1100;color:#fff;padding:3px;' onmouseover=\"Tip('$tooltip')\" onclick=\"UnTip()\">?</b>";}
		$colorid_div = "Color" . $id . "_div";
		$data = "<table style='width:95%;' border='0'><colgroup><col width='40%'><col width='55%'><col width='5%'></colgroup><tr><td>$part1[0]</td><td><div id=\"$colorid_div\" style=\"border:1px solid;
		background-color:#$value;float:left;width:25px;
		height:25px;cursor:pointer;\" onclick=\"show_picker('Color$id','$value','$value');\"/></div>
		<input name=\"$key\" type=\"text\" size=\"6\" value=\"$value\" id=\"Color$id\" onclick=\"show_picker(this.id, '$value', '$value');\" />$part2[0]</td><td style='text-align:right;'>$showtooltip</td></tr></table>";

		return $data;
	}

	function CheckBoxes($key, $label, $value, $entries, $additional, $style, $tooltip) {
		$part1 = explode("@", $label);
		$part2 = explode("@", $additional);
		unset($data);
		for($x=1; $x<=$entries+1; $x++) {
		$check="check" . $x;
			if($tooltip!=""){$showtooltip="<b style='font-weight:bold;background-color:#bb1100;color:#fff;padding:3px;' onmouseover=\"Tip('$tooltip')\" onclick=\"UnTip()\">?</b>";}
			if($value==$x) {$check = "checked";} else {$check="";}
			$c=$x-1;
			$data .= "<table style='width:95%;' border='0'><colgroup><col width='40%'><col width='55%'><col width='5%'></colgroup><tr><td>$part1[$c]</td><td><input style='$style;' type='checkbox' name='$key' value='$x' $check /> $part2[$c]</td><td style='text-align:right;'>$showtooltip</td></tr></table>";
			}
		return $data;
	}

	function RadioBoxes($key, $label, $value, $entries, $additional, $style, $tooltip) {
		$part1 = explode("@", $label);
		$part2 = explode("@", $additional);
		unset($data);
		for($x=0; $x<=$entries; $x++) {
		$check="check" . $x;
			if($tooltip!=""){$showtooltip="<b style='font-weight:bold;background-color:#bb1100;color:#fff;padding:3px;' onmouseover=\"Tip('$tooltip')\" onclick=\"UnTip()\">?</b>";}
			if($value==$x) {$check = "checked";} else {$check="";}
			$data .= "<table style='width:95%;' border='0'><colgroup><col width='40%'><col width='55%'><col width='5%'></colgroup><tr><td>$part1[$x]</td><td><input style='$style;' type='radio' name='$key' value='$x' $check />$part2[$x]</td><td style='text-align:right;'>$showtooltip</td></tr></table>";
			}
		return $data;
	}

	function SelectBox($key, $label, $option, $value, $additional, $style, $tooltip) {
		$part1 = explode("@", $option);
		$part2 = explode("@", $additional);
		unset($data);
			if($tooltip!=""){$showtooltip="<b style='font-weight:bold;background-color:#bb1100;color:#fff;padding:3px;' onmouseover=\"Tip('$tooltip')\" onclick=\"UnTip()\">?</b>";}
			$data .= "<table style='width:95%;' border='0'><colgroup><col width='40%'><col width='55%'><col width='5%'><colgroup><tr><td>$label</td><td><select style='$style;' name='$key'>";
			$data .= "<option>$value</option>";
			for($x=0; $x<=count($part1)-2; $x++) {
				$data .= "<option>$part1[$x]</option>";
			}
			$data .= "</select></td><td style='text-align:right;'>$showtooltip</td></tr></table>";
		return $data;
	}


	/* antispam key generator */
	function RandomAntispamKey() {
	$len=20;
	srand(date("U"));
    $possible="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890+*%&(){}[]=?!$<>-_.,;:/\@#~";
    unset($str);
    	while(strlen($str)<$len) {
    	$str.=substr($possible,(rand()%(strlen($possible))),1);
		}
		return($str);
	}







?>