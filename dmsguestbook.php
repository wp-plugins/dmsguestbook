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
$var_step 				= $options["step"];
$var_page_id 			= $options["page_id"];
$var_forwardchar			= html_entity_decode($options["forwardchar"], ENT_QUOTES);
$var_backwardchar		= html_entity_decode($options["backwardchar"], ENT_QUOTES);
$var_require_email		= $options["require_email"];
$var_require_url			= $options["require_url"];
$var_require_antispam	= $options["require_antispam"];
$var_show_ip				= $options["show_ip"];
$var_show_email			= $options["show_email"];
$var_show_url			= $options["show_url"];
//captcha image text color will be set later
$var_dateformat			= $options["dateformat"];
$var_setlocale			= $options["setlocale"];
$var_offset				= $options["offset"];
$var_formpos				= $options["formpos"];
$var_formposlink			= html_entity_decode($options["formposlink"], ENT_QUOTES);
$var_send_mail			= $options["send_mail"];
$var_mail_adress			= $options["mail_adress"];
$var_sortitem			= $options["sortitem"];
$var_dbid				= $options["dbid"];
$var_language			= $options["language"];
$var_email_image_path	= $options["email_image_path"];
$var_website_image_path	= $options["website_image_path"];
$var_admin_review		= $options["admin_review"];
$var_url_overruled		= $options["url_overruled"];
$var_mandatory_char		= html_entity_decode($options["mandatory_char"], ENT_QUOTES);
$var_form_template		= $options["form_template"];
$var_post_template		= $options["post_template"];



// global var
global $wpdb;
global $wpsmiliestrans, $wp_smiliessearch, $wp_smiliesreplace;
$table_name = $wpdb->prefix . "dmsguestbook";

// URL
$url=get_bloginfo('wpurl');

// language
$language =	create_language($var_language);
$lang_name				=	html_entity_decode($language[0], ENT_QUOTES);
$lang_email				=	html_entity_decode($language[1], ENT_QUOTES);
$lang_url				=	html_entity_decode($language[2], ENT_QUOTES);
$lang_message			=	html_entity_decode($language[3], ENT_QUOTES);
$lang_antispam			=	html_entity_decode($language[4], ENT_QUOTES);
$lang_require			=	html_entity_decode($language[5], ENT_QUOTES);
$lang_submit			=	html_entity_decode($language[6], ENT_QUOTES);
$lang_name_error		=	html_entity_decode($language[7], ENT_QUOTES);
$lang_email_error		=	html_entity_decode($language[8], ENT_QUOTES);
$lang_url_error			=	html_entity_decode($language[9], ENT_QUOTES);
$lang_message_error		=	html_entity_decode($language[10], ENT_QUOTES);
$lang_antispam_error	=	html_entity_decode($language[11], ENT_QUOTES);
$lang_success			=	html_entity_decode($language[12], ENT_QUOTES);
$lang_admin_review		=	html_entity_decode($language[13], ENT_QUOTES);

############################################################################################

	// reset captcha text / mathematics text color
	unset($_SESSION[gb_captcha_color]);
	$_SESSION[captcha_color] = $options["captcha_color"];

	/* guestbook container */
	echo "<div class='css_guestbook_position'>";

