<?php
/*
Plugin Name: DMSGuestbook
Plugin URI: http://danielschurter.net/
Description: Create and customize your own guestbook.
Version: 1.15.4
Author: Daniel M. Schurter
Author URI: http://danielschurter.net/
*/

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/* Deactivate the role by setting "0" if the admin panel doesn't appear. | Default: 1 */
define('ROLE', "1");

/* If you using an other reCAPTCHA library than the DMSGuestbook built in, set the correct path to your recaptca plugin */
define('RECAPTCHAPATH', ABSPATH . "wp-content/plugins/dmsguestbook/recaptcha/recaptchalib.php");

/* All single, alphanumeric (a-z, 0-9) option fields are quoted with base64 by setting 1. | Default: 0
These fields are affected:
- formposlink
- additional_option_title
- mandatory_char
- forwardchar
- backwardchar
*/
define('BASE64', "0");

/* DMSGuestbook version */
define('DMSGUESTBOOKVERSION', "1.15.4");
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */




	/* menu (DMSGuestbook, Manage) */
	add_action('admin_menu', 'add_dmsguestbook');

	function add_dmsguestbook() {
		$options = create_options();
		$Role1 = CheckRole($options["role1"],0);
		$Role2 = CheckRole($options["role2"],0);
		$Role3 = CheckRole($options["role3"],0);

		/* If role isn't set change it to 0 */
		if(ROLE == 0) {
		$Role1 = 0;
		$Role2 = 0;
		$Role3 = 0;
		}

		$maxRole = max($Role1,$Role2,$Role3);

		add_menu_page(__('Options', 'dmsguestbook'), __('<span style=\'font-size:12px;\'>DMSGuestbook</span>', 'dmsguestbook'), $maxRole,
		'dmsguestbook', 'dmsguestbook_meta_description_option_page', '../wp-content/plugins/dmsguestbook/img/guestbook.png');

		if(current_user_can("level_" . $Role2) || ROLE == 0) {
		add_submenu_page( 'dmsguestbook' , __('Entries', 'dmsguestbook'), __('Entries', 'dmsguestbook'), $Role2,
		'Entries', 'dmsguestbook2_meta_description_option_page');
		}

		if(current_user_can("level_" . $Role3) || ROLE == 0) {
		add_submenu_page( 'dmsguestbook' , __('Spam', 'dmsguestbook'), __('Spam', 'dmsguestbook'), $Role3,
		'Spam', 'dmsguestbook5_meta_description_option_page');
		}

		if(current_user_can("level_" . $Role1) || ROLE == 0) {
		add_submenu_page( 'dmsguestbook' , __('phpinfo', 'dmsguestbook'), __('phpinfo', 'dmsguestbook'), $Role1,
		'phpinfo', 'dmsguestbook3_meta_description_option_page');
		}
	}



	/* create db while the activation process */
	add_action('activate_dmsguestbook/admin.php', 'dmsguestbook_install');

	/* version */
	add_action('wp_head', 'addversion');
	function addversion() {
		echo "<meta name=\"DMSGuestbook\" content=\"".DMSGUESTBOOKVERSION."\" />\n";
	}

	/* backup options */
	if(isset($_REQUEST[backup_options])) {
	$filename = "DMSGuestbook_options_" . date("d-m-Y") . ".txt";
	@header("Content-Type: text/plain");
	@header("Content-Disposition: attachment; filename=$filename");
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

	/* global var for DMSGuestbook and option database */
	global $wpdb;

	$table_name = $wpdb->prefix . "dmsguestbook";
	$table_option = $wpdb->prefix . "options";
	$table_posts = $wpdb->prefix . "posts";
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

	<!-- header -->
	<div class="wrap">
    <h2>DMSGuestbook Option</h2>
    <ul>
    <li>1.) Create a page which you want to display the DMSGuestbook.</li>
    <li>2.) Save the page and assign it under "Guestbook settings" -> "Basic" .</li>
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
  			<input class='button-secondary action' style='font-weight:bold; margin:10px 0px; width:250px;' type='submit' value='Update options database' />
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
	  	guestbook int(2) DEFAULT '0' NOT NULL,
	  	spam int(1) DEFAULT '0' NOT NULL,
	  	additional varchar(50) NOT NULL,
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
function addLoadEvent(func) {if ( typeof wpOnload!='function'){wpOnload=func;}else{ var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}}
//]]>
</script>
<script type='text/javascript' src='../wp-content/plugins/dmsguestbook/js/dbx/dbx.js'></script>
<script type='text/javascript' src='../wp-content/plugins/dmsguestbook/js/dbx/dbx-key.js'></script>
<script type="text/javascript" src="../wp-content/plugins/dmsguestbook/js/tooltip/wz_tooltip.js"></script>

<?php
$collaps_dashboard="<a href='admin.php?page=dmsguestbook'>
<img src='../wp-content/plugins/dmsguestbook/img/dashboard.png'><b>Dashboard</b></a>";
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
			<td><?php echo $collaps_dashboard;?></td>
			<td><?php echo $collaps_dbs;?></td>
			<td><?php echo $collaps_basic?></td>
			<td><?php echo $collaps_advanced?></td>
		</tr>
</table>
<br /><br /><br />

<?php
/* dashboard */
if($_REQUEST[page]=="dmsguestbook" && ($_REQUEST[dbs]!=1 && $_REQUEST[basic]!=1 && $_REQUEST[advanced]!=1)) {

	$dashcolor="#21759B";
	function convert($convert) {
		if($convert==1) {
		return("Yes");
		}
		else
			{
			return("No");
			}
	}

	if(function_exists("gd_info")) {
	$gd_array = gd_info();
	$gd_version 			= $gd_array["GD Version"];
	$gd_freetype 			= convert($gd_array["FreeType Support"]);
	$gd_freetype_linkage 	= $gd_array["FreeType Linkage"];
	$gd_png		 			= convert($gd_array["PNG Support"]);
	}


	if(CheckAkismet() !="") {
	$akismet_notify = "Akismet: <span style='color:$dashcolor;'>Yes</span>";
	} else {
		   $akismet_notify = "Akismet: <span style='color:$dashcolor;'>No</span>";
		   }

	$abspath = str_replace("\\","/", ABSPATH);
	$sqlversion 			= $wpdb->get_var("SELECT VERSION()");
	$css_writable 			= convert(is_writable($abspath . "wp-content/plugins/dmsguestbook/dmsguestbook.css"));
	$ttf_readable			= convert(is_readable($abspath . "wp-content/plugins/dmsguestbook/captcha/xfiles.ttf"));
	if(ini_get('memory_limit')) {
	$memory_limit = ini_get('memory_limit');
	}
	else {
	     $memory_limit = "";
	     }

	$result_spam 		= $wpdb->query("SELECT * FROM $table_name WHERE spam = '1'");
	$result_post 		= $wpdb->query("SELECT * FROM $table_name WHERE spam = '0'");
	$result_approval 	= $wpdb->query("SELECT * FROM $table_name WHERE flag = '1'");

	echo "<table style='width:100%;' class='widefat comments' cellspacing='0'>
		<thead>
		<tr>
		<th style='padding:5px 5px 5px 5px;width:25%;'>Dashboard</th>
		<th style='padding:0px 5px 0px 5px;width:25%;'></th>
		<th style='padding:0px 5px 0px 5px;width:50%;'></th>
		</tr>
		</thead>
		<tr>
			<td style='padding:20px;'>
			<b style='font-size:16px;'><a href='admin.php?page=Entries'>$result_post</a> <span style='color:#008000;'>entries</span></b><br />
			<b style='font-size:16px;'><a href='admin.php?page=Entries&approval=1'>$result_approval</a> <span style='color:#ffa500;'>waiting for approval</span></b><br />
			<b style='font-size:16px;'><a href='admin.php?page=Spam'>$result_spam</a> <span style='color:#ff0000;'>spam</span></b><br />
			<div style='height:40px;'></div>
			<b style='font-size:14px;text-decoration:underline;'>Server Settings</b>
			<br />
			Server: <span style='color:$dashcolor;'>$_SERVER[SERVER_SOFTWARE]</span><br />
			MYSQL Server: <span style='color:$dashcolor;'>$sqlversion</span><br />
			Memory Limit: <span style='color:$dashcolor;'>$memory_limit</span><br />
			<br />
			<b style='font-size:14px;text-decoration:underline;'>Graphic Settings</b>
			<br />
			GD Version: </b><span style='color:$dashcolor;'>$gd_version</span><br />
			FreeType Support: <span style='color:$dashcolor;'>$gd_freetype</span><br />
			Freetype Linkage: <span style='color:$dashcolor;'>$gd_freetype_linkage</span><br />
			PNG Support: <span style='color:$dashcolor;'>$gd_png</span><br />
			<br />
			<b style='font-size:14px;text-decoration:underline;'>Permissions</b>
			<br />
			Database settings: <span style='color:$dashcolor;'>$options[role1]</span><br />
			Guestbook settings: <span style='color:$dashcolor;'>$options[role1]</span><br />
			Language settings: <span style='color:$dashcolor;'>$options[role1]</span><br />
			Post settings: <span style='color:$dashcolor;'>$options[role2]</span><br />
			Spam settings: <span style='color:$dashcolor;'>$options[role3]</span><br />
			phpinfo: <span style='color:$dashcolor;'>$options[role1]</span><br />
			</td>

			<td style='padding:20px'>
			<b style='font-size:14px;text-decoration:underline;'>Miscellaneous</b><br />
			$akismet_notify<br />
			CSS file writable: <span style='color:$dashcolor;'>$css_writable</span> <span style='font-size:8px;'>1)</span><br />
			xfiles.ttf readable: <span style='color:$dashcolor;'>$ttf_readable</span><br />
			<br />
			Captcha Image:<br /><img src='../wp-content/plugins/dmsguestbook/captcha/captcha.php' /> <span style='font-size:8px;'>2)</span><br />
			<br />
			<br />
			<div style='background-color:#eeeeee;padding:5px;'>
			<span style='font-size:8px;'>1)</span> If dmsguestbook.css is exist and writable, all CSS settings will be read from it.<br />
			Otherwise these settings will be load from the database.
			<br />
			<br />
			<span style='font-size:8px;'>2)</span>
			If you don't see the image here, check the xfiles.ttf and captcha.png permission in your captcha folder.</div>
			</td>

			<td style='padding:20px;'>";
			echo "<b style='font-size:14px;text-decoration:underline;'>News</b><br />";
			include_once(ABSPATH . WPINC . '/rss.php');
			$rss1 = fetch_rss('http://www.danielschurter.net/mainsite/category/DMSGuestbook/feed/');
			$maxitems = 3;
			@$items = array_slice($rss1->items, 0, $maxitems);

			echo "<ul>";
			if (empty($items)) {
				echo "<li>No items</li>";
			}
			else {
			     foreach ( $items as $item ) :
					echo "<li><a href='$item[link]' title='$item[title]' target='_blank'>$item[title]</a>&nbsp;&nbsp;<span style='color:#666666;font-size:10px;'>". mb_substr($item[pubdate],5 ,12) . "</span><br />
					" . mb_substr($item[description], 0, 80) . " [...]</li>";
			     endforeach;
			     }
			echo "</ul>

			<br /><b style='font-size:14px;text-decoration:underline;'>Infos</b><br />
			<ul>
				<li><a href='http://www.danielschurter.net/mainsite/2009/03/05/dmsguestbook-faq/' target='_blank'>FAQ</a></li>
				<li><a href='http://www.danielschurter.net/mainsite/2007/07/28/dmsguestbook-10/' target='_blank'>Changelog</a></li>
			</ul>

			</td>

		</tr>
	</table>";
}



