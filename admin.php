<?php
/*
Plugin Name: DMSGuestbook
Plugin URI: http://danielschurter.net/
Description: The administration panel is found on the top of this site.
Version: 1.1
Author: Daniel M. Schurter
Author URI: http://danielschurter.net/
*/


//------------------- Menu ----------------------
add_action('admin_menu', 'add_dmsguestbook');

function add_dmsguestbook() {
	add_menu_page(__('DMSGuestbook', 'dmsguestbook'), __('DMSGuestbook', 'dmsguestbook'), 'edit_others_posts', 'dmsguestbook', 'dmsguestbook_meta_description_option_page');

	add_submenu_page( 'dmsguestbook' , __('Manage', 'dmsguestbook'), __('Manage', 'dmsguestbook'), 'edit_others_posts', 'Manage', 'dmsguestbook2_meta_description_option_page');
}








//--------------------- DMSGuestbook admin -----------------------------


//DB beim aktivieren des Plugins erstellen
add_action('activate_dmsguestbook/admin.php', 'dmsguestbook_install');

function dmsguestbook_meta_description_option_page() {
?>

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
		var lnk = "../wp-content/plugins/dmsguestbook/color_picker/color_picker_files/color_picker_interface.html?cur_color="+Current_Color+"&pre_color="+Previous_Color;
		window.open(lnk, "", "width=465, height=350");
	}
</script>



<? $tbc1="style='background-color:#eeeeee; padding:0px 2px;'"; ?>
<? $tbc2="style='background-color:#dddddd; padding:0px 2px;'"; ?>
<? $tbc3="style='background-color:#CFEBF7; text-align:center;'"; ?>

<!-- Start Optionen im Adminbereich (xhtml, außerhalb PHP) -->
     <div class="wrap">
     <h2>DMSGuestbook Option</h2>


     <ul>
     <li>1.) Requirement: Exec-PHP must be activated <a href="http://bluesome.net/post/2005/08/18/50/" target="_blank">[download Exec-PHP]</a></li>
     <li>2.) Create a page where you want to display the DMSGuestbook.</li>
     <li>3.) Set this code into the page: <b style="color:#0000ee; text-decoration:none;">&lt;? DMSGuestBook(); ?&gt;</b> (Code section, not visual, not WYSIWYG) <a href="#" onclick="Example();">Show example</a></li>
     <li>4.) Save the page and set the page id value in the red "Page ID" field.</li>
     <li>5.) Customize the guestbook to your desire!</li>
     </ul>

     <script type="text/javascript">
	 function Example() {
  	 window.open("../wp-content/plugins/dmsguestbook/img/example1.png", "Example", "width=300,height=200,scrollbars=no");
  	 }
	 </script>
	 <br />



<table style='border:1px solid #000000; width:100%;' cellspacing="0" cellpadding="0"><tr><td>
	 <table style='width:100%;'>
	 </tr>
     <form name="form1" method="post" action="<?=$location ?>">

 	 <td <? echo $tbc1; ?>>Page ID:</td>
 	 <td <? echo $tbc1; ?>><input style="width:50px;background-color:#ee8989" name="DMSGuestbook_page_id" value="<?=get_option("DMSGuestbook_page_id");?>" type="text" /></td>
	 <td <? echo $tbc1; ?>>Put the guestbook page id here</td>
	 </tr>

	 <tr>
	 <td <? echo $tbc2; ?>>Guestbook width:</td>
	 <td <? echo $tbc2; ?>><input style="width:50px;" name="DMSGuestbook_width" value="<?=get_option("DMSGuestbook_width");?>" type="text" />%</td>
     <td <? echo $tbc2; ?>>Guestbook width in percent</td>
     </tr>

     <tr>
	 <td <? echo $tbc1; ?>>Separator width:</td>
	 <td <? echo $tbc1; ?>><input style="width:50px;" name="DMSGuestbook_width2" value="<?=get_option("DMSGuestbook_width2");?>" type="text" />%</td>
     <td <? echo $tbc1; ?>>Separator width in percent</td>
     </tr>

	 <tr>
	 <td <? echo $tbc2; ?>>Guestbook position:</td>
	 <td <? echo $tbc2; ?>><input style="width:50px;" name="DMSGuestbook_position" value="<?=get_option("DMSGuestbook_position");?>" type="text" /> px</td>
     <td <? echo $tbc2; ?>>Relative guestbook position in pixel (left to right)</td>
     </tr>

     <tr>
	 <td <? echo $tbc1; ?>>Posts per page:</td>
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
     <td <? echo $tbc1; ?>>Number of entry in each page</td>
     </tr>

     <td <? echo $tbc2; ?>>Outside border color:</td>
     <td <? echo $tbc2; ?>>
<div id="Color1_div" style="border:1px solid; background-color:#<?=get_option("DMSGuestbook_bordercolor1");?>; float:left; width:25px; height:25px; cursor:pointer;" onclick="show_picker('Color1', '<?=get_option("DMSGuestbook_bordercolor1");?>', '<?=get_option("DMSGuestbook_bordercolor1");?>');">&nbsp;</div>
<input name="DMSGuestbook_bordercolor1" type="text" size="6" value="<?=get_option("DMSGuestbook_bordercolor1");?>" id="Color1" onclick="show_picker(this.id, '<?=get_option("DMSGuestbook_bordercolor1");?>', '<?=get_option("DMSGuestbook_bordercolor1");?>');" />
     </td>
     <td <? echo $tbc2; ?>>Color of the outside box</td>
     </tr><tr>

     <td <? echo $tbc1; ?>>Textfield border color:</td>
     <td <? echo $tbc1; ?>>
     <div id="Color2_div" style="border:1px solid; background-color:#<?=get_option("DMSGuestbook_bordercolor2");?>; float:left; width:25px; height:25px; cursor:pointer;" onclick="show_picker('Color2', '<?=get_option("DMSGuestbook_bordercolor2");?>', '<?=get_option("DMSGuestbook_bordercolor2");?>');">&nbsp;</div>