# overall font color
if($var_fontcolor1!="none") {
echo "<div class='css_guestbook_font_color'>"; }



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
			if($var_require_antispam==1) {
				if(isset($_SESSION['captcha_spam']) && sprintf("%s", strip_tags($POSTVARIABLE["securecode"])) == $_SESSION['captcha_spam']) {
					$antispam_result=1;
					$antispamcheck=1;
					unset($_SESSION['captcha_spam']);
				}else { $antispam_result=0; $error5 =  "$lang_antispam_error";}
			}

			// check the result of mathematic antispam
			if($var_require_antispam==2) {
				if(($_SESSION[rand1] + $_SESSION[rand2]) == sprintf("%d", $POSTVARIABLE["securecode"])) {
					$antispam_result=1;
					$antispamcheck=1;
				} else { $antispam_result=0; $error5 =  "$lang_antispam_error";}
			}

			if($var_require_antispam==0){
				$antispam_result=1;
				$antispamcheck=1;
			}



			// if antispam valid or off
			if($antispam_result==1 || $antispam_result==0) {


				/* remove all invalid chars from name field*/
				//$_REQUEST[gbname] = preg_replace("/[[:punct:]]+/i", "", $_REQUEST[gbname]);
				$_REQUEST[gbname] = preg_replace("/[\\\\\"<=>\(\)\{\}\/]+/i", "", $_REQUEST[gbname]);
				// check name text lenght min. 1 char
				if(strlen($_REQUEST[gbname])>=1) {
				$namecheck="1"; }
				else {$error1 = "$lang_name_error<br />";}


				/* remove all invalid chars from email field */
				$_REQUEST[gbemail] = preg_replace("/[^a-z-0-9-_\.@]+/i", "", $_REQUEST[gbemail]);
				// check email email adress were is valid
				if(strlen($_REQUEST[gbemail])>=1 || $var_require_email == 1)
				{
					if(preg_match("/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)*\.([a-zA-Z]{2,6})$/", $_REQUEST[gbemail]))
					{$emailcheck="1";}
					else {$error2 = "$lang_email_error<br />";}
				}
				else {$emailcheck=1;}


				/* remove all invalid chars from url field */
				$_REQUEST[gburl] = preg_replace("/[^a-z-0-9-_,.:?&%=\/]+/i", "", $_REQUEST[gburl]);
				// check url adress were is valid
				if(strlen($_REQUEST[gburl])>=1 || $var_require_url == 1)
				{
					if(preg_match ("/^([^.-:\/][a-z0-9-.:\/]*)\.?+([a-z0-9-]+)*\.([a-z]{2,6})(\/)?([a-z0-9-_,.?&%=\/]*)$/i", $_REQUEST[gburl]))
					{$urlcheck="1";}
					else {$error3 = "$lang_url_error<br />";}
				}
				else {$urlcheck=1;}


				/* remove all html tags from message field */
				$_REQUEST[gbmsg] = strip_tags($_REQUEST[gbmsg]);
				/* if user want to set admin [html] tags */
				$_REQUEST[gbmsg]=str_replace("[html]", "", $_REQUEST[gbmsg]);
				$_REQUEST[gbmsg]=str_replace("[/html]", "", $_REQUEST[gbmsg]);

				// check message text lengt. min. 1 char
				if(strlen($_REQUEST[gbmsg])>=1) {
				$messagecheck="1"; }
				else {$error4 = "$lang_message_error<br />";}


					if($namecheck=='1' && $emailcheck=='1' && $urlcheck=='1' && $messagecheck=='1' && $antispamcheck=='1')
					{
						//set the http:// string if is missing
						if(preg_match ("/^(http(s)?:\/\/)/i", $_REQUEST[gburl]))
						{$newurl = $_REQUEST[gburl];} else {$newurl="http://" . $_REQUEST[gburl];}

						$nname=addslashes($_REQUEST[gbname]);
						$mmu=addslashes($_REQUEST[gbmsg]);

						$date = mktime(date("H")+$var_offset, date("i"), date("s"), date("m"), date("d"), date("Y"));
						$ip = getenv('REMOTE_ADDR');

						$sql=$wpdb->query("INSERT INTO $table_name (
						name, email, url, date, ip, message, flag
						)
						VALUES (
						'" . mysql_real_escape_string($nname) . "',
						'" . mysql_real_escape_string($_REQUEST[gbemail]) . "',
						'" . mysql_real_escape_string($newurl) . "',
						'" . mysql_real_escape_string($date) . "',
						'" . mysql_real_escape_string($ip) . "',
						'" . mysql_real_escape_string($mmu) . "',
						'$var_admin_review')")
						or die ("Database not available!");

						$abspath = str_replace("\\","/", ABSPATH);
						require_once($abspath . 'wp-admin/upgrade-functions.php');
      					dbDelta($sql);

						// success text
						$success = "$lang_success<br />";

						// if admin review (flag=1)
						if($var_admin_review==1) {$success.="$lang_admin_review<br />";}

						if($var_formpos=="bottom") {echo "<div class='css_form_successmessage'>$success</div>"; }

						// send mail
						if($var_send_mail==1) {
							send_email($var_mail_adress, $nname, $_REQUEST[gbemail], $newurl, $ip, $mmu);
						}

						// unset variables
						unset($_REQUEST[gbname]);
						unset($_REQUEST[gbemail]);
						unset($_REQUEST[gburl]);
						unset($_REQUEST[gbmsg]);
					}

	}				if($var_formpos=="bottom") {echo "<a class='css_form_errormessage' href='#guestbookform'>$error1 $error2 $error3 $error4 $error5</a><br /><br />";}


}



	// if guestbook form is on top the side
	if ($var_formpos =="top") {
	input_form($error1, $error2, $error3, $error4, $error5,$success, $url, $var_page_id, $lang_name, $lang_email, $var_require_email, $lang_url, $var_require_url, $lang_message, $submitid, $lang_require, $var_require_antispam, $lang_antispam, $lang_submit, $var_url_overruled,$var_mandatory_char, $var_form_template);
	}
	else {
	     echo "<a class='css_form_link' href='#guestbookform'>$var_formposlink</a>";
	     }

	# start init
	$select = sprintf("%d", $_REQUEST[select]);
	$from 	= sprintf("%d", $_REQUEST[from]);
	if($_REQUEST[from]=="") {$from=0; $select=1;}

	# count all guestbook entries
	# if flag = 1 the admin will review this post
	$query1 = $wpdb->get_results("SELECT id FROM $table_name WHERE flag != '1'");
	$num_rows1 = $wpdb->num_rows;
	//$num_rows1 = mysql_affected_rows();

	/* if widget <a href="" is activated */
	if($_REQUEST[widget_gb_step]==1) {$var_step=1; $num_rows1=1;}

	# read the guestbook
	# if flag = 1 the admin will review this post
	$query2 = $wpdb->get_results("SELECT * FROM $table_name
	WHERE flag != '1' ORDER BY id
	" . sprintf("%s", $var_sortitem) . " LIMIT " . $from .
	"," . sprintf("%d", $var_step) . ";");
	$num_rows2 = $wpdb->num_rows;
	//$num_rows2 = mysql_affected_rows();

	$next=$from+$var_step;
	$back=$from-$var_step;