if($_REQUEST[dbs]==1) {

	$Role1 = CheckRole($options["role1"],0);
	if(!current_user_can("level_" . $Role1) && ROLE != 0) {
		CheckRole($options["role1"],1);
	exit;
	}

/* dmsguestbook datatbase*/
		// search prefix_dmsguestbook
        $result = $wpdb->query("SHOW TABLES LIKE '$table_name'");
		if ($result > 0) {
			/* if prefix_dmsguestbook does exist */
			$return_dmsguestbook_database = "<b style='color:#00bb00;'>[Status OK] $table_name does exist.</b><br /><br />
  			Type \"yes, i am sure\" in this textfield if you want delete $table_name.<br />
  			<b>All guestbook data will be lost!</b><br />
  			<form name='form0' method='post' action='$location'>
  			<input type='text' name='delete' value='' /><br />
  			<input name='action' value='delete' type='hidden' />
  			<input class='button-secondary action' style='font-weight:bold; margin:10px 0px; width:250px;' type='submit' value='delete $table_name' />
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
				<input class='button-primary action' style='font-weight:bold; margin:10px 0px; width:300px;' type='submit' value='create $table_name' />
			</form>
			If you want use char like &auml;,&uuml;,&ouml;... and your mysql version is lower than 4.1, be sure the language
			setting is e.g. \"de-iso-8859-1\" or similar. Check this with your mysql graphical frontenend like phpmyadmin.<br />";
		}

	$return_dmsguestbook_database_error = "<br />If there is something wrong with my $table_name table: <a style='font-weight:bold;background-color:#bb1100;color:#fff;padding:3px;text-decoration:none;' href='../wp-content/plugins/dmsguestbook/default_sql.txt' target='_blank'>Help</a>";


/* dmsguestbook options*/
	/* search all DMSGuestbook option (inform the user about the old dmsguestbook entries) */
	$query_options = $wpdb->get_results("SELECT * FROM $table_option WHERE option_name LIKE 'DMSGuestbook_%'");
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
			<input class='button-secondary action' style='font-weight:bold; margin:10px 0px; width:400px;' type='submit' value='Create new DMSGuestbook options' />
		</form>
		<br /><br />
		<form name='form0' method='post' action='$location'>
				Type \"delete\" to remove all DMSGuestbook option entries from the $table_option table.<br />
				<input type='text' name='confirm_delete_option' value='' /><br />
				<input name='action' value='deleteoption' type='hidden' />
				<input class='button-secondary action' style='font-weight:bold; margin:10px 0px; width:400px;' type='submit' value='Delete DMSGuestbook options from the database' />
			</form>
	<br />If there is something wrong with my<br />DMSGuestbook_options in $table_option: <a style='font-weight:bold;background-color:#bb1100;color:#fff;padding:3px;text-decoration:none;' href='../wp-content/plugins/dmsguestbook/default_options.txt' target='_blank'>Help</a>";

/* backup */
		$return_dmsguestbook_options_backup = "<a class='button-secondary action' style='text-decoration:none;font-weight:bold;' href='$location?backup_options'>Backup DMSGuestbook_options</a>
		<br />
		<br />
		Restore DMSGuestbook_options:<br />
		Open a DMSGuestbook_options_DATE.txt file, copy the whole content and put these to the textfield below.<br />
		All data will be overwrite.
		<form action='$location' method='post'>
		<textarea style='width:450px; height:200px;' name='restore_data'></textarea><br />
		<input type='hidden' name='restore_options' value='1' />
		<input class='button-secondary action' style='font-weight:bold;' type='submit' value='Restore' onclick=\"return confirm('Would you really like to restore all data?');\" />
		</form>";



echo "<b style='font-size:20px;'>Database settings</b><br />";
echo "<table width='100%' border='0'>";
echo "<tr><td>";

echo "<div id='outer'>
		<div class='dbx-group' id='dmsguestbook'>";

	echo "<div class='dbx-box'>
				<div class='dbx-handle' title='DMSGuestbook Database'><b style='color:#464646;'>DMSGuestbook Database</b></div>
				<ul class='dbx-content'>
				<li>&nbsp;</li>
				<li>$return_dmsguestbook_database</li>
				<li>$return_dmsguestbook_database_error</li>
				</ul>
			</div>";

	echo "<div class='dbx-box'>
				<div class='dbx-handle' title='DMSGuestbook options'><b style='color:#464646;'>DMSGuestbook options</b></div>
				<ul class='dbx-content'>
				<li>&nbsp;</li>
				<li>$return_dmsguestbook_options</li>
				</ul>
			</div>";

	echo "<div class='dbx-box'>
				<div class='dbx-handle' title='DMSGuestbook options backup'><b style='color:#464646;'>DMSGuestbook options backup</b></div>
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
		$submitbutton = "<input class='button-primary action' style='font-weight:bold;margin:10px 0px; width:100px;'
		type='submit' value='Save' name='csssave' onclick=\"document.getElementById('save')\" />";


if($num_rows_option==$dmsguestbook_options)
{

if($_REQUEST[basic]==1) {

	$Role1 = CheckRole($options["role1"],0);
	if(!current_user_can("level_" . $Role1) && ROLE != 0) {
		CheckRole($options["role1"],1);
	exit;
	}

reset($options);
while (list($key, $val) = each($options)) {

	if($key == "page_id") {
		$query_posts = $wpdb->get_results("SELECT ID, post_title, post_status FROM $table_posts WHERE post_type = 'page' ORDER BY id ASC");
		$num_rows_posts = $wpdb->num_rows;
		$part_page_id = explode(",", $options["page_id"]);
		$part_language = explode(",", $options["language"]);

		$c=0;
		$data ="";
		$data .= "<table style='width:95%;' border=0><colgroup><col width='40%'><col width='55%'><col width='5%'></colgroup><tr><td>To assign guestbook(s):</td><td>";

		### Language
		unset($tmp);
		$abspath = str_replace("\\","/", ABSPATH);
				if ($handle = opendir($abspath . 'wp-content/plugins/dmsguestbook/language/')) {
					$tmp .= "<select name='langselect' id='langselect'>";
    				while (false !== ($file = readdir($handle))) {
        				if ($file != "." && $file != ".." && $file != "README.txt") {
           				$tmp .= "<option value='$file'>$file</option>";
        				}
    				}
    				$tmp .= "</select>";
    			closedir($handle);
				}
		###

		$data .= "<table>";
		$data .= "<th style='font-size:9px;background-color:#cccccc;padding:2px;'>ID</th>";
		$data .= "<th style='font-size:9px;background-color:#cccccc;padding:2px;'>Page</th>";
		$data .= "<th style='font-size:9px;background-color:#cccccc;padding:2px;'>Page status</th>";
		$data .= "<th style='font-size:9px;background-color:#cccccc;padding:2px;'>Guestbook</th>";
		$data .= "<th style='font-size:9px;background-color:#cccccc;padding:2px;'>Language<br />$tmp</th>";

		foreach ($query_posts as $result) {
			$data .= "<tr><td style='font-size:9px;background-color:#dddddd;padding:2px;'>$result->ID</td><td style='font-size:9px;background-color:#eeeeee;padding:2px;'>" . $result->post_title . "</td> ";
			$data .= "<td style='font-size:9px;background-color:#dddddd;padding:2px;'><a href='page.php?action=edit&post=$result->ID'>$result->post_status</a></td>";
				for($v=0; $v<count($part_page_id); $v++) {
				unset($lang);
					if($result->ID == $part_page_id[$v]) {
					$vv = $v +1;
					$set = "#" . $vv;
					$disabled = "disabled";
					$v=count($part_page_id);
					$lang=$part_language[$vv-1];
					}
					else {
				     	$set = "not selected";
				     	$disabled = "disabled";
				     	}
				}
				$data .= "<td style='font-size:9px;background-color:#dddddd;padding:2px;'><input class='button-secondary action' style='width:70px;' id='pageid$c' name='pageid$c' type='action' value='$set' $disabled onclick=\"PageID('$result->ID', '$c')\"></td>";

				$data .= "<td style='font-size:9px;background-color:#dddddd;padding:2px;'><input style='width:120px;font-size:9px;background-color:#dddddd;border:1px;padding:2px;' type='text' id='language$c' name='language$c' value='$lang' readonly></td></tr>";

			$c++;
		}
		$data .= "</table>";

		$data .= "<input type='hidden' name='page_id' id='page_id' value='$options[page_id]'>";
		$data .= "<input type='hidden' name='language' id='language' value='$options[language]'>";
		$data .= "<input type='hidden' name='countpageid' id='countpageid' value='1'>";
		$data .= "<input id='page_id_clear' name='page_id_clear' class='button-secondary action' style='width:50px;color:#bb0000;' type='action' value='Clear all' onclick=\"PageID_Clear('$num_rows_posts')\"></td>";


		$data .= "<script type='text/javascript'>";
		$data .= "
				function PageID(id, c) {
				var m = document.getElementById('countpageid').value;
  				var newpageid = document.getElementById('page_id').value;
  				newpageid = newpageid.concat(id + ',');
  				document.getElementById('page_id').value = newpageid;
  				document.getElementById('pageid' + c).value = '#' + m;
  				document.getElementById('pageid' + c).disabled = true;

  				document.getElementById('language' + c).value = document.getElementById('langselect').value;

  				var newlanguage = document.getElementById('language').value;
  				newlanguage = newlanguage.concat(document.getElementById('langselect').value + ',');
  				document.getElementById('language').value = newlanguage;

  				m++;
  				document.getElementById('countpageid').value = m;
  				}

  				function PageID_Clear(c) {
  				 	for (var i = 0; i < c; i++) {
  			     	document.getElementById('pageid' + i).disabled = false;
  				 	document.getElementById('pageid' + i).value = 'Set';
  				 	document.getElementById('language' + i).value = '';
  				 	}
  				document.getElementById('page_id').value = '';
  				document.getElementById('language').value = '';
  				document.getElementById('countpageid').value = '1';
  				}
  				";
		$data .= "</script>";
		$tooltip = "Select your guestbook(s) by clicking \'Set\' in ascending order.<br />Reset all settings by clicking on \'Clear all\'.<br />You have to reset all assigned guestbooks before you can set this again.";
		$data .= "<td style='text-align:right;'><b style='font-weight:bold;background-color:#bb1100;color:#fff;padding:3px;' onmouseover=\"Tip('$tooltip')\" onclick=\"UnTip()\">?</b></td>";
		$data .= "</tr></table><br />";
	$return_page_id = $data;
	}

	if($key == "step") {
		$label = "Post per page:";
		$option = "1@3@5@10@15@20@25@30@35@40@45@50@60@70@80@90@100@";
		$value = $options["step"];
		$additional = "";
		$style = "";
		$tooltip = "Number of entry in each page";
		$jscript = "";
  		$return_step = SelectBox($key, $label, $option, $value, $additional, $style, $tooltip, $jscript);
	}

	if($key == "messagetext_length") {
		$label = "Message text length:";
		$type = "text";
		$entries = 0;
		$value = $options["messagetext_length"];
		$char_lenght = "";
		$additional = " chars";
		$style = "width:50px;";
		$tooltip = "Define the maximum allowed lenght each message text<br />Deactivate this feature to set 0";
		$jscript = "";
		$base64 = 0; /* Do not use this unless you edit the preg_replace() in the create_options() function and $var_* in dmsguestbook.php */
		$return_messagetext_length = OneInput($key, $label, $type, $entries, $value, $char_lenght, $additional, $style, $tooltip, $jscript, $base64);
	}

	if($key == "width1") {
		$label = "Guestbook width:";
		$type = "text";
		$entries = 0;
		$value = $options["width1"];
		$char_lenght = "";
		$additional = "%";
		$style = "width:50px;";
		$tooltip = "Guestbook width in percent<br /><br />Variable: {width1}";
		$jscript = "";
		$base64 = 0; /* Do not use this unless you edit the preg_replace() in the create_options() function and $var_* in dmsguestbook.php */
		$return_width1 = OneInput($key, $label, $type, $entries, $value, $char_lenght, $additional, $style, $tooltip, $jscript, $base64);
	}

	if($key == "width2") {
		$label = "Separator width:";
		$type = "text";
		$entries = 0;
		$value = $options["width2"];
		$char_lenght = "";
		$additional = "%";
		$style = "width:50px;";
		$tooltip = "Separator width in percent<br /><br />Variable: {width2}";
		$jscript = "";
		$base64 = 0; /* Do not use this unless you edit the preg_replace() in the create_options() function and $var_* in dmsguestbook.php */
		$return_width2 = OneInput($key, $label, $type, $entries, $value, $char_lenght, $additional, $style, $tooltip, $jscript, $base64);
	}

	if($key == "position1") {
		$label = "Guestbook position (x-axis):";
		$type = "text";
		$entries = 0;
		$value = $options["position1"];
		$char_lenght = "";
		$additional = "px";
		$style = "width:50px;";
		$tooltip = "Absolute guestbook position in pixel horizontal (x-axis)<br /><br />Variable: {position1}";
		$jscript = "";
		$base64 = 0; /* Do not use this unless you edit the preg_replace() in the create_options() function and $var_* in dmsguestbook.php */
		$return_position1 = OneInput($key, $label, $type, $entries, $value, $char_lenght, $additional, $style, $tooltip, $jscript, $base64);
	}

	if($key == "position2") {
		$label = "Guestbook position (y-axis):";
		$type = "text";
		$entries = 0;
		$value = $options["position2"];
		$char_lenght = "";
		$additional = "px";
		$style = "width:50px;";
		$tooltip = "Absolute guestbook position in pixel vertical (y-axis)<br /><br />Variable: {position2}";
		$jscript = "";
		$base64 = 0; /* Do not use this unless you edit the preg_replace() in the create_options() function and $var_* in dmsguestbook.php */
		$return_position2 = OneInput($key, $label, $type, $entries, $value, $char_lenght, $additional, $style, $tooltip, $jscript, $base64);
	}

	if($key == "forwardchar") {
		$tooltip ="Navigation char style<br /><br />e.g. < >";
		$showtooltip="<b style='background-color:#bb1100;color:#fff;padding:3px;' onmouseover=\"Tip('$tooltip')\" onclick=\"UnTip()\">?</b>";
		$base64 = 1;
			/* If base64 is active */
			if(BASE64 == 1 && $base64 == 1) {
			$forwardchar = base64_decode($options[forwardchar]);
			$backwardchar = base64_decode($options[backwardchar]);
			}
			else {
			     $forwardchar = $options[forwardchar];
			     $backwardchar = $options[backwardchar];
			     }
		$return_forwardchar = "<li><table style='width:95%;' border='0'><colgroup><col width='40%'><col width='55%'><col width='5%'><colgroup><tr><td>Navigation char style:</td>
		<td><input style='width:50px;' type='text' name='backwardchar' value='$backwardchar' />
		<input style='width:50px;' type='text' name='forwardchar' value='$forwardchar' />
		<input type='hidden' name='base64-forwardchar' value='$base64' />
		<input type='hidden' name='base64-backwardchar' value='$base64' />
		</td>
		<td style='text-align:right;'>$showtooltip</td></tr></table></li>";
	}

	if($key == "navigationsize") {
		$label = "Navigation char size:";
		$type = "text";
		$entries = 0;
		$value = $options["navigationsize"];
		$char_lenght = "";
		$additional = "px";
		$style = "width:50px;";
		$tooltip = "Navigation font size in pixel<br /><br />Variable: {navigationsize}";
		$jscript = "";
		$base64 = 0; /* Do not use this unless you edit the preg_replace() in the create_options() function and $var_* in dmsguestbook.php */
		$return_navigationsize = OneInput($key, $label, $type, $entries, $value, $char_lenght, $additional, $style, $tooltip, $jscript, $base64);
	}

	if($key == "formpos") {
		$label = "Guestbook form position:";
		$option = "top@bottom@";
		$value = $options["formpos"];
		$additional = "";
		$style = "";
		$tooltip = "Visible the guestbook input form on top or bottom";
		$jscript = "";
  		$return_formpos = SelectBox($key, $label, $option, $value, $additional, $style, $tooltip, $jscript);
	}

	if($key == "formposlink") {
		$label = "Link text:";
		$type = "text";
		$entries = 0;
		$value = $options["formposlink"];
		$char_lenght = "";
		$additional = "";
		$style = "width:150px;";
		$tooltip = "Define a link text if you selected \'bottom\'";
		$jscript = "";
		$base64 = 1;
		$return_formposlink = OneInput($key, $label, $type, $entries, $value, $char_lenght, $additional, $style, $tooltip, $jscript, $base64);
	}

	if($key == "sortitem") {
		$label = "Sort guestbook items:";
		$option = "ASC@DESC@";
		$value = $options["sortitem"];
		$additional = "";
		$style = "";
  		$tooltip = "DESC = Newer post first<br />ASC = Older post first";
  		$jscript = "";
  		$return_sortitem = SelectBox($key, $label, $option, $value, $additional, $style, $tooltip, $jscript);
	}

  	if($key == "dbid") {
  		$label = "Database id:";
		$entries = "0";
		$value = $options["dbid"];
		$additional = "";
		$style = "";
		$tooltip = "Use the database id to consecutively numbered each guestbook entry";
		$jscript = "";
  		$return_dbid = CheckBoxes($key, $label, $value, $entries, $additional, $style, $tooltip, $jscript);
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
		$tooltip = "Create your own input form template and use it is on your guestbook site<br /><br />See an examle in /template/form/default.tpl";
		$jscript = "";
  		$return_form_template = SelectBox($key, $label, $option, $value, $additional, $style, $tooltip, $jscript);
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
		$tooltip = "Create your own guestbook post template and use it is on your guestbook site<br /><br />See an examle in /template/post/default.tpl";
		$jscript = "";
  		$return_post_template = SelectBox($key, $label, $option, $value, $additional, $style, $tooltip, $jscript);
	}

	if($key == "nofollow") {
  		$label = "rel=\"nofollow\" tag for posted url's:";
		$entries = "0";
		$value = $options["nofollow"];
		$additional = "";
		$style = "";
		$tooltip = "Activate the nofollow tag for posted url\'s<br /><a href=\'http://en.wikipedia.org/wiki/Nofollow\' target=\'_blank\'>http://en.wikipedia.org/wiki/Nofollow</a>";
		$jscript = "";
  		$return_nofollow = CheckBoxes($key, $label, $value, $entries, $additional, $style, $tooltip, $jscript);
	}

	if($key == "additional_option") {
		unset($tmp);
		$abspath = str_replace("\\","/", ABSPATH);
				if ($handle = opendir($abspath . 'wp-content/plugins/dmsguestbook/module/')) {
    				while (false !== ($file = readdir($handle))) {
        				if ($file != "." && $file != ".." && $file != "README.txt") {
           				$tmp .= "$file" . "@";
        				}
    				}
    			closedir($handle);
				}
		$label = "Additional selectbox:";
		$option = "none@" . $tmp;
		$value = $options["additional_option"];
		$additional = "";
		$style = "";
		$tooltip = "Define a selectbox and fill this with your own values.<br />See some examples in your \'dmsguestbook/module\' folder.";
		$jscript = "";
  		$return_additional_option = SelectBox($key, $label, $option, $value, $additional, $style, $tooltip, $jscript);
	}

	if($key == "additional_option_title") {
		$label = "Additional selectbox title:";
		$type = "text";
		$entries = 0;
		$value = $options["additional_option_title"];
		$char_lenght = "";
		$additional = "";
		$style = "width:150px;";
		$tooltip = "This text will be shown on your input form guestbook page.<br />You could leave this textfield blank by using space character.";
		$jscript = "";
		$base64 = 1;
		$return_additional_option_title = OneInput($key, $label, $type, $entries, $value, $char_lenght, $additional, $style, $tooltip, $jscript, $base64);
	}

	if($key == "show_additional_option") {
  		$label = "Show additional value:";
		$entries = "0";
		$value = $options["show_additional_option"];
		$additional = "";
		$style = "";
		$tooltip = "Show additional text in each guestbook post.<br />You could edit the appearance in \'template/post/default.tpl\'<br />The default setting will be set the additional text on the footer of guestbook post.";
		$jscript = "";
  		$return_show_additional_option = CheckBoxes($key, $label, $value, $entries, $additional, $style, $tooltip, $jscript);
	}

	if($key == "separatorcolor") {
		$label = "Separator color:";
		$value = $options["separatorcolor"];
		$char_lenght = 6;
		$id = 1;
		$additional = "";
		$style = "width:150px;";
		$tooltip = "Separator between header and body in each entry<br /><br />Variable: {separatorcolor}";
		$return_separatorcolor = ColorInput($key, $label, $id, $value, $additional, $style, $tooltip, $jscript);
	}

	if($key == "bordercolor1") {
		$label = "Outside border color:";
		$value = $options["bordercolor1"];
		$char_lenght = 6;
		$id = 2;
		$additional = "";
		$style = "width:150px;";
		$tooltip = "Color of the outside box<br /><br />Variable: {bordercolor1}";
		$jscript = "";
		$return_bordercolor1 = ColorInput($key, $label, $id, $value, $additional, $style, $tooltip, $jscript);
	}

	if($key == "bordercolor2") {
		$label = "Textfield border color:";
		$value = $options["bordercolor2"];
		$char_lenght = 6;
		$id = 3;
		$additional = "";
		$style = "width:150px;";
		$tooltip = "Color of all textfield borders<br /><br />Variable: {bordercolor2}";
		$jscript = "";
		$return_bordercolor2 = ColorInput($key, $label, $id, $value, $additional, $style, $tooltip, $jscript);
	}

	if($key == "navigationcolor") {
		$label = "Navigation char color:";
		$value = $options["navigationcolor"];
		$char_lenght = 6;
		$id = 4;
		$additional = "";
		$style = "width:150px;";
		$tooltip = "Define the navigation color<br /><br />Variable: {navigationcolor}";
		$jscript = "";
		$return_navigationcolor = ColorInput($key, $label, $id, $value, $additional, $style, $tooltip, $jscript);
	}

	if($key == "fontcolor1") {
		$label = "Font color:";
		$value = $options["fontcolor1"];
		$char_lenght = 6;
		$id = 5;
		$additional = "";
		$style = "width:150px;";
		$tooltip = "Overall font color<br /><br />Variable: {fontcolor1}";
		$jscript = "";
		$return_fontcolor1 = ColorInput($key, $label, $id, $value, $additional, $style, $tooltip, $jscript);
	}

	if($key == "captcha_color") {
		$label = "Antispam image text color:";
		$value = $options["captcha_color"];
		$char_lenght = 6;
		$id = 6;
		$additional = "";
		$style = "width:150px;";
		$tooltip = "Antispam image text color<br /><br />Variable: {captcha_color}";
		$jscript = "";
		$return_captcha_color = ColorInput($key, $label, $id, $value, $additional, $style, $tooltip, $jscript);
	}

	if($key == "dateformat") {
		$label = "Date / Time format:";
		$type = "text";
		$entries = 0;
		$value = $options["dateformat"];
		$char_lenght = "";
		$additional = "";
		$style = "width:200px;";
		$tooltip = "Set the date and time format<br />More infos: <a href=\'http://www.php.net/manual/en/function.strftime.php\' target=\'_blank\'>http://www.php.net/manual/en/function.strftime.php</a>";
		$jscript = "";
		$base64 = 0; /* Do not use this unless you edit the preg_replace() in the create_options() function and $var_* in dmsguestbook.php */
		$return_dateformat = OneInput($key, $label, $type, $entries, $value, $char_lenght, $additional, $style, $tooltip, $jscript, $base64);
	}

	if($key == "setlocale") {
		$label = "Setlocale:";
		$type = "text";
		$entries = 0;
		$value = $options["setlocale"];
		$char_lenght = "";
		$additional = "";
		$style = "width:80px;";
		$tooltip = "Set your language: e.g. en_EN, de_DE, fr_FR, it_IT, de, ge ...<br />(must be installed on your system)";
		$jscript = "";
		$base64 = 0; /* Do not use this unless you edit the preg_replace() in the create_options() function and $var_* in dmsguestbook.php */
		$return_setlocale = OneInput($key, $label, $type, $entries, $value, $char_lenght, $additional, $style, $tooltip, $jscript, $base64);
	}

	if($key == "offset") {
		$label = "Offset:";
		$option = "-12@-11@-10@-9@-8@-7@-6@-5@-4@-3@-2@-1@0@+1@+2@+3@+4@+5@+6@+7@+8@+9@+10@+11@+12@";
		$value = $options["offset"];
		$additional = "";
		$style = "";
		$tooltip = "Time offset: Use this offset if your Wordpress installation is not in the same country where you live.<br />e.g. You live in London and the Wordpress installation is on a server in Chicago.<br />If You want to show the date in GMT (Greenwich Mean Time), set the offset -6 and check the correct time below.<br /><br /> Notice: Don\'t use the %z or %Z parameter if your offset isn\'t 0.";
		$jscript = "";
  		$return_offset = SelectBox($key, $label, $option, $value, $additional, $style, $tooltip, $jscript);
	}

	if($key == "send_mail") {
  		$label = "Send a mail:";
		$entries = "0";
		$value = $options["send_mail"];
		$additional = "";
		$style = "";
		$tooltip = "Receive a notification email when user write an new guestbook post";
		$jscript = "";
  		$return_send_mail = CheckBoxes($key, $label, $value, $entries, $additional, $style, $tooltip, $jscript);
	}

	if($key == "mail_adress") {
		$label = "Email adress:";
		$type = "text";
		$entries = 0;
		$value = $options["mail_adress"];
		$char_lenght = "";
		$additional = "";
		$style = "width:150px;";
		$tooltip = "The email address which the message to be sent is<br />Multiple email adresses are allowed, split these with the \';\' separator.<br />e.g. test1@example.com;test2@example.com;test3@example.com";
		$jscript = "";
		$base64 = 0; /* Do not use this unless you edit the preg_replace() in the create_options() function and $var_* in dmsguestbook.php */
		$return_mail_adress = OneInput($key, $label, $type, $entries, $value, $char_lenght, $additional, $style, $tooltip, $jscript, $base64);
	}

	if($key == "mail_method") {
		$label = "Send method:";
		$option = "Mail@SMTP@";
		$value = $options["mail_method"];
		$additional = "";
		$style = "";
		$tooltip = "Use PHP internal Mail function if your server supporting this.<br />A SMTP server could be need username and password as authentification which you must known.";
		$jscript = "onChange=\"smtpContainer();\"";
  		$return_mail_send_method = SelectBox($key, $label, $option, $value, $additional, $style, $tooltip, $jscript);
	}

	if($key == "smtp_host") {
		$label = "SMTP host:";
		$type = "text";
		$entries = 0;
		$value = $options["smtp_host"];
		$char_lenght = "";
		$additional = "";
		$style = "width:150px;";
		$tooltip = "The SMTP server which do you want to connect.";
		$jscript = "";
		$base64 = 0; /* Do not use this unless you edit the preg_replace() in the create_options() function and $var_* in dmsguestbook.php */
		$return_smtp_host = OneInput($key, $label, $type, $entries, $value, $char_lenght, $additional, $style, $tooltip, $jscript, $base64);
	}

	if($key == "smtp_port") {
		$label = "SMTP port:";
		$option = "25@465@587@";
		$value = $options["smtp_port"];
		$additional = "";
		$style = "";
		$tooltip = "25 = standard port<br />465 = SMTP over SSL port<br />587 = Alternative SMTP port<br /><br />Check your mail documentation for further information.";
		$jscript = "";
  		$return_smtp_port = SelectBox($key, $label, $option, $value, $additional, $style, $tooltip, $jscript);
	}

	if($key == "smtp_username") {
		$label = "SMTP username:";
		$type = "text";
		$entries = 0;
		$value = $options["smtp_username"];
		$char_lenght = "";
		$additional = "";
		$style = "width:150px;";
		$tooltip = "SMTP username if is needed.<br /><br />Check your mail documentation for further information.";
		$jscript = "";
		$base64 = 0; /* Do not use this unless you edit the preg_replace() in the create_options() function and $var_* in dmsguestbook.php */
		$return_smtp_username = OneInput($key, $label, $type, $entries, $value, $char_lenght, $additional, $style, $tooltip, $jscript, $base64);
	}

	if($key == "smtp_password") {
		$label = "SMTP password:";
		$type = "password";
		$entries = 0;
		$value = $options["smtp_password"];
		$char_lenght = "";
		$additional = "";
		$style = "width:140px;";
		$tooltip = "SMTP password if is needed.<br /><br />Check your mail documentation for further information.";
		$jscript = "";
		$base64 = 0; /* Do not use this unless you edit the preg_replace() in the create_options() function and $var_* in dmsguestbook.php */
		$return_smtp_password = OneInput($key, $label, $type, $entries, $value, $char_lenght, $additional, $style, $tooltip, $jscript, $base64);
	}

	if($key == "smtp_auth") {
  		$label = "SMTP authentification:";
		$entries = "0";
		$value = $options["smtp_auth"];
		$additional = "";
		$style = "";
		$tooltip = "SMTP authentification if is needed. <br /><br />Check your mail documentation for further information.";
		$jscript = "";
  		$return_smtp_auth = CheckBoxes($key, $label, $value, $entries, $additional, $style, $tooltip, $jscript);
	}

	if($key == "smtp_ssl") {
  		$label = "SMTP SSL:";
		$entries = "0";
		$value = $options["smtp_ssl"];
		$additional = "";
		$style = "";
		$tooltip = "SMTP SSL (secure socket layer) if is needed.<br /><br />Check your mail documentation for further information.";
		$jscript = "";
  		$return_smtp_ssl = CheckBoxes($key, $label, $value, $entries, $additional, $style, $tooltip, $jscript);
	}

	if($key == "akismet") {
		$CheckAkismet = CheckAkismet();
			if($CheckAkismet != "") {
			$akismet_description ="DMSGuestbook has found an Akismet key: $CheckAkismet";
			}
			else {
			     $akismet_description = "No WordPress API key for Akismet was found! Activate the Akismet plugin and create a key. More information under: <a href='http://akismet.com/' target='_blank'>http://akismet.com/</a> and <a href='http://en.wordpress.com/api-keys/' target='_blank'>http://en.wordpress.com/api-keys/</a>";
			     }

  		$label = "Akismet:";
		$entries = "0";
		$value = $options["akismet"];
		$additional = "";
		$style = "";
		$tooltip = "More infos: <a href=\'http://akismet.com/\' target=\'_blank\'>http://akismet.com</a>";
		$jscript = "onClick=\"akismetContainer();\"";
  		$return_akismet = CheckBoxes($key, $label, $value, $entries, $additional, $style, $tooltip, $jscript);
  		$return_akismet_key = "$akismet_description";
	}

	if($key == "akismet_action") {
  		$label = "Move spam to the spam folder@Block guestbook post if spam is found on it@";
		$entries = "1";
		$value = $options["akismet_action"];
		$additional = "";
		$style = "";
		$tooltip = "What should Akismet do if spam are detected?";
		$jscript = "";
  		$return_akismet_action = RadioBoxes($key, $label, $value, $entries, $additional, $style, $tooltip, $jscript);
	}


	if($key == "require_antispam") {
  		$label = "Antispam off:@Antispam image:@Antispam mathematic figures:@reCAPTCHA:@";
		$entries = "3";
		$value = $options["require_antispam"];
		$additional = "";
		$style = "";
		$tooltip = "Image:<br /><img src=\'../wp-content/plugins/dmsguestbook/captcha/captcha.php\' /><br />If you don\'t see the image here, check the xfiles.ttf and captcha.png permission in your captcha folder<br /><br />Mathematic figures:<br />4 + 9 = 13<br /><br />reCAPTCHA: <a href=\'http://recaptcha.net/\' target=\'_blank\'>learn more about reCAPTCHA</a>";
		$jscript = "onClick=\"recaptchaKeys();\"";
  		$return_require_antispam = RadioBoxes($key, $label, $value, $entries, $additional, $style, $tooltip, $jscript);
	}

	if($key == "recaptcha_publickey") {
		$label = "reCAPTCHA public key:";
		$type = "text";
		$entries = 0;
		$value = $options["recaptcha_publickey"];
		$char_lenght = "";
		$additional = "";
		$style = "width:140px;";
		$tooltip = "Enter here you reCAPTCHA public key.<br /><a href=\'http://recaptcha.net/\' target=\'_blank\'>Learn more about reCAPTCHA</a>.";
		$jscript = "";
		$base64 = 0; /* Do not use this unless you edit the preg_replace() in the create_options() function and $var_* in dmsguestbook.php */
		$return_recaptcha_publickey = OneInput($key, $label, $type, $entries, $value, $char_lenght, $additional, $style, $tooltip, $jscript, $base64);
	}

	if($key == "recaptcha_privatekey") {
		$label = "reCAPTCHA private key:";
		$type = "text";
		$entries = 0;
		$value = $options["recaptcha_privatekey"];
		$char_lenght = "";
		$additional = "";
		$style = "width:140px;";
		$tooltip = "Enter here you reCAPTCHA private key.<br /><a href=\'http://recaptcha.net/\' target=\'_blank\'>Learn more about reCAPTCHA</a>.";
		$jscript = "";
		$base64 = 0; /* Do not use this unless you edit the preg_replace() in the create_options() function and $var_* in dmsguestbook.php */
		$return_recaptcha_privatekey = OneInput($key, $label, $type, $entries, $value, $char_lenght, $additional, $style, $tooltip, $jscript, $base64);
	}

	if($key == "antispam_key") {
	$value=RandomAntispamKey();
		$label = "Antispam key:";
		$type = "hidden";
		$entries = 0;
		$value = RandomAntispamKey();
		$char_lenght = 20;
		$additional = $value;
		$style = "width:0px;";
		$tooltip = "Set a random key to prevent spam.<br />Every page refresh will create a new key which can be saved.<br />It\'s used for: Antispam image & Antispam mathematic figures.";
		$jscript = "";
		$base64 = 0; /* Do not use this unless you edit the preg_replace() in the create_options() function and $var_* in dmsguestbook.php */
		$return_antispam_key = OneInput($key, $label, $type, $entries, $value, $char_lenght, $additional, $style, $tooltip, $jscript, $base64);
	}

	if($key == "require_email") {
  		$label = "Email:";
		$entries = "0";
		$value = $options["require_email"];
		$additional = "";
		$style = "";
  		$tooltip = "User must fill out the email text field";
  		$jscript = "";
  		$return_require_email = CheckBoxes($key, $label, $value, $entries, $additional, $style, $tooltip, $jscript);
	}

	if($key == "require_url") {
  		$label = "Website:";
		$entries = "0";
		$value = $options["require_url"];
		$additional = "";
		$style = "";
		$tooltip = "User must fill out the website text field";
		$jscript = "";
  		$return_require_url = CheckBoxes($key, $label, $value, $entries, $additional, $style, $tooltip, $jscript);
	}

	if($key == "mandatory_char") {
		$label = "Mandatory char:";
		$type = "text";
		$entries = 0;
		$value = $options["mandatory_char"];
		$char_lenght = 1;
		$additional = "";
		$style = "width:20px;";
		$tooltip = "Mandatory char were to display on guestbook input form";
		$jscript = "";
		$base64 = 1;
		$return_mandatory_char = OneInput($key, $label, $type, $entries, $value, $char_lenght, $additional, $style, $tooltip, $jscript, $base64);
	}

	if($key == "show_email") {
  		$label = "Show email:";
		$entries = "0";
		$value = $options["show_email"];
		$additional = "";
		$style = "";
		$tooltip = "Visible email for everyone in each post";
		$jscript = "";
  		$return_show_email = CheckBoxes($key, $label, $value, $entries, $additional, $style, $tooltip, $jscript);
	}

	if($key == "show_url") {
  		$label = "Show website:";
		$entries = "0";
		$value = $options["show_url"];
		$additional = "";
		$style = "";
		$tooltip = "Visible website for everyone in each post";
		$jscript = "";
  		$return_show_url = CheckBoxes($key, $label, $value, $entries, $additional, $style, $tooltip, $jscript);
	}

	if($key == "show_ip") {
  		$label = "Show ip adress:";
		$entries = "0";
		$value = $options["show_ip"];
		$additional = "";
		$style = "";
		$tooltip = "Visible ip for everyone in each post";
		$jscript = "";
  		$return_show_ip = CheckBoxes($key, $label, $value, $entries, $additional, $style, $tooltip, $jscript);
	}

	if($key == "ip_mask") {
		$label = "Mask ip adress:";
		$option = "*.123.123.123@*.*.123.123@*.*.*.123@123.123.123.*@123.123.*.*@123.*.*.*@";
		$value = $options["ip_mask"];
		$additional = "";
		$style = "";
  		$tooltip = "Mask ip adress if this is visible";
  		$jscript = "";
  		$return_ip_mask = SelectBox($key, $label, $option, $value, $additional, $style, $tooltip, $jscript);
	}

	if($key == "email_image_path") {
			$part1=explode("/", $options["email_image_path"]);
			$image=end($part1);
		$label = "Email image path:";
		$type = "text";
		$entries = 0;
		$value = $options["email_image_path"];
		$char_lenght = "";
		$additional = "";
		$style = "width:200px;";
		$tooltip = "Email image path:<br /><a href=\'$options[email_image_path]\' target=\'_blank\'>$options[email_image_path]</a><br /><br />Actually image: <img src=\'$options[email_image_path]\'>";
		$jscript = "";
		$base64 = 0; /* Do not use this unless you edit the preg_replace() in the create_options() function and $var_* in dmsguestbook.php */
		$return_email_image_path = OneInput($key, $label, $type, $entries, $value, $char_lenght, $additional, $style, $tooltip, $jscript, $base64);
	}

	if($key == "website_image_path") {
			$part1=explode("/", $options["website_image_path"]);
			$image=end($part1);
		$label = "Website image path:";
		$type = "text";
		$entries = 0;
		$value = $options["website_image_path"];
		$char_lenght = "";
		$additional = "";
		$style = "width:200px;";
		$tooltip = "Website image path:<br /><a href=\'$options[website_image_path]\' target=\'_blank\'>$options[website_image_path]</a><br /><br />Actually image: <img src=\'$options[website_image_path]\'>";
		$jscript = "";
		$base64 = 0; /* Do not use this unless you edit the preg_replace() in the create_options() function and $var_* in dmsguestbook.php */
		$return_website_image_path = OneInput($key, $label, $type, $entries, $value, $char_lenght, $additional, $style, $tooltip, $jscript, $base64);
	}

	if($key == "admin_review") {
  		$label = "Admin must every post review:";
		$entries = "0";
		$value = $options["admin_review"];
		$additional = "";
		$style = "";
		$tooltip = "Admin must review every post before this can display on the page.<br />You can edit the guestbook review status under \'Entries\'";
		$jscript = "";
  		$return_admin_review = CheckBoxes($key, $label, $value, $entries, $additional, $style, $tooltip, $jscript);
	}

	if($key == "url_overruled") {
		$label = "URL overrule:";
		$type = "text";
		$entries = 0;
		$value = $options["url_overruled"];
		$additional = "";
		$style = "width:200px;";
		$tooltip = "You can overrule this link if you have trouble with the guestbook form submit.<br /><br />Examples:<br />$url/?p=3<br />$url/?page_id=3<br />$url/3/<br />$url/YourGuestBookName";
		$jscript = "";
		$base64 = 0; /* Do not use this unless you edit the preg_replace() in the create_options() function and $var_* in dmsguestbook.php */
		$return_url_overruled = OneInput($key, $label, $type, $entries, $value, $char_lenght, $additional, $style, $tooltip, $jscript, $base64);
	}

	if($key == "gravatar") {
  		$label = "User can use Gravatar:";
		$entries = "0";
		$value = $options["gravatar"];
		$additional = "";
		$style = "";
		$tooltip = "More infos: <a href=\'http://en.gravatar.com\' target=\'_blank\'>http://en.gravatar.com</a>";
		$jscript = "";
  		$return_gravatar = CheckBoxes($key, $label, $value, $entries, $additional, $style, $tooltip, $jscript);
	}

	if($key == "gravatar_rating") {
		$label = "Gravatar rating:";
		$option = "G@PG@R@X@";
		$value = $options["gravatar_rating"];
		$additional = "";
		$style = "";
  		$tooltip = "You can specify a rating of G, PG, R, or X.<br />[G] A G rated gravatar is suitable for display on all websites with any audience type.<br />[PG] PG rated gravatars may contain rude gestures, provocatively dressed individuals, the lesser swear words, or mild violence.<br />[R] R rated gravatars may contain such things as harsh profanity, intense violence, nudity, or hard drug use.<br />[X] X rated gravatars may contain hardcore sexual imagery or extremely disturbing violence.";
  		$jscript = "";
  		$return_gravatar_rating = SelectBox($key, $label, $option, $value, $additional, $style, $tooltip, $jscript);
	}

	if($key == "gravatar_size") {
		$label = "Gravatar size:";
		$type = "text";
		$entries = 0;
		$char_lenght = 3;
		$value = $options["gravatar_size"];
		$additional = "px";
		$style = "width:30px;";
		$tooltip = "Image size in pixel";
		$jscript = "";
		$base64 = 0; /* Do not use this unless you edit the preg_replace() in the create_options() function and $var_* in dmsguestbook.php */
		$return_gravatar_size = OneInput($key, $label, $type, $entries, $value, $char_lenght, $additional, $style, $tooltip, $jscript, $base64);
	}

	if($key == "role1") {
		$label = "DMSGuestbook settings:";
		$option = "Administrator@Editor@Author@Contributor@Subscriber@";
		$value = $options["role1"];
		$additional = "";
		$style = "";
  		$tooltip = "This role affects with:<br />- Database settings<br />- Guestbook settings<br />- Language settings<br />- phpinfo";
  		$jscript = "";
  		$return_role1 = SelectBox($key, $label, $option, $value, $additional, $style, $tooltip, $jscript);
	}

	if($key == "role2") {
		$label = "Entries:";
		$option = "Administrator@Editor@Author@Contributor@Subscriber@";
		$value = $options["role2"];
		$additional = "";
		$style = "";
  		$tooltip = "This role affect with:<br />- Guestbook entries (show, edit, delete)";
  		$jscript = "";
  		$return_role2 = SelectBox($key, $label, $option, $value, $additional, $style, $tooltip, $jscript);
	}

	if($key == "role3") {
		$label = "Spam:";
		$option = "Administrator@Editor@Author@Contributor@Subscriber@";
		$value = $options["role3"];
		$additional = "";
		$style = "";
  		$tooltip = "This role affect with:<br />- Spam entries (show, edit, delete)";
  		$jscript = "";
  		$return_role3 = SelectBox($key, $label, $option, $value, $additional, $style, $tooltip, $jscript);
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

	   $tooltip = "{width1} = Guestbook width<br />{width2} = Separator width<br />{position1} = Relative guestbook position (left to right)<br />{separatorcolor} = Separator between header and body in each entry<br />{bordercolor1} = Border of the outside box<br />{bordercolor2} = Color of all textfield border<br />{navigationcolor} = Define the navigation color<br />{fontcolor1} = Overall font color<br />{navigationsize} = Size of both navigation chars<br />{captcha_color} = Antispam image text color<br /><br />Stylesheet (CSS) Help & Tutorials:<br />English: <a href=\'http://www.html.net/tutorials/css/\' target=\'_blank\'>http://www.html.net/tutorials/css/</a><br />German: <a href=\'http://www.css4you.de/\' target=\'_blank\'>http://www.css4you.de/</a><br />Or ask Google and friends :-)";


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
				 	$return_css .= "<tr><td style='text-align:center;'><br />($y)</td>
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

			$tooltip = "Class heredity:<br /><br />e.g.<br /><b>a.</b>css_navigation_char<b>:hover</b> {color:#ff0000;}<br />All url link with css_navigation_char (navigation link)<br />become hover color red when user drag over it<br /><br /><b>td</b>.css_guestbook_message_nr_name {background-color:#00ff00;}<br />All td with css_guestbook_message_nr_name (guestbook name & id)<br />become background color green<br /><br />";
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
				<div class='dbx-handle' title='Basic'><b style='color:#464646;'>Basic</b></div>
				<ul class='dbx-content'>
				<li>&nbsp;</li>
				<li>$return_page_id</li>
				<li>$return_step</li>
				<li>$return_formpos</li>
				<li>$return_formposlink</li>
				<li>$return_messagetext_length</li>
				</ul>
			</div>";

	echo "<div class='dbx-box'>
				<div class='dbx-handle' title='Extended'><b style='color:#464646;'>Extended</b></div>
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
				<li>$return_additional_option</li>
				<li>$return_additional_option_title</li>
				<li>$return_show_additional_option</li>
				</ul>
			</div>";

	echo "<div class='dbx-box'>
				<div class='dbx-handle' title='Color'><b style='color:#464646;'>Color</b></div>
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

			setlocale(LC_TIME, $options["setlocale"]);
			$offset = mktime(date("H")+$options["offset"], date("i"), date("s"), date("m")  , date("d"), date("Y"));
     		$time_example = htmlentities(strftime($options["dateformat"], $offset), ENT_QUOTES);

			echo "<div class='dbx-box'>
				<div class='dbx-handle' title='Time / Date'><b style='color:#464646;'>Time / Date</b></div>
				<ul class='dbx-content'>
				<li>&nbsp;</li>
				<li>$return_dateformat</li>
				<li>$return_setlocale</li>
				<li>$return_offset</li>
				<li>&nbsp;</li>
				<li>Example: $time_example</li>
				<li>&nbsp;</li>
				</ul>
			</div>";

			echo "<div class='dbx-box'>
				<div class='dbx-handle' title='Email Notification'><b style='color:#464646;'>Email Notification</b></div>
				<ul class='dbx-content'>
				<li>&nbsp;</li>
				<li>$return_send_mail</li>
				<li>$return_mail_adress</li>
				<li>$return_mail_send_method</li>
				<li>&nbsp;</li>
				<span style='display:none;' id='smtp_container'>
				<li>$return_smtp_host</li>
				<li>$return_smtp_port</li>
				<li>&nbsp;</li>
				<li>$return_smtp_auth</li>
				<li>$return_smtp_ssl</li>
				<li>$return_smtp_username</li>
				<li>$return_smtp_password</li>
				</span>
				<li>&nbsp;</li>
				</ul>
			</div>";


?>
				<!-- Check SMTP is on or not-->
				<script type="text/javascript">
					smtpContainer();
						function smtpContainer() {
						var mail_method = document.getElementById('mail_method').value;

						if(mail_method == "SMTP") {
						document.getElementById('smtp_container').style.display="block";
						}

						if(mail_method == "Mail") {
						document.getElementById('smtp_container').style.display="none";
						}
					}
				</script>

<?php

			echo "<div class='dbx-box'>
				<div class='dbx-handle' title='Captcha / Akismet'><b style='color:#464646;'>Captcha / Akismet</b></div>
				<ul class='dbx-content'>
				<li>&nbsp;</li>
				<li>$return_require_antispam</li>
				<span style='display:none;' id='recaptcha_keys'>
				<li>$return_recaptcha_publickey</li>
				<li>$return_recaptcha_privatekey</li>
				<li>&nbsp;</li>
				</span>
				<li>$return_antispam_key</li>
				<li>&nbsp;</li>
				<li>&nbsp;</li>
				<li>$return_akismet</li>
				<span style='display:none;' id='akismet_container'>
				<li>$return_akismet_key</li>
				<li>&nbsp;</li>
				<li>$return_akismet_action</li>
				</span>
				<li>&nbsp;</li>
				</ul>
			</div>";


?>
			<!-- Check reCAPTCHA is on or not-->
				<script type="text/javascript">
					recaptchaKeys();
						function recaptchaKeys() {

						if(document.form1.require_antispam[3].checked == true) {
						document.getElementById('recaptcha_keys').style.display="block";
						}

						if(document.form1.require_antispam[3].checked == false) {
						document.getElementById('recaptcha_keys').style.display="none";
						}
					}
				</script>

			<!-- Check Akismet is on or not-->
				<script type="text/javascript">
					akismetContainer();
						function akismetContainer() {
						var akismet = document.getElementById('akismet').checked;

						if(akismet == "1") {
						document.getElementById('akismet_container').style.display="block";
						}

						if(akismet == "0") {
						document.getElementById('akismet_container').style.display="none";
						}
					}
				</script>
<?php



			echo "<div class='dbx-box'>
				<div class='dbx-handle' title='Mandatory'><b style='color:#464646;'>Mandatory</b></div>
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
				<div class='dbx-handle' title='Gravatar'><b style='color:#464646;'>Gravatar</b></div>
				<ul class='dbx-content'>
				<li>&nbsp;</li>
				<li>$return_gravatar</li>
				<li>$return_gravatar_rating</li>
				<li>$return_gravatar_size</li>
				<li>&nbsp;</li>
				</ul>
			</div>";

			echo "<div class='dbx-box'>
				<div class='dbx-handle' title='Miscellaneous'><b style='color:#464646;'>Miscellaneous</b></div>
				<ul class='dbx-content'>
				<li>&nbsp;</li>
				<li>$return_email_image_path</li>
				<li>$return_website_image_path</li>
				<li>$return_admin_review</li>
				<li>$return_url_overruled</li>
				<li>&nbsp;</li>
				</ul>
			</div>";

			echo "<div class='dbx-box'>
				<div class='dbx-handle' title='Miscellaneous'><b style='color:#464646;'>Role</b></div>
				<ul class='dbx-content'>
				<li>&nbsp;</li>
				<li>$return_role1</li>
				<li>$return_role2</li>
				<li>$return_role3</li>
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
				<div class='dbx-handle' title='CSS'><b style='color:#464646;'>CSS</b></div>
				<ul class='dbx-content'>
				<li>&nbsp;</li>
				<li>If dmsguestbook.css is exist and writable, all CSS settings will be read from it.<br />
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
echo "<input class='button-primary action' style='font-weight:bold; margin:10px 0px; width:100px;' type='submit' value='Save' />";
echo "</form></td>";

	 	#restore default settings button -->
		echo "<td><form name='form3' method='post' action='$location'>
		<input name='action2' value='default_settings' type='hidden' />
		<input class='button-secondary action' style='font-weight:bold; margin:10px 0px;' type='submit'
		value='Restore default settings - All data will be replaced' onclick=\"return confirm('Would you really like to restore all data?');\" />
     	</form></td>";

echo "</tr></table>";
?>
<script type="text/javascript">if(typeof wpOnload=='function')wpOnload();</script>
<?php
}
?>

<!-- language -->
<?php
if($_REQUEST[advanced]==1) {

	$Role1 = CheckRole($options["role1"],0);
	if(!current_user_can("level_" . $Role1) && ROLE != 0) {
		CheckRole($options["role1"],1);
	exit;
	}

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
	if(preg_match('/^[a-z0-9_]+\.+(txt)/i', "$_REQUEST[file]")==1) {
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

			/* Convert text to base64 if is selected */
			if(BASE64 == 1 & $POSTVARIABLE["base64-$key"] == 1) {
			$POSTVARIABLE[$key] = base64_encode($POSTVARIABLE[$key]);
			}

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
							$multi_gb = explode(",", $POSTVARIABLE[$key]);
							unset($POSTVARIABLE[$key]);
							$multi_gb = array_unique($multi_gb);
								for($m=0; $m<count($multi_gb); $m++) {
									if(is_numeric($multi_gb[$m])) {
									$POSTVARIABLE[$key] .= $multi_gb[$m] . ",";
									}
								}

							$multi_lang = explode(",", $POSTVARIABLE[language]);
							unset($POSTVARIABLE[language]);
								for($m=0; $m<count($multi_lang); $m++) {
									if(is_string($multi_lang[$m])) {
									$POSTVARIABLE[language] .= $multi_lang[$m] . ",";
									}
								}
							$POSTVARIABLE[language] = rtrim($POSTVARIABLE[language], ",");


							$POSTVARIABLE[$key] = rtrim($POSTVARIABLE[$key], ",");
							$save_to_db.="<" . $key . ">" . sprintf("%s",$POSTVARIABLE[$key]) . "</" . $key . ">\r\n";
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
				$notice ="/*\nUse the DMSGuestbook admin interface for change these css settings.\nDon't edit this file direct, your change could be overwrite by the DMSGuestbook admin.\nIf dmsguestbook.css is exist and writable, all CSS settings will be read from it.\nOtherwise these settings will be load from the database.\n*/\n\n";
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

		// all guestbooks are selected when this site is loading
		if($_REQUEST[guestbook]=="") {$_REQUEST[guestbook]="all";}

		$options=create_options();

		// check Akismet is activated
		$CheckAkismet = CheckAkismet();
			if($CheckAkismet != "" && $options[akismet] == 1) {
			}

?>
		<div class="wrap">
		<h2>Entries</h2>

<?php

		/* maximum guestbook entries were displayed on page */
		if($_REQUEST[tinymce]==1) {
		$gb_step=1;
		$editor = "WHERE id = '$_REQUEST[id]'";
		}

		if($_REQUEST[htmleditor]==1) {
		$gb_step=1;
		$editor = "WHERE id = '$_REQUEST[id]'";
		}

		if($_REQUEST[approval]==1) {
		$flag="AND flag='1'";
		} else {
			   $flag="";
			   }

		if($_REQUEST[htmleditor]!=1 && $_REQUEST[tinymce]!=1) {
		$gb_step=$options["step"];

			if($_REQUEST[search]!="") {
			$_REQUEST[search] = preg_replace("/[\<\>\"\'\`\´]+/i", "", $_REQUEST[search]);

				if($_REQUEST[guestbook]=="all") {
				$search_param ="WHERE spam = '0' AND (name LIKE '$_REQUEST[search]' OR email LIKE '$_REQUEST[search]' OR url LIKE '$_REQUEST[search]' OR ip
				LIKE '$_REQUEST[search]' OR message LIKE '$_REQUEST[search]')";
				} else {
					   $search_param ="WHERE guestbook = '" . sprintf("%d", $_REQUEST[guestbook]) . "' AND spam = '0' AND (name LIKE '$_REQUEST[search]' OR email LIKE '$_REQUEST[search]'
					   OR url LIKE '$_REQUEST[search]' OR ip LIKE '$_REQUEST[search]' OR message LIKE '$_REQUEST[search]')";
					   }
			}
			else {
			     $search_param="";
			     	if($_REQUEST[guestbook]=="all") {
			     	$editor = "WHERE spam = '0'";
			     	} else {
			     		   $editor = "WHERE guestbook = '" . sprintf("%d", $_REQUEST[guestbook]) . "' AND spam = '0'";
			     		   }
			     }
		}


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
		$table_posts = $wpdb->prefix . "posts";

		/* count all search database entries / mysql_query */
    	$query0 = $wpdb->get_results("SELECT * FROM $table_name $search_param $editor $flag");
    	$num_rows0 = $wpdb->num_rows;

		/* read all search guestbook entries */
		$query1 = $wpdb->get_results("SELECT * FROM $table_name $search_param $editor $flag ORDER BY id " . sprintf("%s", $options["sortitem"]) . " LIMIT
		" . sprintf("%d", $_REQUEST[from]) . "," . sprintf("%d", $gb_step) . ";");
		$num_rows1 = $wpdb->num_rows;
?>
		<br />
		<br />
		<table>
		<tr>
		<td style="vertical-align: top;">
		<table style="background-color:#fff; border:1px solid #aaaaaa; width:450px; padding:5px;">
		<tr>
		<td><form name="search" method="post" action="<?php echo $location ?>">
		<input style="width:250px;" type="text" name="search" value="<?php echo $_REQUEST[search]; ?>" />
		<input type="hidden" name="guestbook" value="<?php echo $_REQUEST[guestbook]; ?>" />
		<input class="button-secondary action" style="font-weight:bold;" type="submit" value="Search" />
		<input class="button-secondary action" style="font-weight:bold;" type="button" value="Clear" onClick="document.search.search.value = ''"; />
	 	</form></td>
	 	</tr>
	 	<tr>
	 	<td>Search in: Name, Message, IP, Website and Email Fields.<br />Use % to specify search patterns. e.g. %fox% or %fox or fox%</td>
	 	</tr>
	 	</table>
		</td>

		<td style="width:20px;"></td>
		<?php if($_REQUEST[guestbook] == "all") {$active="all";} else {$active=$_REQUEST[guestbook]+1;} ?>
		<td style="vertical-align: top;">
		<table style="background-color:#fff; border:1px solid #aaaaaa; width:400px; padding:5px;">
		<tr><td><b style="font-size:14px;">Active: Guestbook <?php echo $active; ?></b></td></tr>
		<?php
			$multi_page_id = explode(",", $options["page_id"]);
			echo "<tr><td><a href='$location?page=Entries&guestbook=all'><b>Guestbook: All</b></a></td></tr>";

			$guestbook_count=1;
			for($m=0; $m<count($multi_page_id); $m++) {
			$query_posts = $wpdb->get_results("SELECT ID, post_title FROM $table_posts WHERE ID = $multi_page_id[$m] ORDER BY id ASC");
				foreach ($query_posts as $result) {
				echo "<tr><td><a href='$location?page=Entries&guestbook=$m'><b>Guestbook #" . $guestbook_count . "</b></a> (Page: $result->post_title || ID: $result->ID)</td></tr>";
				}
			$guestbook_count++;
			}
		?>
		</table>
		</td>
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
			echo "<a style='color:#bb1100; text-decoration:none;' href='admin.php?page=Entries&from=$q&select=$y&guestbook=$_REQUEST[guestbook]&search=$_REQUEST[search]&approval=$_REQUEST[approval]'> $y</a>";
			}
			else {
				 echo "<a style='color:#000000; text-decoration:none;' href='admin.php?page=Entries&from=$q&select=$y&guestbook=$_REQUEST[guestbook]&search=$_REQUEST[search]&approval=$_REQUEST[approval]'> $y</a>";
				 }
		}
		echo "</div>
		<br /><br />";




	# overview
	if($_REQUEST[tinymce]!=1 && $_REQUEST[htmleditor]!=1) {
		echo "
		<form name='myForm' method='post' action='$location'>
		<p>
		<select name='action'>
		<option value='-1'>Bulk Action</option>
		<option value='markasspam'>Mark as Spam</option>
		<option value='deletepost2'>Delete</option>
		<option value='setvisible'>Set post visible</option>
		<option value='sethidden'>Set post hidden</option>
		</select>

		<input class='button-secondary action' type='submit' value='Apply'
		onclick=\"return confirm('Would you really like to do this?');\" /></p>

	    <table class='widefat comments' cellspacing='0'>
		<thead>
		<tr>
		<th style='padding:7px 7px 7px 0px; width:20px;'><input type='checkbox' id='selectall1' name='selectall1' onClick=\"AllSelectboxes1();\"></th>
		<th style='padding:0px 5px 0px 5px; width:20px;'>ID</th>
	 	<th style='padding:0px 5px 0px 5px; width:100px;'>Author</th>
	 	<th style='padding:0px 5px 0px 5px; width:300px;'>Comment</th>
		<th style='padding:0px 5px 0px 5px; width:200px;'>Action</th>
		</tr>
		</thead>";
	}

			if($num_rows0 == 0) {
			echo "<tr><td></td><td></td><td></td><td style='text-align:center;'><b>No entries found.</b></td><td></td></tr>";
			}

			setlocale(LC_TIME, $options["setlocale"]);
			foreach ($query1 as $result) {
	 			// build the data / time variable
				$sec=date("s", "$result->date");
				$min=date("i", "$result->date");
				$hour=date("H", "$result->date");
				$day=date("d", "$result->date");
				$month=date("m", "$result->date");
				$year=date("Y", "$result->date");
				$date = htmlentities(strftime ($options["dateformat"], mktime ($hour, $min, $sec, $month, $day, $year)));

				$gbname 	= preg_replace("/[\\\\\"=\(\)\{\}]+/i", "", stripslashes($result->name));
				$gbemail 	= preg_replace("/[^a-z-0-9-_\.@]+/i", "", $result->email);
				$gburl 		= preg_replace("/[^a-z-0-9-_,.:?&%=\/]+/i", "", $result->url);
				$gbip 		= preg_replace("/[^0-9\.]+/i", "", $result->ip);
				$gbmsg 		= preg_replace("/(\<\/textarea\>)||(\\\\)/i", "", stripslashes($result->message));
				$gbguestbook= preg_replace("/[^0-9]+/i", "", $result->guestbook);
				$gbadditional= $result->additional;

				$guestbook = ($result->guestbook +1);

				if($result->email=="") {
				$email = "";
				} else {
					   $email = "<a href='mailto:$result->email'>$result->email</a><br />";
					   }

				if($gburl=="http://" || $gburl=="http://") {
				$concat_name_url = $gbname;
				} else {
					   $concat_name_url = "<a href='$gburl' target='_blank'>$gbname</a>";
					   }

				if($gbadditional !="") {
				$gbadditional2 = "<br />\"$gbadditional\"";
				}

		if($_REQUEST[tinymce]!=1 && $_REQUEST[htmleditor]!=1) {
			if($result->flag == 1) {
			$adminreview="<a style='color:#D98500;' href='admin.php?page=Entries&action=adminreview&flag=0&id=$result->id&from=$_REQUEST[from]&select=$_REQUEST[select]&guestbook=$_REQUEST[guestbook]&search=$_REQUEST[search]&approval=$_REQUEST[approval]'>[Visible]</a> | ";
			$adminreview_color="style='background-color:#E5CDCD;'";
			}
			else {
			     $adminreview = "<a href='admin.php?page=Entries&action=adminreview&flag=1&id=$result->id&from=$_REQUEST[from]&select=$_REQUEST[select]&guestbook=$_REQUEST[guestbook]&search=$_REQUEST[search]&approval=$_REQUEST[approval]'>[Hidden]</a> | ";
			     $adminreview_color="";
			     }

			echo "<tr>";
			echo "<td $adminreview_color><input type='checkbox' id='selectpost' name='selectpost[]' value='$result->id'></td>";
			echo "<td $adminreview_color>$result->id</td>";
			echo "<td $adminreview_color><b>$concat_name_url</b><br />$email" . "$gbip<br />Guestbook #$guestbook" . "$gbadditional2</td>";
			echo "<td $adminreview_color><span style='color:#777777;'>Submitted on </span>$date<br />$gbmsg</td>";

			$action_tinymce = "<a href='admin.php?page=Entries&tinymce=1&guestbook=$_REQUEST[guestbook]&id=$result->id'>Edit (TinyMCE)</a>";
			$action_htmleditor = "<a href='admin.php?page=Entries&htmleditor=1&guestbook=$_REQUEST[guestbook]&id=$result->id'>Edit (HTML)</a>";
			$action_spam = "<a href='admin.php?page=Entries&action=markasspam&guestbook=$_REQUEST[guestbook]&id=$result->id'>Spam</a>";
			$action_delete = "<a style='color:#D54E21;' href='admin.php?page=Entries&action=deletepost&guestbook=$_REQUEST[guestbook]&id=$result->id' onclick=\"return confirm('Would you really like to delete this dataset?');\">Delete</a>";
			echo "<td $adminreview_color>";
			echo "$adminreview $action_tinymce | $action_htmleditor | $action_spam | $action_delete";
			echo "</td>";
			echo "</tr>";
		}

			}
		echo "</form>";

	if($_REQUEST[tinymce]!=1 && $_REQUEST[htmleditor]!=1) {
		echo "<thead>
		<tr>
		<th style='padding:7px 7px 7px 0px; width:20px;'><input type='checkbox' id='selectall2' name='selectall2' onClick=\"AllSelectboxes2();\"></th>
		<th style='padding:0px 5px 0px 5px; width:20px;'>ID</th>
	 	<th style='padding:0px 5px 0px 5px; width:100px;'>Author</th>
	 	<th style='padding:0px 5px 0px 5px; width:300px;'>Comment</th>
		<th style='padding:0px 5px 0px 5px; width:200px;'>Action</th>
		</tr>
		</thead>";
	}




	if(($_REQUEST[tinymce]==1 || $_REQUEST[htmleditor]==1) && $result->id !="" & $result->spam ==0) {
		echo "
		<table class='widefat comments' cellspacing='0'>

		<thead>
		<tr>
	 	<th style='padding:0px 5px 0px 5px; width:450px;'>Author</th>
	 	<th style='padding:0px 5px 0px 5px;'>Comment</th>
		<th style='padding:0px 5px 0px 5px; width:50px;'>Action</th>
		</tr>
		</thead>";

		echo "<a class='button-secondary action' href='admin.php?page=Entries&guestbook=$_REQUEST[guestbook]&id=$result->id'>Back</a><br /><br />";
		echo "
		<tr>
	 			<form name='edit_form' method='post' action='$location'>
	 			<td style='font-size:10px; border:1px solid #eeeeee; background-color:#$bgcolor'>
	 			<table border='0'>
	 			<tr><td><b>ID:</b></td><td>$result->id</td></tr>
	 			<tr><td><b>Guestbook: </b></td><td><select name='gb_guestbook'>";

                    $display_gb_count = $gbguestbook+1;

                    echo "<option value='$gbguestbook' selected>Assigned to: Guestbook $display_gb_count</option>";
                    $c=1;
                    $multi_page_id = explode(",", $options["page_id"]);
                    for($m=0; $m<count($multi_page_id); $m++) {
                        if($gbguestbook != $m) {
                        echo "<option value='$m'>Switch to: Guestbook $c</option>";
                        }
                    $c++;
                    }
    				echo "</select></td></tr>";

				if($result->flag == 1) {$check = "checked"; } else {$check="";}
                echo "<tr><td><b>Admin review:</b></td><td><input type='checkbox' name='gb_flag' value='1' $check /> If the Admin review checkbox is activated, the post will not be shown on the guestbook page.</td></tr>";

	 			echo "<tr><td style='font-size:10px;'><b>Date:</b></td>
	 			<td style='font-size:10px;'>$date<br />
	 			Day.Month.Year,Hour:Minute:Second<br />
	 			<input style='font-size:10px; width:200px;' type='text' name='gb_date' value='$date2' /><br />
	 			(DD.MM.YYYY,HH:MM:SS)</td></tr>
	 			<input type='hidden' name='hidden_date' value='$date' />";


				echo "
				<tr><td><b>Name: </b></td><td><input style='font-size:10px;' type='text' name='gb_name' value='$gbname' /></td></tr>
	 			<tr><td style='font-size:10px;'><b>Email:</b> </td> <td><input style='font-size:10px; width:200px;'
	 			type='text' name='gb_email' value='$gbemail' /></td></tr>
	 			<tr><td style='font-size:10px;'><b>Website:</b> </td> <td><input style='font-size:10px; width:200px;'
	 			type='text' name='gb_url' value='$gburl' /><br />Don't remove the \"http(s)://\" tag.</td></tr>
	 			<tr><td style='font-size:10px;'><b>Additional:</b> </td> <td><input style='font-size:10px; width:200px; background-color:#eee;'
	 			type='text' id='gb_additional' name='gb_additional' value='$gbadditional' readonly />
	 			<input class='button-secondary action' style='font-weight:bold; color:#bb0000; margin:10px 0px;' type='button'
	 			value='X' onclick='deleteAdditional();' /></td></tr>
	 			<tr><td style='font-size:10px;'><b>IP:</b></td> <td><input style='font-size:10px; width:200px; background-color:#eee;'
	 			type='text' id='gb_ip' name='gb_ip' value='$gbip' maxlength='15' readonly />
	 			<input class='button-secondary action' style='font-weight:bold; color:#bb0000; margin:10px 0px;' type='button'
	 			value='X' onclick='deleteIP();' />
	 			<a style='font-size:10px;' href='http://www.ripe.net/whois?searchtext=$result->ip' target='_blank'>[query]</a>
	 			</td></tr>
				</table>

	 			<td style='border:1px solid #eeeeee; background-color:#$bgcolor'>";
	 				if($_REQUEST[htmleditor]==1) {
						echo "
						<script type=\"text/javascript\" src=\"../wp-content/plugins/dmsguestbook/js/quicktags/quicktags.js\"></script>
						<script type=\"text/javascript\">
							quicktagsL10n = {
								quickLinks: \"(Quick Links)\",
								wordLookup: \"Enter a word to look up:\",
								dictionaryLookup: \"Dictionary lookup\",
								lookup: \"lookup\",
								closeAllOpenTags: \"Close all open tags\",
								closeTags: \"close tags\",
								enterURL: \"Enter the URL\",
								enterImageURL: \"Enter the URL of the image\",
								enterImageDescription: \"Enter a description of the image\"
							}
							try{convertEntities(quicktagsL10n);}catch(e){};
						edToolbar()
						</script>";
					}
				echo "
				<textarea style='height:400px; width:99%;' id='gb_message' name='gb_message'>$gbmsg</textarea>
				<br />
	 			<br />
	 			</td>";

	 			if($_REQUEST[htmleditor]==1) {
					echo "<script type=\"text/javascript\">
					{
					edCanvas = document.getElementById('gb_message');
					}
					</script>";
				}

					echo "
					<script type='text/javascript'>
						function deleteAdditional() {
						check = confirm('Would you really like to clear this text field? Don\'t forget to press the \"Save\" button.');
							if(check == true) {
							document.getElementById('gb_additional').value = '';
							}
						}

						function deleteIP() {
						check = confirm('Would you really like to clear this text field? Don\'t forget to press the \"Save\" button.');
							if(check == true) {
							document.getElementById('gb_ip').value = '';
							}
						}
					</script>

	 			<td style='text-align:center;font-size:10px; border:1px solid #eeeeee; background-color:#$bgcolor'>
	 			<form name='edit_form' method='post' action='$location'>
	 			<input name='editdata' value='edit' type='hidden' />
	 			<input name='id' value='$result->id' type='hidden' />
	 			<input type='hidden' name='guestbook' value='$_REQUEST[guestbook]' />
	 			<input class='button-primary action' style='font-weight:bold; color:#0000bb; margin:10px 0px;'
	 			type='submit' value='Save' onclick=\"return confirm('Would you really like to edit this dataset?');\" />
	 			</form>";

				echo "
				<form name='spam_form' method='post' action='$location'>
	 			<input name='action' value='markasspam' type='hidden' />
	 			<input type='hidden' name='guestbook' value='$_REQUEST[guestbook]' />
				<input name='id' value='$result->id' type='hidden' />
				<input name='tinymce' value='0' type='hidden' />
	 			<input name='htmleditor' value='0' type='hidden' />
	 			<input class='button-secondary action' style='font-weight:bold; color:#000000; margin:10px 0px;' type='submit'
	 			value='Spam' />
	 			</form>";

				echo "
	 			<form name='delete_form' method='post' action='$location'>
	 			<input name='action' value='deletepost' type='hidden' />
	 			<input type='hidden' name='guestbook' value='$_REQUEST[guestbook]' />
				<input name='id' value='$result->id' type='hidden' />
				<input name='tinymce' value='0' type='hidden' />
	 			<input name='htmleditor' value='0' type='hidden' />
	 			<input class='button-secondary action' style='font-weight:bold; color:#bb0000; margin:10px 0px;' type='submit'
	 			value='X' onclick=\"return confirm('Would you really like to delete this dataset?');\" />
	 			</form>
	 			</td>
	 			</tr>";

	 			echo "<thead>
				<tr>
	 			<th style='padding:0px 5px 0px 5px;'>Author</th>
	 			<th style='padding:0px 5px 0px 5px;'>Comment</th>
				<th style='padding:0px 5px 0px 5px;'>Action</th>
				</tr>
				</thead>";

					if($_REQUEST[tinymce]==1) {
						echo "
						<!-- TinyMCE -->
						<script type=\"text/javascript\" src=\"../wp-content/plugins/dmsguestbook/js/tinymce/jscripts/tiny_mce/tiny_mce.js\"></script>
						<script type=\"text/javascript\">
							tinyMCE.init({
								mode : \"textareas\",
								theme : \"advanced\",
								theme_advanced_buttons1 : \"bold, italic, underline, strikethrough, justifyleft, justifycenter, justifyright, justifyfull, blockquote, bullist, numlist, outdent, indent, link, unlink, image, hr, code, cleanup, removeformat, forecolor, backcolor, charmap, separator, undo, redo\",
								theme_advanced_buttons2 : \"\"
							});
						</script>
						<!-- /TinyMCE -->";
					}



	}


		echo "
		</table>";

		echo "<script type=\"text/javascript\">
			function AllSelectboxes1() {
				countall = document.forms.myForm.selectpost.length;

				selectallbox1 = document.getElementById('selectall1').checked;

 				for(var i = 0; i < countall; i++)
  				{
  				thisElement = document.forms.myForm.selectpost[i];

					if(selectallbox1 == true) {
					thisElement.checked = true;
					document.getElementById('selectall2').checked = true;
					}

					if(selectallbox1 == false) {
					thisElement.checked = false;
					document.getElementById('selectall2').checked = false;
					}
 				}
			}


			function AllSelectboxes2() {
				countall = document.forms.myForm.selectpost.length;

				selectallbox2 = document.getElementById('selectall2').checked;

 				for(var i = 0; i < countall; i++)
  				{
  				thisElement = document.forms.myForm.selectpost[i];

					if(selectallbox2 == true) {
					thisElement.checked = true;
					document.getElementById('selectall1').checked = true;
					}

					if(selectallbox2 == false) {
					thisElement.checked = false;
					document.getElementById('selectall1').checked = false;
					}
 				}
			}
		</script>";

?>
		</table>
		</div>
		<br /><br />
<?php
	} /* end of manage guestbook entries */





	/* Spam */
	function dmsguestbook5_meta_description_option_page() {
		$options=create_options();

		// check Akismet is activated
		$CheckAkismet = CheckAkismet();
			if($CheckAkismet != "" && $options[akismet] == 1) {
			$aktivatedAkismet = 1;
			}

		global $wpdb;
		$table_name = $wpdb->prefix . "dmsguestbook";
		$table_posts = $wpdb->prefix . "posts";

		/* initialize */
		if($_REQUEST[from]=="") {$_REQUEST[from]=0; $_REQUEST[select]=1;}
		$gb_step=$options["step"];

		$query1 = $wpdb->get_results("SELECT * FROM $table_name WHERE spam = '1'");
    	$num_rows1 = $wpdb->num_rows;

		/* count all search database entries / mysql_query */
    	$query0 = $wpdb->get_results("SELECT * FROM $table_name WHERE spam = '1' ORDER BY id " . sprintf("%s", $options["sortitem"]) . " LIMIT
		" . sprintf("%d", $_REQUEST[from]) . "," . sprintf("%d", $gb_step) . ";");
    	$num_rows0 = $wpdb->num_rows;


		echo "
		<div class='wrap'>
		<h2>Spam</h2>";

		if($num_rows1 >=1 ) {
		echo "<a style='font-size:10px;' href='admin.php?page=Spam&action=deleteallpost'
		onclick=\"return confirm('Would you really like to delete ALL data entries?');\">Delete all Spam ($num_rows1) entries</a>";
		}

		echo "
		<br /><br />
		<div style='width:100%; text-align:center;'>
		<div style='font-size:11px;'>($num_rows1)</div>";

		for($q=0; $q<$num_rows1; ($q=$q+$gb_step))
		{
		$y++;
			if($_REQUEST[select]==$y) {
			echo "<a style='color:#bb1100; text-decoration:none;' href='admin.php?page=Spam&from=$q&select=$y'> $y</a>";
			}
			else {
				 echo "<a style='color:#000000; text-decoration:none;' href='admin.php?page=Spam&from=$q&select=$y'> $y</a>";
				 }
		}
		echo "</div>
		<br /><br />";


		echo "
		<form name='myForm' method='post' action='$location'>
		<p>
		<select name='action'>
		<option value='-1'>Bulk Action</option>
		<option value='unmarkspam'>Unmark Spam</option>
		<option value='deletepost2'>Delete</option>
		</select>

		<input class='button-secondary action' type='submit' value='Apply' onclick=\"return confirm('Would you really like to do this?');\" /></p>
	    <table class='widefat comments' cellspacing='0'>
		<thead>
		<tr>
		<th style='padding:7px 7px 7px 0px; width:20px;'><input type='checkbox' id='selectall1' name='selectall1' onClick=\"AllSelectboxes1();\"></th>
		<th style='padding:0px 5px 0px 5px; width:20px;'>ID</th>
	 	<th style='padding:0px 5px 0px 5px; width:100px;'>Author</th>
	 	<th style='padding:0px 5px 0px 5px; width:300px;'>Comment</th>
		<th style='padding:0px 5px 0px 5px; width:200px;'>Action</th>
		</tr>
		</thead>";

			if($num_rows0 == 0) {
			echo "<tr><td></td><td></td><td></td><td style='text-align:center;'><b>No entries found.</b></td><td></td></tr>";
			}

			foreach ($query0 as $result) {
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
				$gbguestbook= preg_replace("/[^0-9]+/i", "", $result->guestbook);
				$gbadditional= $result->additional;
				$guestbook = ($result->guestbook +1);

				if($result->email=="") {
				$email = "";
				} else {
					   $email = "<a href='mailto:$result->email'>$result->email</a><br />";
					   }

				if($gburl=="http://" || $gburl=="http://") {
				$concat_name_url = $gbname;
				} else {
					   $concat_name_url = "<a href='$gburl' target='_blank'>$gbname</a>";
					   }

				if($gbadditional !="") {
				$gbadditional = "<br />\"$gbadditional\"";
				}

			echo "<tr>";
			echo "<td><input type='checkbox' id='selectpost' name='selectpost[]' value='$result->id'></td>";
			echo "<td>$result->id</td>";
			echo "<td><b>$concat_name_url</b><br />$email" . "$gbip<br />Guestbook #$guestbook" . "$gbadditional</td>";
			echo "<td><span style='color:#777777;'>Submitted on </span>$date<br />$gbmsg</td>";
			$unmark_spam = "<a style='color:#D98500;' href='admin.php?page=Spam&action=unmarkspam&guestbook=$_REQUEST[guestbook]&id=$result->id'>Unmark Spam</a>";
			$delete_spam = "<a style='color:#D54E21;' href='admin.php?page=Spam&action=deletepost&guestbook=$_REQUEST[guestbook]&id=$result->id' onclick=\"return confirm('Would you really like to delete this dataset?');\">Delete</a>";
			echo "<td>";
			echo "$unmark_spam | $delete_spam";
			echo "</td>";
			echo "</tr>";
			}
		echo "</form>";

		echo "<thead>
		<tr>
		<th style='padding:7px 7px 7px 0px; width:20px;'><input type='checkbox' id='selectall2' name='selectall2' onClick=\"AllSelectboxes2();\"></th>
		<th style='padding:0px 5px 0px 5px; width:20px;'>ID</th>
	 	<th style='padding:0px 5px 0px 5px; width:100px;'>Author</th>
	 	<th style='padding:0px 5px 0px 5px; width:300px;'>Comment</th>
		<th style='padding:0px 5px 0px 5px; width:200px;'>Action</th>
		</tr>
		</thead>";

		echo "
		</table>
		</div>";
		echo "<br /><br />";

		echo "<script type=\"text/javascript\">
			function AllSelectboxes1() {
				countall = document.forms.myForm.selectpost.length;

				selectallbox1 = document.getElementById('selectall1').checked;

 				for(var i = 0; i < countall; i++)
  				{
  				thisElement = document.forms.myForm.selectpost[i];

					if(selectallbox1 == true) {
					thisElement.checked = true;
					document.getElementById('selectall2').checked = true;
					}

					if(selectallbox1 == false) {
					thisElement.checked = false;
					document.getElementById('selectall2').checked = false;
					}
 				}
			}


			function AllSelectboxes2() {
				countall = document.forms.myForm.selectpost.length;

				selectallbox2 = document.getElementById('selectall2').checked;

 				for(var i = 0; i < countall; i++)
  				{
  				thisElement = document.forms.myForm.selectpost[i];

					if(selectallbox2 == true) {
					thisElement.checked = true;
					document.getElementById('selectall1').checked = true;
					}

					if(selectallbox2 == false) {
					thisElement.checked = false;
					document.getElementById('selectall1').checked = false;
					}
 				}
			}
		</script>";
	}
	/* end of Spam */



	/* edit */
	if ('edit' == $POSTVARIABLE['editdata']) {

	   	/* set http(s):// if not exist */
		if(substr("$_REQUEST[gb_url]", 0, 7) != "http://" && substr("$_REQUEST[gb_url]", 0, 8) != "https://") {
	   	$_REQUEST[gb_url]="http://";
		}

		/* Don't quote ampersand, TinyMCE does it*/
		if($_REQUEST[tinymce]==1) {
		$gbmessage = $_REQUEST[gb_message];
		}

		/* Quote ampersand, quickhtml doesn't it*/
		if($_REQUEST[htmleditor]==1) {
		$gbmessage = str_replace("&","&amp;",$_REQUEST[gb_message]);
		}

		$table_name = $wpdb->prefix . "dmsguestbook";
		$updatedata = $wpdb->query("UPDATE $table_name SET
		name 		= 	'" . mysql_real_escape_string(addslashes($_REQUEST[gb_name])) . "',
		email 		= 	'" . mysql_real_escape_string($_REQUEST[gb_email]) . "',
		url 		= 	'" . mysql_real_escape_string($_REQUEST[gb_url]) . "',
		ip	 		= 	'" . mysql_real_escape_string($_REQUEST[gb_ip]) . "',
		message 	= 	'" . mysql_real_escape_string(addslashes($gbmessage)) ."',
		guestbook	=	'" . sprintf("%d", $_REQUEST[gb_guestbook]) . "',
		additional	=	'" . $_REQUEST[gb_additional] . "',
		flag		=	'" . sprintf("%d", $_REQUEST[gb_flag]) . "'
		WHERE id = '" . sprintf("%d", $_REQUEST[id]) . "' ");
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
		message("<b>Dataset ($_REQUEST[id]) was saved</b>", 50, 800);
	}
/* end of manage guestbook entries */


	/* delete multi post / spam */
	if('deletepost2' == $POSTVARIABLE['action']) {
	$table_name = $wpdb->prefix . "dmsguestbook";
	$dataset="";
		for($c=0; $c<count($_REQUEST[selectpost]); $c++) {
		$deletedata = $wpdb->query("DELETE FROM $table_name WHERE id = '" . sprintf("%d", "{$_REQUEST[selectpost][$c]}") . "'");
		$delete = mysql_query($deletedata);
		$dataset .= "{$_REQUEST[selectpost][$c]}, ";
		}

		if(count($_REQUEST[selectpost]) !=0) {
		message("<b>Dataset ($dataset) was deleted...</b>", 50, 800);
		}
	}

	/* delete single post / spam*/
	if('deletepost' == $_REQUEST['action']) {
	$table_name = $wpdb->prefix . "dmsguestbook";
		$deletedata = $wpdb->query("DELETE FROM $table_name WHERE id = '" . sprintf("%d", "$_REQUEST[id]") . "'");
		$delete = mysql_query($deletedata);
		message("<b>Dataset ($_REQUEST[id]) was deleted...</b>", 50, 800);
	}

	/* delete ALL spam*/
	if('deleteallpost' == $_REQUEST['action']) {
	$table_name = $wpdb->prefix . "dmsguestbook";
		$deletealldata = $wpdb->query("DELETE FROM $table_name WHERE spam = '1'");
		$delete = mysql_query($deletealldata);
		message("<b>All Dataset were deleted...</b>", 140, 800);
	}

	/* single spam */
	if('markasspam' == $_REQUEST['action']) {
	$table_name = $wpdb->prefix . "dmsguestbook";
		$updatedata3 = $wpdb->query("UPDATE $table_name SET
		spam 		= 	'1'
		WHERE id = '" . sprintf("%d", $_REQUEST[id]) . "'");
  		$update3 = mysql_query($updatedata3);
		SpamHam($_REQUEST[id], "spam");
	}


	/* multi spam  */
	if ('markasspam' == $POSTVARIABLE['action']) {
	$table_name = $wpdb->prefix . "dmsguestbook";
		for($c=0; $c<count($_REQUEST[selectpost]); $c++) {
			$updatedata4 = $wpdb->query("UPDATE $table_name SET
			spam 		= 	'1'
			WHERE id = '" . sprintf("%d", "{$_REQUEST[selectpost][$c]}") . "'");
  			$update4 = mysql_query($updatedata4);
  			SpamHam("{$_REQUEST[selectpost][$c]}", "spam");
  		}
	}

	/* not spam multi */
	if ('unmarkspam' == $POSTVARIABLE['action']) {
	$table_name = $wpdb->prefix . "dmsguestbook";
		for($c=0; $c<count($_REQUEST[selectpost]); $c++) {
			$updatedata4 = $wpdb->query("UPDATE $table_name SET
			spam 		= 	'0'
			WHERE id = '" . sprintf("%d", "{$_REQUEST[selectpost][$c]}") . "'");
  			$update4 = mysql_query($updatedata4);
  			SpamHam("{$_REQUEST[selectpost][$c]}", "ham");
  		}
	}

	/* not spam single */
	if ('unmarkspam' == $_REQUEST['action']) {
	$table_name = $wpdb->prefix . "dmsguestbook";
		$updatedata4 = $wpdb->query("UPDATE $table_name SET
		spam 		= 	'0'
		WHERE id = '" . sprintf("%d", "$_REQUEST[id]") . "'");
  		$update4 = mysql_query($updatedata4);
  		SpamHam($_REQUEST[id], "ham");
	}

	/* set admin review or not */
	if ('adminreview' == $_REQUEST['action']) {
	$table_name = $wpdb->prefix . "dmsguestbook";
		$updatedata5 = $wpdb->query("UPDATE $table_name SET
		flag 		= 	'" . sprintf("%d", "$_REQUEST[flag]") . "'
		WHERE id = '" . sprintf("%d", "$_REQUEST[id]") . "'");
  		$update5 = mysql_query($updatedata5);
	}

	/* multi set admin review set hidden */
	if ('sethidden' == $POSTVARIABLE['action']) {
	$table_name = $wpdb->prefix . "dmsguestbook";
		for($c=0; $c<count($_REQUEST[selectpost]); $c++) {
			$updatedata6 = $wpdb->query("UPDATE $table_name SET
			flag 		= 	'1'
			WHERE id = '" . sprintf("%d", "{$_REQUEST[selectpost][$c]}") . "'");
  			$update6 = mysql_query($updatedata6);
  		}
	}

	/* multi set admin review set visible */
	if ('setvisible' == $POSTVARIABLE['action']) {
	$table_name = $wpdb->prefix . "dmsguestbook";
		for($c=0; $c<count($_REQUEST[selectpost]); $c++) {
			$updatedata7 = $wpdb->query("UPDATE $table_name SET
			flag 		= 	'0'
			WHERE id = '" . sprintf("%d", "{$_REQUEST[selectpost][$c]}") . "'");
  			$update7 = mysql_query($updatedata7);
  		}
	}


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
	  		guestbook int(2) DEFAULT '0' NOT NULL,
	  		spam int(1) DEFAULT '0' NOT NULL,
	  		additional varchar(50) NOT NULL,
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
   			/* add flag field (> 1.8.0) */
   			if($wpdb->get_var("SHOW FIELDS FROM $table_name LIKE 'flag'")=="") {
   			$sql = $wpdb->query("ALTER TABLE " . $table_name . " ADD flag int(2) NOT NULL");
   			require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
      		dbDelta($sql);
   			}

   			/* add gravatar field (> 1.10.0) */
   			if($wpdb->get_var("SHOW FIELDS FROM $table_name LIKE 'gravatar'")=="") {
   			$sql = $wpdb->query("ALTER TABLE " . $table_name . " ADD gravatar varchar(32) NOT NULL");
   			require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
      		dbDelta($sql);
   			}

   			/* add guestbook field (> 1.13.0) */
   			if($wpdb->get_var("SHOW FIELDS FROM $table_name LIKE 'guestbook'")=="") {
   			$sql = $wpdb->query("ALTER TABLE " . $table_name . " ADD guestbook int(2) DEFAULT '0' NOT NULL");
   			require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
      		dbDelta($sql);
   			}

   			/* add additional field (> 1.14.0) */
   			if($wpdb->get_var("SHOW FIELDS FROM $table_name LIKE 'additional'")=="") {
   			$sql = $wpdb->query("ALTER TABLE " . $table_name . " ADD additional varchar(50) NOT NULL");
   			require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
      		dbDelta($sql);
   			}

   			/* add spam field (> 1.14.0) */
   			if($wpdb->get_var("SHOW FIELDS FROM $table_name LIKE 'spam'")=="") {
   			$sql = $wpdb->query("ALTER TABLE " . $table_name . " ADD spam int(1) DEFAULT '0' NOT NULL");
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
	global $DMSGuestbookContent;
		$options=create_options();
		$multi_page_id = explode(",", $options["page_id"]);
		$multi_language = explode(",", $options["language"]);

		for($m=0; $m<count($multi_page_id); $m++) {
			if(in_array(is_page($multi_page_id[$m]),$multi_page_id)) {
			$page_id = $multi_page_id[$m];
			$multi_gb_id = $m;
			$multi_gb_language = $multi_language[$m];
			}
		}

			if(is_page($page_id) AND $page_id!="")
			{
				$post_id = get_post($page_id);
				if ($_COOKIE['wp-postpass_' . COOKIEHASH] == $post_id->post_password || $post_id->post_password == "") {

				include_once("dmsguestbook.php");
				$content = $content . $DMSGuestbookContent;
				return $content;
				}
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

Form title property (name, email, url, additional selectbox, message)|
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

additional selectbox|
css_form_additional_option|
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
"page_id" => "0",							/* id */
"width1" => "95",							/* guestbook width */
"width2" => "35",							/* separator width */
"step" => "10",								/* step */
"messagetext_length" => "0",				/* allowed length of each message text */
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
"recaptcha_publickey" => "0",				/* reCAPTCHA public key */
"recaptcha_privatekey" => "0",				/* reCAPTCHA private key */
"akismet" => "0",							/* avtivate Akismet */
"akismet_action" => "0",					/* 0=move to spam folder, 1=block spam */
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
"mail_method" => "Mail",					/* using the php build in method mail or an external smtp server */
"smtp_host" => "smtp.example.tld",			/* smtp host */
"smtp_port" => "25",						/* smtp port */
"smtp_username" => "MyUsername",			/* username if authentification is required */
"smtp_password" => "MyPassword",			/* passwort if authentification is required */
"smtp_auth" => "0",							/* activate the authentification */
"smtp_ssl" => "0",							/* using ssl encryption */
"sortitem" => "DESC",						/* each post sort by*/
"dbid" => "0",								/* show database id instead continous number*/
"language" => "0",							/* language */
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
"additional_option" => "none",				/* an additional selectbox. see in your dmsguestbook/module folder for examples */
"additional_option_title" => "-",			/* define a input form title text for additional selectbox */
"show_additional_option" => "0",			/* show additional text in each guestbook post. Edit this appearance in template/post/default.tpl */
"role1" => "Administrator",					/* roles for: database / guestbook / language settings, phpinfo */
"role2" => "Administrator",					/* roles for: entries */
"role3" => "Administrator",					/* roles for: spam */
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
						$opt["$key"] = preg_replace("/[^a-z-0-9-~_,.:?&%=\/]+/i", "", html_entity_decode($part2[0]), ENT_QUOTES);
				}
				elseif($key=="email_image_path") {
						$opt["$key"] = preg_replace("/[^a-z-0-9-~_,.:?&%=\/]+/i", "", html_entity_decode($part2[0]), ENT_QUOTES);
				}
				elseif($key=="website_image_path") {
						$opt["$key"] = preg_replace("/[^a-z-0-9-~_,.:?&%=\/]+/i", "", html_entity_decode($part2[0]), ENT_QUOTES);
				}
				elseif($key=="css") {
					   $opt["$key"] = preg_replace("/[\<\>\"\'\\`\\´]+/i", "", html_entity_decode($part2[0]), ENT_QUOTES);
					   }
				elseif($key=="css_customize") {
					   $opt["$key"] = preg_replace("/[\<\>\"\'\\`\\´]+/i", "", html_entity_decode($part2[0]), ENT_QUOTES);
					   }
				elseif($key=="messagetext_length") {
					   $opt["$key"] = preg_replace("/[^0-9]/i", "", html_entity_decode($part2[0], ENT_QUOTES));
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
				elseif($key=="smtp_host") {
				       $opt["$key"] = preg_replace("/[^a-z-0-9.]+/i", "", html_entity_decode($part2[0]), ENT_QUOTES);
					   }
				elseif($key=="smtp_username") {
					   $opt["$key"] = preg_replace("/[\"\'\`\´\/\\\\]/i", "", html_entity_decode($part2[0], ENT_QUOTES));
					   }
				elseif($key=="smtp_password") {
					   $opt["$key"] = preg_replace("/[\"\'\`\´\/\\\\]/i", "", html_entity_decode($part2[0], ENT_QUOTES));
					   }
				elseif($key=="mandatory_char") {
					   $opt["$key"] = preg_replace("/[\"\'\`\´\/\\\\]/i", "", html_entity_decode($part2[0], ENT_QUOTES));
					   }
				elseif($key=="additional_option_title") {
					   $opt["$key"] = preg_replace("/[\"\'\`\´\/\\\\]/i", "", html_entity_decode($part2[0], ENT_QUOTES));
					   }
				elseif($key=="gravatar_size") {
					   $opt["$key"] = preg_replace("/[^0-9]/i", "", html_entity_decode($part2[0], ENT_QUOTES));
					   }
				elseif($key=="recaptcha_publickey") {
					   $opt["$key"] = preg_replace("/[\<\>\"\'\\`\\´]+/i", "", html_entity_decode($part2[0]), ENT_QUOTES);
					   }
				elseif($key=="recaptcha_privatekey") {
					   $opt["$key"] = preg_replace("/[\<\>\"\'\\`\\´]+/i", "", html_entity_decode($part2[0]), ENT_QUOTES);
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
	if($setcolor==1) {$colorresult="F9F9F9";}
	if($setcolor==2) {$colorresult="FFFFFF";}
	if($setcolor==3) {$colorresult="F5F5F5";}

	if($tablecolor==1) {$colorresult="style='background-color:#$colorresult; padding:2px 2px;'"; }
	if($tablecolor==2) {$colorresult="style='background-color:#$colorresult; padding:0px 2px; text-align:center;'"; }
	return $colorresult;
	}


	/* advanced file */
	function check_writable($folder, $file) {
	$abspath = str_replace("\\","/", ABSPATH);
		if(is_writable($abspath . "wp-content/plugins/dmsguestbook/" . $folder . $file)) {
		echo "<br />$_REQUEST[file] <font style='color:#00bb00;'>is writable!</font><br />Set $file readonly again when your finished to customize!";
		$save_advanced_button = "<input class='button-primary action' style='font-weight:bold; margin:10px 0px; width:250px;' type='submit' value='Save' />";
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

	function OneInput($key, $label, $type, $entries, $value, $char_lenght, $additional, $style, $tooltip, $jscript, $base64) {
		$part1 = explode("@", $label);
		$part2 = explode("@", $additional);
		unset($data);
			/* If base64 is active */
			if(BASE64 == 1 && $base64 == 1) {
			$value = base64_decode($value);
			}
			for($x=0; $x<=$entries; $x++) {
				if($tooltip!=""){$showtooltip="<b style='font-weight:bold;background-color:#bb1100;color:#fff;padding:3px;' onmouseover=\"Tip('$tooltip')\" onclick=\"UnTip()\">?</b>";}
			$data .= "<table style='width:95%;' border='0'><colgroup><col width='40%'><col width='55%'><col width='5%'></colgroup><tr>
			<td>$part1[$x]</td>
			<td><input style='$style;' type='$type' name='$key' id='$key' value='$value' maxlength='$char_lenght' $jscript />
			<input type='hidden' name='base64-$key' value='$base64' />$part2[$x]</td><td style='text-align:right;'>$showtooltip</td></tr></table>";
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
		<input name=\"$key\" id=\"$key\" type=\"text\" size=\"6\" value=\"$value\" id=\"Color$id\" onclick=\"show_picker(this.id, '$value', '$value');\" />$part2[0]</td><td style='text-align:right;'>$showtooltip</td></tr></table>";

		return $data;
	}

	function CheckBoxes($key, $label, $value, $entries, $additional, $style, $tooltip, $jscript) {
		$part1 = explode("@", $label);
		$part2 = explode("@", $additional);
		unset($data);
		for($x=1; $x<=$entries+1; $x++) {
		$check="check" . $x;
			if($tooltip!=""){$showtooltip="<b style='font-weight:bold;background-color:#bb1100;color:#fff;padding:3px;' onmouseover=\"Tip('$tooltip')\" onclick=\"UnTip()\">?</b>";}
			if($value==$x) {$check = "checked";} else {$check="";}
			$c=$x-1;
			$data .= "<table style='width:95%;' border='0'><colgroup><col width='40%'><col width='55%'><col width='5%'></colgroup><tr><td>$part1[$c]</td><td><input style='$style;' type='checkbox' name='$key' id='$key' value='$x' $check $jscript /> $part2[$c]</td><td style='text-align:right;'>$showtooltip</td></tr></table>";
			}
		return $data;
	}

	function RadioBoxes($key, $label, $value, $entries, $additional, $style, $tooltip, $jscript) {
		$part1 = explode("@", $label);
		$part2 = explode("@", $additional);
		unset($data);
		for($x=0; $x<=$entries; $x++) {
		$check="check" . $x;
			if($tooltip!=""){$showtooltip="<b style='font-weight:bold;background-color:#bb1100;color:#fff;padding:3px;' onmouseover=\"Tip('$tooltip')\" onclick=\"UnTip()\">?</b>";}
			if($value==$x) {$check = "checked";} else {$check="";}
			$data .= "<table style='width:95%;' border='0'><colgroup><col width='40%'><col width='55%'><col width='5%'></colgroup><tr><td>$part1[$x]</td><td><input style='$style;' type='radio' name='$key' id='$key' value='$x' $check $jscript />$part2[$x]</td><td style='text-align:right;'>$showtooltip</td></tr></table>";
			}
		return $data;
	}

	function SelectBox($key, $label, $option, $value, $additional, $style, $tooltip, $jscript) {
		$part1 = explode("@", $option);
		$part2 = explode("@", $additional);
		unset($data);
			if($tooltip!=""){$showtooltip="<b style='font-weight:bold;background-color:#bb1100;color:#fff;padding:3px;' onmouseover=\"Tip('$tooltip')\" onclick=\"UnTip()\">?</b>";}
			$data .= "<table style='width:95%;' border='0'><colgroup><col width='40%'><col width='55%'><col width='5%'><colgroup><tr><td>$label</td><td><select style='$style;' name='$key' id='$key' $jscript>";
			$data .= "<option selected>$value</option>";
			for($x=0; $x<=count($part1)-2; $x++) {
				if($part1[$x] != $value) {
				$data .= "<option value='$part1[$x]'>$part1[$x]</option>";
				}
			}
			$data .= "</select></td><td style='text-align:right;'>$showtooltip</td></tr></table>";
		return $data;
	}


	/* antispam key generator */
	function RandomAntispamKey() {
	$len=20;
	srand(date("U"));
    $possible="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890+*%&(){}[]=?!$-_.,;:/\#~";
    unset($str);
    	while(strlen($str)<$len) {
    	$str.=substr($possible,(rand()%(strlen($possible))),1);
		}
		return($str);
	}


	function CheckAkismet() {
		global $wpdb;
		$table_option = $wpdb->prefix . "options";
		$query_akismet = $wpdb->get_results("SELECT option_value FROM $table_option WHERE option_name = 'wordpress_api_key'");
		$num_rows_akismet = $wpdb->num_rows;

			foreach ($query_akismet as $result) {
			$akismet_description = "$result->option_value";
			}

		if($num_rows_akismet == 0) {
		return(0);
		}
		else {
		     return $akismet_description;
		     }

	}

	/* Submit spam or ham */
	function SpamHam($id, $type) {
	global $wpdb;
	$table_name = $wpdb->prefix . "dmsguestbook";
		$selectspam = $wpdb->get_results("SELECT * FROM $table_name WHERE id = '" . sprintf("%d", $id) . "'");
		$num_rows_spam = $wpdb->num_rows;

		include_once "../wp-content/plugins/dmsguestbook/microakismet/class.microakismet.inc.php";
		$url=get_bloginfo('wpurl');

			foreach ($selectspam as $result) {
  			// The array of data we need
			$vars    = array();
			$vars["user_ip"]              = $result->ip;
   			$vars["comment_content"]      = $result->message;
   			$vars["comment_author"]       = $result->name;
   			$vars["comment_author_url"]	  = $result->url;
   			$vars["comment_author_email"] = $result->email;
			$vars["comment_type"]		  = "comment";

			$CheckAkismet = CheckAkismet();

			// ... Add vars as before ...
			$akismet	= new MicroAkismet(  $CheckAkismet,
										     $url,
										     "$url/1.0" );

				if($type=="spam" && $CheckAkismet !="") {
				$akismet->spam( $vars );
				}

				if($type=="ham" && $CheckAkismet !="") {
				$akismet->ham( $vars );
				}
			}
	}

	function CheckRole($level, $msg) {
		$userlevel="";
		$roles = array("0","1","2","3","4","5","6","7","8","9","10");
		for($x=0; $x<count($roles); $x++) {
			if(current_user_can("level_" . $x) == 1) {
			$userlevel = $x;
			}
		}

		if($msg==1) {
		echo "<b>You need <i>" . $level . "</i> rights to have access to this page.</b>";
		}

		if($level == "Administrator" && in_array($userlevel, array("8","9","10")) ) {
		$role = $userlevel;
		}
		if($level == "Editor" && in_array($userlevel, array("5","6","7","8","9","10")) ) {
		$role = $userlevel;
		}
		if($level == "Author" && in_array($userlevel, array("2","3","4","5","6","7","8","9","10")) ) {
		$role = $userlevel;
		}
		if($level == "Contributor" && in_array($userlevel, array("1","2","3","4","5","6","7","8","9","10"))) {
		$role = $userlevel;
		}
		if($level == "Subscriber" && in_array($userlevel, array("0","1","2","3","4","5","6","7","8","9","10"))) {
		$role = $userlevel;
		}
		if($userlevel == "") {
		$role = 10;
		}
	return $role;
	}

?>

