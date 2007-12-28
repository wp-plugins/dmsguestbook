<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
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

// collect some variables
$gb_step 				= get_option("DMSGuestbook_step");
$gb_page_id 			= get_option("DMSGuestbook_page_id");
$gb_width				= get_option("DMSGuestbook_width") . "%";
$gb_width2				= get_option("DMSGuestbook_width2") ."%";
$gb_position 			= get_option("DMSGuestbook_position") . "px";
$gb_hairlinecolor		= "#" . get_option("DMSGuestbook_hairlinecolor");
$gb_bordercolor1		= "#" . get_option("DMSGuestbook_bordercolor1");
$gb_bordercolor2		= "#" . get_option("DMSGuestbook_bordercolor2");
$gb_navicolor			= "#" . get_option("DMSGuestbook_bordercolor3");
$gb_fontcolor1			= "#" . get_option("DMSGuestbook_fontcolor1");
$gb_forwardarrowchar	= get_option("DMSGuestbook_forwardarrowchar");
$gb_backwardarrowchar	= get_option("DMSGuestbook_backwardarrowchar");
$gb_arrowsize			= get_option("DMSGuestbook_arrowsize") . "px";
$gb_offset				= get_option("DMSGuestbook_offset");
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
$gb_setlocale			= get_option("DMSGuestbook_setlocale");
$gb_formpos				= get_option("DMSGuestbook_formpos");
$gb_formposlink			= get_option("DMSGuestbook_formposlink");
$gb_send_mail			= get_option("DMSGuestbook_send_mail");
$gb_mail_adress			= get_option("DMSGuestbook_mail_adress");

// global var
global $wpdb;
$table_name = $wpdb->prefix . "dmsguestbook";

// URL
$url=get_bloginfo('url');

// END collect some variables


############################################################################################
// css settings
include_once ("options.php");

// windows systems
$guestbook_font_color=str_replace("\r\n", "", $guestbook_font_color);
$guestbook_position=str_replace("\r\n", "", $guestbook_position);
$namefield=str_replace("\r\n", "", $namefield);
$emailfield=str_replace("\r\n", "", $emailfield);
$urlfield=str_replace("\r\n", "", $urlfield);
$textfieldspace=str_replace("\r\n", "", $textfieldspace);
$messagefield=str_replace("\r\n", "", $messagefield);
$antispamtext=str_replace("\r\n", "", $antispamtext);
$antispamcontent=str_replace("\r\n", "", $antispamcontent);
$antispamcontent_position=str_replace("\r\n", "", $antispamcontent_position);
$antispam_inputfield=str_replace("\r\n", "", $antispam_inputfield);
$submit_position=str_replace("\r\n", "", $submit_position);
$submit=str_replace("\r\n", "", $submit);
$errormessage=str_replace("\r\n", "", $errormessage);
$successmessage=str_replace("\r\n", "", $successmessage);
$guestbookform_link=str_replace("\r\n", "", $guestbookform_link);
$navigation_overview=str_replace("\r\n", "", $navigation_overview);
$navigation_totalcount=str_replace("\r\n", "", $navigation_totalcount);
$navigation_select=str_replace("\r\n", "", $navigation_select);
$navigation_notselect=str_replace("\r\n", "", $navigation_notselect);
$navigation_char=str_replace("\r\n", "", $navigation_char);
$navigation_char_position=str_replace("\r\n", "", $navigation_char_position);
$guestbook_message_nr_name=str_replace("\r\n", "", $guestbook_message_nr_name);
$guestbook_message_email=str_replace("\r\n", "", $guestbook_message_email);
$guestbook_message_url=str_replace("\r\n", "", $guestbook_message_url);
$guestbook_message_date_ip=str_replace("\r\n", "", $guestbook_message_date_ip);
$guestbook_email=str_replace("\r\n", "", $guestbook_email);
$guestbook_url=str_replace("\r\n", "", $guestbook_url);
$guestbook_message_hairline=str_replace("\r\n", "", $guestbook_message_hairline);
$guestbook_message_body=str_replace("\r\n", "", $guestbook_message_body);
$embedded1=str_replace("\r\n", "", $embedded1);
$embedded2=str_replace("\r\n", "", $embedded2);

