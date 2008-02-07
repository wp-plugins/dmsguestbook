<?php
/*
Plugin Name: DMSGuestbook
Plugin URI: http://danielschurter.net/
Description: The administration panel is found on the top of this site.
Version: 1.8.1
Author: Daniel M. Schurter
Author URI: http://danielschurter.net/
*/

define('DMSGUESTBOOKVERSION', "1.8.1");


	/* menu (DMSGuestbook, Manage) */
	add_action('admin_menu', 'add_dmsguestbook');

	function add_dmsguestbook() {
		add_menu_page(__('Options', 'dmsguestbook'), __('DMSGuestbook', 'dmsguestbook'),
		'edit_others_posts', 	'dmsguestbook', 'dmsguestbook_meta_description_option_page');

		add_submenu_page( 'dmsguestbook' , __('Manage', 'dmsguestbook'), __('Manage', 'dmsguestbook'), 'edit_others_posts',
		'Manage', 'dmsguestbook2_meta_description_option_page');

		add_submenu_page( 'dmsguestbook' , __('FAQ', 'dmsguestbook'), __('FAQ', 'dmsguestbook'), 'edit_others_posts',
		'FAQ', 'dmsguestbook4_meta_description_option_page');

		add_submenu_page( 'dmsguestbook' , __('phpinfo', 'dmsguestbook'), __('phpinfo', 'dmsguestbook'), 'edit_others_posts',
		'phpinfo', 'dmsguestbook3_meta_description_option_page');
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
	update_option("DMSGuestbook_options", mysql_real_escape_string($_REQUEST[restore_data]));
	message("<b>Options was saved...</b>", 140, 800);
	}

	if($_REQUEST[restore_options]==1 && $_REQUEST[restore_data]=="") {
	message("<b>Options was not saved, textfield is empty...</b>", 140, 800);
	}


# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

/* DMSGuestbook adminpage main function */
function dmsguestbook_meta_description_option_page() {

	/* initialize */
	$options 			= create_options();
	$options_name 		= default_options_array();
	version_control();

	/* global var for DMSGuestbook and option database */
	global $wpdb;
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
		var lnk = "../wp-content/plugins/dmsguestbook/color_picker/color_picker_files/color_picker_interface.html\
		?cur_color="+Current_Color+"&pre_color="+Previous_Color;
		window.open(lnk, "", "width=465, height=350");
	}
	</script>


	<!-- header -->
	<div class="wrap">
    <h2>DMSGuestbook Option</h2>
    <ul>
    <li>1.) Create a page where you want to display the DMSGuestbook.</li>
    <li>2.) Save the page and set the page id value in the red "Page ID" field under "Basic settings".</li>
    <li>3.) Customize the guestbook to your desire!</li>
    </ul>
	<br />

<?php


	/* user can create new DMSGuestbook database if these failed during the installation. */
    if($_REQUEST[action]=="createnew") {
		$sql = $wpdb->query("CREATE TABLE " . $table_name . " (
	  	id mediumint(9) NOT NULL AUTO_INCREMENT,
	  	name varchar(50) DEFAULT '' NOT NULL,
	  	email varchar(50) DEFAULT '' NOT NULL,
	  	url varchar(50) DEFAULT '' NOT NULL,
	  	date int(10) NOT NULL,
	  	ip varchar(15) DEFAULT '' NOT NULL,
	  	message longtext NOT NULL,
	  	flag int(2) NOT NULL,
	  	UNIQUE KEY id (id)
	  	) " . mysql_real_escape_string($_REQUEST[collate]) . ")");
	  	$abspath = str_replace("\\","/", ABSPATH);
	  	require_once($abspath . 'wp-admin/upgrade-functions.php');
	  	dbDelta($sql);
	  	message("<b>$table_name was created...</b>",200,800);
	}

	/* user can delete DMSGuestbook database after the confirmation */
	if($_REQUEST[action]=="delete" && $_REQUEST[delete]=="yes, i am sure") {
		$wpdb->query('DROP TABLE IF EXISTS ' . $table_name);
		$abspath = str_replace("\\","/", ABSPATH);
	  	require_once($abspath . 'wp-admin/upgrade-functions.php');
	  	message("<b>$table_name was deleted...</b>",200,800);
	}

	/* user can create DMSGuestbook option if the failed during the installation. */
	if($_REQUEST[action]=="createoption") {
		initialize_option();
	  	message("<b>DMSGuestbook options <br />were created...</b><br />Don't forget to set the page id.",200,800);
	}

	/* user can delete all DMSGuestbook_ entries in DMSGuestbook option after confirmation. */
    if($_REQUEST[action]=="deleteoption" && $_REQUEST[confirm_delete_option]=="delete") {
		$wpdb->query('DELETE FROM ' . $table_option . ' WHERE option_name LIKE "DMSGuestbook_%"');
	  	$abspath = str_replace("\\","/", ABSPATH);
	  	require_once($abspath . 'wp-admin/upgrade-functions.php');
	  	message("<b>All DMSGuestbook options were deleted...</b>",200,800);
	}
	?>




<!-- table for DMSGuestbook and DMSGuestbook option environment-->
<?php

/* set table color */
$tbc1=settablecolor(1,1);
$tbc2=settablecolor(2,1);
$tbc3=settablecolor(3,2);
$color1=settablecolor(1,0);