?>
	<div class="css_navigation_totalcount">(<?php echo $num_rows1;?>)</div>
	<div class="css_navigation_overview">
<?php
	for($x=0; $x<$num_rows1; ($x=$x+$var_step))
	{
	$y++;
		if($select==$y) {
		echo "<a class='css_navigation_select' href='$url/index.php?page_id=$var_page_id&amp;from=$x&amp;select=$y'>$y</a>&nbsp;";
		}
		else {
		     echo "<a class='css_navigation_notselect' href='$url/index.php?page_id=$var_page_id&amp;from=$x&amp;select=$y'>$y</a>&nbsp;";
			 }
	}
	echo "</div>";

	// navigation char forward construct
	if($next>=$num_rows1) {} else {
	$_REQUEST[select_forward]=$select+1;
	$forward ="<a class='css_navigation_char'  href='$url/index.php?page_id=$var_page_id&amp;from=$next&amp;select=$_REQUEST[select_forward]'>$var_forwardchar</a>";
	}

	// navigation char backward construct
	if($back<=-1) {} else {
	$_REQUEST[select_backward]=$select-1;
	$backward = "<a class='css_navigation_char'  href='$url/index.php?page_id=$var_page_id&amp;from=$back&amp;select=$_REQUEST[select_backward]'>$var_backwardchar</a>";
	}

	// show top navigation
	navigation($num_rows1, $var_step, $var_width, $backward, $forward);

	// setlocale
	setlocale(LC_TIME, "$var_setlocale");


	// show DMSGuestbook entries
	foreach ($query2 as $dbresult) {
	$itemnr=($from++)+1;
		// DMSGuestbook post container
		//echo "<div class='css_guestbook_position'>";
		echo "<div class='css_post_embedded'>";

		// build the dta / time variable
		$sec=date("s", "$dbresult->date");
		$min=date("i", "$dbresult->date");
		$hour=date("H", "$dbresult->date");
		$day=date("d", "$dbresult->date");
		$month=date("m", "$dbresult->date");
		$year=date("Y", "$dbresult->date");
		$displaydate = strftime ("$var_dateformat", mktime ($hour, $min, $sec, $month, $day, $year));
		$displaydate=htmlentities($displaydate, ENT_QUOTES);

		// remove quote /
		$message_name=stripslashes($dbresult->name);
		$message_text=stripslashes($dbresult->message);

		// add slash if ip is visible
		if($var_show_ip==1) {
			$slash="&nbsp;/&nbsp;";
			$part1=explode(".", $dbresult->ip);
			$part2=explode(".", $options["ip_mask"]);
				if($part2[0]=="*") {$part1[0]=str_repeat("*", strlen($part1[0]));}
				if($part2[1]=="*") {$part1[1]=str_repeat("*", strlen($part1[1]));;}
				if($part2[2]=="*") {$part1[2]=str_repeat("*", strlen($part1[2]));;}
				if($part2[3]=="*") {$part1[3]=str_repeat("*", strlen($part1[3]));;}
				$show_ip = $part1[0] . "." . $part1[1] . "." . $part1[2] . "." . $part1[3];
		} else {
			   $show_ip=""; $slash="";
			   }

		// show email icon
		if($var_show_email==1 && $dbresult->email != "") {
					# convert to ascii, better spam protection
					unset($ascii_email, $ascii_email_array);
					for($p=0; $p<strlen($dbresult->email); $p++) {
					$ascii_email_array[]=ord($dbresult->email[$p]);
					$ascii_email .= "&#" . $ascii_email_array[$p] . ";";
					}
			$show_email="<a href='mailto:$ascii_email'><img class='css_post_email_image' src='$var_email_image_path' alt='email' /></a>";
		} else {
			   $show_email="";
			   }

		// show url icon
		if($var_show_url==1 && ($dbresult->url != "http://" && $dbresult->url != "https://")) {
					# convert to ascii, better spam protection
					unset($ascii_url, $ascii_url_array);
					for($p=0; $p<strlen($dbresult->url); $p++) {
					$ascii_url_array[]=ord($dbresult->url[$p]);
					$ascii_url .= "&#" . $ascii_url_array[$p] . ";";
					}
			$show_url="<a href='$ascii_url' rel='nofollow' target='_blank'><img class='css_post_url_image' src='$var_website_image_path' alt='url' /></a>&nbsp;";
		} else {
			   $show_url="";
			   }


	// to decide database id or continuous number
	if($var_dbid==1) {
	$show_id = $dbresult->id;
	} else {
		   $show_id = $itemnr;
		   }

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

		$message_text = str_replace("\\","",$message_text);

		include("template/post/$var_post_template");
		echo $GuestbookEntries1;
		echo "</div>";
		echo $GuestbookEntries2;
		//echo "</div>";
	}

	// show bottom navigation
	navigation($num_rows1, $var_step, $var_width, $backward, $forward);

	// if guestbook form is on bottom the side
	if ($var_formpos =="bottom") {
	echo "<a name='guestbookform' class='css_form_link'></a>";
	input_form($error1, $error2, $error3, $error4, $error5,$success, $url, $var_page_id, $lang_name, $lang_email, $var_require_email, $lang_url, $var_require_url, $lang_message, $submitid, $lang_require, $var_require_antispam, $lang_antispam, $lang_submit, $var_url_overruled,$var_mandatory_char, $var_form_template);
	}