// unix systems
$guestbook_font_color=str_replace("\n", "", $guestbook_font_color);
$guestbook_position=str_replace("\n", "", $guestbook_position);
$namefield=str_replace("\n", "", $namefield);
$emailfield=str_replace("\n", "", $emailfield);
$urlfield=str_replace("\n", "", $urlfield);
$textfieldspace=str_replace("\n", "", $textfieldspace);
$messagefield=str_replace("\n", "", $messagefield);
$antispamtext=str_replace("\n", "", $antispamtext);
$antispamcontent=str_replace("\n", "", $antispamcontent);
$antispamcontent_position=str_replace("\n", "", $antispamcontent_position);
$antispam_inputfield=str_replace("\n", "", $antispam_inputfield);
$submit_position=str_replace("\n", "", $submit_position);
$submit=str_replace("\n", "", $submit);
$errormessage=str_replace("\n", "", $errormessage);
$successmessage=str_replace("\n", "", $successmessage);
$guestbookform_link=str_replace("\n", "", $guestbookform_link);
$navigation_overview=str_replace("\n", "", $navigation_overview);
$navigation_totalcount=str_replace("\n", "", $navigation_totalcount);
$navigation_select=str_replace("\n", "", $navigation_select);
$navigation_notselect=str_replace("\n", "", $navigation_notselect);
$navigation_char=str_replace("\n", "", $navigation_char);
$navigation_char_position=str_replace("\n", "", $navigation_char_position);
$guestbook_message_nr_name=str_replace("\n", "", $guestbook_message_nr_name);
$guestbook_message_email=str_replace("\n", "", $guestbook_message_email);
$guestbook_message_url=str_replace("\n", "", $guestbook_message_url);
$guestbook_message_date_ip=str_replace("\n", "", $guestbook_message_date_ip);
$guestbook_email=str_replace("\n", "", $guestbook_email);
$guestbook_url=str_replace("\n", "", $guestbook_url);
$guestbook_message_hairline=str_replace("\n", "", $guestbook_message_hairline);
$guestbook_message_body=str_replace("\n", "", $guestbook_message_body);
$embedded1=str_replace("\n", "", $embedded1);
$embedded2=str_replace("\n", "", $embedded2);
############################################################################################


	// reset captcha text / mathematics text color
	unset($_SESSION[gb_captcha_color]);
	$_SESSION[gb_captcha_color] = get_option("DMSGuestbook_captcha_color");