$collaps_dbs="<a href='admin.php?page=dmsguestbook&dbs=1'>
<img src='../wp-content/plugins/dmsguestbook/img/server.png'><b>Database settings</b></a>";
$collaps_basic="<a href='admin.php?page=dmsguestbook&basic=1'>
<img src='../wp-content/plugins/dmsguestbook/img/basic.png'><b>Basic settings</b></a>";
$collaps_advanced="<a href='admin.php?page=dmsguestbook&advanced=1'>
<img src='../wp-content/plugins/dmsguestbook/img/advanced.png'><b>Advanced settings</b></a>";
?>

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
?>
	<b style="font-size:20px;">Database settings</b>
	<table style="width:100%;">
		<tr>
		<td style="background-color:#<?php echo $color1; ?>;padding:20px;width:500px;border: #000000 solid 1px;">
<?php

		// search prefix_dmsguestbook
        $result = $wpdb->query("SHOW TABLES LIKE '$table_name'");
		if ($result > 0) {
?>
			<!-- if prefix_dmsguestbook is exist -->
			<b style="color:#00bb00;">[Status OK] "<?php echo $table_name;?>" is exist.</b><br /><br />
  			Type "yes, i am sure" in this textfield if you want delete <?php echo $table_name;?>.<br />
  			<b>All guestbook data will be lost!</b><br />
  			<form name="form0" method="post" action="<?php echo $location;?>">
  			<input type="text" name="delete" value=""><br />
  			<input name="action" value="delete" type="hidden" />
  			<input style="font-weight:bold; margin:10px 0px; width:250px;" type="submit" value="delete <?php echo $table_name; ?>" />
			</form>
<?php
		} else {
?>
			<!-- if prefix_dmsguestbook isn't exist -->
			<b style="color:#bb0000;padding:5px;"><?php echo $table_name;?> isn't exist.</b><br /><br />
			<form name="form0" method="post" action="<?php echo $location;?>">
				  <select name="collate">
				  	<option value="DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci">utf8_unicode_ci</option>
					<option value="DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci">utf8_general_ci</option>
					<option value="">if you use mySQL 4.0.xx or lower</option>
				</select><br />
				<input name="action" value="createnew" type="hidden" />
				<input style="font-weight:bold; margin:10px 0px; width:300px;" type="submit" value="create <?php echo $table_name;?>" />
			</form>
			If you want use char like &auml;,&uuml;,&ouml;... and your mysql version is lower than 4.1, be sure the language
			setting is e.g. "de-iso-8859-1" or similar. Check this with your mysql graphical frontenend like phpmyadmin.<br />
<?php
		}
?>
	<br /><a href="../wp-content/plugins/dmsguestbook/default_sql.txt" target="_blank">Is something wrong with my <?php echo $table_name;?> table?</a>
	</td>
	<td style="background-color:#<?php echo $color1; ?>;padding:20px;width:500px;border: #000000 solid 1px;">


<?php
	/* search all DMSGuestbook option (inform the user about the old dmsguestbook entries) */
	$query_options = $wpdb->get_results("SELECT * FROM $table_option WHERE option_name LIKE 'DMSGuestbook_%'");
	$num_rows_option = mysql_affected_rows();

	/* search to DMSGuestbook_options */
	$query_options1 = $wpdb->get_results("SELECT * FROM $table_option WHERE option_name LIKE 'DMSGuestbook_options'");
	$num_rows_option1 = mysql_affected_rows();

		if($num_rows_option1==1) {
		echo "<b style='color:#00bb00'>[Status OK] \"DMSGuestbook_options\" found in $table_option.</b><br />";
		}

		if($num_rows_option1==0) {
		echo "<b style='color:#bb0000'>No \"DMSGuestbook_options\" found in $table_option.</b><br />";
		}

		if($num_rows_option >= 2) {
		echo "<b style='color:#bb0000'>Notice: You have some old \"DMSGuestbook_xxxx\" rows in your $table_option, but this have no functionality impact.</b>";
		}
?>
		<form name="form0" method="post" action="<?php echo $location;?>"'>
			<input name="action" value="createoption" type="hidden" />
			<input style="font-weight:bold; margin:10px 0px; width:400px;" type="submit" value="Create new DMSGuestbook options" />
		</form>
		<br /><br />
		<form name="form0" method="post" action="<?php echo $location;?>">
				Type "delete" to remove all DMSGuestbook option entries from the <?php echo $table_option;?> table.<br />
				<input type="text" name="confirm_delete_option" value=""><br />
				<input name="action" value="deleteoption" type="hidden" />
				<input style="font-weight:bold; margin:10px 0px; width:400px;" type="submit" value="Delete DMSGuestbook options fom the database" />
			</form>
	<br /><a href="../wp-content/plugins/dmsguestbook/default_options.txt" target="_blank">Is something wrong with my DMSGuestbook_options in <?php echo $table_option;?>?</a>

	<br />
	<br />
	<tr><td></td>
	<td style="background-color:#<?php echo $color1; ?>;padding:20px;width:500px;border: #000000 solid 1px;">
	<a href='<?php echo $location;?>?backup_options'>[backup DMSGuestbook_options]</a>
	<br />
	<br />
	Restore DMSGuestbook_options:<br />
	Open a DMSGuestbook_options_DATE.txt file, copy the whole content and put these to the textfield below.
	<form action="<?php echo $location;?>" method="post">
	<textarea style="width:99%; height:200px;" name="restore_data"></textarea>
	<input type="hidden" name="restore_options" value="1">
	<input type="submit" value="restore" onclick="return confirm('Would you really to restore all data?');">
	</form>
	</td>
	</tr>
	</table>


<?php
}
?>
<!-- end table for DMSGuestbook and DMSGuestbook option environment -->


