<?php
/*
Plugin Name: DMSGuestbook
Plugin URI: http://danielschurter.net/
Description: The administration panel is found on the top of this site.
Version: 1.2.1
Author: Daniel M. Schurter
Author URI: http://danielschurter.net/
*/

define('DMSGUESTBOOKVERSION', "1.2.1");

//------------------- menu (DMSGuestbook, Manage)----------------------
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
//--------------------- create db while the activation process -----------------------------
	add_action('activate_dmsguestbook/admin.php', 'dmsguestbook_install');


// ------------------- version-----------
	add_action('wp_head', 'addversion');
	function addversion() {
		echo "<meta name='DMSGuestbook' content='".DMSGUESTBOOKVERSION."' />\n";
	}


//--------------------- DMSGuestbook adminpage main function -----------------------------
function dmsguestbook_meta_description_option_page() {
	version_control();
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


	<!-- some table color -->
	<? $tbc1="style='background-color:#eeeeee; padding:0px 2px;'"; ?>
	<? $tbc2="style='background-color:#dddddd; padding:0px 2px;'"; ?>
	<? $tbc3="style='background-color:#CFEBF7; text-align:center;'"; ?>

	<!-- header -->
	<div class="wrap">
    <h2>DMSGuestbook Option</h2>
    <ul>
    <li>1.) Requirement: Exec-PHP must be activated <a href="http://bluesome.net/post/2005/08/18/50/" target="_blank">[download Exec-PHP]</a></li>
    <li>2.) Create a page where you want to display the DMSGuestbook.</li>
    <li>3.) Set this code into the page: <b style="color:#0000ee; text-decoration:none;">&lt;? DMSGuestBook(); ?&gt;</b>
    (Code section, not visual, not WYSIWYG) <a href="#" onclick="Example();">Show example</a></li>
    <li>4.) Save the page and set the page id value in the red "Page ID" field.</li>
    <li>5.) Customize the guestbook to your desire!</li>
    </ul>

	<!-- example image -->
    <script type="text/javascript">
	function Example() {
  	window.open("../wp-content/plugins/dmsguestbook/img/example1.png", "Example", "width=300,height=200,scrollbars=no");
  	}
	</script>
	<br />

<?
	// global var for DMSGuestbook and option database
	global $wpdb;
	$table_name = $wpdb->prefix . "dmsguestbook";
	$table_option = $wpdb->prefix . "options";

	// user can create new DMSGuestbook database if these failed during the installation.
    if($_REQUEST[action]=="createnew") {
		$sql = $wpdb->query("CREATE TABLE " . $table_name . " (
	  	id mediumint(9) NOT NULL AUTO_INCREMENT,
	  	name varchar(50) DEFAULT '' NOT NULL,
	  	email varchar(50) DEFAULT '' NOT NULL,
	  	url varchar(50) DEFAULT '' NOT NULL,
	  	date int(10) NOT NULL,
	  	ip varchar(15) DEFAULT '' NOT NULL,
	  	message longtext NOT NULL,
	  	UNIQUE KEY id (id)
	  	)$_REQUEST[collate]");
	  	require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
	  	dbDelta($sql);
	  	message("<b>$table_name was created...</b>",200,800);
	}

	// user can delete DMSGuestbook database after the confirmation
	if($_REQUEST[action]=="delete" && $_REQUEST[delete]=="yes, i am sure") {
		$wpdb->query('DROP TABLE IF EXISTS ' . $table_name);
	  	require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
	  	message("<b>$table_name was deleted...</b>",200,800);
	}

	// user can create DMSGuestbook option if the failed during the installation.
	if($_REQUEST[action]=="createoption") {
		initialize_option();
	  	message("<b>DMSGuestbook options <br />were created...</b><br />Don't forget to set the page id.",200,800);
	}

	// user can delete all DMSGuestbook_ entries in DMSGuestbook option after confirmation.
    if($_REQUEST[action]=="deleteoption" && $_REQUEST[confirm_delete_option]=="delete") {
		$wpdb->query('DELETE FROM ' . $table_option . ' WHERE option_name LIKE "DMSGuestbook_%"');
	  	require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
	  	message("<b>All DMSGuestbook options were deleted...</b>",200,800);
	}
?>

<!-- table for DMSGuestbook and DMSGuestbook option environment-->
	<table style="width:100%;">
		<tr>
		<td style="background-color:#dddddd;padding:20px;width:500px;border: #000000 solid 1px;">
<?

		// search prefix_dmsguestbook
        $result = $wpdb->query("SHOW TABLES LIKE '$table_name'");
		if ($result > 0) {
?>
			<!-- if prefix_dmsguestbook is exist -->
			<b style="color:#00bb00;"><?=$table_name;?> is exist</b><br /><br />
  			Type "yes, i am sure" in this textfield if you want delete <?=$table_name;?>.<br />
  			<b>All guestbook data will be lost!</b><br />
  			<form name="form0" method="post" action="<?=$location;?>">
  			<input type="text" name="delete" value=""><br />
  			<input name="action" value="delete" type="hidden" />
  			<input style="font-weight:bold; margin:10px 0px; width:250px;" type="submit" value="delete <?=$table_name; ?>" />
			</form>
<?
		} else {
?>
			<!-- if prefix_dmsguestbook isn't exist -->
			<b style="color:#bb0000;padding:5px;border: #000000 solid 1px;"><?=$table_name;?> isn't exist</b><br /><br />
			<form name="form0" method="post" action="<?=$location;?>">
				  <select name="collate">
				  	<option value="DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci">utf8_unicode_ci</option>
					<option value="DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci">utf8_general_ci</option>
					<option value="">if you use mySQL 4.0.xx or lower</option>
				</select><br />
				<input name="action" value="createnew" type="hidden" />
				<input style="font-weight:bold; margin:10px 0px; width:300px;" type="submit" value="create <?=$table_name;?>" />
			</form>
			If you want use char like &auml;,&uuml;,&ouml;... and your mysql version is lower than 4.1, be sure the language
			setting is e.g. "de-iso-8859-1" or similar. Check this with your mysql graphical frontenend like phpmyadmin.
<?
		}
?>

	</td>
	<td style="background-color:#dddddd;padding:20px;width:500px;border: #000000 solid 1px;">
<?
	// search all DMSGuestbook option
	$query_options = $wpdb->get_results("SELECT * FROM $table_option WHERE option_name LIKE 'DMSGuestbook_%'");
	$num_rows_option = mysql_affected_rows();

		// set all DMSGuestbook options were exist in wp_options
		$dmsguestbook_options = 35;

		//if all DMSGuestbook option aren't exist
		if($num_rows_option!=$dmsguestbook_options)
		{
?>
		<b style="color:#bb0000">One or more DMSGuestbook options in <?=$table_option;?> are missing.</b>
		<form name="form0" method="post" action="<?=$location;?>"'>
			<input name="action" value="createoption" type="hidden" />
			<input style="font-weight:bold; margin:10px 0px; width:400px;" type="submit" value="Create new DMSGuestbook options" />
		</form>
	</td></tr></table>
<?
	}	else
			{
?>			<!-- user can delete all DMSGuestbook options from the wp option page -->
			<b style="color:#00bb00">[Status OK] <?=$num_rows_option;?> DMSGuestbook option enries in <?=$table_option;?>.</b><br /><br />
			<form name="form0" method="post" action="<?=$location;?>">
				Type "delete" to remove all DMSGuestbook option entries from the <?=$table_option;?> table.<br />
				<input type="text" name="confirm_delete_option" value=""><br />
				<input name="action" value="deleteoption" type="hidden" />
				<input style="font-weight:bold; margin:10px 0px; width:400px;" type="submit" value="Delete DMSGuestbook options fom the database" />
			</form>
	</td></tr></table>
<?
	}