<input name="DMSGuestbook_bordercolor2" type="text" size="6" value="<?=get_option("DMSGuestbook_bordercolor2");?>" id="Color2" onclick="show_picker(this.id, '<?=get_option("DMSGuestbook_bordercolor2");?>', '<?=get_option("DMSGuestbook_bordercolor2");?>');" />
     <td <? echo $tbc1; ?>>Color of all textfield borders</td>
     </tr><tr>

     <td <? echo $tbc2; ?>>Navigation char color:</td>
     <td <? echo $tbc2; ?>>
   <div id="Color3_div" style="border:1px solid; background-color:#<?=get_option("DMSGuestbook_bordercolor3");?>; float:left; width:25px; height:25px; cursor:pointer;" onclick="show_picker('Color3', '<?=get_option("DMSGuestbook_bordercolor3");?>', '<?=get_option("DMSGuestbook_bordercolor3");?>');">&nbsp;</div>
<input name="DMSGuestbook_bordercolor3" type="text" size="6" value="<?=get_option("DMSGuestbook_bordercolor3");?>" id="Color3" onclick="show_picker(this.id, '<?=get_option("DMSGuestbook_bordercolor3");?>', '<?=get_option("DMSGuestbook_bordercolor3");?>');" />
     <td <? echo $tbc2; ?>>Define the navigation color</td>
     </tr><tr>

  <td <? echo $tbc1; ?>>Separator color:</td>
     <td <? echo $tbc1; ?>>
      <div id="Color4_div" style="border:1px solid; background-color:#<?=get_option("DMSGuestbook_hairlinecolor");?>; float:left; width:25px; height:25px; cursor:pointer;" onclick="show_picker('Color4', '<?=get_option("DMSGuestbook_hairlinecolor");?>', '<?=get_option("DMSGuestbook_hairlinecolor");?>');">&nbsp;</div>
<input name="DMSGuestbook_hairlinecolor" type="text" size="6" value="<?=get_option("DMSGuestbook_hairlinecolor");?>" id="Color4" onclick="show_picker(this.id, '<?=get_option("DMSGuestbook_hairlinecolor");?>', '<?=get_option("DMSGuestbook_hairlinecolor");?>');" />
     <td <? echo $tbc1; ?>>Separator between header and body in each entry</td>
     </tr><tr>

 <td <? echo $tbc2; ?>>Font color:</td>
     <td <? echo $tbc2; ?>>
    <div id="Color5_div" style="border:1px solid; background-color:#<?=get_option("DMSGuestbook_fontcolor1");?>; float:left; width:25px; height:25px; cursor:pointer;" onclick="show_picker('Color5', '<?=get_option("DMSGuestbook_fontcolor1");?>', '<?=get_option("DMSGuestbook_fontcolor1");?>');">&nbsp;</div>
<input name="DMSGuestbook_fontcolor1" type="text" size="6" value="<?=get_option("DMSGuestbook_fontcolor1");?>" id="Color5" onclick="show_picker(this.id, '<?=get_option("DMSGuestbook_fontcolor1");?>', '<?=get_option("DMSGuestbook_fontcolor1");?>');" />
     <td <? echo $tbc2; ?>>Overall font color</td>
     </tr><tr>

	<td <? echo $tbc1; ?>>Antispam image text color:</td>
     <td <? echo $tbc1; ?>>
    <div id="Color6_div" style="border:1px solid; background-color:#<?=get_option("DMSGuestbook_captcha_color");?>; float:left; width:25px; height:25px; cursor:pointer;" onclick="show_picker('Color6', '<?=get_option("DMSGuestbook_captcha_color");?>', '<?=get_option("DMSGuestbook_captcha_color");?>');">&nbsp;</div>
