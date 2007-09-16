<?php
#################################################################
/*
Author: Daniel Schurter
Email: DMSGuestbook@danielschurter.net
Url: http://DanielSchurter.net

DMSGuestbook is released under the GNU General Public License
http://www.gnu.org/licenses/gpl.html
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details.
*/
#################################################################




@session_start();
$gb_step 				= get_option("DMSGuestbook_step");
$gb_page_id 			= get_option("DMSGuestbook_page_id");
$gb_width				= get_option("DMSGuestbook_width") . "%";
$gb_width2				= get_option("DMSGuestbook_width2") ."%";
$gb_position 			= get_option("DMSGuestbook_position") . "px";
$gb_hairlinecolor		= "#" . get_option("DMSGuestbook_hairlinecolor");
$gb_bordercolor1		= "#" . get_option("DMSGuestbook_bordercolor1");
$gb_bordercolor2		= "#" . get_option("DMSGuestbook_bordercolor2");
$gb_bordercolor3		= "#" . get_option("DMSGuestbook_bordercolor3");
$gb_fontcolor1			= "#" . get_option("DMSGuestbook_fontcolor1");
$gb_forwardarrowchar	= html_entity_decode(get_option("DMSGuestbook_forwardarrowchar"), ENT_QUOTES);
$gb_backwardarrowchar	= html_entity_decode(get_option("DMSGuestbook_backwardarrowchar"), ENT_QUOTES);
$gb_arrowsize			= get_option("DMSGuestbook_arrowsize") . "px";
$gb_name 				= html_entity_decode(get_option("DMSGuestbook_name"), ENT_QUOTES);
$gb_email 				= html_entity_decode(get_option("DMSGuestbook_email"), ENT_QUOTES);
$gb_url 				= html_entity_decode(get_option("DMSGuestbook_url"), ENT_QUOTES);
$gb_message 			= html_entity_decode(get_option("DMSGuestbook_message"), ENT_QUOTES);
$gb_antispam 			= html_entity_decode(get_option("DMSGuestbook_antispam"), ENT_QUOTES);
$gb_require 			= html_entity_decode(get_option("DMSGuestbook_require"), ENT_QUOTES);
$gb_submit	 			= html_entity_decode(get_option("DMSGuestbook_submit"), ENT_QUOTES);
$gb_name_error 			= html_entity_decode(get_option("DMSGuestbook_name_error"), ENT_QUOTES);
$gb_email_error 		= html_entity_decode(get_option("DMSGuestbook_email_error"), ENT_QUOTES);
$gb_url_error 			= html_entity_decode(get_option("DMSGuestbook_url_error"), ENT_QUOTES);
$gb_message_error 		= html_entity_decode(get_option("DMSGuestbook_message_error"), ENT_QUOTES);
$gb_antispam_error 		= html_entity_decode(get_option("DMSGuestbook_antispam_error"), ENT_QUOTES);
$gb_success		 		= html_entity_decode(get_option("DMSGuestbook_success"), ENT_QUOTES);
$gb_require_email		= get_option("DMSGuestbook_require_email");
$gb_require_url			= get_option("DMSGuestbook_require_url");
$gb_require_antispam	= get_option("DMSGuestbook_require_antispam");
$gb_show_ip				= get_option("DMSGuestbook_show_ip");
$gb_show_email			= get_option("DMSGuestbook_show_email");
$gb_show_url			= get_option("DMSGuestbook_show_url");
$gb_dateformat			= get_option("DMSGuestbook_dateformat");


unset($_SESSION[gb_captcha_color]);
$_SESSION[gb_captcha_color] = get_option("DMSGuestbook_captcha_color");


global $wpdb;
$table_name = $wpdb->prefix . "dmsguestbook";

//URL
$url=get_bloginfo('url');

if($gb_fontcolor1!="none") {
echo "<body style='color:$gb_fontcolor1;'>"; }