?>
<!-- end table for DMSGuestbook and DMSGuestbook option environment -->


<!-- main table with all DMSGuestbook options -->
<?
	if($num_rows_option==$dmsguestbook_options)
	{
?>	<br /><br /><br />
	<b>Additional options can be found in option.php</b>
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
     		<form name="form1" method="post" action="<?=$location ?>">

 	 		<!-- page id -->
 	 		<td <? echo $tbc1; ?>>Page ID:</td>
 	 		<td <? echo $tbc1; ?>><input style="width:50px;background-color:#ee8989" name="DMSGuestbook_page_id"\
 	 		value="<?=get_option("DMSGuestbook_page_id");?>" type="text" /></td>
	 		<td <? echo $tbc1; ?>>Put the guestbook page id here</td>
	 		</tr>

			<!-- guestbook width -->
	 		<tr><td <? echo $tbc2; ?>>Guestbook width:</td>
	 		<td <? echo $tbc2; ?>><input style="width:50px;" name="DMSGuestbook_width"\
	 		value="<?=get_option("DMSGuestbook_width");?>" type="text" />%</td>
     		<td <? echo $tbc2; ?>>Guestbook width in percent</td></tr>

			<!-- seperator width -->
     		<tr><td <? echo $tbc1; ?>>Separator width:</td>
	 		<td <? echo $tbc1; ?>><input style="width:50px;" name="DMSGuestbook_width2"\
	 		value="<?=get_option("DMSGuestbook_width2");?>" type="text" />%</td>
     		<td <? echo $tbc1; ?>>Separator width in percent</td></tr>

			<!-- guestbook position -->
	 		<tr><td <? echo $tbc2; ?>>Guestbook position:</td>
	 		<td <? echo $tbc2; ?>><input style="width:50px;" name="DMSGuestbook_position"\
	 		value="<?=get_option("DMSGuestbook_position");?>" type="text" /> px</td>
     		<td <? echo $tbc2; ?>>Relative guestbook position in pixel (left to right)</td></tr>

			<!-- post per page -->
     		<tr><td <? echo $tbc1; ?>>Posts per page:</td>
     		<td <? echo $tbc1; ?>><select name="DMSGuestbook_step">
          		<option selected><?=get_option("DMSGuestbook_step");?></option>
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
     		<td <? echo $tbc1; ?>>Number of entry in each page</td></tr>

			<!-- outside border color -->
     		<td <? echo $tbc2; ?>>Outside border color:</td>
     		<td <? echo $tbc2; ?>>
			<div id="Color1_div" style="border:1px solid; background-color:#<?=get_option("DMSGuestbook_bordercolor1");?>;
			float:left;width:25px; height:25px; cursor:pointer;" onclick="show_picker('Color1','<?=get_option("DMSGuestbook_bordercolor1");?>',
			'<?=get_option("DMSGuestbook_bordercolor1");?>');">&nbsp;</div>
			<input name="DMSGuestbook_bordercolor1" type="text" size="6" value="<?=get_option("DMSGuestbook_bordercolor1");?>"
			id="Color1" onclick="show_picker(this.id, '<?=get_option("DMSGuestbook_bordercolor1");?>',
			'<?=get_option("DMSGuestbook_bordercolor1");?>');" /></td>
			<td <? echo $tbc2; ?>>Color of the outside box</td></tr>

     		<!-- textfield border color -->
			<tr><td <? echo $tbc1; ?>>Textfield border color:</td>
     		<td <? echo $tbc1; ?>>
     		<div id="Color2_div" style="border:1px solid; background-color:#<?=get_option("DMSGuestbook_bordercolor2");?>;
     		float:left; width:25px; height:25px; cursor:pointer;" onclick="show_picker('Color2', '<?=get_option("DMSGuestbook_bordercolor2");?>',
     		'<?=get_option("DMSGuestbook_bordercolor2");?>');">&nbsp;</div>
			<input name="DMSGuestbook_bordercolor2" type="text" size="6" value="<?=get_option("DMSGuestbook_bordercolor2");?>"
			id="Color2" onclick="show_picker(this.id, '<?=get_option("DMSGuestbook_bordercolor2");?>',
			'<?=get_option("DMSGuestbook_bordercolor2");?>');" />
     		<td <? echo $tbc1; ?>>Color of all textfield borders</td></tr>

     		<!-- navigation char color -->
     		<tr><td <? echo $tbc2; ?>>Navigation char color:</td>
     		<td <? echo $tbc2; ?>>
   			<div id="Color3_div" style="border:1px solid; background-color:#<?=get_option("DMSGuestbook_bordercolor3");?>;
   			float:left; width:25px; height:25px; cursor:pointer;" onclick="show_picker('Color3', '<?=get_option("DMSGuestbook_bordercolor3");?>',
   			'<?=get_option("DMSGuestbook_bordercolor3");?>');">&nbsp;</div>
			<input name="DMSGuestbook_bordercolor3" type="text" size="6" value="<?=get_option("DMSGuestbook_bordercolor3");?>"
			id="Color3" onclick="show_picker(this.id, '<?=get_option("DMSGuestbook_bordercolor3");?>',
			'<?=get_option("DMSGuestbook_bordercolor3");?>');" />
     		<td <? echo $tbc2; ?>>Define the navigation color</td></tr>

     		<!-- seperator color -->
			<tr><td <? echo $tbc1; ?>>Separator color:</td>
     		<td <? echo $tbc1; ?>>
      		<div id="Color4_div" style="border:1px solid; background-color:#<?=get_option("DMSGuestbook_hairlinecolor");?>;
      		float:left; width:25px; height:25px; cursor:pointer;" onclick="show_picker('Color4', '<?=get_option("DMSGuestbook_hairlinecolor");?>',
      		'<?=get_option("DMSGuestbook_hairlinecolor");?>');">&nbsp;</div>
			<input name="DMSGuestbook_hairlinecolor" type="text" size="6" value="<?=get_option("DMSGuestbook_hairlinecolor");?>"
			id="Color4" onclick="show_picker(this.id, '<?=get_option("DMSGuestbook_hairlinecolor");?>',
			'<?=get_option("DMSGuestbook_hairlinecolor");?>');" />
			<td <? echo $tbc1; ?>>Separator between header and body in each entry</td></tr>

			<!-- font color -->
			<tr><td <? echo $tbc2; ?>>Font color:</td>
     		<td <? echo $tbc2; ?>>
    		<div id="Color5_div" style="border:1px solid; background-color:#<?=get_option("DMSGuestbook_fontcolor1");?>;
    		float:left; width:25px; height:25px; cursor:pointer;" onclick="show_picker('Color5', '<?=get_option("DMSGuestbook_fontcolor1");?>',
    		'<?=get_option("DMSGuestbook_fontcolor1");?>');">&nbsp;</div>
			<input name="DMSGuestbook_fontcolor1" type="text" size="6" value="<?=get_option("DMSGuestbook_fontcolor1");?>"
			id="Color5" onclick="show_picker(this.id, '<?=get_option("DMSGuestbook_fontcolor1");?>', '<?=get_option("DMSGuestbook_fontcolor1");?>');" />
     		<td <? echo $tbc2; ?>>Overall font color</td></tr>

     		<!-- antispam image text color -->
     		<tr><td <? echo $tbc1; ?>>Antispam image text color:</td>
     		<td <? echo $tbc1; ?>>
    		<div id="Color6_div" style="border:1px solid; background-color:#<?=get_option("DMSGuestbook_captcha_color");?>;
    		float:left; width:25px; height:25px; cursor:pointer;" onclick="show_picker('Color6', '<?=get_option("DMSGuestbook_captcha_color");?>',
    		'<?=get_option("DMSGuestbook_captcha_color");?>');">&nbsp;</div>
			<input name="DMSGuestbook_captcha_color" type="text" size="6" value="<?=get_option("DMSGuestbook_captcha_color");?>"
			id="Color6" onclick="show_picker(this.id, '<?=get_option("DMSGuestbook_captcha_color");?>',
			'<?=get_option("DMSGuestbook_captcha_color");?>');" />
     		<td <? echo $tbc1; ?>>Antispam image text color</td></tr>

     		<!-- navigation char style -->
     		<tr><td <? echo $tbc2; ?>>Navigation char style:</td>
     		<td <? echo $tbc2; ?>>
     		<input style="width:50px;" name="DMSGuestbook_backwardarrowchar" value="<?=get_option("DMSGuestbook_backwardarrowchar");?>" type="text" />
     		<input style="width:50px;" name="DMSGuestbook_forwardarrowchar" value="<?=get_option("DMSGuestbook_forwardarrowchar");?>"
     		type="text" /></td>
     		<td <? echo $tbc2; ?>>Use a char, number or word</td></tr>

     		<!-- navigation char size -->
     		<tr><td <? echo $tbc1; ?>>Navigation char size:</td>
     		<td <? echo $tbc1; ?>><input style="width:50px;" name="DMSGuestbook_arrowsize"
     		value="<?=get_option("DMSGuestbook_arrowsize");?>" type="text" />px</td>
     		<td <? echo $tbc1; ?>>Size in pixel</td></tr>

     		<!-- date / time format / setlocale -->
     		<? setlocale(LC_TIME, get_option("DMSGuestbook_setlocale")); ?>
     		<tr><td <? echo $tbc2; ?>>Date / Time format:</td>
     		<td <? echo $tbc2; ?>><input style="width:300px;" name="DMSGuestbook_dateformat"
     		value="<?=get_option("DMSGuestbook_dateformat");?>" type="text" /><input style="width:100px;" name="DMSGuestbook_setlocale"
     		value="<?=get_option("DMSGuestbook_setlocale");?>" type="text" /><br />
     		<? echo htmlentities(strftime(get_option('DMSGuestbook_dateformat')), ENT_QUOTES); ?></td>
     		<td <? echo $tbc2; ?>>Set the date and time format. More infos:
     		<a href='http://www.php.net/manual/en/function.strftime.php' target='_blank'>Date & Time parameters</a><br />
     		set your language: e.g. en_EN, de_DE, fr_FR, it_IT, de, ge ... (must be installed on your system)
     		</td>
     		</tr>

     		<!-- caption -->
     		<tr><td <? echo $tbc1; ?>>Caption: *</td>

     		<!-- name text -->
     		<td <? echo $tbc1; ?>>
     		<input style="width:300px;" name="DMSGuestbook_name"
     		value="<?=get_option("DMSGuestbook_name");?>" type="text" />
     		Name text<br />

     		<!-- email text -->
     		<input style="width:300px;" name="DMSGuestbook_email"
     		value="<?=get_option("DMSGuestbook_email");?>" type="text" />
     		Email text<br />

			<!-- url text -->
     		<input style="width:300px;" name="DMSGuestbook_url"
     		value="<?=get_option("DMSGuestbook_url");?>" type="text" />
     		Url text<br />

     		<!-- message text -->
     		<input style="width:300px;" name="DMSGuestbook_message"
     		value="<?=get_option("DMSGuestbook_message");?>" type="text" />
     		Message text<br />

     		<!-- antispam text -->
     		<textarea style="width:300px;"
     		name="DMSGuestbook_antispam" rows="4"/><?=get_option("DMSGuestbook_antispam");?></textarea>
     		Antispam text<br />

     		<!-- mandatory text -->
     		<input style="width:300px;" name="DMSGuestbook_require"
     		value="<?=get_option("DMSGuestbook_require");?>" type="text" />
     		Mandatory text<br />

     		<!-- submit text -->
     		<input style="width:300px;" name="DMSGuestbook_submit"
     		value="<?=get_option("DMSGuestbook_submit");?>" type="text" />
     		Submit text<br />

			<!-- name error text -->
			<input style="width:300px;" name="DMSGuestbook_name_error"
			value="<?=get_option("DMSGuestbook_name_error");?>" type="text" />
			Name error text<br />

	 		<!-- email error text -->
	 		<input style="width:300px;" name="DMSGuestbook_email_error"
	 		value="<?=get_option("DMSGuestbook_email_error");?>" type="text" />
	 		Email error text<br />

	 		<!-- url error text -->
	 		<input style="width:300px;" name="DMSGuestbook_url_error"
	 		value="<?=get_option("DMSGuestbook_url_error");?>" type="text" />
	 		Url error text<br />

	 		<!-- message error text -->
	 		<input style="width:300px;" name="DMSGuestbook_message_error"
	 		value="<?=get_option("DMSGuestbook_message_error");?>" type="text" />
	 		Message error text<br />

	 		<!-- antispam error text -->
	 		<input style="width:300px;" name="DMSGuestbook_antispam_error"
	 		value="<?=get_option("DMSGuestbook_antispam_error");?>" type="text" />
	 		Antispam error text<br />

	 		<!-- success text -->
	 		<input style="width:300px;" name="DMSGuestbook_success"
	 		value="<?=get_option("DMSGuestbook_success");?>" type="text" />
	 		Success text<br /></td>

	 		<!-- caption description -->
     		<td <? echo $tbc1; ?>>
     		Set the text caption for name, email address, url address,<br />
     		message text and antispam (anti robot) text.<br /><br />
     		Mandatory text informs the user about the<br />
     		fields that need to be filled out<br />
     		<br /><br />
     		Submit text is the caption of the form submit button <br /><br />
     		Name error text, email error text, url error text,<br />
     		message error text and antispam error text,<br />
     		inform the user about missing or wrong data inputs.<br /><br />
     		Success text is shown when the guestbook<br />
     		input was saved to database.
     		</td></tr>

     		<!-- mantatory -->
     		<tr><td <? echo $tbc2; ?>>Mandatory:</td>
     		<td <? echo $tbc2; ?>>
     		<? if(get_option("DMSGuestbook_require_email")==1) {$check1 = "checked"; } else {$check1="";} ?>
     		<? if(get_option("DMSGuestbook_require_url")==1) {$check2 = "checked"; } else {$check2="";} ?>
     		<? if(get_option("DMSGuestbook_require_antispam")==0) {$check3_0 = "checked"; } else {$check3_0="";} ?>
     		<? if(get_option("DMSGuestbook_require_antispam")==1) {$check3_1 = "checked"; } else {$check3_1="";} ?>
     		<? if(get_option("DMSGuestbook_require_antispam")==2) {$check3_2 = "checked"; } else {$check3_2="";} ?>
     		<input type="checkbox" name="DMSGuestbook_require_email" value="1" <? echo $check1; ?>> Email <br />
     		<input type="checkbox" name="DMSGuestbook_require_url" value="1" <? echo $check2; ?>> Url <br />
     		<input type="radio" name="DMSGuestbook_require_antispam" value="0" <? echo $check3_0; ?>> Antispam off<br />
     		<input type="radio" name="DMSGuestbook_require_antispam" value="1" <? echo $check3_1; ?>> Antispam image<br />
     		<input type="radio" name="DMSGuestbook_require_antispam" value="2" <? echo $check3_2; ?>> Antispam mathematic figures</td>

	 		<td <? echo $tbc2; ?>>User must fill out: Email text field / Url address field / Antispam field <br /><br />
	 		<hr style="border: 1px solid;"></hr>
	 		Image: <br /><img src="../wp-content/plugins/dmsguestbook/captcha/captcha.php" alt="[do you see this image?]"><br />
	 		If you don't see the image here, check the xfiles.ttf and captcha.png permission in your captcha folder.<br /><br />
	 		<? truetype_permission("xfiles.ttf"); ?><br /><? truetype_permission("captcha.png"); ?><br /><br />
	 		Requirement: PNG support ->
	 			<? if (ImageTypes() & IMG_PNG) {
    				echo "[PNG support is available]"; } ?>
			    <? $array = gd_info();?>
			<br />
	 		Requirement: GD 2.0.1 or above -> <? echo $array["GD Version"];?>
	 		<br />
	 		Requirement: FreeType support -> <? if($array["FreeType Support"]==1) {echo "[FreeType support enabled]";};?>
	 		<br /><br />
	 		<hr style="border: 1px solid;"></hr>
	 		Mathematic figures: <br />4 + 9 = <input style="width:30px;" type="text" name="" value="13"><br />
	 		</td>
	 		</tr>

	 		<!-- visible data -->
	 		<tr><td <? echo $tbc1; ?>>Visible data:</td>
     		<td <? echo $tbc1; ?>>
     		<? if(get_option("DMSGuestbook_show_ip")==1) {$check1 = "checked"; } else {$check1="";} ?>
     		<? if(get_option("DMSGuestbook_show_email")==1) {$check2 = "checked"; } else {$check2="";} ?>
     		<? if(get_option("DMSGuestbook_show_url")==1) {$check3 = "checked"; } else {$check3="";} ?>
     		<input type="checkbox" name="DMSGuestbook_show_ip" value="1" <? echo $check1; ?>> IP adress <br />
     		<input type="checkbox" name="DMSGuestbook_show_email" value="1" <? echo $check2; ?>> Email <br />
     		<input type="checkbox" name="DMSGuestbook_show_url" value="1" <? echo $check3; ?>> Url
			</td>
	 		<td <? echo $tbc1; ?>>Visible data for everyone in each entry</td>
		   </tr>
		  </table>

	   <!-- submit button -->
	   <tr><td <? echo $tbc3; ?>>
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
		<form name="form3" method="post" action="<?=$location ?>">
		<input name="action2" value="default_settings" type="hidden" />
		<input style="font-weight:bold; margin:10px 0px;" type="submit"
		value="Restore default settings - All data will be replaced" onclick="return confirm('Would you really to restore all data?');">
     	</form>
     	</td>
		<td style="width:50px;"></td>

	 	<!-- default language english-->
	 	<td style="text-align:center;">
	 	* Switch text caption to english language
	 	<form method="post" action="<?=$location ?>">
	 	<input style="border:1px;" name="true" value="default_english"
	 	type="image" src="../wp-content/plugins/dmsguestbook/img/uk.gif" alt="default english">
	 	</form>
	 	</td>
     	<td style="width:30px;"></td>

	 	<!-- default language german -->
	 	<td style="text-align:center;">
	 	* Switch text caption to german language
	 	<form method="post" action="<?=$location ?>">
	 	<input style="border:1px;" name="true" value="default_german"
	 	type="image" src="../wp-content/plugins/dmsguestbook/img/ger.gif" alt="default deutsch">
	 	</form>
	 	</td>
	 	<td style="width:30px;"></td>

	 	<!-- default language swiss german-->
	 	<td style="text-align:center;">
	 	* Switch text caption to swiss german language
	 	<form method="post" action="<?=$location ?>">
	 	<input style="border:1px;" name="true"
	 	value="default_swissgerman" type="image" src="../wp-content/plugins/dmsguestbook/img/ch.gif" alt="default deutsch">
	 	</form>
	 	</td>
	 </tr>
	 </table>
	 </div>