<input name="DMSGuestbook_captcha_color" type="text" size="6" value="<?=get_option("DMSGuestbook_captcha_color");?>" id="Color6" onclick="show_picker(this.id, '<?=get_option("DMSGuestbook_captcha_color");?>', '<?=get_option("DMSGuestbook_captcha_color");?>');" />
     <td <? echo $tbc1; ?>>Antispam image text color</td>
     </tr><tr>

     <td <? echo $tbc2; ?>>Navigation char style:</td>
     <td <? echo $tbc2; ?>><input style="width:50px;" name="DMSGuestbook_backwardarrowchar" value="<?=get_option("DMSGuestbook_backwardarrowchar");?>" type="text" />
     <input style="width:50px;" name="DMSGuestbook_forwardarrowchar" value="<?=get_option("DMSGuestbook_forwardarrowchar");?>" type="text" /></td>
     <td <? echo $tbc2; ?>>Use a char, number or word</td>
     </tr><tr>

     <td <? echo $tbc1; ?>>Navigation char size:</td>
     <td <? echo $tbc1; ?>><input style="width:50px;" name="DMSGuestbook_arrowsize" value="<?=get_option("DMSGuestbook_arrowsize");?>" type="text" />px</td>
     <td <? echo $tbc1; ?>>Size in pixel</td>
     </tr><tr>

	 <td <? echo $tbc2; ?>>Date / Time format:</td>
     <td <? echo $tbc2; ?>><input style="width:150px;" name="DMSGuestbook_dateformat" value="<?=get_option("DMSGuestbook_dateformat");?>" type="text" />&nbsp;<? echo date(get_option('DMSGuestbook_dateformat')); ?></td>
     <td <? echo $tbc2; ?>>Set the date and time format. More infos: <a href='http://www.php.net/manual/en/function.date.php' target='_blank'>Date & Time parameters</td>
     </tr><tr>

     <td <? echo $tbc1; ?>>Caption: *</td>
     <td <? echo $tbc1; ?>><input style="width:350px;" name="DMSGuestbook_name" value="<?=get_option("DMSGuestbook_name");?>" type="text" /> Name text<br />
     <input style="width:350px;" name="DMSGuestbook_email" value="<?=get_option("DMSGuestbook_email");?>" type="text" /> Email text<br />
     <input style="width:350px;" name="DMSGuestbook_url" value="<?=get_option("DMSGuestbook_url");?>" type="text" /> Url text<br />
     <input style="width:350px;" name="DMSGuestbook_message" value="<?=get_option("DMSGuestbook_message");?>" type="text" /> Message text<br />
     <textarea style="width:350px;" name="DMSGuestbook_antispam" rows="4"/><?=get_option("DMSGuestbook_antispam");?></textarea> Antispam text<br />
     <input style="width:350px;" name="DMSGuestbook_require" value="<?=get_option("DMSGuestbook_require");?>" type="text" /> Mandatory text<br />
     <input style="width:350px;" name="DMSGuestbook_submit" value="<?=get_option("DMSGuestbook_submit");?>" type="text" /> Submit text<br />
	 <input style="width:350px;" name="DMSGuestbook_name_error" value="<?=get_option("DMSGuestbook_name_error");?>" type="text" /> Name error text<br />
	 <input style="width:350px;" name="DMSGuestbook_email_error" value="<?=get_option("DMSGuestbook_email_error");?>" type="text" /> Email error text<br />
	 <input style="width:350px;" name="DMSGuestbook_url_error" value="<?=get_option("DMSGuestbook_url_error");?>" type="text" /> Url error text<br />
	 <input style="width:350px;" name="DMSGuestbook_message_error" value="<?=get_option("DMSGuestbook_message_error");?>" type="text" /> Message error text<br />
	 <input style="width:350px;" name="DMSGuestbook_antispam_error" value="<?=get_option("DMSGuestbook_antispam_error");?>" type="text" /> Antispam error text<br />
	 <input style="width:350px;" name="DMSGuestbook_success" value="<?=get_option("DMSGuestbook_success");?>" type="text" /> Success text<br />
     </td>
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
     </td>
     </tr><tr>

     <td <? echo $tbc2; ?>>Mandatory:</td>
     <td <? echo $tbc2; ?>>
     <? if(get_option("DMSGuestbook_require_email")==1) {$check1 = "checked"; } else {$check1="";} ?>
     <? if(get_option("DMSGuestbook_require_url")==1) {$check2 = "checked"; } else {$check2="";} ?>
     <? if(get_option("DMSGuestbook_require_antispam")==1) {$check3 = "checked"; } else {$check3="";} ?>
     <input type="checkbox" name="DMSGuestbook_require_email" value="1" <? echo $check1; ?>> Email <br />
     <input type="checkbox" name="DMSGuestbook_require_url" value="1" <? echo $check2; ?>> Url <br />
     <input type="checkbox" name="DMSGuestbook_require_antispam" value="1" <? echo $check3; ?>> Antispam
     </td>
	 <td <? echo $tbc2; ?>>User must fill out: Email text field / Url adress field / Antispam field</td>
	 </tr><tr>

	 <td <? echo $tbc1; ?>>Visible data:</td>
     <td <? echo $tbc1; ?>>
     <? if(get_option("DMSGuestbook_show_ip")==1) {$check1 = "checked"; } else {$check1="";} ?>
     <? if(get_option("DMSGuestbook_show_email")==1) {$check2 = "checked"; } else {$check2="";} ?>
     <? if(get_option("DMSGuestbook_show_url")==1) {$check3 = "checked"; } else {$check3="";} ?>
     <input type="checkbox" name="DMSGuestbook_show_ip" value="1" <? echo $check1; ?>> IP adress <br />
     <input type="checkbox" name="DMSGuestbook_show_email" value="1" <? echo $check2; ?>> Email <br />
     <input type="checkbox" name="DMSGuestbook_show_url" value="1" <? echo $check3; ?>> Url
     </td>
	 <td <? echo $tbc1; ?>>Visible data for everyone in each entry</td>
	 </tr><tr>
	 </table>
	 </td></tr>

	 <tr><td <? echo $tbc3; ?>>
	 <input name="action" value="insert" type="hidden" />
     <input style="font-weight:bold; margin:10px 0px; width:300px;" type="submit" value="Save" />
     </td></tr>
     </form>

