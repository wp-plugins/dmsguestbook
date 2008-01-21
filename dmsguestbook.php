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

/* collect some variables */
$gb_step 				= $options[0];
$gb_page_id 			= $options[1];
$gb_width				= $options[2] . "%";
$gb_width2				= $options[3] . "%";
$gb_position 			= $options[4] . "px";
$gb_hairlinecolor		= "#" . $options[5];
$gb_bordercolor1		= "#" . $options[6];
$gb_bordercolor2		= "#" . $options[7];
$gb_navicolor			= "#" . $options[8];
$gb_fontcolor1			= "#" . $options[9];
$gb_forwardarrowchar	= htmlentities($options[10], ENT_QUOTES);
$gb_backwardarrowchar	= htmlentities($options[11], ENT_QUOTES);
$gb_arrowsize			= $options[12] . "px";
$gb_require_email		= $options[13];
$gb_require_url			= $options[14];
$gb_require_antispam	= $options[15];
$gb_show_ip				= $options[16];
$gb_show_email			= $options[17];
$gb_show_url			= $options[18];
// captcha image text color will be set later
$gb_dateformat			= $options[20];
$gb_setlocale			= $options[21];
$gb_offset				= $options[22];
$gb_formpos				= $options[23];
$gb_formposlink			= $options[24];
$gb_send_mail			= $options[25];
$gb_mail_adress			= $options[26];
$gb_sortitem			= $options[27];
$gb_dbid				= $options[28];
$gb_language			= $options[29];


// global var
global $wpdb;
global $wpsmiliestrans, $wp_smiliessearch, $wp_smiliesreplace;

$table_name = $wpdb->prefix . "dmsguestbook";

// URL
$url=get_bloginfo('url');


############################################################################################
// css settings
include_once ("stylesheet.php");

// language
$language =	create_language($gb_language);
$gb_name			=	html_entity_decode($language[0], ENT_QUOTES);
$gb_email			=	html_entity_decode($language[1], ENT_QUOTES);
$gb_url				=	html_entity_decode($language[2], ENT_QUOTES);
$gb_message			=	html_entity_decode($language[3], ENT_QUOTES);
$gb_antispam		=	html_entity_decode($language[4], ENT_QUOTES);
$gb_require			=	html_entity_decode($language[5], ENT_QUOTES);
$gb_submit			=	html_entity_decode($language[6], ENT_QUOTES);
$gb_name_error		=	html_entity_decode($language[7], ENT_QUOTES);
$gb_email_error		=	html_entity_decode($language[8], ENT_QUOTES);
$gb_url_error		=	html_entity_decode($language[9], ENT_QUOTES);
$gb_message_error	=	html_entity_decode($language[10], ENT_QUOTES);
$gb_antispam_error	=	html_entity_decode($language[11], ENT_QUOTES);
$gb_success			=	html_entity_decode($language[12], ENT_QUOTES);

	// reset captcha text / mathematics text color
	unset($_SESSION[gb_captcha_color]);
	$_SESSION[gb_captcha_color] = $options[19];