<?
	}
}	//--------------------- end of DMSGuestbook adminpage main function -----------------------------




	// --------------------- some arrays ------------------
	// all options
	$option = array (
	"10","","100","35","0","EEEEEE","AAAAAA","DEDEDE",
	"000000","000000",">","<","20","0","0","1",
	"0","1","1","000000","%a, %e %B %Y %H:%M:%S %z","en_EN"
	);

	// english text
	$english = array (
	"Name",
	"Email",
	"Url",
	"Text",
	"<b>Antispam measures</b><br />Please insert the letter and number combination into the text field before submitting the guestbook entry.",
	"mandatory",
	"Inscribe",
	"Name is too short!",
	"Invalid email adress!",
	"Invalid url adress!",
	"Text is too short!",
	"Wrong letter-figure combination!",
	"Thank you for this guestbook entry!"
	);

	// german text
	$german = array (
	"Name",
	"Email",
	"Url",
	"Text",
	"<b>Antispam Massnahme</b><br />Vor dem Absenden des G&auml;stebucheintrags, die Buchstaben -und Zahlenkombination in das Textfeld eintragen.",
	"erforderlich",
	"eintragen",
	"Name ist zu kurz!",
	"Ung&uuml;ltige E-Mail Adresse!",
	"Ung&uuml;ltige Url Adresse!",
	"Text ist zu kurz!",
	"Die Buchstaben -und Zahlenkombination ist falsch!",
	"Danke f&uuml;r deinen G&auml;stebuch Eintrag!"
	);

	// swiss german text :-)
	$swissgerman = array (
	"Nam&auml;",
	"Iimeil",
	"Uuueer&auml;l",
	"Tegscht",
	"<b>Antisp&auml;m Massnahme</b><br />Vor em Abs&auml;nde vom Geschtebuech Ihtrag, d Buechstabe -und Zahl&auml;kombination is Tegschtf&auml;ld 			iitr&auml;hge.",
	"bruchts",
	"iihtr&auml;hge",
	"De Name isch zchurz!",
	"Ung&uuml;ltigi Imeil Adr&auml;sse!",
	"Ung&uuml;ltigi Uuueer&auml;l Adr&auml;sse!",
	"Tegscht isch zchurz!",
	"D Buechstabe und Zahlekombination isch falsch!",
	"Messi f&uuml;r din Geschtebuech ihtrag!"
	);
	// --------------------- end some arrays ------------------



	// --------------------- default: english ------------------
	if ('default_english' == $HTTP_POST_VARS['true'])
	{
		update_option("DMSGuestbook_name",					$english[0]);
		update_option("DMSGuestbook_email",					$english[1]);
		update_option("DMSGuestbook_url",					$english[2]);
		update_option("DMSGuestbook_message",				$english[3]);
		update_option("DMSGuestbook_antispam",				$english[4]);
		update_option("DMSGuestbook_require", 				$english[5]);
		update_option("DMSGuestbook_submit",				$english[6]);
		update_option("DMSGuestbook_name_error",			$english[7]);
		update_option("DMSGuestbook_email_error",			$english[8]);
		update_option("DMSGuestbook_url_error",				$english[9]);
		update_option("DMSGuestbook_message_error", 		$english[10]);
		update_option("DMSGuestbook_antispam_error",		$english[11]);
		update_option("DMSGuestbook_success",				$english[12]);

		message("<b>[English]<br />Don't forget to save...</b>",200,800);
	}


	// --------------------- default: german ------------------
	if ('default_german' == $HTTP_POST_VARS['true'])
	{
		update_option("DMSGuestbook_name",					$german[0]);
		update_option("DMSGuestbook_email",					$german[1]);
		update_option("DMSGuestbook_url",					$german[2]);
		update_option("DMSGuestbook_message",				$german[3]);
		update_option("DMSGuestbook_antispam",				$german[4]);
		update_option("DMSGuestbook_require", 				$german[5]);
		update_option("DMSGuestbook_submit",				$german[6]);
		update_option("DMSGuestbook_name_error",			$german[7]);
		update_option("DMSGuestbook_email_error",			$german[8]);
		update_option("DMSGuestbook_url_error",				$german[9]);
		update_option("DMSGuestbook_message_error", 		$german[10]);
		update_option("DMSGuestbook_antispam_error",		$german[11]);
		update_option("DMSGuestbook_success",				$german[12]);

		message("<b>[German]<br />Don't forget to save...</b>",200,800);
	}



	// --------------------- default: swiss german :-) ------------------
	if ('default_swissgerman' == $HTTP_POST_VARS['true'])
	{
		update_option("DMSGuestbook_name",					$swissgerman[0]);
		update_option("DMSGuestbook_email",					$swissgerman[1]);
		update_option("DMSGuestbook_url",					$swissgerman[2]);
		update_option("DMSGuestbook_message",				$swissgerman[3]);
		update_option("DMSGuestbook_antispam",				$swissgerman[4]);
		update_option("DMSGuestbook_require", 				$swissgerman[5]);
		update_option("DMSGuestbook_submit",				$swissgerman[6]);
		update_option("DMSGuestbook_name_error",			$swissgerman[7]);
		update_option("DMSGuestbook_email_error",			$swissgerman[8]);
		update_option("DMSGuestbook_url_error",				$swissgerman[9]);
		update_option("DMSGuestbook_message_error", 		$swissgerman[10]);
		update_option("DMSGuestbook_antispam_error",		$swissgerman[11]);
		update_option("DMSGuestbook_success",				$swissgerman[12]);

		message("<b>[Swiss german]<br />Don't forget to save...</b>",200,800);
	}


	// --------------------- write DMSGuestbook option in wordpress options database ------------------
	if ('insert' == $HTTP_POST_VARS['action'])
	{
		// all text will be quotet in html
		// possible char " will be quotet in &amp;quot
		$value1 = htmlentities($HTTP_POST_VARS['DMSGuestbook_forwardarrowchar'], ENT_QUOTES);
		$value1 = str_replace("\"", "&amp;quot;", $HTTP_POST_VARS['DMSGuestbook_forwardarrowchar']);

		$value2 = htmlentities($HTTP_POST_VARS['DMSGuestbook_backwardarrowchar'], ENT_QUOTES);
		$value2 = str_replace("\"", "&amp;quot;", $HTTP_POST_VARS['DMSGuestbook_backwardarrowchar']);

		$value3 = htmlentities($HTTP_POST_VARS['DMSGuestbook_name'], ENT_QUOTES);
		$value3 = str_replace("\"", "&amp;quot;", $HTTP_POST_VARS['DMSGuestbook_name']);

		$value4 = htmlentities($HTTP_POST_VARS['DMSGuestbook_email'], ENT_QUOTES);
		$value4 = str_replace("\"", "&amp;quot;", $HTTP_POST_VARS['DMSGuestbook_email']);

		$value5 = htmlentities($HTTP_POST_VARS['DMSGuestbook_url'], ENT_QUOTES);
		$value5 = str_replace("\"", "&amp;quot;", $HTTP_POST_VARS['DMSGuestbook_url']);

		$value6 = htmlentities($HTTP_POST_VARS['DMSGuestbook_message'], ENT_QUOTES);
		$value6 = str_replace("\"", "&amp;quot;", $HTTP_POST_VARS['DMSGuestbook_message']);

		$value7 = htmlentities($HTTP_POST_VARS['DMSGuestbook_antispam'], ENT_QUOTES);
		$value7 = str_replace("\"", "&amp;quot;", $HTTP_POST_VARS['DMSGuestbook_antispam']);

		$value8 = htmlentities($HTTP_POST_VARS['DMSGuestbook_require'], ENT_QUOTES);
		$value8 = str_replace("\"", "&amp;quot;", $HTTP_POST_VARS['DMSGuestbook_require']);

		$value9 = htmlentities($HTTP_POST_VARS['DMSGuestbook_submit'], ENT_QUOTES);
		$value9 = str_replace("\"", "&amp;quot;", $HTTP_POST_VARS['DMSGuestbook_submit']);

		$value10 = htmlentities($HTTP_POST_VARS['DMSGuestbook_name_error'], ENT_QUOTES);
		$value10 = str_replace("\"", "&amp;quot;", $HTTP_POST_VARS['DMSGuestbook_name_error']);

		$value11 = htmlentities($HTTP_POST_VARS['DMSGuestbook_email_error'], ENT_QUOTES);
		$value11 = str_replace("\"", "&amp;quot;", $HTTP_POST_VARS['DMSGuestbook_email_error']);

		$value12 = htmlentities($HTTP_POST_VARS['DMSGuestbook_url_error'], ENT_QUOTES);
		$value12 = str_replace("\"", "&amp;quot;", $HTTP_POST_VARS['DMSGuestbook_url_error']);

		$value13 = htmlentities($HTTP_POST_VARS['DMSGuestbook_message_error'], ENT_QUOTES);
		$value13 = str_replace("\"", "&amp;quot;", $HTTP_POST_VARS['DMSGuestbook_message_error']);

		$value14 = htmlentities($HTTP_POST_VARS['DMSGuestbook_antispam_error'], ENT_QUOTES);
		$value14 = str_replace("\"", "&amp;quot;", $HTTP_POST_VARS['DMSGuestbook_antispam_error']);

		$value15 = htmlentities($HTTP_POST_VARS['DMSGuestbook_success'], ENT_QUOTES);
		$value15 = str_replace("\"", "&amp;quot;", $HTTP_POST_VARS['DMSGuestbook_success']);

    	update_option("DMSGuestbook_step",$HTTP_POST_VARS['DMSGuestbook_step']);
		update_option("DMSGuestbook_page_id",$HTTP_POST_VARS['DMSGuestbook_page_id']);
		update_option("DMSGuestbook_width",$HTTP_POST_VARS['DMSGuestbook_width']);
		update_option("DMSGuestbook_width2",$HTTP_POST_VARS['DMSGuestbook_width2']);
		update_option("DMSGuestbook_position",$HTTP_POST_VARS['DMSGuestbook_position']);
		update_option("DMSGuestbook_hairlinecolor",$HTTP_POST_VARS['DMSGuestbook_hairlinecolor']);
		update_option("DMSGuestbook_bordercolor1",$HTTP_POST_VARS['DMSGuestbook_bordercolor1']);
		update_option("DMSGuestbook_bordercolor2",$HTTP_POST_VARS['DMSGuestbook_bordercolor2']);
		update_option("DMSGuestbook_bordercolor3",$HTTP_POST_VARS['DMSGuestbook_bordercolor3']);
		update_option("DMSGuestbook_fontcolor1",$HTTP_POST_VARS['DMSGuestbook_fontcolor1']);
		update_option("DMSGuestbook_forwardarrowchar",$value1);
		update_option("DMSGuestbook_backwardarrowchar",$value2);
		update_option("DMSGuestbook_arrowsize",htmlentities($HTTP_POST_VARS['DMSGuestbook_arrowsize'], ENT_QUOTES));
		update_option("DMSGuestbook_name",$value3);
		update_option("DMSGuestbook_email",$value4);
		update_option("DMSGuestbook_url",$value5);
		update_option("DMSGuestbook_message",$value6);
		update_option("DMSGuestbook_antispam",$value7);
		update_option("DMSGuestbook_require",$value8);
		update_option("DMSGuestbook_submit",$value9);
		update_option("DMSGuestbook_name_error",$value10);
		update_option("DMSGuestbook_email_error",$value11);
		update_option("DMSGuestbook_url_error",$value12);
		update_option("DMSGuestbook_message_error",$value13);
		update_option("DMSGuestbook_antispam_error",$value14);
		update_option("DMSGuestbook_success",$value15);

		update_option("DMSGuestbook_require_email",htmlentities($HTTP_POST_VARS['DMSGuestbook_require_email'], ENT_QUOTES));
		update_option("DMSGuestbook_require_url",htmlentities($HTTP_POST_VARS['DMSGuestbook_require_url'], ENT_QUOTES));
		update_option("DMSGuestbook_require_antispam",htmlentities($HTTP_POST_VARS['DMSGuestbook_require_antispam'], ENT_QUOTES));
		update_option("DMSGuestbook_show_ip",htmlentities($HTTP_POST_VARS['DMSGuestbook_show_ip'], ENT_QUOTES));
		update_option("DMSGuestbook_show_email",htmlentities($HTTP_POST_VARS['DMSGuestbook_show_email'], ENT_QUOTES));
		update_option("DMSGuestbook_show_url",htmlentities($HTTP_POST_VARS['DMSGuestbook_show_url'], ENT_QUOTES));
		update_option("DMSGuestbook_captcha_color",htmlentities($HTTP_POST_VARS['DMSGuestbook_captcha_color'], ENT_QUOTES));
		update_option("DMSGuestbook_dateformat",htmlentities($HTTP_POST_VARS['DMSGuestbook_dateformat'], ENT_QUOTES));
		update_option("DMSGuestbook_setlocale",htmlentities($HTTP_POST_VARS['DMSGuestbook_setlocale'], ENT_QUOTES));

		message("<b>saved...</b>",200,800);
	}
	// --------------------- end of write DMSGuestbook option in wordpress options database ------------------



	// ------ reset DMSGuestbook -----------
	if ('default_settings' == $HTTP_POST_VARS['action2']) {
	default_option();
	}