?>
	</div>





<?php
function input_form($error1, $error2, $error3, $error4, $error5,$success, $url, $var_page_id, $lang_name, $lang_email, $var_require_email, $lang_url, $var_require_url, $lang_message, $submitid, $lang_require, $var_require_antispam, $lang_antispam, $lang_submit, $var_url_overruled, $var_mandatory_char, $var_form_template) {

	$captcha1 = captcha1($url);
	$captcha2 = captcha2();

	$gbname 	= $_REQUEST[gbname];
	$gbemail 	= $_REQUEST[gbemail];
	$gburl 		= $_REQUEST[gburl];
	$gbmsg 		= str_replace("\\","",$_REQUEST[gbmsg]);

	if($var_require_email==1){$var_mandatory_email=$var_mandatory_char; } else {$var_mandatory_email=""; }
	if($var_require_url==1) 	{$var_mandatory_url=$var_mandatory_char; }   else {$var_mandatory_url=""; }

	include("template/form/$var_form_template");

	//echo "<div class='css_guestbook_position'>";
	echo "<div class='css_form_embedded'>";
	echo $var_form1;

	#Form
	
	if(strlen($var_url_overruled)>4) {
	echo "<form action=\"$var_url_overruled\" method=\"post\">";
	}
	else {
	     echo "<form action=" . "\"" . get_permalink($var_page_id) . "\"" . " method=\"post\">";
	     }

	echo $var_form2;

	if($var_require_antispam==1) {
	echo $var_form3;
	}

	if($var_require_antispam==2) {
	echo $var_form4;
	}

	if($var_require_antispam==0) {
	}

	echo $var_form5 . "<input type='hidden' name='newentry' value='1' />
	<input type='hidden' name='Itemid' value='$submitid' />
	</form>";

	echo $var_form6;
	echo "</div>";
	echo $var_form7;
	//echo "</div>";
}







	# #	# # # # # - FUNCTIONS - # # # # # # #

	/* language */
	function create_language($var_language)
	{
		$abspath = str_replace("\\","/", ABSPATH);
		$handle = fopen ($abspath . "wp-content/plugins/dmsguestbook/language/" . $var_language, "r");
		unset($stringtext);
			if($handle) {
				while (!feof($handle)) {
    			$buffer = fgets($handle, 4096);
				$stringtext=$stringtext . $buffer;
				}
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
		"success",
		"admin_review"
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
	function navigation($num_rows1, $var_step, $var_width, $backward, $forward) {
		if($num_rows1 > $var_step) {
		echo "<div class='css_navigation_char_position'>";
		echo $backward . " " .$forward;
		echo "</div>";
	 	}
	return 0;
	}

	/* captcha image */
	function captcha1($url) {
	$captcha1 = $url . "/wp-content/plugins/dmsguestbook/captcha/captcha.php";
	return $captcha1;
	}

	/* captcha mathematic */
	function captcha2() {
		unset($_SESSION[rand1]);
		unset($_SESSION[rand2]);
		srand();
		$rand1 = rand(1, 9);
		$rand2 = rand(1, 9);
		$captcha2 = $rand1 . " + " . $rand2 . "=";
		$_SESSION[rand1] = $rand1;
		$_SESSION[rand2] = $rand2;
		return $captcha2;
	}

	/* email send function */
	function send_email($var_mail_adress, $nname, $gbemail, $newurl, $ip, $mmu) {
		$date=date("d.m.Y, h:i:s");
		$host = str_replace("www.", "", "$_SERVER[HTTP_HOST]");
		$mail_recipient="$var_mail_adress";
		$mail_sender="DMSGuestbook@".$host;
		$subject="You have a new guestbook post!";
		$mail_text="From: $nname\nMail: $gbemail\nWebsite: $newurl\n\nMessage:\n$mmu\n\nIP: $ip\nDate: $date";
		mail($mail_recipient, $subject, $mail_text,"from:$mail_sender");
	}


/* end guestbook container */
echo "</div>";

?>