$embedded1 = "width:$gb_width; font-size:12px; text-align:left; padding:0px 10px; margin:0px 0px 0px 0px; line-height:1.4em; border:1px solid $gb_bordercolor1;";
$embedded2 = "width:$gb_width; font-size:12px; text-align:left; padding:10px 10px; margin:0px 0px 0px 0px; line-height:1.4em; border:1px solid $gb_bordercolor1;";
$errormessage = "color:#ee0000; font-size: 11px; text-decoration: none; font-weight:bold;";



// Neuer DMSGuestbook Eintrag
if($_REQUEST[newentry]==1)
{

	if(isset($_SESSION['captcha_spam']) AND $_POST["sicherheitscode"] == $_SESSION['captcha_spam'] OR $gb_require_antispam!=1){
	unset($_SESSION['captcha_spam']);

				if(strlen($_REQUEST[gbname])>=1) {
				$namecheck="1"; }
				else {$error1 = "$gb_name_error<br />";}


				if(strlen($_REQUEST[gbemail])>=1 OR $gb_require_email == 1)
				{
					if(preg_match("/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)*\.([a-zA-Z]{2,6})$/", $_REQUEST[gbemail]))
					{$emailcheck="1";}
					else {$error2 = "$gb_email_error<br />";}
				}
				else {$emailcheck=1;}


				if(strlen($_REQUEST[gburl])>=1 OR $gb_require_url == 1)
				{
					if(preg_match ("/^([^.-:\/][a-z0-9-.:\/]*)\.?+([a-z0-9-]+)*\.([a-z]{2,6})(\/)?([a-z0-9-_,.?&%=\/]*)$/i", $_REQUEST[gburl]))
					{$urlcheck="1";}
					else {$error3 = "$gb_url_error<br />";}
				}
				else {$urlcheck=1;}


				if(strlen($_REQUEST[gbmsg])>=1) {
				$messagecheck="1"; }
				else {$error4 = "$gb_message_error<br />";}


				if($namecheck=='1' AND $emailcheck=='1' AND $urlcheck=='1' AND $messagecheck=='1')
				{
					if(preg_match ("/^(http(s)?:\/\/)/i", $_REQUEST[gburl]))
					{$newurl = $_REQUEST[gburl];} else {$newurl="http://" . $_REQUEST[gburl];}

				$message_o_html=strip_tags($_REQUEST[gbmsg]);

				$nname=addslashes($_REQUEST[gbname]);
				$mmu=addslashes($message_o_html);

				$date=date("U");
				$ip = getenv('REMOTE_ADDR');

				$sql="INSERT INTO $table_name (name, email, url, date, ip, message)
				VALUES ('$nname', '$_REQUEST[gbemail]', '$newurl', '$date', '$ip', '$mmu')";
				require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
      			dbDelta($sql);

				$success = "$gb_success<br />";

				#clear
				unset($_REQUEST[gbname]);
				unset($_REQUEST[gbemail]);
				unset($_REQUEST[gburl]);
				unset($_REQUEST[gbmsg]);
				}

	}
	else {$error5 =  "$gb_antispam_error<br />";}

}

$_REQUEST[gbmsg] = str_replace("\r\n", "&#10;", $_REQUEST[gbmsg]);


?>
<!-- Das ganze Gästebuch ausrichten (relative) -->
<div style="position:relative; top:0px; left:<? echo $gb_position; ?>;">

<div style="padding:20px 0px 0px 0px;"></div>
<div style="<?php echo $embedded1; ?>">
<div style="padding:10px 0px 0px 0px;"> </div>
<?php echo "<div style='$errormessage'>$error1</div>";?>
<?php echo "<div style='$errormessage'>$error2</div>";?>
<?php echo "<div style='$errormessage'>$error3</div>";?>
<?php echo "<div style='$errormessage'>$error4</div>";?>
<?php echo "<div style='$errormessage'>$error5</div>";?>
<?php echo "<div style='$errormessage'>$success</div>";?>
<br />
<div style="text-align:left;padding:0px 0px 0px 0px;">
<form action="<? echo $url; ?>/index.php?page_id=<?php echo $gb_page_id; ?>" method="post"></div>