//--------------------- manage guestbook entries ---------------------------
function dmsguestbook2_meta_description_option_page() {
		version_control();
?>
		<div class="wrap">
		<h2>Manage DMSGuestbook</h2>
		<ul>
	 		<li>You can edit all text fields, except the date.</li>
	 		<li>You can use HTML tags in the name and text box. But, be care with this :-)</li>
			<li>If you edit the url field, don't delete the "http(s)://" prefix.</li>
		</ul>
<?
		// maximum guestbook entries were displayed on page
		$gb_step=get_option("DMSGuestbook_step");

		// initialize
		if($_REQUEST[from]=="") {$_REQUEST[from]=0; $_REQUEST[select]=1;}

		// global var for DMSGuestbook
		global $wpdb;
		$table_name = $wpdb->prefix . "dmsguestbook";

		// count all database entries //mysql_query
    	$query0 = $wpdb->get_results("SELECT * FROM  $table_name");
    	$num_rows0 = mysql_affected_rows();

		// read all guestbook entries
		$query1 = $wpdb->get_results("SELECT * FROM  $table_name ORDER BY id DESC LIMIT $_REQUEST[from],$gb_step;");
		$num_rows1 = mysql_affected_rows();

?>

		<br /><br />
		<div style="width:<?=$gb_width;?>; text-align:center;">
		<div style="font-size:11px;">(<?=$num_rows0;?>)</div>