# overall font color
if($gb_fontcolor1!="none") {
echo "<div style='$guestbook_font_color'>"; }



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

						$_REQUEST[gbmsg]=str_replace("[html]", "", $_REQUEST[gbmsg]);
						$message_o_html=str_replace("[/html]", "", $_REQUEST[gbmsg]);

						$nname=strip_tags($_REQUEST[gbname]);
						$nname=addslashes($nname);

						$mmu = $message_o_html;
						$mmu=addslashes($message_o_html);

						$date = mktime(date("H")+$gb_offset, date("i"), date("s"), date("m")  , date("d"), date("Y"));
						$ip = getenv('REMOTE_ADDR');

						$sql=$wpdb->query("INSERT INTO $table_name (name, email, url, date, ip, message)
						VALUES ('$nname', '$_REQUEST[gbemail]', '$newurl', '$date', '$ip', '$mmu')");
						$abspath = str_replace("\\","/", ABSPATH);
						require_once($abspath . 'wp-admin/upgrade-functions.php');
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
	//$_REQUEST[gbmsg] = str_replace("\r\n", "&#10;", $_REQUEST[gbmsg]);


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
	$query2 = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id $gb_sortitem LIMIT $_REQUEST[from],$gb_step;");
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
		echo "<a style='$navigation_select' href='$url/index.php?page_id=$gb_page_id&amp;from=$x&amp;select=$y'>$y</a>&nbsp;";
		}
		else {
		     echo "<a style='$navigation_notselect' href='$url/index.php?page_id=$gb_page_id&amp;from=$x&amp;select=$y'>$y</a>&nbsp;";
			 }

	}
	echo "</div>";




	// navigation char forward construct
	if($_REQUEST[next]>=$num_rows1) {} else {
	$_REQUEST[select_forward]=$_REQUEST[select]+1;
	$forward ="<a style='$navigation_char' href='$url/index.php?page_id=$gb_page_id&amp;from=$_REQUEST[next]&amp;select=$_REQUEST[select_forward]'>$gb_forwardarrowchar</a>";
	}

	// navigation char backward construct
	if($_REQUEST[back]<=-1) {} else {
	$_REQUEST[select_backward]=$_REQUEST[select]-1;
	$backward = "<a style='$navigation_char' href='$url/index.php?page_id=$gb_page_id&amp;from=$_REQUEST[back]&amp;select=$_REQUEST[select_backward]'>$gb_backwardarrowchar</a>";
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
			$show_ip=substr($dbresult->ip, 0, -2) . "**";
		} else {
			   $show_ip=""; $slash="";
			   }

		// show email icon
		if($gb_show_email==1 && $dbresult->email != "") {
					# convert to ascii, better spam protection
					unset($ascii_email, $ascii_email_array);
					for($p=0; $p<strlen($dbresult->email); $p++) {
					$ascii_email_array[]=ord($dbresult->email[$p]);
					$ascii_email .= "&#" . $ascii_email_array[$p] . ";";
					}
			$show_email="<a href='mailto:$ascii_email'><img style='$guestbook_email' src='$guestbook_email_image' alt='email' /></a>";
		} else {
			   $show_email="";
			   }

		// show url icon
		if($gb_show_url==1 && $dbresult->url != "http://") {
					# convert to ascii, better spam protection
					unset($ascii_url);
					for($p=0; $p<strlen($dbresult->url); $p++) {
					$ascii_url_array[]=ord($dbresult->url[$p]);
					$ascii_url .= "&#" . $ascii_url_array[$p] . ";";
					}
			$show_url="<a href='$ascii_url' rel='nofollow' target='_blank'><img style='$guestbook_url' src='$guestbook_url_image' alt='url' /></a>&nbsp;";
		} else {
			   $show_url="";
			   }

	// to decide database id or continuous number
	if($gb_dbid==1) {
	$show_id = $dbresult->id;
	} else {
		   $show_id = $_REQUEST[itemnr];
		   }

	// guestbook entries
	echo "<div>";
		echo "<table style='margin:0px 0px; padding:0px 0px; border:1px; width:100%;' cellspacing='0' cellpadding='0' border='0'>";
			// header
			echo "<tr><td style='$guestbook_message_nr_name'>($show_id) $message_name</td>";
			// email & url
			echo "<td style='width:1px;'></td><td style='$guestbook_message_email'>"
			. $show_url . "</td>" . "<td style='$guestbook_message_url'>" . $show_email . "</td></tr>";
			// date & ip
			echo "<tr><td style='$guestbook_message_date_ip'>$displaydate" . $slash . $show_ip . "</td></tr>";
		echo "</table>";
		// hairline
		echo "<hr style='$guestbook_message_hairline' />";
		echo "</div>";
		/* message body
			cut all administrator html data between [html] and [/html]. this data will not be taget with &#38; &#60; [...]
			insert an additional \r\n if admin forgot a line break. otherwise will display just [html]some html code [/html] */
			$message_text=str_replace("[/html]", "[/html]\r\n", $message_text);
			$html_tag1 = explode("[html]", $message_text);
			$html_tag2 = explode("[/html]\r\n", $html_tag1[1]);

			$search_tags=array("&","<",">");
			$replace_tags=array("&#38;","&#60;","&#62;");
			for($r=0; $r<count($search_tags); $r++) {
			$message_text=str_replace($search_tags[$r], $replace_tags[$r], $message_text);
			}

			// parse ; correct
			$message_text=str_replace("&#38;#59", "&#59;", $message_text);

			// replace the administartor [html] tag
			unset($number);
			$trigger=0;
			$search=array("[html]","[/html]");
			for($s=0; $s<count($search); $s++) {
			$c1=explode($search[$s], $message_text);
				if (count($c1)-1 <> 1) {$trigger++;}
				$number=($number + (count($c1)-1));
			}

			if($trigger==0) {
			/* replace pseudo administrator html tag
			   e.g. [html]some html code[/html] is valid
			   e.g. [html]some html [html]code[/html] is not valid
			   e.g. [html]some html code[/html][/html] is not valid */
			$message_text = preg_replace("/\[html\].*[^\[html\]].*\[\/html\][^\[\/html\]]/", $html_tag2[0], $message_text);
			}

		$message_text=str_replace("\r\n", " <br /> ", $message_text);
		$message_text=str_replace("\n", " <br /> ", $message_text);

		// smilies
		if(get_option('use_smilies')==1) {
		$message_text=preg_replace($wp_smiliessearch, $wp_smiliesreplace, $message_text);
		}

	echo "<div style='$guestbook_message_body'>$message_text</div>";
	echo "</div>";
	echo "<table style='margin:0px 0px 20px 0px; padding:0px 0px; border:1px; width:100%;' cellspacing='0' cellpadding='0' border='0'>";
	echo "<tr><td></td></tr></table>";
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
	<input style="<?php echo $namefield;?>" type="text" name="gbname" value="<?php echo $_REQUEST[gbname];?>" maxlength="50" />&nbsp;<?php echo $gb_name;?> *</div>

	<?php #email ?>
	<div style="<?php echo $textfieldspace;?>">
	<input style="<?php echo $emailfield;?>" type="text" name="gbemail" value="<?php echo $_REQUEST[gbemail];?>" />&nbsp;<?php echo $gb_email;?>
		<?php  # email mandatory or not ?>
		<?php if($gb_require_email==1) {echo "*"; } else {echo ""; } ?></div>

	<?php #url ?>
	<div style="<?php echo $textfieldspace;?>">
	<input style="<?php echo $urlfield;?>" type="text" name="gburl" value="<?php echo $_REQUEST[gburl];?>" />&nbsp;<?php echo $gb_url;?>
		<?php # url mandatory or not ?>
		<?php if($gb_require_url==1) {echo "*"; } else {echo ""; } ?></div>

	<?php #message ?>
	<div style="<?php echo $textfieldspace;?>">
	<textarea style="<?php echo $messagefield;?>" name="gbmsg" rows="0" cols="0"><?php echo $_REQUEST[gbmsg]?></textarea>&nbsp;<?php echo $gb_message;?> *</div>

	<input type="hidden" name="newentry" value="1" />
	<input type="hidden" name="Itemid" value="<?php echo $submitid;?>" /><div style="text-align:left;">* <?php echo $gb_require;?></div>


		<?php # image antispam switch ?>