<!-- main table with all DMSGuestbook options -->
<?php
	if($num_rows_option==$dmsguestbook_options)
	{
?>




<?php
if($_REQUEST[basic]==1)
{
?>
	<b style="font-size:20px;">Basic settings</b>
	<table style="border:1px solid #000000; width:100%;" cellspacing="0" cellpadding="0">
	  <tr>
		<td>
	 	 <table style="width:100%;">
	 	 	<colgroup>
    			<col width="15%">
		    	<col width="45%">
    			<col width="40%">
  			</colgroup>
	 	 </tr>
     		<form name="form1" method="post" action="<?php echo $location ?>">

 	 		<!-- page id -->
 	 		<td <?php echo $tbc1; ?>>Page ID:</td>
 	 		<td <?php echo $tbc1; ?>><input style="width:50px;background-color:#ee8989" name="<?php echo $options_name[1][0]; ?>"\
 	 		value="<?php echo $options[1];?>" type="text" /></td>
	 		<td <?php echo $tbc1; ?>>Put the guestbook page id here</td>
	 		</tr>

			<!-- guestbook width -->
	 		<tr><td <?php echo $tbc2; ?>>Guestbook width:</td>
	 		<td <?php echo $tbc2; ?>><input style="width:50px;" name="<?php echo $options_name[2][0]; ?>"\
	 		value="<?php echo $options[2];?>" type="text" />%</td>
     		<td <?php echo $tbc2; ?>>Guestbook width in percent</td></tr>

			<!-- seperator width (hairline) -->
     		<tr><td <?php echo $tbc1; ?>>Separator width:</td>
	 		<td <?php echo $tbc1; ?>><input style="width:50px;" name="<?php echo $options_name[3][0]; ?>"\
	 		value="<?php echo $options[3];?>" type="text" />%</td>
     		<td <?php echo $tbc1; ?>>Separator width in percent</td></tr>

			<!-- guestbook position -->
	 		<tr><td <?php echo $tbc2; ?>>Guestbook position:</td>
	 		<td <?php echo $tbc2; ?>><input style="width:50px;" name="<?php echo $options_name[4][0]; ?>"\
	 		value="<?php echo $options[4];?>" type="text" /> px</td>
     		<td <?php echo $tbc2; ?>>Relative guestbook position in pixel (left to right)</td></tr>

			<!-- post per page -->
     		<tr><td <?php echo $tbc1; ?>>Posts per page:</td>
     		<td <?php echo $tbc1; ?>><select name="<?php echo $options_name[0][0]; ?>">
          		<option selected><?php echo $options[0];?></option>
          		<option>1</option>
          		<option>3</option>
          		<option>5</option>
          		<option>10</option>
          		<option>15</option>
          		<option>20</option>
				<option>25</option>
          		<option>30</option>
				<option>35</option>
				<option>40</option>
				<option>45</option>
				<option>50</option>
				<option>60</option>
				<option>70</option>
				<option>80</option>
				<option>90</option>
				<option>100</option>
          		</select></td>
     		<td <?php echo $tbc1; ?>>Number of entry in each page</td></tr>

			<!-- outside border color -->
     		<td <?php echo $tbc2; ?>>Outside border color:</td>
     		<td <?php echo $tbc2; ?>>
			<div id="Color1_div" style="border:1px solid; background-color:#<?php echo $options[6];?>;
			float:left;width:25px; height:25px; cursor:pointer;" onclick="show_picker('Color1','<?php echo $options[6];?>',
			'<?php echo $options[6];?>');">&nbsp;</div>
			<input name="<?php echo $options_name[6][0]; ?>" type="text" size="6" value="<?php echo $options[6];?>"
			id="Color1" onclick="show_picker(this.id, '<?php echo $options[6];?>',
			'<?php echo $options[6];?>');" /></td>
			<td <?php echo $tbc2; ?>>Color of the outside box</td></tr>

     		<!-- textfield border color -->
			<tr><td <?php echo $tbc1; ?>>Textfield border color:</td>
     		<td <?php echo $tbc1; ?>>
     		<div id="Color2_div" style="border:1px solid; background-color:#<?php echo $options[7];?>;
     		float:left; width:25px; height:25px; cursor:pointer;" onclick="show_picker('Color2', '<?php echo $options[7];?>',
     		'<?php echo $options[7];?>');">&nbsp;</div>
			<input name="<?php echo $options_name[7][0]; ?>" type="text" size="6" value="<?php echo $options[7];?>"
			id="Color2" onclick="show_picker(this.id, '<?php echo $options[7];?>',
			'<?php echo $options[7];?>');" />
     		<td <?php echo $tbc1; ?>>Color of all textfield borders</td></tr>

     		<!-- navigation char color -->
     		<tr><td <?php echo $tbc2; ?>>Navigation char color:</td>
     		<td <?php echo $tbc2; ?>>
   			<div id="Color3_div" style="border:1px solid; background-color:#<?php echo $options[8];?>;
   			float:left; width:25px; height:25px; cursor:pointer;" onclick="show_picker('Color3', '<?php echo $options[8];?>',
   			'<?php echo $options[8];?>');">&nbsp;</div>
			<input name="<?php echo $options_name[8][0]; ?>" type="text" size="6" value="<?php echo $options[8];?>"
			id="Color3" onclick="show_picker(this.id, '<?php echo $options[8];?>',
			'<?php echo $options[8];?>');" />
     		<td <?php echo $tbc2; ?>>Define the navigation color</td></tr>

     		<!-- seperator color -->
			<tr><td <?php echo $tbc1; ?>>Separator color:</td>
     		<td <?php echo $tbc1; ?>>
      		<div id="Color4_div" style="border:1px solid; background-color:#<?php echo $options[5];?>;
      		float:left; width:25px; height:25px; cursor:pointer;" onclick="show_picker('Color4', '<?php echo $options[5];?>',
      		'<?php echo $options[5];?>');">&nbsp;</div>
			<input name="<?php echo $options_name[5][0]; ?>" type="text" size="6" value="<?php echo $options[5];?>"
			id="Color4" onclick="show_picker(this.id, '<?php echo $options[5];?>',
			'<?php echo $options[5];?>');" />
			<td <?php echo $tbc1; ?>>Separator between header and body in each entry</td></tr>

			<!-- font color -->
			<tr><td <?php echo $tbc2; ?>>Font color:</td>
     		<td <?php echo $tbc2; ?>>
    		<div id="Color5_div" style="border:1px solid; background-color:#<?php echo $options[9];?>;
    		float:left; width:25px; height:25px; cursor:pointer;" onclick="show_picker('Color5', '<?php echo $options[9];?>',
    		'<?php echo $options[9];?>');">&nbsp;</div>
			<input name="<?php echo $options_name[9][0]; ?>" type="text" size="6" value="<?php echo $options[9];?>"
			id="Color5" onclick="show_picker(this.id, '<?php echo $options[9];?>', '<?php echo $options[9];?>');" />
     		<td <?php echo $tbc2; ?>>Overall font color</td></tr>

     		<!-- antispam image text color -->
     		<tr><td <?php echo $tbc1; ?>>Antispam image text color:</td>
     		<td <?php echo $tbc1; ?>>
    		<div id="Color6_div" style="border:1px solid; background-color:#<?php echo $options[19];?>;
    		float:left; width:25px; height:25px; cursor:pointer;" onclick="show_picker('Color6', '<?php echo $options[19];?>',
    		'<?php echo $options[19];?>');">&nbsp;</div>
			<input name="<?php echo $options_name[19][0]; ?>" type="text" size="6" value="<?php echo $options[19];?>"
			id="Color6" onclick="show_picker(this.id, '<?php echo $options[19];?>',
			'<?php echo $options[19];?>');" />
     		<td <?php echo $tbc1; ?>>Antispam image text color</td></tr>

     		<!-- navigation char style -->
     		<tr><td <?php echo $tbc2; ?>>Navigation char style:</td>
     		<td <?php echo $tbc2; ?>>
     		<input style="width:50px;" name="<?php echo $options_name[11][0]; ?>" value="<?php echo $options[11];?>" type="text" />
     		<input style="width:50px;" name="<?php echo $options_name[10][0]; ?>" value="<?php echo $options[10];?>"
     		type="text" /></td>
     		<td <?php echo $tbc2; ?>>Use a char, number or word</td></tr>

     		<!-- navigation char size -->
     		<tr><td <?php echo $tbc1; ?>>Navigation char size:</td>
     		<td <?php echo $tbc1; ?>><input style="width:50px;" name="<?php echo $options_name[12][0]; ?>"
     		value="<?php echo $options[12];?>" type="text" />px</td>
     		<td <?php echo $tbc1; ?>>Size in pixel</td></tr>

			<!-- guestbook form position -->
     		<tr><td <?php echo $tbc2; ?>>Guestbook form position:</td>
     		<td <?php echo $tbc2; ?>><select name="<?php echo $options_name[23][0]; ?>">
          		<option selected><?php echo $options[23];?></option>
          		<option>top</option>
          		<option>bottom</option>
          		</select>&nbsp;Link text: <input style="width:260px;" name="<?php echo $options_name[24][0]; ?>"
     		value="<?php echo $options[24];?>" type="text" /></td>
     		<td <?php echo $tbc2; ?>>Visible the guestbook input form on top or bottom<br />Define a link text if you selected "bottom"</td></tr>

     		<!-- date / time format / setlocale -->
     		<?php setlocale(LC_TIME, $options[20]); ?>
     		<tr><td <?php echo $tbc1; ?>>Date / Time format /<br />Time offset:</td>
     		<td <?php echo $tbc1; ?>><input style="width:300px;" name="<?php echo $options_name[20][0]; ?>"
     		value="<?php echo $options[20];?>" type="text" /><input style="width:100px;" name="<?php echo $options_name[21][0]; ?>"
     		value="<?php echo $options[21];?>" type="text" />
     		<select name="<?php echo $options_name[22][0]; ?>">
          		<option selected><?php echo $options[22];?></option>
          		<option>-12</option>
          		<option>-11</option>
          		<option>-10</option>
          		<option>-9</option>
          		<option>-8</option>
          		<option>-7</option>
				<option>-6</option>
          		<option>-5</option>
				<option>-4</option>
				<option>-3</option>
				<option>-2</option>
				<option>-1</option>
				<option>0</option>
				<option>+1</option>
          		<option>+2</option>
          		<option>+3</option>
          		<option>+4</option>
          		<option>+5</option>
          		<option>+6</option>
				<option>+7</option>
          		<option>+8</option>
				<option>+9</option>
				<option>+10</option>
				<option>+11</option>
				<option>+12</option>
				</select><br />

			<?php
			$offset = mktime(date("H")+$options[22], date("i"), date("s"), date("m")  , date("d"), date("Y"));
			?>

     		<?php echo htmlentities(strftime($options[20], $offset), ENT_QUOTES); ?></td>



     		<td <?php echo $tbc1; ?>>Set the date and time format. More infos:
     		<a href='http://www.php.net/manual/en/function.strftime.php' target='_blank'>Date & Time parameters</a><br />
     		set your language: e.g. en_EN, de_DE, fr_FR, it_IT, de, ge ... (must be installed on your system)<br />
     		Time offset: Use this offset if you Wordpress installation is not in the same country where you live.<br />
     		e.g: You live in London and the Wordpress installation is on a server in Chicago. You want to show the date in GMT (Greenwich Mean Time), set the offset -6 and check the correct time below.<br />Notice: don't use the %z or %Z parameter if you offset is not 0
     		</td>
     		</tr>

			<!-- language -->
			<tr><td <?php echo $tbc2; ?>> Language</td>
			<td <?php echo $tbc2; ?>>
			<select name="<?php echo $options_name[29][0]; ?>">
          		<option selected><?php echo $options[29];?></option>