if($gb_fontcolor1!="none") {
echo "<body style='$guestbook_font_color'>"; }





		// --------- save the guestbook entry --------
		if($_REQUEST[newentry]==1)
		{

			// --------------------- check the old HTTP_POST_VARS and new $_POST var -------------
			if(!empty($HTTP_POST_VARS)) {
			$POSTVARIABLE   = $HTTP_POST_VARS;
			}
			else {
		 		 $POSTVARIABLE = $_POST;
		 		 }

			// check the result of visual antispam
			if($gb_require_antispam==1) {
				if(isset($_SESSION['captcha_spam']) && $POSTVARIABLE["securecode"] == $_SESSION['captcha_spam']) {
					$antispam_result=1;
					unset($_SESSION['captcha_spam']);
				}else { $antispam_result=0;}
			}


			// check the result of mathematic antispam
			if($gb_require_antispam==2) {
				if(($_SESSION[rand1] + $_SESSION[rand2]) == $POSTVARIABLE["securecode"]) {
					$antispam_result=1;
				} else { $antispam_result=0; }
			}


			if($gb_require_antispam==0){
				$antispam_result=1;
			}

			// if antispam valid or off
			if($antispam_result==1) {

				// check name text lenght min. 1 char
				if(strlen($_REQUEST[gbname])>=1) {
				$namecheck="1"; }
				else {$error1 = "$gb_name_error<br />";}

				// check email email adress were is valid
				if(strlen($_REQUEST[gbemail])>=1 || $gb_require_email == 1)
				{
					if(preg_match("/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)*\.([a-zA-Z]{2,6})$/", $_REQUEST[gbemail]))
					{$emailcheck="1";}
					else {$error2 = "$gb_email_error<br />";}
				}
				else {$emailcheck=1;}

				// check url adress were is valid
				if(strlen($_REQUEST[gburl])>=1 || $gb_require_url == 1)
				{
					if(preg_match ("/^([^.-:\/][a-z0-9-.:\/]*)\.?+([a-z0-9-]+)*\.([a-z]{2,6})(\/)?([a-z0-9-_,.?&%=\/]*)$/i", $_REQUEST[gburl]))
					{$urlcheck="1";}
					else {$error3 = "$gb_url_error<br />";}
				}
				else {$urlcheck=1;}

				// check message text lengt. min. 1 char
				if(strlen($_REQUEST[gbmsg])>=1) {
				$messagecheck="1"; }
				else {$error4 = "$gb_message_error<br />";}


					if($namecheck=='1' && $emailcheck=='1' && $urlcheck=='1' && $messagecheck=='1')
					{
						//set the http:// string if is missing
						if(preg_match ("/^(http(s)?:\/\/)/i", $_REQUEST[gburl]))
						{$newurl = $_REQUEST[gburl];} else {$newurl="http://" . $_REQUEST[gburl];}

						// remove all html tags from the name and message field
						// quote slashes and replace ;
						$message_o_html=strip_tags($_REQUEST[gbmsg]);
						$nname=strip_tags($_REQUEST[gbname]);
						$nname=addslashes($nname);
						$nname=str_replace(";", "&#59", $nname);
						$mmu=addslashes($message_o_html);
						$mmu = str_replace(";", "&#59", $mmu);

						$date = mktime(date("H")+$gb_offset, date("i"), date("s"), date("m")  , date("d"), date("Y"));
						$ip = getenv('REMOTE_ADDR');

						$sql=$wpdb->query("INSERT INTO $table_name (name, email, url, date, ip, message)
						VALUES ('$nname', '$_REQUEST[gbemail]', '$newurl', '$date', '$ip', '$mmu')");
						require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
      					dbDelta($sql);

						// success text
						$success = "$gb_success<br />";
						if($gb_formpos=="bottom") {echo "<div style='$successmessage'>$success</div>"; }

						// send mail
						if($gb_send_mail==1) {
							send_email($gb_mail_adress, $nname, $_REQUEST[gbemail], $newurl, $ip, $mmu);
						}

						// unset variables
						unset($_REQUEST[gbname]);
						unset($_REQUEST[gbemail]);
						unset($_REQUEST[gburl]);
						unset($_REQUEST[gbmsg]);
					}
					else {echo "<meta http-equiv='refresh' content='0; URL=#guestbookform'>";}

	}
	else {$error5 =  "$gb_antispam_error<br />"; if($gb_formpos=="bottom") {echo "<meta http-equiv='refresh' content='0; URL=#guestbookform'>";} }

}

	// replace <br /> from the message box if submit failed
	$_REQUEST[gbmsg] = str_replace("\r\n", "&#10;", $_REQUEST[gbmsg]);



######################
# smilies hack
######################

if($namecheck!='1' && $emailcheck!='1' && $urlcheck!='1' && $messagecheck!='1')
{
	global $wp_smiliesreplace;
	$wp_smiliesreplace = $_REQUEST[gbmsg];
}

if($_REQUEST[newentry]!=1) {
	smilies_init();
}
######################



	// if guestbook form is on top the side
	if ($gb_formpos =="top") {
	guestbook_position2($guestbook_position, $embedded1, $textfieldspace, $errormessage, $successmessage, $error1, $error2, $error3, $error4, $error5, 		 $success, $url, $gb_page_id, $namefield, $gb_name, $emailfield, $gb_email, $gb_require_email, $urlfield, $gb_url, $gb_require_url, $messagefield, 		  $gb_message, $submitid, $gb_require, $gb_require_antispam, $antispamtext, $gb_antispam, $antispamcontent_position, $antispamcontent, 					   $antispam_inputfield, $submit_position, $submit, $gb_submit);
	}
	else {
	     echo "<a style='$guestbookform_link' href='#guestbookform'>$gb_formposlink</a>";
	     }

	# start init
	if($_REQUEST[from]=="") {$_REQUEST[from]=0; $_REQUEST[select]=1;}

	# count all guestbook entries
	$query1 = $wpdb->get_results("SELECT * FROM  $table_name");
	$num_rows1 = mysql_affected_rows();

	# read the guestbook
	$query2 = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC LIMIT $_REQUEST[from],$gb_step;");
	$num_rows2 = mysql_affected_rows();

	$_REQUEST[next]=$_REQUEST[from]+$gb_step;
	$_REQUEST[back]=$_REQUEST[from]-$gb_step;
?>
	<div style="<?php echo $navigation_overview;?>">
	<div style="<?php echo $navigation_totalcount;?>">(<?php echo $num_rows1;?>)</div>
<?php
	for($x=0; $x<$num_rows1; ($x=$x+$gb_step))
	{
	$y++;
		if($_REQUEST[select]==$y) {
		echo "<a style='$navigation_select' href='$url/index.php?page_id=$gb_page_id&from=$x&select=$y'>$y</a> ";
		}
		else {
			 echo "<a style='$navigation_notselect' href='$url/index.php?page_id=$gb_page_id&from=$x&select=$y'>$y</a> ";
			 }

	}
	echo "</div>";
	echo "<br />";



	// navigation char forward construct
	if($_REQUEST[next]>=$num_rows1) {} else {
	$_REQUEST[select_forward]=$_REQUEST[select]+1;
	$forward ="<a style='$navigation_char' href='$url/index.php?page_id=$gb_page_id&from=$_REQUEST[next]&select=$_REQUEST[select_forward]'>$gb_forwardarrowchar</a>";
	}

	// navigation char backward construct
	if($_REQUEST[back]<=-1) {} else {
	$_REQUEST[select_backward]=$_REQUEST[select]-1;
	$backward = "<a style='$navigation_char' href='$url/index.php?page_id=$gb_page_id&from=$_REQUEST[back]&select=$_REQUEST[select_backward]'>$gb_backwardarrowchar</a>";
	}

	// show top navigation
	navigation($num_rows1, $gb_step, $gb_width, $backward, $forward, $navigation_char_position);

	// setlocale
	setlocale(LC_TIME, "$gb_setlocale");


	// show DMSGuestbook entries
	foreach ($query2 as $dbresult) {
	$_REQUEST[itemnr]=($_REQUEST[from]++)+1;
		// DMSGuestbook post container
		echo "<div style='$embedded2'>";

		// build the dta / time variable
		$sec=date("s", "$dbresult->date");
		$min=date("i", "$dbresult->date");
		$hour=date("H", "$dbresult->date");
		$day=date("d", "$dbresult->date");
		$month=date("m", "$dbresult->date");
		$year=date("Y", "$dbresult->date");
		$displaydate = strftime ("$gb_dateformat", mktime ($hour, $min, $sec, $month, $day, $year));
		$displaydate=htmlentities($displaydate, ENT_QUOTES);

		// remove quote /
		$message_name=stripslashes($dbresult->name);
		$message_text=stripslashes($dbresult->message);

		// add slash if ip is visible
		if($gb_show_ip==1) {
			$slash="&nbsp;/&nbsp;";
			$show_ip=$dbresult->ip . "&nbsp;";
		} else {
			   $show_ip=""; $slash="";
			   }

		// show email icon
		if($gb_show_email==1 && $dbresult->email != "") {
			$show_email="<a href='mailto:$dbresult->email'><img style='$guestbook_email' src='$guestbook_email_image'></a>";
		} else {
			   $show_email="";
			   }

		// show url icon
		if($gb_show_url==1 && $dbresult->url != "http://") {
			$show_url="<a href='$dbresult->url' rel='nofollow' target='_blank'><img style='$guestbook_url' src='$guestbook_url_image' alt='url'></a>&nbsp;";
		} else {
			   $show_url="";
			   }


	// guestbook entries
	echo "<div>";
		echo "<table style='margin:0px 0px; padding:0px 0px; border:0px; width:100%;' cellspacing='0' cellpadding='0' border='0'>";
			// header
			echo "<tr><td style='$guestbook_message_nr_name'>($_REQUEST[itemnr]) $message_name</td>";
			// email & url
			echo "<td style='width:1px;'></td><td style='$guestbook_message_email'>"
			. $show_url . "</td>" . "<td style='$guestbook_message_url'>" . $show_email . "</td></tr>";
			// date & ip
			echo "<tr><td style='$guestbook_message_date_ip'>$displaydate" . $slash . $show_ip . "</td></tr>";
		echo "</table>";
		// hairline
		echo "<hr style='$guestbook_message_hairline'>";
		echo "</div>";
		// message body
		echo "<div style='$guestbook_message_body'>$message_text</div>";
	echo "</div>";
	echo "<div style='margin:0px 0px 20px 0px;'></div>";
	}



	// show bottom navigation
	navigation($num_rows1, $gb_step, $gb_width, $backward, $forward, $navigation_char_position);


	// if guestbook form is on bottom the side
	if ($gb_formpos =="bottom") {
	guestbook_position2($guestbook_position, $embedded1, $textfieldspace, $errormessage, $successmessage, $error1, $error2, $error3, $error4, $error5, 		 "", $url, $gb_page_id, $namefield, $gb_name, $emailfield, $gb_email, $gb_require_email, $urlfield, $gb_url, $gb_require_url, $messagefield, 			  $gb_message, $submitid, $gb_require, $gb_require_antispam, $antispamtext, $gb_antispam, $antispamcontent_position, $antispamcontent, 					   $antispam_inputfield, $submit_position, $submit, $gb_submit);
	echo "<a name='guestbookform' id='guestbookform'></a>";
	}
?>
	</div>





<?php
// --- SOME FUNCTIONS ---

	// navigation
	function navigation($num_rows1, $gb_step, $gb_width, $backward, $forward, $navigation_char_position) {
			if($num_rows1 > $gb_step) {
			echo "<div style='$navigation_char_position'>";
			echo "$backward $forward";
			echo "</div>";
	 		}
		return 0;
		}


	// captcha mathematic
	function captcha2() {
		unset($_SESSION[rand1]);
		unset($_SESSION[rand2]);
		srand();
		$rand1 = rand(1, 9);
		$rand2 = rand(1, 9);
		echo $rand1 . " + " . $rand2 . " =";
		$_SESSION[rand1] = $rand1;
		$_SESSION[rand2] = $rand2;
		return 0;
		}


	// guestbook input form
	function guestbook_position2($guestbook_position, $embedded1, $textfieldspace, $errormessage, $successmessage, $error1, $error2, $error3, $error4, 		 $error5, $success, $url, $gb_page_id, $namefield, $gb_name, $emailfield, $gb_email, $gb_require_email, $urlfield, $gb_url, $gb_require_url, 			  $messagefield, $gb_message, $submitid, $gb_require, $gb_require_antispam, $antispamtext, $gb_antispam, $antispamcontent_position, $antispamcontent, 	   $antispam_inputfield, $submit_position, $submit, $gb_submit) {
?>

	<?php # DMSGuestbook main input block ?>
	<div style="<?php echo $guestbook_position;?>;">
	<div style="<?php echo $embedded1;?>">
	<div style="<?php echo $textfieldspace;?>"></div>

	<?php # error & success messages ?>
	<div style="<?php echo $errormessage;?>"><?php echo $error1;?></div>
	<div style="<?php echo $errormessage;?>"><?php echo $error2;?></div>
	<div style="<?php echo $errormessage;?>"><?php echo $error3;?></div>
	<div style="<?php echo $errormessage;?>"><?php echo $error4;?></div>
	<div style="<?php echo $errormessage;?>"><?php echo $error5;?></div>
	<div style="<?php echo $successmessage;?>"><?php echo $success;?></div>
	<br />

	<?php #form ?>
	<form action="<?php echo $url;?>/index.php?page_id=<?php echo $gb_page_id;?>" method="post">

	<?php # name ?>
	<div style="<?php echo $textfieldspace;?>">
	<input style="<?php echo $namefield;?>" type="text" name="gbname" value="<?php echo $_REQUEST[gbname];?>" maxlength="50">&nbsp;<?php echo $gb_name;?> *</div>

	<?php #email ?>
	<div style="<?php echo $textfieldspace;?>">
	<input style="<?php echo $emailfield;?>" type="text" name="gbemail" value="<?php echo $_REQUEST[gbemail];?>">&nbsp;<?php echo $gb_email;?>
		<?php  # email mandatory or not ?>
		<?php if($gb_require_email==1) {echo "*"; } else {echo ""; } ?></div>

	<?php #url ?>
	<div style="<?php echo $textfieldspace;?>">
	<input style="<?php echo $urlfield;?>" type="text" name="gburl" value="<?php echo $_REQUEST[gburl];?>">&nbsp;<?php echo $gb_url;?>
		<?php # url mandatory or not ?>
		<?php if($gb_require_url==1) {echo "*"; } else {echo ""; } ?></div>

	<?php #message ?>
	<div style="<?php echo $textfieldspace;?>">
	<textarea style="<?php echo $messagefield;?>" name="gbmsg"><?php echo $_REQUEST[gbmsg];?></textarea>&nbsp;<?php echo $gb_message;?> *</div>

	<input type="hidden" name="newentry" value="1">
	<input type="hidden" name="Itemid" value="<?php echo $submitid;?>"><div style="text-align:left;">* <?php echo $gb_require;?></div>
	<br /><br /><br />


		<?php # image antispam switch ?>
<?php	if($gb_require_antispam==1)
		{ ?>
		<div style="<?php echo $antispamtext;?>"><?php echo $gb_antispam;?></div>
		<div style="<?php echo $antispamcontent_position;?>">
		<img style="<?php echo $antispamcontent;?>" src="<?php echo $url;?>/wp-content/plugins/dmsguestbook/captcha/captcha.php"></div>
		<div style="<?php echo $antispamcontent_position;?>">
		<input style="<?php echo $antispam_inputfield;?>" type="text" name="securecode"></div>
<?php	} ?>


		<?php # mathematic antispam switch ?>
<?php	if($gb_require_antispam==2)
		{ ?>
		<div style="<?php echo $antispamtext;?>"><?php echo $gb_antispam;?></div>
		<div style="<?php echo $antispamcontent_position;?>">
		<?php captcha2(); ?><input style="<?php echo $antispam_inputfield;?>" type="text" name="securecode"></div>
<?php	} ?>

		<?php # no antispam ?>
<?php	if($gb_require_antispam==0) {} ?>


	<?php # submit button ?>
	<div style="<?php echo $submit_position;?>"><input style="<?php echo $submit;?>" type="submit" value="<?php echo $gb_submit;?>"></div></form>
	<br /><br />
		<div style="padding:10px 0px 0px 0px;"> </div>
	</div>
	<div style="padding:30px 0px 0px 0px;"> </div>
<?php	}


	// email send function
	function send_email($gb_mail_adress, $nname, $gbemail, $newurl, $ip, $mmu) {

	$date=date("d.m.Y, h:i:s");
	$host = str_replace("www.", "", "$_SERVER[HTTP_HOST]");
	$mail_recipient="$gb_mail_adress";
	$mail_sender="DMSGuestbook@".$host;
	$subject="You have a new guestbook post!";
	$mail_text="From: $nname\nMail: $gbemail\nWebsite: $newurl\n\nMessage:\n$mmu\n\nIP: $ip\nDate: $date";
	mail($mail_recipient, $subject, $mail_text,"from:$mail_sender");
	}


?>