<?
		for($q=0; $q<$num_rows0; ($q=$q+$gb_step))
		{
		$y++;
			if($_REQUEST[select]==$y) {
?>			<a style="color:#bb1100; text-decoration:none;" href="admin.php?page=Manage&from=<?=$q;?>&select=<?=$y;?>"><?=$y;?></a>
<?			}
			else {
?>				 <a style="color:#000000; text-decoration:none;" href="admin.php?page=Manage&from=<?=$q;?>&select=<?=$y;?>"><?=$y;?></a>
<?				 }
		}
?>		</div>
		<br /><br />

<?		$tbc3="style='background-color:#CFEBF7; text-align:center; height:35px;'";
?>
		<table style="border:1px solid #000000; width:100%;">
		<tr <? echo $tbc3; ?>>
			<th>ID</th>
	 		<th>Name</th>
	 		<th>Message</th>
	 		<th>Header</th>
	 		<th></th>
	 		<th></th>
		</tr>

<?

			foreach ($query1 as $result) {
			$bgcolor="eeeeee";
	    		if($bgcolor=="dddddd") {$bgcolor="eeeeee";} else {$bgcolor="dddddd";}

	 			// build the data / time variable
				$sec=date("s", "$result->date");
				$min=date("i", "$result->date");
				$hour=date("H", "$result->date");
				$day=date("d", "$result->date");
				$month=date("m", "$result->date");
				$year=date("Y", "$result->date");
				$date = strftime (get_option("DMSGuestbook_dateformat"), mktime ($hour, $min, $sec, $month, $day, $year));

?>
	 			<tr>
	 			<form name="edit_form" method="post" action="<?=$location ?>">
	 			<td style="font-size:10px; border:1px solid #eeeeee; background-color:#<? echo $bgcolor; ?>"><?=$result->id;?></td>
	 			<td style="border:1px solid #eeeeee; background-color:#<?=$bgcolor;?>">
	 			<input style="font-size:10px; border:1px solid #eeeeee;" type="text" name="gb_name" value="<?=$result->name;?>"></td>
	 			<td style="border:1px solid #eeeeee; background-color:#<?=$bgcolor;?>">
	 			<textarea style="height:80px; width:500px;font-size:10px;" name="gb_message"><?=$result->message;?></textarea></td>
	 			<td style="font-size:10px; border:1px solid #eeeeee; background-color:#<?=$bgcolor;?>">

	 			<table border="0">
	 			<tr><td style="font-size:10px;">Date:</td> <td style="font-size:10px;"><?=$date;?></td></tr>
	 			<tr><td style="font-size:10px;">IP:</td> <td><input style="font-size:10px; width:200px;"
	 			type="text" name="gb_ip" value="<?=$result->ip; ?>" maxlength="15"></td></tr>
	 			<tr><td style="font-size:10px;">Email: </td> <td><input style="font-size:10px;  width:200px;"
	 			type="text" name="gb_email" value="<?=$result->email;?>"></td></tr>
	 			<tr><td style="font-size:10px;">Url: </td> <td><input style="font-size:10px;  width:200px;"
	 			type="text" name="gb_url" value="<?=$result->url;?>"></td></tr>
				</table>

	 			<td style="font-size:10px; border:1px solid #eeeeee; background-color:#<?=$bgcolor;?>">
	 			<form name="edit_form" method="post" action="<?=$location ?>">
	 			<input name="editdata" value="edit" type="hidden" />
	 			<input name="id" value="<?=$result->id;?>" type="hidden" />
	 			<input style="font-weight:bold; color:#0000bb; margin:10px 0px;"
	 			type="submit" value="edit" onclick="return confirm('Would you really to edit this dataset?');">
	 			</form>
	 			</td>

	 			<td style="font-size:10px; border:1px solid #eeeeee; background-color:#<?=$bgcolor;?>">
	 			<form name="delete_form" method="post" action="<?=$location ?>">
	 			<input name="deletedata" value="delete" type="hidden" />
				<input name="id" value="<?=$result->id;?>" type="hidden" />
	 			<input style="font-weight:bold; color:#bb0000; margin:10px 0px;" type="submit"
	 			value="X" onclick="return confirm('Would you really to delete this dataset?');">
	 			</form>
	 			</td></tr>
<?			}
?>
		</table>
		</div>