<div style="text-align:left;padding:0px 0px 5px 0px;">
<input style="border:1px solid <? echo $gb_bordercolor2; ?>;" type="text" name="gbname" value="<?php echo $_REQUEST[gbname]; ?>" maxlength="30"> <? echo $gb_name; ?> *</div>

<div style="text-align:left;padding:0px 0px 5px 0px;">
<input style="border:1px solid <? echo $gb_bordercolor2; ?>;" type="text" name="gbemail" value="<?php echo $_REQUEST[gbemail]; ?>" > <? echo $gb_email; ?> <? if($gb_require_email==1) {echo "*"; } else {echo ""; } ?> </div>

<div style="text-align:left;padding:0px 0px 5px 0px;">
<input style="border:1px solid <? echo $gb_bordercolor2; ?>;" type="text" name="gburl" value="<?php echo $_REQUEST[gburl]; ?>" > <? echo $gb_url; ?> <? if($gb_require_url==1) {echo "*"; } else {echo ""; } ?></div>


<div style="text-align:left;padding:0px 0px 5px 0px;">
<textarea style="border:1px solid <? echo $gb_bordercolor2; ?>; width:80%" name="gbmsg" rows="10"><?php echo $_REQUEST[gbmsg]; ?></textarea> <? echo $gb_message; ?> *</div>

<input type="hidden" name="newentry" value="1">
<input type="hidden" name="Itemid" value="<?php echo "$submitid"; ?>"><div style="text-align:left;">* <? echo $gb_require; ?></div>
<br />
<br />
<br />

<?
if($gb_require_antispam==1)
	{
?>
<div style="text-align:center;"><? echo $gb_antispam; ?></div>
<div style="text-align:center;padding:5px 0px; margin:0px 0px;"><img style='border:1px solid <? echo $gb_bordercolor2; ?>' src="<? echo $url; ?>/wp-content/plugins/dmsguestbook/captcha/captcha.php" title="Sicherheitscode"></div>
<div style="text-align:center;"><input style="width:60px; border:1px solid <? echo $gb_bordercolor2; ?>" type="text" name="sicherheitscode"></div>
<?
if($gb_require_antispam==1)
{
	}
}
?>


<div style="text-align:center;padding:20px 0px 10px 0px;"><input type="submit" value="<? echo $gb_submit; ?>"></form></div>
<br /><br />
<div style="padding:10px 0px 0px 0px;"> </div>
</div>
<div style="padding:30px 0px 0px 0px;"> </div>

<?php


	#Initialisieren
	if($_REQUEST[from]=="") {$_REQUEST[from]=0; $_REQUEST[select]=1;}

	#Die Anzahl Datensätze aus der DB lesen
	$query1 = mysql_query("SELECT * FROM  $table_name");
	$num_rows1 = mysql_affected_rows();

	#Das Gästebuch aus der DB auslesen.
	$query2 = mysql_query("SELECT * FROM $table_name ORDER BY id DESC LIMIT $_REQUEST[from],$gb_step;");
	$num_rows2 = mysql_affected_rows();

	$_REQUEST[next]=$_REQUEST[from]+$gb_step;
	$_REQUEST[back]=$_REQUEST[from]-$gb_step;


	echo "<div style='width:$gb_width; text-align:center;'>";
	echo "<div style='font-size:11px;'>($num_rows1)</div>";
	for($x=0; $x<$num_rows1; ($x=$x+$gb_step))
	{
	$y++;
		if($_REQUEST[select]==$y) {
		echo "<a style='color:#bb1100; text-decoration:none;' href='$url/index.php?page_id=$gb_page_id&from=$x&select=$y'>$y</a> ";
		}
		else {
			 echo "<a style='color:#000000; text-decoration:none;' href='$url/index.php?page_id=$gb_page_id&from=$x&select=$y'>$y</a> ";
			 }

	}
	echo "</div>";
	echo "<br />";



	//DMSGuestbook Seiten-Link's
	if($_REQUEST[next]>=$num_rows1) {} else {
	$_REQUEST[select_forward]=$_REQUEST[select]+1;
	$forward ="<a style='color:$gb_bordercolor3; font-size:$gb_arrowsize; text-decoration:none; font-weight:bold;' href='$url/index.php?page_id=$gb_page_id&from=$_REQUEST[next]&select=$_REQUEST[select_forward]'>$gb_forwardarrowchar</a>";
	}

	if($_REQUEST[back]<=-1) {} else {
	$_REQUEST[select_backward]=$_REQUEST[select]-1;
	$backward = "<a style='color:$gb_bordercolor3; font-size:$gb_arrowsize; text-decoration:none; font-weight:bold;' href='$url/index.php?page_id=$gb_page_id&from=$_REQUEST[back]&select=$_REQUEST[select_backward]'>$gb_backwardarrowchar</a>";
	}