</td></tr></table>

	 <br /><br />

	 <table><tr><td>
	 <form name="form3" method="post" action="<?=$location ?>">
	 <input name="action2" value="default_settings" type="hidden" />
	 <input style="font-weight:bold; margin:10px 0px;" type="submit" value="Restore default settings - All data will be replaced" onclick="return confirm('Would you really to restore all data?');">
     </form>
	 </td>
	 <td style="width:50px;"></td>
	 <td style="text-align:center;">
	 <!-- Default Sprachauswahl Eng.-->
	 * Switch text caption to english language
	 <form method="post" action="<?=$location ?>">
	 <input style="border:1px;" name="true" value="default_english" type="image" src="../wp-content/plugins/dmsguestbook/img/uk.gif" alt="default english">
	 </form>
	 </td>
     <td style="width:30px;"></td>
	 <td style="text-align:center;">
	 <!-- Default Sprachauswahl deu.-->
	 * Switch text caption to german language
	 <form method="post" action="<?=$location ?>">
	 <input style="border:1px;" name="true" value="default_german" type="image" src="../wp-content/plugins/dmsguestbook/img/ger.gif" alt="default deutsch">
	 </form>
	 </td>
	 <td style="width:30px;"></td>
	 <td style="text-align:center;">
	 <!-- Default Sprachauswahl schweizerdeu.-->
	 * Switch text caption to swiss german language
	 <form method="post" action="<?=$location ?>">
	 <input style="border:1px;" name="true" value="default_swissgerman" type="image" src="../wp-content/plugins/dmsguestbook/img/ch.gif" alt="default deutsch">
	 </form>
	 </td>
	 </tr></table>




</div>





<?php
} // Ende Funktion dmsguestbook_meta_description_option_page()



$dmsguestbook_step = get_option('DMSGuestbook_step');
$dmsguestbook_page_id = get_option('DMSGuestbook_page_id');
$dmsguestbook_width = get_option('DMSGuestbook_width');
$dmsguestbook_width2 = get_option('DMSGuestbook_width2');
$dmsguestbook_position = get_option('DMSGuestbook_position');
$dmsguestbook_hairlinecolor = get_option('DMSGuestbook_hairlinecolor');
$dmsguestbook_bordercolor1 = get_option('DMSGuestbook_bordercolor1');
$dmsguestbook_bordercolor2= get_option('DMSGuestbook_bordercolor2');
$dmsguestbook_bordercolor3= get_option('DMSGuestbook_bordercolor3');
$dmsguestbook_fontcolor1= get_option('DMSGuestbook_fontcolor1');
$dmsguestbook_backwardarrowchar= get_option('DMSGuestbook_backwardarrowchar');
$dmsguestbook_arrowsize= get_option('DMSGuestbook_arrowsize');
$dmsguestbook_name= get_option('DMSGuestbook_name');
$dmsguestbook_email= get_option('DMSGuestbook_email');
$dmsguestbook_url= get_option('DMSGuestbook_url');
$dmsguestbook_message= get_option('DMSGuestbook_message');
$dmsguestbook_antispam= get_option('DMSGuestbook_antispam');
$dmsguestbook_require= get_option('DMSGuestbook_require');
$dmsguestbook_submit= get_option('DMSGuestbook_submit');
$dmsguestbook_name_error= get_option('DMSGuestbook_name_error');
$dmsguestbook_email_error= get_option('DMSGuestbook_email_error');
$dmsguestbook_url_error= get_option('DMSGuestbook_url_error');
$dmsguestbook_message_error= get_option('DMSGuestbook_message_error');
$dmsguestbook_antispam_error= get_option('DMSGuestbook_antispam_error');
$dmsguestbook_success= get_option('DMSGuestbook_success');
$dmsguestbook_require_email= get_option('DMSGuestbook_require_email');
$dmsguestbook_require_url= get_option('DMSGuestbook_require_url');
$dmsguestbook_require_antispam= get_option('DMSGuestbook_require_antispam');
$dmsguestbook_show_ip= get_option('DMSGuestbook_show_ip');
$dmsguestbook_show_email= get_option('DMSGuestbook_show_email');
$dmsguestbook_show_url= get_option('DMSGuestbook_show_url');
$dmsguestbook_captcha_color= get_option('DMSGuestbook_captcha_color');
$dmsguestbook_dateformat= get_option('DMSGuestbook_dateformat');