<?
	} //--------------------- end of manage guestbook entries ---------------------------



	//--------------------- edit ---------------------------
	if ('edit' == $HTTP_POST_VARS['editdata']) {
		$table_name = $wpdb->prefix . "dmsguestbook";
		$updatedata = $wpdb->query("UPDATE $table_name SET
		name 		= 	'$_REQUEST[gb_name]',
		email 		= 	'$_REQUEST[gb_email]',
		url 		= 	'$_REQUEST[gb_url]',
		ip	 		= 	'$_REQUEST[gb_ip]',
		message 	= 	'$_REQUEST[gb_message]'
		WHERE id = '$_REQUEST[id]'");
  		$update = mysql_query($updatedata);
		message("<b>Dataset ($_REQUEST[id]) was saved</b>", 140, 800);
	}

	//--------------------- delete ---------------------------
	if ('delete' == $HTTP_POST_VARS['deletedata']) {
		$table_name = $wpdb->prefix . "dmsguestbook";
		$deletedata = $wpdb->query("DELETE FROM $table_name WHERE id = '$_REQUEST[id]'");
		$delete = mysql_query($deletedata);
		message("<b>Dataset ($_REQUEST[id]) was deleted...</b>", 140, 800);
	}


//--------------------- end of manage guestbook entries ---------------------------








	# #	# # # # # - FUNCTIONS - # # # # # # #

	// --------------------- DMSGuestbook first time database install ------------------
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
	  		UNIQUE KEY id (id)
	  		)DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
      		require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
      		dbDelta($sql);
   			}

  		initialize_option();
		}


	//--------------- DMSGuestbook option first time initialize -------------------------
	function initialize_option() {
		foreach($GLOBALS['option'] as $temp_option) {
   		$option[]=$temp_option;}

   		foreach($GLOBALS['english'] as $temp_english) {
   		$english[]=$temp_english; }

	  if(!get_option("DMSGuestbook_step")) 					{update_option("DMSGuestbook_step", 		$option[0]);}
	  if(!get_option("DMSGuestbook_page_id")) 				{update_option("DMSGuestbook_page_id",		$option[1]);}
	  if(!get_option("DMSGuestbook_width"))					{update_option("DMSGuestbook_width",		$option[2]);}
	  if(!get_option("DMSGuestbook_width2"))				{update_option("DMSGuestbook_width2",		$option[3]);}
	  if(!get_option("DMSGuestbook_position"))				{update_option("DMSGuestbook_position",		$option[4]);}
	  if(!get_option("DMSGuestbook_hairlinecolor"))			{update_option("DMSGuestbook_hairlinecolor",$option[5]);}
	  if(!get_option("DMSGuestbook_bordercolor1"))			{update_option("DMSGuestbook_bordercolor1",	$option[6]);}
	  if(!get_option("DMSGuestbook_bordercolor2"))			{update_option("DMSGuestbook_bordercolor2",	$option[7]);}
	  if(!get_option("DMSGuestbook_bordercolor3"))			{update_option("DMSGuestbook_bordercolor3",	$option[8]);}
	  if(!get_option("DMSGuestbook_fontcolor1"))			{update_option("DMSGuestbook_fontcolor1",	$option[9]);}
	  if(!get_option("DMSGuestbook_forwardarrowchar"))		{update_option("DMSGuestbook_forwardarrowchar",	$option[10]);}
	  if(!get_option("DMSGuestbook_backwardarrowchar"))		{update_option("DMSGuestbook_backwardarrowchar",$option[11]);}
	  if(!get_option("DMSGuestbook_arrowsize"))				{update_option("DMSGuestbook_arrowsize",	$option[12]);}
	  if(!get_option("DMSGuestbook_require_email"))			{update_option("DMSGuestbook_require_email",$option[13]);}
	  if(!get_option("DMSGuestbook_require_url"))			{update_option("DMSGuestbook_require_url",	$option[14]);}
	  if(!get_option("DMSGuestbook_require_antispam"))		{update_option("DMSGuestbook_require_antispam",	$option[15]);}
	  if(!get_option("DMSGuestbook_show_ip"))				{update_option("DMSGuestbook_show_ip",		$option[16]);}
	  if(!get_option("DMSGuestbook_show_url"))				{update_option("DMSGuestbook_show_url",		$option[17]);}
	  if(!get_option("DMSGuestbook_show_email"))			{update_option("DMSGuestbook_show_email",	$option[18]);}
	  if(!get_option("DMSGuestbook_captcha_color"))			{update_option("DMSGuestbook_captcha_color",$option[19]);}
	  if(!get_option("DMSGuestbook_dateformat"))			{update_option("DMSGuestbook_dateformat",	$option[20]);}
	  if(!get_option("DMSGuestbook_setlocale"))				{update_option("DMSGuestbook_setlocale",	$option[21]);}

	  if(!get_option("DMSGuestbook_name"))					{update_option("DMSGuestbook_name",			$english[0]);}
	  if(!get_option("DMSGuestbook_email"))					{update_option("DMSGuestbook_email",		$english[1]);}
	  if(!get_option("DMSGuestbook_url"))					{update_option("DMSGuestbook_url",			$english[2]);}
	  if(!get_option("DMSGuestbook_message"))				{update_option("DMSGuestbook_message",		$english[3]);}
	  if(!get_option("DMSGuestbook_antispam"))				{update_option("DMSGuestbook_antispam",		$english[4]);}
	  if(!get_option("DMSGuestbook_require"))				{update_option("DMSGuestbook_require",		$english[5]);}
	  if(!get_option("DMSGuestbook_submit"))				{update_option("DMSGuestbook_submit",		$english[6]);}
	  if(!get_option("DMSGuestbook_name_error"))			{update_option("DMSGuestbook_name_error",	$english[7]);}
	  if(!get_option("DMSGuestbook_email_error"))			{update_option("DMSGuestbook_email_error",	$english[8]);}
	  if(!get_option("DMSGuestbook_url_error"))				{update_option("DMSGuestbook_url_error",	$english[9]);}
	  if(!get_option("DMSGuestbook_message_error"))			{update_option("DMSGuestbook_message_error",$english[10]);}
	  if(!get_option("DMSGuestbook_antispam_error"))		{update_option("DMSGuestbook_antispam_error",	$english[11]);}
	  if(!get_option("DMSGuestbook_success"))				{update_option("DMSGuestbook_success",		$english[12]);}
	}


	//--------------- DMSGuestbook initialize when DMSGuestbook() is set in a page-------------------------
	function DMSGuestBook() {
		global $wpdb;
		$table_name2 = $wpdb->prefix . "posts";
		$page_id=get_option("DMSGuestbook_page_id");

		# check if exist DMSGuestBook(); in posts
		$query_posts = $wpdb->get_results("SELECT * FROM $table_name2 WHERE ID = '$page_id'");
		$num_rows_posts = mysql_affected_rows();
			if($num_rows_posts==1)
			{
			include_once ("dmsguestbook.php");
			}
			else	{
					echo "Wrong page id or missing <b style='color:#0000ee; text-decoration:none;'>
					&lt;? DMSGuestBook(); ?&gt;</b> in the guestbook page.";
					}
		}


	// --------------------- reset DMSGuestbook ------------------
	function default_option() {
   		foreach($GLOBALS['option'] as $temp_option) {
   		$option[]=$temp_option; }

		foreach($GLOBALS['english'] as $temp_english) {
   		$english[]=$temp_english; }

	  update_option("DMSGuestbook_step",		$option[0]);
	  update_option("DMSGuestbook_page_id",		$option[1]);
	  update_option("DMSGuestbook_width",		$option[2]);
	  update_option("DMSGuestbook_width2",		$option[3]);
	  update_option("DMSGuestbook_position",	$option[4]);
	  update_option("DMSGuestbook_hairlinecolor",	$option[5]);
	  update_option("DMSGuestbook_bordercolor1",	$option[6]);
	  update_option("DMSGuestbook_bordercolor2",	$option[7]);
	  update_option("DMSGuestbook_bordercolor3",	$option[8]);
	  update_option("DMSGuestbook_fontcolor1",		$option[9]);
	  update_option("DMSGuestbook_forwardarrowchar",$option[10]);
	  update_option("DMSGuestbook_backwardarrowchar",	$option[11]);
	  update_option("DMSGuestbook_arrowsize",		$option[12]);
	  update_option("DMSGuestbook_require_email",	$option[13]);
	  update_option("DMSGuestbook_require_url",		$option[14]);
	  update_option("DMSGuestbook_require_antispam",	$option[15]);
	  update_option("DMSGuestbook_show_ip",				$option[16]);
	  update_option("DMSGuestbook_show_email",		$option[17]);
	  update_option("DMSGuestbook_show_url",		$option[18]);
	  update_option("DMSGuestbook_captcha_color",	$option[19]);
	  update_option("DMSGuestbook_dateformat",		$option[20]);
	  update_option("DMSGuestbook_setlocale",		$option[21]);

	  update_option("DMSGuestbook_name",			$english[0]);
	  update_option("DMSGuestbook_email",			$english[1]);
	  update_option("DMSGuestbook_url",				$english[2]);
	  update_option("DMSGuestbook_message",			$english[3]);
	  update_option("DMSGuestbook_antispam",		$english[4]);
	  update_option("DMSGuestbook_require",			$english[5]);
	  update_option("DMSGuestbook_submit",			$english[6]);
	  update_option("DMSGuestbook_name_error",		$english[7]);
	  update_option("DMSGuestbook_email_error",		$english[8]);
	  update_option("DMSGuestbook_url_error",		$english[9]);
	  update_option("DMSGuestbook_message_error",	$english[10]);
	  update_option("DMSGuestbook_antispam_error",	$english[11]);
	  update_option("DMSGuestbook_success",			$english[12]);
	  message("<b>Restore default settings...</b> <br />Don't forget to set the page id.", 200, 800);
	}
	// --------------------- end of reset DMSGuestbook ------------------


	// --------------------- DMSGuestbook admin message handling ------------------
	function message ($message_text, $top, $left) {
		$date=date("H:i:s");
		echo "<div style='position:absolute; top:" . $top . "px; left:" . $left . "px;' id='message' class='updated fade'><p>
		$message_text <br /></p><p style='font-size:10px;'>[$date]</p>
		<img  style='position:absolute; top:-5px; left:5px; height:13px; width:9px;'
		src='../wp-content/plugins/dmsguestbook/img/icon_pin.png'></div>";
		}


	// --------------------- show phpinfo()------------------
	function dmsguestbook3_meta_description_option_page() {
		echo "<div class='wrap'>";
		phpinfo();
		echo "</div>";
		}

	// --------------------- show faq------------------
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

	// version control
	function version_control()
		{
		@$file = fopen ("http://danielschurter.net/dmsguestbook/release.txt", "r");
			if (!$file) {
    		}
		$line = @fgets ($file, 1024);
		@fclose($file);

		echo "v" . DMSGUESTBOOKVERSION;
		if(DMSGUESTBOOKVERSION < "$line") {echo "<br /><a href='http://wordpress.org/extend/plugins/dmsguestbook' target='_blank'>A new version is available</a>";}
		}


	// show permission
	function truetype_permission($file) {
		$abspath = getcwd();
    	$abspath = str_replace("\\","/", $abspath);
    	clearstatcache();
		$fileperms=fileperms("../wp-content/plugins/dmsguestbook/captcha/$file");
		$fileperms = decoct($fileperms);
		echo "<b>" . $file . "</b>" . " have permission: " . substr($fileperms, 2, 6);
		}



?>