<?php
				$abspath = str_replace("\\","/", ABSPATH);
				if ($handle = opendir($abspath . 'wp-content/plugins/dmsguestbook/language/')) {
    				while (false !== ($file = readdir($handle))) {
        				if ($file != "." && $file != ".." && $file != "README.txt") {
           				echo "<option>$file</option>";
        				}
    				}
    			closedir($handle);
				}
?>
          		</select>
          	</td>
          	<td <?php echo $tbc2; ?>>Edit languages under "Advanced settings"<br /><a href='../wp-content/plugins/dmsguestbook/language/README.txt' target='_blank'>How to create my own language template?</a><br /></td>
          	</tr>

     		<!-- mantatory -->
     		<tr><td <?php echo $tbc1; ?>>Mandatory:</td>
     		<td <?php echo $tbc1; ?>>
     		<?php if($options[13]==1) {$check1 = "checked"; } else {$check1="";} ?>
     		<?php if($options[14]==1) {$check2 = "checked"; } else {$check2="";} ?>
     		<?php if($options[15]==0) {$check3_0 = "checked"; } else {$check3_0="";} ?>
     		<?php if($options[15]==1) {$check3_1 = "checked"; } else {$check3_1="";} ?>
     		<?php if($options[15]==2) {$check3_2 = "checked"; } else {$check3_2="";} ?>
     		<input type="checkbox" name="<?php echo $options_name[13][0]; ?>" value="1" <?php echo $check1; ?>> Email <br />
     		<input type="checkbox" name="<?php echo $options_name[14][0]; ?>" value="1" <?php echo $check2; ?>> Website <br />
     		<input type="radio" name="<?php echo $options_name[15][0]; ?>" value="0" <?php echo $check3_0; ?>> Antispam off<br />
     		<input type="radio" name="<?php echo $options_name[15][0]; ?>" value="1" <?php echo $check3_1; ?>> Antispam image<br />
     		<input type="radio" name="<?php echo $options_name[15][0]; ?>" value="2" <?php echo $check3_2; ?>> Antispam mathematic figures</td>

	 		<td <?php echo $tbc1; ?>>User must fill out: Email text field / Website address field / Antispam field <br /><br />
	 		<hr style="border: 1px solid;"></hr>
	 		Image: <br /><img src="../wp-content/plugins/dmsguestbook/captcha/captcha.php" alt="[do you see this image?]"><br />
	 		If you don't see the image here, check the xfiles.ttf and captcha.png permission in your captcha folder.<br /><br />
	 		<?php truetype_permission("xfiles.ttf"); ?><br /><?php truetype_permission("captcha.png"); ?><br /><br />
	 		Requirement: PNG support ->
	 			<?php if (ImageTypes() & IMG_PNG) {
    				echo "[PNG support is available]"; } ?>
			    <?php $array = gd_info();?>
			<br />
	 		Requirement: GD 2.0.1 or above -> <?php echo $array["GD Version"];?>
	 		<br />
	 		Requirement: FreeType support -> <?php if($array["FreeType Support"]==1) {echo "[FreeType support enabled]";};?>
	 		<br /><br />
	 		<hr style="border: 1px solid;"></hr>
	 		Mathematic figures: <br />4 + 9 = <input style="width:30px;" type="text" name="" value="13"><br />
	 		</td>
	 		</tr>

	 		<!-- visible data -->
	 		<tr><td <?php echo $tbc2; ?>>Visible data:</td>
     		<td <?php echo $tbc2; ?>>
     		<?php if($options[16]==1) {$check1 = "checked"; } else {$check1="";} ?>
     		<?php if($options[17]==1) {$check2 = "checked"; } else {$check2="";} ?>
     		<?php if($options[18]==1) {$check3 = "checked"; } else {$check3="";} ?>
     		<input type="checkbox" name="<?php echo $options_name[16][0]; ?>" value="1" <?php echo $check1; ?>> IP adress <br />
     		<input type="checkbox" name="<?php echo $options_name[17][0]; ?>" value="1" <?php echo $check2; ?>> Email <br />
     		<input type="checkbox" name="<?php echo $options_name[18][0]; ?>" value="1" <?php echo $check3; ?>> Website
			</td>
	 		<td <?php echo $tbc2; ?>>Visible data for everyone in each entry</td>
		   </tr>

			<!-- sort each entries -->
	 		<tr><td <?php echo $tbc1; ?>>Sort guestbook items:</td>
     		<td <?php echo $tbc1; ?>>
     		<select name="<?php echo $options_name[27][0]; ?>">
          		<option selected><?php echo $options[27];?></option>
          		<option>ASC</option>
          		<option>DESC</option>
          		</select>
          	<br />
          	<?php if($options[28]==1) {$check4 = "checked"; } else {$check4="";} ?>
          	<input type="checkbox" name="<?php echo $options_name[28][0]; ?>" value="1" <?php echo $check4; ?>> Database id
			</td>
	 		<td <?php echo $tbc1; ?>>DESC = Newer post first.<br />ASC = Older post first.<br />Use the database id to consecutively numbered each guestbook entry.</td>
		   </tr>

		   <!-- send mail -->
	 		<tr><td <?php echo $tbc2; ?>>Notification mail:</td>
     		<td <?php echo $tbc2; ?>>
     		<?php if($options[25]==1) {$check1 = "checked"; } else {$check1="";} ?>
     		<input type="checkbox" name="<?php echo $options_name[25][0]; ?>" value="1" <?php echo $check1; ?>> Send a mail<br />
			<input style="width:300px;" name="<?php echo $options_name[26][0]; ?>"
     		value="<?php echo $options[26]?>" type="text" />
     		Email address<br />
			</td>
	 		<td <?php echo $tbc2; ?>>
	 		Receive a notification email when user write an new guestbook post.<br />
	 		The email address where the message to be sent is.
	 		</td>
		   </tr>

		    <!-- review mode -->
	 		<tr><td <?php echo $tbc1; ?>>Review mode:</td>
     		<td <?php echo $tbc1; ?>>
     		<?php if($options[30]==1) {$check30 = "checked"; } else {$check30="";} ?>
     		<input type="checkbox" name="<?php echo $options_name[30][0]; ?>" value="1" <?php echo $check30; ?>>
			</td>
	 		<td <?php echo $tbc1; ?>>
	 		Admin must review every post before this can display on the page.<br />
	 		You can edit the guestbook review status under "Manage"
	 		</td>
		   </tr>

		  </table>

	   <!-- submit button -->
	   <tr><td <?php echo $tbc3; ?>>
	   <input name="action" value="insert" type="hidden" />
       <input style="font-weight:bold; margin:10px 0px; width:300px;" type="submit" value="Save" />
       </td></tr>
	   </form>
	  </td>
	 </tr>
	</table>

	<br />
	<br />

	<table>
	<tr>
		<td>
		<!-- restore default settings button -->
		<form name="form3" method="post" action="<?php echo $location ?>">
		<input name="action2" value="default_settings" type="hidden" />
		<input style="font-weight:bold; margin:10px 0px;" type="submit"
		value="Restore default settings - All data will be replaced" onclick="return confirm('Would you really to restore all data?');">
     	</form>
     	</td>
		<td style="width:50px;"></td>
	 </tr>
	 </table>