//Navigation
if($num_rows1 > $gb_step) {
echo "<div style='text-align:center; width:$gb_width;'>";
echo "$backward $forward";
echo "</div>";}
echo "<div style='padding:0px 0px; margin:0px 0px 20px 0px;'></div>";



// DMSGuestbook Post's anzeigen
for ($gb=0; $gb<$num_rows2; $gb++)
{
$_REQUEST[itemnr]=($_REQUEST[from]++)+1;

$dbresult[$gb] = mysql_fetch_array($query2);
echo "<div style='$embedded2'>";
$displaydate=date($gb_dateformat, "{$dbresult[$gb][4]}");

$url=get_bloginfo('url');

	if($gb_show_ip==1) 		{$slash="&nbsp;/&nbsp;";} else {$slash="";}
	if($gb_show_ip==1) 		{$show_ip="{$dbresult[$gb][5]}&nbsp;";} else {$show_ip="";}
	if($gb_show_email==1) 	{$show_email="<a href='mailto:{$dbresult[$gb][2]}'><img style='heigh:15px; width:15px;border:0px;'src='$url/wp-content/plugins/dmsguestbook/img/mail_generic.gif' alt='Email'></a>";} else {$show_email="";}
	if($gb_show_url==1) 	{$show_url="<a href='{$dbresult[$gb][3]}' target='_blank'><img style='heigh:15px; width:15px;border:0px;' src='$url/wp-content/plugins/dmsguestbook/img/gohome.gif' alt='Url'></a>&nbsp;";} else {$show_url="";}

	echo "<div style='font-size:11px;'>";
	echo "<table style='margin:0px 0px; padding:0px 0px; border:0px; width:100%;' cellspacing='0' cellpadding='0' border='0'>";
	echo "<tr><td style='font-size:11px;'>($_REQUEST[itemnr]) {$dbresult[$gb][1]}</td>";
	echo "<td style='width:1px;'></td><td style='width:20px;font-size:11px;'>" . $show_url . "</td>" .  "<td style='width:20px; font-size:11px;'>" . $show_email . "</td></tr>";
	echo "<tr><td style='font-size:11px;'>$displaydate" . "$slash" . "$show_ip</td></tr>";

	echo "</table>";
	echo "<hr style='border:solid $gb_hairlinecolor 1px; height:1px; width:$gb_width2; text-align:left; margin:5px 0px;'>";
	echo "</div>";
	echo "<div style='margin:0px; 0px;'>{$dbresult[$gb][6]}</div>";


echo "</div>";
echo "<div style='margin:0px 0px 20px 0px;'></div>";
}

//Navigation
if($num_rows1 > $gb_step) {
echo "<div style='text-align:center; width:$gb_width;'>";
echo "$backward $forward";
echo "</div>"; }


?>

<!-- Ende relatives Ausrichren-->
</div>