//Default: Englisch
if ('default_english' == $HTTP_POST_VARS['true'])
{
		update_option("DMSGuestbook_name",					"Name");
		update_option("DMSGuestbook_email",					"Email");
		update_option("DMSGuestbook_url",					"Url");
		update_option("DMSGuestbook_message",				"Text");
		update_option("DMSGuestbook_antispam",				"<b>Antispam measures</b><br />
Please insert the letter and number combination into the text field before submitting the guestbook entry");
		update_option("DMSGuestbook_require", 				"mandatory");
		update_option("DMSGuestbook_submit",				"Inscribe");
		update_option("DMSGuestbook_name_error",			"Name is too short!");
		update_option("DMSGuestbook_email_error",			"Invalid email adress!");
		update_option("DMSGuestbook_url_error",				"Invalid url adress!");
		update_option("DMSGuestbook_message_error", 		"Text is too short!");
		update_option("DMSGuestbook_antispam_error",		"Wrong letter-figure combination!");
		update_option("DMSGuestbook_success",				"Thank you for this guestbook entry!");
		$date=date("H:i:s");
		echo "<div style='position:absolute; top:200px; left:800px;' id='message' class='updated fade'><p><b>saved...</b> <br /></p><p style='font-size:10px;'>[$date]</p><img  style='position:absolute; top:-5px; left:5px; height:13px; width:9px;' src='../wp-content/plugins/dmsguestbook/img/icon_pin.png'></div>";
}



//Default: Deutsch
if ('default_german' == $HTTP_POST_VARS['true'])
{
		update_option("DMSGuestbook_name",					"Name");
		update_option("DMSGuestbook_email",					"Email");
		update_option("DMSGuestbook_url",					"Url");
		update_option("DMSGuestbook_message",				"Text");
		update_option("DMSGuestbook_antispam",				"<b>Antispam Massnahme</b><br />
Vor dem Absenden des G&auml;stebucheintrags, die Buchstaben -und Zahlenkombination in das Textfeld eintragen.");
		update_option("DMSGuestbook_require", 				"erforderlich");
		update_option("DMSGuestbook_submit",				"eintragen");
		update_option("DMSGuestbook_name_error",			"Name ist zu kurz!");
		update_option("DMSGuestbook_email_error",			"Ung&uuml;ltige E-Mail Adresse!");
		update_option("DMSGuestbook_url_error",				"Ung&uuml;ltige Url Adresse!");
		update_option("DMSGuestbook_message_error", 		"Text ist zu kurz!");
		update_option("DMSGuestbook_antispam_error",		"Die Buchstaben -und Zahlenkombination ist falsch!");
		update_option("DMSGuestbook_success",				"Danke f&uuml;r deinen G&auml;stebuch Eintrag!");
		$date=date("H:i:s");
		echo "<div style='position:absolute; top:200px; left:800px;' id='message' class='updated fade'><p><b>saved...</b> <br /></p><p style='font-size:10px;'>[$date]</p><img  style='position:absolute; top:-5px; left:5px; height:13px; width:9px;' src='../wp-content/plugins/dmsguestbook/img/icon_pin.png'></div>";
}



//Default: Schweizerdeutsch :-)
if ('default_swissgerman' == $HTTP_POST_VARS['true'])
{
		update_option("DMSGuestbook_name",					"Nam&auml;");
		update_option("DMSGuestbook_email",					"Iimeil");
		update_option("DMSGuestbook_url",					"Uuueer&auml;l");
		update_option("DMSGuestbook_message",				"Tegscht");
		update_option("DMSGuestbook_antispam",				"<b>Antisp&auml;m Massnahme</b><br />
Vor em Abs&auml;nde vom Geschtebuech Ihtrag, d Buechstabe -und Zahl&auml;kombination is Tegschtf&auml;ld iitr&auml;hge.");
		update_option("DMSGuestbook_require", 				"bruchts");
		update_option("DMSGuestbook_submit",				"iihtr&auml;hge");
		update_option("DMSGuestbook_name_error",			"De Name isch zchurz!");
		update_option("DMSGuestbook_email_error",			"Ung&uuml;ltigi Imeil Adr&auml;sse!");
		update_option("DMSGuestbook_url_error",				"Ung&uuml;ltigi Uuueer&auml;l Adr&auml;sse!");
		update_option("DMSGuestbook_message_error", 		"Tegscht isch zchurz!");
		update_option("DMSGuestbook_antispam_error",		"D Buechstabe und Zahlekombination isch falsch!");
		update_option("DMSGuestbook_success",				"Messi; f&uuml;r din Geschtebuech ihtrag!");
		$date=date("H:i:s");
		echo "<div style='position:absolute; top:200px; left:800px;' id='message' class='updated fade'><p><b>saved...</b> <br /></p><p style='font-size:10px;'>[$date]</p><img  style='position:absolute; top:-5px; left:5px; height:13px; width:9px;' src='../wp-content/plugins/dmsguestbook/img/icon_pin.png'></div>";
}

if ('insert' == $HTTP_POST_VARS['action'])
{
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
		update_option("DMSGuestbook_forwardarrowchar",$HTTP_POST_VARS['DMSGuestbook_forwardarrowchar']);
		update_option("DMSGuestbook_backwardarrowchar",$HTTP_POST_VARS['DMSGuestbook_backwardarrowchar']);
		update_option("DMSGuestbook_arrowsize",$HTTP_POST_VARS['DMSGuestbook_arrowsize']);
		update_option("DMSGuestbook_name",$HTTP_POST_VARS['DMSGuestbook_name']);
		update_option("DMSGuestbook_email",$HTTP_POST_VARS['DMSGuestbook_email']);
		update_option("DMSGuestbook_url",$HTTP_POST_VARS['DMSGuestbook_url']);
		update_option("DMSGuestbook_message",$HTTP_POST_VARS['DMSGuestbook_message']);
		update_option("DMSGuestbook_antispam",$HTTP_POST_VARS['DMSGuestbook_antispam']);
		update_option("DMSGuestbook_require",$HTTP_POST_VARS['DMSGuestbook_require']);
		update_option("DMSGuestbook_submit",$HTTP_POST_VARS['DMSGuestbook_submit']);
		update_option("DMSGuestbook_name_error",$HTTP_POST_VARS['DMSGuestbook_name_error']);
		update_option("DMSGuestbook_email_error",$HTTP_POST_VARS['DMSGuestbook_email_error']);
		update_option("DMSGuestbook_url_error",$HTTP_POST_VARS['DMSGuestbook_url_error']);
		update_option("DMSGuestbook_message_error",$HTTP_POST_VARS['DMSGuestbook_message_error']);
		update_option("DMSGuestbook_antispam_error",$HTTP_POST_VARS['DMSGuestbook_antispam_error']);
		update_option("DMSGuestbook_success",$HTTP_POST_VARS['DMSGuestbook_success']);
		update_option("DMSGuestbook_require_email",$HTTP_POST_VARS['DMSGuestbook_require_email']);
		update_option("DMSGuestbook_require_url",$HTTP_POST_VARS['DMSGuestbook_require_url']);
		update_option("DMSGuestbook_require_antispam",$HTTP_POST_VARS['DMSGuestbook_require_antispam']);
		update_option("DMSGuestbook_show_ip",$HTTP_POST_VARS['DMSGuestbook_show_ip']);
		update_option("DMSGuestbook_show_email",$HTTP_POST_VARS['DMSGuestbook_show_email']);
		update_option("DMSGuestbook_show_url",$HTTP_POST_VARS['DMSGuestbook_show_url']);
		update_option("DMSGuestbook_captcha_color",$HTTP_POST_VARS['DMSGuestbook_captcha_color']);
		update_option("DMSGuestbook_dateformat",$HTTP_POST_VARS['DMSGuestbook_dateformat']);
		$date=date("H:i:s");
		echo "<div style='position:absolute; top:200px; left:800px;' id='message' class='updated fade'><p><b>saved...</b> <br /></p><p style='font-size:10px;'>[$date]</p><img  style='position:absolute; top:-5px; left:5px; height:13px; width:9px;' src='../wp-content/plugins/dmsguestbook/img/icon_pin.png'></div>";
}





//--------------- DMSGuestbook initialisieren -------------------------
function DMSGuestBook()
{
	global $wpdb;
	$table_name2 = $wpdb->prefix . "posts";
	$page_id=get_option("DMSGuestbook_page_id");

	#Prüfe ob DMSGuestBook(); in der posts Tabelle ist
	$query_posts = mysql_query("SELECT * FROM $table_name2 WHERE id = '$page_id'");
	$num_rows_posts = mysql_affected_rows();
		if($num_rows_posts!=0)
		{
		include_once ("dmsguestbook.php");
		}
		else	{
				echo "Wrong page id or missing <b style='color:#0000ee; text-decoration:none;'>&lt;? DMSGuestBook(); ?&gt;</b> in the guestbook page.";
				}
}




// --------------------- DMSGuestbook installieren ------------------
function dmsguestbook_install () {
   global $wpdb;

   $table_name = $wpdb->prefix . "dmsguestbook";
   if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

      $sql = "CREATE TABLE " . $table_name . " (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  name varchar(50) DEFAULT '' NOT NULL,
	  email varchar(50) DEFAULT '' NOT NULL,
	  url varchar(50) DEFAULT '' NOT NULL,
	  date varchar(50) DEFAULT '' NOT NULL,
	  ip varchar(15) DEFAULT '' NOT NULL,
	  message longtext NOT NULL,
	  UNIQUE KEY id (id)
	  ) COLLATE utf8_general_ci;";

      require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
      dbDelta($sql);
   }
  initialize_option();
}


function initialize_option()
{
	  if(!get_option("DMSGuestbook_step")) 					{update_option("DMSGuestbook_step","10");}
	  if(!get_option("DMSGuestbook_page_id")) 				{update_option("DMSGuestbook_page_id","");}
	  if(!get_option("DMSGuestbook_width"))					{update_option("DMSGuestbook_width","100");}
	  if(!get_option("DMSGuestbook_width2"))				{update_option("DMSGuestbook_width2","35");}
	  if(!get_option("DMSGuestbook_position"))				{update_option("DMSGuestbook_position","0");}
	  if(!get_option("DMSGuestbook_hairlinecolor"))			{update_option("DMSGuestbook_hairlinecolor","EEEEEE");}
	  if(!get_option("DMSGuestbook_bordercolor1"))			{update_option("DMSGuestbook_bordercolor1","AAAAAA");}
	  if(!get_option("DMSGuestbook_bordercolor2"))			{update_option("DMSGuestbook_bordercolor2","DEDEDE");}
	  if(!get_option("DMSGuestbook_bordercolor3"))			{update_option("DMSGuestbook_bordercolor3","000000");}
	  if(!get_option("DMSGuestbook_fontcolor1"))			{update_option("DMSGuestbook_fontcolor1","000000");}
	  if(!get_option("DMSGuestbook_forwardarrowchar"))		{update_option("DMSGuestbook_forwardarrowchar",">");}
	  if(!get_option("DMSGuestbook_backwardarrowchar"))		{update_option("DMSGuestbook_backwardarrowchar","<");}
	  if(!get_option("DMSGuestbook_arrowsize"))				{update_option("DMSGuestbook_arrowsize","20");}
	  if(!get_option("DMSGuestbook_name"))					{update_option("DMSGuestbook_name","Name");}
	  if(!get_option("DMSGuestbook_email"))					{update_option("DMSGuestbook_email","Email");}
	  if(!get_option("DMSGuestbook_url"))					{update_option("DMSGuestbook_url","Url");}
	  if(!get_option("DMSGuestbook_message"))				{update_option("DMSGuestbook_message","Text");}
	  if(!get_option("DMSGuestbook_antispam"))				{update_option("DMSGuestbook_antispam","<b>Antispam Massnahme</b><br />
Vor dem Absenden des G&auml;stebucheintrags, die Buchstaben - und Zahlenkombination in das Textfeld eintragen.");}
	  if(!get_option("DMSGuestbook_require"))				{update_option("DMSGuestbook_require","erforderlich");}
	  if(!get_option("DMSGuestbook_submit"))				{update_option("DMSGuestbook_submit","eintragen");}
	  if(!get_option("DMSGuestbook_name_error"))			{update_option("DMSGuestbook_name_error","Name ist zu kurz");}
	  if(!get_option("DMSGuestbook_email_error"))			{update_option("DMSGuestbook_email_error","Ung&uuml;ltige E-Mail Adresse");}
	  if(!get_option("DMSGuestbook_url_error"))				{update_option("DMSGuestbook_url_error","Ung&uuml;ltige Url");}
	  if(!get_option("DMSGuestbook_message_error"))			{update_option("DMSGuestbook_message_error","Text ist zu kurz");}
	  if(!get_option("DMSGuestbook_antispam_error"))		{update_option("DMSGuestbook_antispam_error","Die Kombination ist falsch!");}
	  if(!get_option("DMSGuestbook_success"))				{update_option("DMSGuestbook_success","Danke f&uuml;r deinen G&auml;stebuch Eintrag!");}
	  if(!get_option("DMSGuestbook_require_email"))			{update_option("DMSGuestbook_require_email","0");}
	  if(!get_option("DMSGuestbook_require_url"))			{update_option("DMSGuestbook_require_url","0");}
	  if(!get_option("DMSGuestbook_require_antispam"))		{update_option("DMSGuestbook_require_antispam","1");}
	  if(!get_option("DMSGuestbook_show_ip"))				{update_option("DMSGuestbook_show_ip","0");}
	  if(!get_option("DMSGuestbook_show_url"))				{update_option("DMSGuestbook_show_url","1");}
	  if(!get_option("DMSGuestbook_show_email"))			{update_option("DMSGuestbook_show_email","1");}
	  if(!get_option("DMSGuestbook_captcha_color"))			{update_option("DMSGuestbook_captcha_color","000000");}
	  if(!get_option("DMSGuestbook_dateformat"))			{update_option("DMSGuestbook_dateformat","D, d M Y H:i:s O");}
}




// ------ DMSGuestbook zurücksetzen -----------
if ('default_settings' == $HTTP_POST_VARS['action2'])
{
default_option();
}

function default_option()
{
	  update_option("DMSGuestbook_step","10");
	  update_option("DMSGuestbook_page_id","");
	  update_option("DMSGuestbook_width","100");
	  update_option("DMSGuestbook_width2","35");
	  update_option("DMSGuestbook_position","0");
	  update_option("DMSGuestbook_hairlinecolor","EEEEEE");
	  update_option("DMSGuestbook_bordercolor1","AAAAAA");
	  update_option("DMSGuestbook_bordercolor2","DEDEDE");
	  update_option("DMSGuestbook_bordercolor3","000000");
	  update_option("DMSGuestbook_fontcolor1","000000");
	  update_option("DMSGuestbook_forwardarrowchar",">");
	  update_option("DMSGuestbook_backwardarrowchar","<");
	  update_option("DMSGuestbook_arrowsize","20");
	  update_option("DMSGuestbook_name","Name");
	  update_option("DMSGuestbook_email","Email");
	  update_option("DMSGuestbook_url","Url");
	  update_option("DMSGuestbook_message","Text");
	  update_option("DMSGuestbook_antispam","<b>Antispam Massnahme</b><br />
Vor dem Absenden des G&auml;stebucheintrags, die Buchstaben - und Zahlenkombination in das Textfeld eintragen.");
	  update_option("DMSGuestbook_require","erforderlich");
	  update_option("DMSGuestbook_submit","eintragen");
	  update_option("DMSGuestbook_name_error","Name ist zu kurz");
	  update_option("DMSGuestbook_email_error","Ung&uuml;ltige E-Mail Adresse");
	  update_option("DMSGuestbook_url_error","Ung&uuml;ltige Url");
	  update_option("DMSGuestbook_message_error","Text ist zu kurz");
	  update_option("DMSGuestbook_antispam_error","Die Kombination ist falsch!");
	  update_option("DMSGuestbook_success","Danke f&uuml;r deinen G&auml;stebuch Eintrag!");
	  update_option("DMSGuestbook_require_email","0");
	  update_option("DMSGuestbook_require_url","0");
	  update_option("DMSGuestbook_require_antispam","1");
	  update_option("DMSGuestbook_show_ip","0");
	  update_option("DMSGuestbook_show_email","1");
	  update_option("DMSGuestbook_show_url","1");
	  update_option("DMSGuestbook_captcha_color","000000");
	  update_option("DMSGuestbook_dateformat","D, d M Y H:i:s O");
	  $date=date("H:i:s");
	  echo "<div style='position:absolute; top:200px; left:800px;' id='message' class='updated fade'><p><b>Restore default settings...</b> <br />Don't forget to set the page id.<br /></p><p style='font-size:10px;'>[$date]</p><img  style='position:absolute; top:-5px; left:5px; height:13px; width:9px;' src='../wp-content/plugins/dmsguestbook/img/icon_pin.png'></div>";
}



//# # # # # # # # # # # # #


//--------------------- Manage DMSGuestbook ---------------------------
function dmsguestbook2_meta_description_option_page() {
?>
	 <div class="wrap">
     <h2>Manage DMSGuestbook</h2>

	 <ul>
	 	<li>You can edit all text fields, except the date.</li>
	 	<li>You can use HTML tags in the name and text box. But, be care with this :-)</li>
	 	<li>If you edit the url field, don't delete the "http(s)://" prefix.</li>
	 </ul>
<?
	$gb_step=get_option("DMSGuestbook_step");
	#Initialisieren
	if($_REQUEST[from]=="") {$_REQUEST[from]=0; $_REQUEST[select]=1;}

	 global $wpdb;
	 $table_name = $wpdb->prefix . "dmsguestbook";

	 #Die Anzahl Datensätze aus der DB lesen
     $query0 = mysql_query("SELECT * FROM  $table_name");
     $num_rows0 = mysql_affected_rows();

	 $query1 = mysql_query("SELECT * FROM  $table_name ORDER BY id DESC LIMIT $_REQUEST[from],$gb_step;");
	 $num_rows1 = mysql_affected_rows();


	echo "<br /><br />";
	echo "<div style='width:$gb_width; text-align:center;'>";
	echo "<div style='font-size:11px;'>($num_rows0)</div>";
	for($q=0; $q<$num_rows0; ($q=$q+$gb_step))
	{
	$y++;
		if($_REQUEST[select]==$y) {
		echo "<a style='color:#bb1100; text-decoration:none;' href='admin.php?page=Manage&from=$q&select=$y'>$y</a> ";
		}
		else {
			 echo "<a style='color:#000000; text-decoration:none;' href='admin.php?page=Manage&from=$q&select=$y'>$y</a> ";
			 }

	}
	echo "</div>";
	echo "<br />";
	echo "<br />";

	 $tbc3="style='background-color:#CFEBF7; text-align:center; height:35px;'";
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
     $bgcolor="eeeeee";
	 for($x=0; $x<$num_rows1; $x++)
	 {
	     if($bgcolor=="dddddd") {$bgcolor="eeeeee";} else {$bgcolor="dddddd";}

	 $result[$x] = mysql_fetch_array($query1);
	 $date=date(get_option("DMSGuestbook_dateformat"),"{$result[$x][date]}");
	 ?>

	 <tr>
	 	<form name="edit_form" method="post" action="<?=$location ?>">
	 	<td style="font-size:10px; border:1px solid #eeeeee; background-color:#<? echo $bgcolor; ?>"><? echo "{$result[$x][id]}"; ?></td>
	 	<td style="border:1px solid #eeeeee; background-color:#<? echo $bgcolor; ?>">
	 	<input style="font-size:10px; border:1px solid #eeeeee;" type="text" name="gb_name" value="<? echo "{$result[$x][name]}"; ?>"></td>
	 	<td style="border:1px solid #eeeeee; background-color:#<? echo $bgcolor; ?>">
	 	<textarea style="height:80px; width:500px;font-size:10px;" name="gb_message"><? echo "{$result[$x][message]}"; ?></textarea></td>
	 	<td style="font-size:10px; border:1px solid #eeeeee; background-color:#<? echo $bgcolor; ?>">

	 	<table border="0">
	 	<tr><td style="font-size:10px;">Date:</td> <td style="font-size:10px;"><? echo $date; ?></td></tr>
	 	<tr><td style="font-size:10px;">IP:</td> <td><input style="font-size:10px; width:200px;" type="text" name="gb_ip" value="<? echo "{$result[$x][ip]}"; ?>" maxlength="15"></td></tr>
	 	<tr><td style="font-size:10px;">Email: </td> <td><input style="font-size:10px;  width:200px;" type="text" name="gb_email" value="<? echo "{$result[$x][email]}"; ?>"></td></tr>
	 	<tr><td style="font-size:10px;">Url: </td> <td><input style="font-size:10px;  width:200px;" type="text" name="gb_url" value="<? echo "{$result[$x][url]}"; ?>"></td></tr>
		</table>

	 	<td style="font-size:10px; border:1px solid #eeeeee; background-color:#<? echo $bgcolor; ?>">
	 	<form name="edit_form" method="post" action="<?=$location ?>">
	 	<input name="editdata" value="edit" type="hidden" />
	 	<input name="id" value="<? echo "{$result[$x][id]}"; ?>" type="hidden" />
	 	<input style="font-weight:bold; color:#0000bb; margin:10px 0px;" type="submit" value="edit" onclick="return confirm('Would you really to edit this dataset?');">
	 	</form>
	 	</td>

	 	<td style="font-size:10px; border:1px solid #eeeeee; background-color:#<? echo $bgcolor; ?>">
	 	<form name="delete_form" method="post" action="<?=$location ?>">
	 	<input name="deletedata" value="delete" type="hidden" />
		<input name="id" value="<? echo "{$result[$x][id]}"; ?>" type="hidden" />
	 	<input style="font-weight:bold; color:#bb0000; margin:10px 0px;" type="submit" value="X" onclick="return confirm('Would you really to delete this dataset?');">
	 	</form>
	 	</td>


	 </tr>

	 <?
	 }
	 ?>
	 </table>


	 </div>
<?
}


	// Daten bearbeiten
	if ('edit' == $HTTP_POST_VARS['editdata'])
	{
	$table_name = $wpdb->prefix . "dmsguestbook";
	$updatedata = "UPDATE $table_name SET
	name 		= 	'$_REQUEST[gb_name]',
	email 		= 	'$_REQUEST[gb_email]',
	url 		= 	'$_REQUEST[gb_url]',
	ip	 		= 	'$_REQUEST[gb_ip]',
	message 	= 	'$_REQUEST[gb_message]'
	WHERE id = '$_REQUEST[id]'";
  	$update = mysql_query($updatedata);

  	$date=date("H:i:s");
  	echo "<div style='position:absolute; top:140px; left:800px;' id='message' class='updated fade'><p><b>Dataset ($_REQUEST[id]) was saved...</b> <br /></p><p style='font-size:10px;'>[$date]</p><img  style='position:absolute; top:-5px; left:5px; height:13px; width:9px;' src='../wp-content/plugins/dmsguestbook/img/icon_pin.png'></div>";
	}

	// Daten löschen
	if ('delete' == $HTTP_POST_VARS['deletedata'])
	{
	$table_name = $wpdb->prefix . "dmsguestbook";
	$deletedata = "DELETE FROM $table_name WHERE id = '$_REQUEST[id]'";
	$delete = mysql_query($deletedata);

	$date=date("H:i:s");
	echo "<div style='position:absolute; top:140px; left:800px;' id='message' class='updated fade'><p><b>Dataset ($_REQUEST[id]) was deleted...</b> <br /></p><p style='font-size:10px;'>[$date]</p><img  style='position:absolute; top:-5px; left:5px; height:13px; width:9px;' src='../wp-content/plugins/dmsguestbook/img/icon_pin.png'></div>";
	}


?>