<?php	if($gb_require_antispam==1)
		{ ?>
		<div style="<?php echo $antispamtext;?>"><?php echo $gb_antispam;?></div>
		<div style="<?php echo $antispamcontent_position;?>">
		<img style="<?php echo $antispamcontent;?>" src="<?php echo $url;?>/wp-content/plugins/dmsguestbook/captcha/captcha.php" alt="" /></div>
		<div style="<?php echo $antispamcontent_position;?>">
		<input style="<?php echo $antispam_inputfield;?>" type="text" name="securecode" /></div>
<?php	} ?>


		<?php # mathematic antispam switch ?>
<?php	if($gb_require_antispam==2)
		{ ?>
		<div style="<?php echo $antispamtext;?>"><?php echo $gb_antispam;?></div>
		<div style="<?php echo $antispamcontent_position;?>">
		<?php captcha2(); ?><input style="<?php echo $antispam_inputfield;?>" type="text" name="securecode" /></div>
<?php	} ?>

		<?php # no antispam ?>
<?php	if($gb_require_antispam==0) {} ?>


	<?php # submit button ?>
	<div style="<?php echo $submit_position;?>"><input style="<?php echo $submit;?>" type="submit" value="<?php echo $gb_submit;?>" /></div></form>
	<p style="padding:10px 0px 0px 0px;"></p>
	</div>
	<div style="padding:30px 0px 0px 0px;"></div>
<?php	}






	# #	# # # # # - FUNCTIONS - # # # # # # #

	/* language */
	function create_language($gb_language)
	{
		$abspath = str_replace("\\","/", ABSPATH);
		$handle = fopen ($abspath . "wp-content/plugins/dmsguestbook/language/" . $gb_language, "r");
		unset($stringtext);
			while (!feof($handle)) {
    		$buffer = fgets($handle, 4096);
			$stringtext=$stringtext . $buffer;
			}
		fclose($handle);

		$string_flag=array(
		"name",
		"email",
		"url",
		"message",
		"antispam",
		"mandatory",
		"submit",
		"name_error",
		"email_error",
		"url_error",
		"message_error",
		"antispam_error",
		"success"
		);

		unset($language);
		for($c=0; $c<count($string_flag); $c++) {
		$part1 = explode("<" . $string_flag[$c] . ">", $stringtext);
		$part2 = explode("</" . $string_flag[$c] . ">", $part1[1]);
		$language[$c]=htmlentities($part2[0], ENT_QUOTES);
		$language[$c]=str_replace("&lt;", "<", $language[$c]);
		$language[$c]=str_replace("&gt;", ">", $language[$c]);
		}
		return $language;
	}


	/* create navigation */
	function navigation($num_rows1, $gb_step, $gb_width, $backward, $forward, $navigation_char_position) {
		if($num_rows1 > $gb_step) {
		echo "<div style='" . $navigation_char_position . "'>";
		echo $backward . " " .$forward;
		echo "</div>";
	 	}
	return 0;
	}


	/* captcha mathematic */
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


	/* email send function */
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
</div>