<?php
}
?>


<?php
if($_REQUEST[advanced]==1)
{
	$color3=settablecolor(3,0);
	unset($buffer);
	echo "<b style='font-size:20px;'>Advanced settings</b><br />";
	$abspath = str_replace("\\","/", ABSPATH);

		if ($handle = opendir($abspath . 'wp-content/plugins/dmsguestbook/language/')) {
    		/* option.php */
    		echo "<a href='admin.php?page=dmsguestbook&advanced=1&folder=&file=stylesheet.php'>stylesheet.php</a><br />";
    		/* language */
    		while (false !== ($file = readdir($handle))) {
        		if ($file != "." && $file != "..") {
           		echo "<a href='admin.php?page=dmsguestbook&advanced=1&folder=language/&file=$file'>$file</a><br />";
        		}
    		}
    		closedir($handle);
		}

if($_REQUEST[file]!="") {
	$handle = fopen ($abspath . "wp-content/plugins/dmsguestbook/" . $_REQUEST[folder] . $_REQUEST[file], "r");
	if(is_writable($abspath . "wp-content/plugins/dmsguestbook/" . $_REQUEST[folder] . $_REQUEST[file])) {
	echo "<br />$_REQUEST[file] <font style='color:#00bb00;'>is writable!</font><br />Set $file readonly again when your finished to customize!";
	$save_advanced_button = "<input style='font-weight:bold; margin:10px 0px; width:250px;' type='submit' value='save' />";
	}
	else {
			if($_REQUEST[file]!="") {
	     	echo "<br />$_REQUEST[file] is <font style='color:#bb0000;'>not writable!</font><br />Set the write permission for $_REQUEST[file] to customize this file.";
	     	}
	     }

	while (!feof($handle)) {
    	$buffer .= fgets($handle, 4096);
	}
	fclose ($handle);
}

	$showfiledata = htmlentities($buffer, ENT_QUOTES);
	//$showfiledata = str_replace("&lt;", "<", $showfiledata);
	//$showfiledata = str_replace("&gt;", ">", $showfiledata);

?>
	<br />
	<table style="border:0px solid #000000; width:100%;background-color:#<?php echo $color3; ?>;" cellspacing="0" cellpadding="0">
	  <tr>
		<form name="form0" method="post" action="<?php echo $location;?>">
		<td><textarea style="width:99%; height:500px;" name="advanced_data"><?php echo $showfiledata;?></textarea></td>
	  </tr>
		<input name="action3" value="save_advanced_data" type="hidden" />
	  	<input name="folder" value="<?php echo $_REQUEST[folder]; ?>" type="hidden" />
	  	<input name="file" value="<?php echo $_REQUEST[file]; ?>" type="hidden" />
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
		$save_options = default_options_array();
		unset($save_to_db);

		for($s=0; $s<count($save_options); $s++) {
			if($POSTVARIABLE[$save_options[$s][0]]==""){$POSTVARIABLE[$save_options[$s][0]]=0;}
		$save_to_db.="<" . $save_options[$s][0] . ">" . htmlentities($POSTVARIABLE[$save_options[$s][0]], ENT_QUOTES) . "</" . $save_options[$s][0] . ">\r\n";
		}

		$save_to_db = str_replace("\"", "&amp;quot;", $save_to_db);
		update_option("DMSGuestbook_options", $save_to_db);
		message("<b>saved...</b>",200,800);
	}
	/* end of write DMSGuestbook option in wordpress options database */



	/* reset DMSGuestbook */
	if ('default_settings' == $POSTVARIABLE['action2']) {
	default_option();
	}


	/* save advanced */
	if ('save_advanced_data' == $POSTVARIABLE['action3']) {
	$abspath = str_replace("\\","/", ABSPATH);
	$handle = fopen($abspath . "wp-content/plugins/dmsguestbook/" . $POSTVARIABLE['folder'] . $POSTVARIABLE['file'], "w");

	$writetofile = str_replace("\\", "", $POSTVARIABLE['advanced_data']);

	fwrite($handle, $writetofile);
	fclose($handle);
	message("<b>saved...</b>",200,800);
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
			Name field: Write HTML code direct in this field. (e.g. &lt;b&gt;name&lt;/b&gt;)</li>
			<li>If you edit the url field, don't delete the "http(s)://" prefix.</li>
		</ul>
		* If this checkbox is activated, the post will not be shown on the guestbook page.
<?php
		/* maximum guestbook entries were displayed on page */
		$gb_step=$options[0];

		/* initialize */
		if($_REQUEST[from]=="") {$_REQUEST[from]=0; $_REQUEST[select]=1;}

		/* global var for DMSGuestbook */
		global $wpdb;
		$table_name = $wpdb->prefix . "dmsguestbook";

		/* count all database entries / mysql_query */
    	$query0 = $wpdb->get_results("SELECT * FROM  $table_name");
    	$num_rows0 = mysql_affected_rows();

		/* read all guestbook entries */
		$query1 = $wpdb->get_results("SELECT * FROM  $table_name ORDER BY id " . sprintf("%s", $options[27]) . " LIMIT
		" . sprintf("%d", $_REQUEST[from]) . "," . sprintf("%d", $gb_step) . ";");
		$num_rows1 = mysql_affected_rows();

?>

		<br /><br />
		<div style="width:<?php echo $gb_width;?>; text-align:center;">
		<div style="font-size:11px;">(<?php echo $num_rows0;?>)</div>

<?php
		for($q=0; $q<$num_rows0; ($q=$q+$gb_step))
		{
		$y++;
			if($_REQUEST[select]==$y) {
?>			<a style="color:#bb1100; text-decoration:none;" href="admin.php?page=Manage&from=<?php echo $q;?>&select=<?php echo $y;?>"><?php echo $y;?></a>
<?php			}
			else {
?>				 <a style="color:#000000; text-decoration:none;" href="admin.php?page=Manage&from=<?php echo $q;?>&select=<?php echo $y;?>"><?php echo $y;?></a>
<?php				 }
		}
?>		</div>
		<br /><br />

<?php
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
			setlocale(LC_TIME, $options[21]);
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
				$date = strftime ($options[20], mktime ($hour, $min, $sec, $month, $day, $year));

?>
	 			<tr>
	 			<form name="edit_form" method="post" action="<?php echo $location ?>">
	 			<td style="font-size:10px; border:1px solid #eeeeee; background-color:#<?php echo $bgcolor; ?>"><?php echo $result->id;?></td>

				<td style="font-size:10px; border:1px solid #eeeeee; background-color:#<?php echo $bgcolor; ?>;text-align:center;">
				<?php if($result->flag==1) {$check = "checked"; } else {$check="";} ?>
     			<input type="checkbox" name="gb_flag" value="1" <?php echo $check; ?>>
     			</td>

	 			<td style="border:1px solid #eeeeee; background-color:#<?php echo $bgcolor;?>">
	 			<input style="font-size:10px;" type="text" name="gb_name" value="<?php echo $result->name;?>"></td>
	 			<td style="border:1px solid #eeeeee; background-color:#<?php echo $bgcolor;?>">
	 			<textarea style="height:120px; width:500px;font-size:10px;" name="gb_message"><?php echo $result->message;?></textarea></td>
	 			<td style="font-size:10px; border:1px solid #eeeeee; background-color:#<?php echo $bgcolor;?>">

	 			<table border="0">
	 			<tr><td style="font-size:10px;">Date:</td>
	 			<td style="font-size:10px;"><?php echo $date;?><br />
	 			Day.Month.Year,Hour:Minute:Second
	 			<input style="font-size:10px; width:200px;" type="text" name="gb_date" value="<?php echo $date2?>"><br />
	 			(DD.MM.YYYY,HH:MM:SS)</td></tr>
	 			<input type="hidden" name="hidden_date" value="<?php echo $date;?>">

				<tr><td style="height:5px;"></td></tr>

	 			<tr><td style="font-size:10px;">IP:</td> <td><input style="font-size:10px; width:200px;"
	 			type="text" name="gb_ip" value="<?php echo $result->ip; ?>" maxlength="15">&nbsp;<a style="font-size:10px;" href="http://www.ripe.net/whois?searchtext=<?php echo $result->ip; ?>" target="_blank">[query]</a></td></tr>
	 			<tr><td style="font-size:10px;">Email: </td> <td><input style="font-size:10px;  width:200px;"
	 			type="text" name="gb_email" value="<?php echo $result->email;?>"></td></tr>
	 			<tr><td style="font-size:10px;">Website: </td> <td><input style="font-size:10px;  width:200px;"
	 			type="text" name="gb_url" value="<?php echo $result->url;?>"></td></tr>
				</table>

	 			<td style="font-size:10px; border:1px solid #eeeeee; background-color:#<?php echo $bgcolor;?>">
	 			<form name="edit_form" method="post" action="<?php echo $location ?>">
	 			<input name="editdata" value="edit" type="hidden" />
	 			<input name="id" value="<?php echo $result->id;?>" type="hidden" />
	 			<input style="font-weight:bold; color:#0000bb; margin:10px 0px;"
	 			type="submit" value="edit" onclick="return confirm('Would you really to edit this dataset?');">
	 			</form>
	 			</td>

	 			<td style="font-size:10px; border:1px solid #eeeeee; background-color:#<?php echo $bgcolor;?>">
	 			<form name="delete_form" method="post" action="<?php echo $location ?>">
	 			<input name="deletedata" value="delete" type="hidden" />
				<input name="id" value="<?php echo $result->id;?>" type="hidden" />
	 			<input style="font-weight:bold; color:#bb0000; margin:10px 0px;" type="submit"
	 			value="X" onclick="return confirm('Would you really to delete this dataset?');">
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
		$table_name = $wpdb->prefix . "dmsguestbook";
		$updatedata = $wpdb->query("UPDATE $table_name SET
		name 		= 	'" . mysql_real_escape_string($_REQUEST[gb_name]) . "',
		email 		= 	'" . mysql_real_escape_string($_REQUEST[gb_email]) . "',
		url 		= 	'" . mysql_real_escape_string($_REQUEST[gb_url]) . "',
		ip	 		= 	'" . mysql_real_escape_string($_REQUEST[gb_ip]) . "',
		message 	= 	'" . mysql_real_escape_string($_REQUEST[gb_message]) ."',
		flag		=	'" . sprintf("%d", $_REQUEST[gb_flag]) . "'
		WHERE id = '" . sprintf("%d", $_REQUEST[id]) . "'");
  		$update = mysql_query($updatedata);

		if(strlen($_REQUEST[gb_date])!=0) {
		$teil0 = explode(",", $_REQUEST[gb_date]);
		$teil1 = explode(".", $teil0[0]);
		$teil2 = explode(":", $teil0[1]);
		$timestamp = @mktime($teil2[0],$teil2[1],$teil2[2],$teil1[1],$teil1[0],$teil1[2]);
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


	/* add field for flags (> 1.8.0) */
   	function update_db_fields() {
   		global $wpdb;
   		$table_name = $wpdb->prefix . "dmsguestbook";
   			if($wpdb->get_var("SHOW FIELDS FROM $table_name LIKE 'flag'")=="") {
   			$sql = $wpdb->query("ALTER TABLE " . $table_name . " ADD flag INT(2) NOT NULL");
   			require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
      		dbDelta($sql);
   			}
   	}


	/* DMSGuestbook option first time initialize */
	function initialize_option() {
		$options=default_options_array();
		$count=0;
   		unset($save_options);

		$count_option=$count+1;
		for($c=0; $c<count($options); $c++) {
		$save_options.="<" . $options[$count][0] . ">" . $options[$count][1] . "</" . $options[$count][0] . ">\r\n";
   		$count++;
		}

		if(!get_option("DMSGuestbook_options")) {update_option("DMSGuestbook_options", $save_options);}
	}




	/* display the dmsguestbook.php */
	function DMSGuestBook($content) {
		$options=create_options();
		$page_id=$options[1];
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
		$options = array (
		array("step", "10"),							/* step */
		array("page_id", "-"),							/* id */
		array("width1", "100"),							/* guestbook width */
		array("width2", "35"),							/* separator width */
		array("position", "0"),							/* position */
		array("hairlinecolor", "EEEEEE"),				/* hairline color (separator) */
		array("bordercolor1", "AAAAAA"),				/* outside border color */
		array("bordercolor2", "DEDEDE"),				/* textfield border color */
		array("navigationcolor", "000000"),				/* navigation char color*/
		array("fontcolor1", "000000"),					/* font color */
		array("forwardarrowchar", ">"),					/* forward char */
		array("backwardarrowchar", "<"),				/* backward char */
		array("arrowsize", "20"),						/* forward / backward char size */
		array("require_email", "0"),					/* require email */
		array("require_url", "0"),						/* require url */
		array("require_antispam", "1"),					/* require antispam */
		array("show_ip", "0"),							/* show ip */
		array("show_url", "1"),							/* show url */
		array("show_email", "1"),						/* show email */
		array("captcha_color", "000000"),				/* captcha color */
		array("dateformat", "%a, %e %B %Y %H:%M:%S %z"),/* date format */
		array("setlocale", "en_EN"),					/* setlocale */
		array("offset", "0"),							/* date offset */
		array("formpos", "top"),						/* form position */
		array("formposlink", "-"),						/* form link if is set formpos = bottom */
		array("send_mail", "0"),						/* notification mail */
		array("mail_adress", "name@example.com"),		/* notification mail to this adress */
		array("sortitem", "DESC"),						/* each post sort by*/
		array("dbid", "0"),								/* show database id instead continous number*/
		array("language", "english.txt"),				/* language */
		array("admin_review", "0")						/* admin must review every post before this can display on page */
		);
	return $options;
	}


	/* reset DMSGuestbook  */
	function default_option() {
		$options=default_options_array();
		$count=0;
   		unset($save_options);

		$count_option=$count+1;
		for($c=0; $c<count($options); $c++) {
		$save_options.="<" . $options[$count][0] . ">" . $options[$count][1] . "</" . $options[$count][0] . ">\r\n";
   		$count++;
		}
		update_option("DMSGuestbook_options", $save_options);
	  	message("<b>Restore default settings...</b> <br />Don't forget to set the page id.", 200, 800);
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

			for($c=0; $c<count($options); $c++) {
			$part1 = explode("<" . $options[$c][0] . ">", $stringtext);
			$part2 = explode("</" . $options[$c][0] . ">", $part1[1]);
			$options[$c] = html_entity_decode($part2[0], ENT_QUOTES);
			}
	return $options;
	}


	function settablecolor($setcolor,$tablecolor) {
	if($setcolor==1) {$colorresult="EDF6FF";}
	if($setcolor==2) {$colorresult="E5F2FF";}
	if($setcolor==3) {$colorresult="CFDBE6";}
	if($tablecolor==1) {$colorresult="style='background-color:#$colorresult; padding:2px 2px;'"; }
	if($tablecolor==2) {$colorresult="style='background-color:#$colorresult; padding:0px 2px; text-align:center;'"; }
	return $colorresult;
	}


?>