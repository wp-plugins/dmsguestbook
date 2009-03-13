<?php
/*
Plugin Name: DMSGuestbook widget
Plugin URI: http://DanielSchurter.net
Description: Add a DMSGuestbook widget to your sidebar.
Author: Daniel M. Schurter
Version: 2.10
Author URI: http://DanielSchurter.net
*/


/* initializing */
function widget_dmsguestbook_init() {
	if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
		return;
	function widget_dmsguestbook_control() {
		$options = $newoptions = get_option('widget_dmsguestbook');
		if ( !is_array($newoptions) )
			$newoptions = array(
				'title'=> 'Guestbook',
				'guestbook_id'=> '0',
				'entries'=> '5',
				'wordcut'=> '25',
				'dateformat'=> '%a, %e %b %Y, %H:%M:%S %z',
				'widget_header'=> '<div style="background-color:#F7CDC1;padding:5px; border:1px dashed #dd8888;">',
				'widget_footer'=> '</div>',
				'widget_data'=> '<b style="font-weight:bold;">\\r\\n<a href="LINK1">SHOW_NAME</a>\\r\\n</b>\\r\\n<br />\\r\\nSHOW_MESSAGE\\r\\n<br />\\r\\n<br />');

			/* check the old HTTP_POST_VARS and new $_POST var */
			if(!empty($HTTP_POST_VARS)) {
			$POSTVARIABLE   = $HTTP_POST_VARS;
			}
			else {
		 	     $POSTVARIABLE = $_POST;
		 	     }

			if ( $POSTVARIABLE['submit'] ) {

				/* prevent XSS */
				$remove_tags="(\<textarea\>||\<\/textarea\>)";

				$newoptions['title'] = mysql_real_escape_string(preg_replace("/[[:punct:]]/", "", $POSTVARIABLE['DMSGuestbook_title']));
				$newoptions['guestbook_id'] = sprintf("%s", $POSTVARIABLE['DMSGuestbook_guestbook_id']);
				$newoptions['entries'] = sprintf("%d", $POSTVARIABLE['DMSGuestbook_entries']);
				$newoptions['wordcut'] = sprintf("%d", $POSTVARIABLE['DMSGuestbook_wordcut']);
				$newoptions['dateformat'] = mysql_real_escape_string(preg_replace("/[\"\'\`\´\/\\\\]/i", "", $POSTVARIABLE['DMSGuestbook_dateformat']));
				$newoptions['widget_header'] = mysql_real_escape_string(preg_replace("/$remove_tags/", "", $POSTVARIABLE['DMSGuestbook_widget_header']));
				$newoptions['widget_data'] = mysql_real_escape_string(preg_replace("/$remove_tags/", "", $POSTVARIABLE['DMSGuestbook_widget_data']));
				$newoptions['widget_footer'] = mysql_real_escape_string(preg_replace("/$remove_tags/", "", $POSTVARIABLE['DMSGuestbook_widget_footer']));
				}

			if ($options != $newoptions) {
				$options = $newoptions;
				update_option('widget_dmsguestbook', $options);
				}


global $wpdb;
$table_posts = $wpdb->prefix . "posts";

$options0 = get_option('DMSGuestbook_options');
$part3 = explode("<page_id>", $options0);
$part4 = explode("</page_id>", $part3[1]);
$multi_page_id = explode(",", $part4[0]);

for($m=0; $m<count($multi_page_id); $m++) {
	$m2 = $m + 1;
	$query_posts = $wpdb->get_results("SELECT ID, post_title FROM $table_posts WHERE ID = $multi_page_id[$m] ORDER BY id ASC");
	if($options['guestbook_id'] != $m) {
		foreach ($query_posts as $result) {
		$data .= "<option value='$m,$multi_page_id[$m]'>Guestbook: #$m2 (Page: $result->post_title || ID: $result->ID)</option>";
		}
	}
	else {
		 	foreach ($query_posts as $result) {
	     	$dataS .= "<option value='$m,$multi_page_id[$m]' selected>Guestbook: #$m2 (Page: $result->post_title || ID: $result->ID)</option>";
	     	}
	     }
}
$data = $dataS . $data;
?>
		<!-- Widget examples -->
		<script type="text/javascript">
		function example(what) {
     	var header;
		var widget;
		var footer;

     	if(what == "default_widget") {
     		header = "<div style=\"background-color:#F7CDC1;padding:5px;border:1px dashed #dd8888;\">";
     		widget = "<b style=\"font-weight:bold;\">\n<a href=\"LINK1\">SHOW_NAME</a>\n</b>\n<br />\nSHOW_MESSAGE\n<br />\n<br />";
  			footer = "</div>";
  		}
  		if(what == "example1_widget") {
  			header = "<div style=\"border:1px solid #333333;padding:5px;\">";
     		widget = "<b style=\"font-weight:bold;\">\n(SHOW_NR) SHOW_NAME\n</b>\n<br />\n<a href=\"LINK1\">SHOW_MESSAGE</a>\n<br />\n<br />";
  			footer = "</div>";
  		}
  		if(what == "example2_widget") {
  			header = "<div style=\"background-color:#000000;padding:5px;\">";
     		widget = "<b style=\"font-weight:bold;color:#ffffff;\">\n(SHOW_NR) <a href=\"LINK1\">SHOW_NAME</a>\n</b>\n<br />\n<i style=\"color:#bb1100;\">SHOW_MESSAGE</i>\n<br />\n<br />";
  			footer = "</div>";
  		}
  		if(what == "example3_widget") {
  			header = "<div style=\"background-image: url(wp-content/plugins/dmsguestbook/img/testimage.gif);padding:5px;border:1px dashed #000000;\">";
     		widget = "<b>Nr:</b> SHOW_NR\n<br />\n<b>ID:</b> SHOW_ID\n<br />\n<b>Name:</b> <a href=\"LINK1\">SHOW_NAME</a>\n<br />\n<b>Message:</b>SHOW_MESSAGE\n<br />\n<b>Date:</b> SHOW_DATE\n<br />\n<br />";
     		footer = "</div>";
  		}
  		if(what == "example4_widget") {
  			header = "<div style=\"width:100%;height:15px;background-color:#bb1100;\"></div>\n<div style=\"background-color:#dfdfdf;padding:5px;text-align:right;border:1px solid #bb1100;\">";
     		widget = "<b style=\"font-size:15px;\"><a href=\"LINK1\">SHOW_MESSAGE</a></b>\n<br />\nBy: <span style=\"text-transform:uppercase;\">[SHOW_NAME]</span>\n<br />\n<br />\n<br />";
  			footer = "</div>\n<div style=\"width:100%;height:15px;background-color:#bb1100;\"></div>";
  		}
		if(what == "example5_widget") {
			header = "<div style=\"background-color:#000000;color:#088a4b;padding:3px;letter-spacing:3px;\">";
     		widget = "<span style=\"font-size:8px;\">\nSHOW_NAME:~$ ./script.sh\n</span>\n<br />\n<span style=\"font-size:8px;\">\nid: (SHOW_ID)<br />\ndate: SHOW_DATE<br />\nmsg: SHOW_MESSAGE\n</span>\n<br />\n<br />\n<br />";
  			footer = "</div>";
  		}

		document.getElementById('DMSGuestbook_widget_header').value = header;
  		document.getElementById('DMSGuestbook_widget_data').value = widget;
		document.getElementById('DMSGuestbook_widget_footer').value = footer;
		}
		</script>



<?php
		echo '<table style="width:100%"><tr>';
		echo '<th style="width:100px;"></th><th></th></tr>';
		echo '<td><b>Headline:</b></td>
		<td><input style="width:150px;" id="DMSGuestbook_title" name="DMSGuestbook_title" type="text" value="'.str_replace("\\", "", $options['title']).'" /> This will be shown on top of you widget.</td><tr>';
		echo '<td><b>Page id:</b></td>
		<td><select style="width:150px;" id="DMSGuestbook_guestbook_id" name="DMSGuestbook_guestbook_id">' . $data . '</select> Which guestbook would you like to display on your sidebar?
		</td><tr>';
		echo '<tr><td><b>Number:</b></td>
		<td><input style="width:30px;" type="text" name="DMSGuestbook_entries" value="'.$options['entries'].'"> How many guestbook entries do you want to see on your widget?</td></tr>';
		echo '<tr><td><b>Lenght:</b></td>
		<td>Cut message text after <input style="width:30px;" type="text" name="DMSGuestbook_wordcut" value="'.$options['wordcut'].'"> characters.</td></tr>';

		echo '<tr><td><b>Date:</b></td>
		<td><input style="width:250px;" type="text" name="DMSGuestbook_dateformat" value="'.$options['dateformat'].'"> Set the date and time format.<br />
More infos: <a href=\"http://www.php.net/manual/en/function.strftime.php\" target=\"_blank\">http://www.php.net/manual/en/function.strftime.php</a></td></tr>';

		echo "<tr><td></td><td><br />Use <a href='http://w3schools.com/html/default.asp' target='_blank'>HTML</a> & <a href='http://www.w3.org/Style/CSS/learning' target='_blank'>CSS</a> elements to customize your settings.<br />
		Don't forget to close all tags!<br />
		</td></tr>";

		echo '<td><b>Frame Header:</b></td>
		<td><textarea style="width:90%;height:90px;background-color:#C4D3FF;border:1px solid #7F9DB9;" id="DMSGuestbook_widget_header" name="DMSGuestbook_widget_header" />'.str_replace("\\","", str_replace("\\r\\n","\n",$options['widget_header'])).'</textarea>
		</td><tr>';


		echo "<td><b>Widget data:</b><br /><br />
		<a onclick=\"example('default_widget')\">Default</a><br />
		<a onclick=\"example('example1_widget')\">Example #1</a><br />
		<a onclick=\"example('example2_widget')\">Example #2</a><br />
		<a onclick=\"example('example3_widget')\">Example #3</a><br />
		<a onclick=\"example('example4_widget')\">Example #4</a><br />
		<a onclick=\"example('example5_widget')\">Example #5</a><br />
		</td>
		<td><textarea style='width:90%;height:300px;background-color:#FFD3C4;border:1px solid #7F9DB9;' id='DMSGuestbook_widget_data' name='DMSGuestbook_widget_data' />".str_replace("\\","", str_replace("\\r\\n","\n",$options['widget_data']))."</textarea>
		</td><tr>";

		echo '<td><b>Frame Footer:</b></td>
		<td><textarea style="width:90%;height:90px;background-color:#C4D3FF;border:1px solid #7F9DB9;" id="DMSGuestbook_widget_footer" name="DMSGuestbook_widget_footer" />'.str_replace("\\","", str_replace("\\r\\n","\n",$options['widget_footer'])).'</textarea>
		</td><tr>';

		echo '<input type="hidden" id="submit" name="submit" value="1" />';

		/* describe options */
		$url=get_bloginfo('wpurl');
		echo "<tr><td></td><td><br /><b style='font-size:16px;text-decoration: underline;'>Options</b><br />
		<b style='font-weight:normal;font-size:11px;'><br /><br />

		<b>LINK1</b> - Auto link to a guestbook post<br />
	    Example: <i style='color:#dd0000;'>&lt;a href=\"LINK1\"&gt;</i><br />
		LINK1 is trying to generate a link to guestbook with the \"page_id=id\" statement which is defined under page id.<br />
		Don't forget to close the &lt;a href=\"\"... tag with &lt;/a&gt;.<br />
		<br />
		<b>LINK2</b> - Auto link to a guestbook post<br />
	    Example: <i style='color:#dd0000;'>&lt;a href=\"LINK2\"&gt;</i><br />
		LINK2 is trying to generate a link to guestbook with the \"p=id\" statement where is defined under page id.<br />
		Don't forget to close the &lt;a href=\"\"... tag with &lt;/a&gt;.<br />
		<br />
		<b>SHOW_POST</b> - Link to the guestbook post (manually)<br />
		Example 1: <i style='color:#dd0000;'>&lt;a href=\"$url/?page_id=YourGuestbookId&SHOW_POST\"&gt;</i><br />
		<br />
		Example 2: <i style='color:#dd0000;'>&lt;a href=\"$url/?p=YourGuestbookId&SHOW_POST\"&gt;</i><br />
		<br />
		Example 3: <i style='color:#dd0000;'>&lt;a href=\"$url/YourGuestbookPageName/?SHOW_POST\"&gt;</i><br />
		<br />
		Using SHOW_POST when do you want to connect from an other webpage to your guestbook page, when do you want to define your own path or when do you have problems with LINK1 or LINK2<br />
		<br />
		<b>SHOW_NR</b> - Count guestbook entries<br />
		Example: <i style='color:#dd0000;'>&lt;b&gt;(SHOW_NR)&lt;/b&gt;</i><br />
		Entries will be display in bold.<br />
		<br />
		<b>SHOW_ID</b> - Show the database unique id<br />
		Example: <i style='color:#dd0000;'>&lt;b&gt;(SHOW_ID)&lt;/b&gt;</i><br />
		Id will be display in bold.<br />
		<br />
		<b>SHOW_DATE</b> - Show the date which guestbook post was saved<br />
		Example: <i style='color:#dd0000;'>&lt;i&gt;SHOW_DATE&lt;/i&gt;</i><br />
		Date will be display in italic.<br />
		<br />
		<b>SHOW_NAME</b> - Show the visitor name<br />
		Example: <i style='color:#dd0000;'>&lt;span style=\"text-decoration: underline;\"&gt;SHOW_NAME&lt;/span&gt;</i><br />
		Visitor name will be display underlined.<br />
		<br />
		<b>SHOW_MESSAGE</b> - Show the guestbook post text<br />
		Example: <i style='color:#dd0000;'>&lt;span style=\"font-size:9px;\"&gt;SHOW_MESSAGE&lt;/span&gt;</i><br />
		The whole guestbook post will be display in font size 9 pixel.<br />
		<br />
		</td></tr>";

		echo '</table>';
	}

/* what you see in the side of your webpage */
function widget_dmsguestbook($args) {
	extract($args);
	$options = get_option('widget_dmsguestbook');
	$title = str_replace("\\", "", $options['title']);
	$guestbook_id = $options['guestbook_id'];
	$entries = $options['entries'];
	$wordcut = $options['wordcut'];
	$dateformat = $options['dateformat'];
	$header = str_replace("\\", "", str_replace("\\r\\n", "", $options['widget_header']));
	$footer = str_replace("\\", "", str_replace("\\r\\n", "", $options['widget_footer']));
	$widget_data = str_replace("\\","", str_replace("\\r\\n", "", $options['widget_data']));

	echo "<!-- Start DMSGuestbook widget -->\n";
	echo $before_widget . $before_title . $title . $after_title . "<br />";


		global $wpdb;
		$table_name = $wpdb->prefix . "dmsguestbook";
			/* read options, use ASC or DESC */
			$options = get_option('DMSGuestbook_options');
			$part1 = explode("<sortitem>", $options);
			$part2 = explode("</sortitem>", $part1[1]);

			$part3 = explode("<setlocale>", $options);
			$setlocale = explode("</setlocale>", $part3[1]);

			$guestbook_id_part1 = explode(",", $guestbook_id);

		$query = $wpdb->get_results("SELECT id, name, message, date FROM $table_name WHERE flag != '1' && guestbook = '" . sprintf("%d", $guestbook_id_part1[0]) . "' && spam = '0' ORDER BY id
		" . sprintf("%s", $part2[0]) . " LIMIT " . sprintf("%d", $entries) . "");

		echo $header;
		$itemnr=0;
		$itemnr2=0;
		foreach ($query as $result) {
		$itemnr2++;
			/* rewrite tags */
			$url=get_bloginfo('wpurl');
			setlocale(LC_TIME, $setlocale[0]);
			$widget_data0 = str_replace("SHOW_POST", "from=$itemnr&amp;widget_gb_step=1&amp;select=1&amp;widget=1&amp;itemnr=$itemnr2", $widget_data);
			$widget_data1 = str_replace("SHOW_ID", "$result->id", $widget_data0);
			$widget_data2 =	str_replace("SHOW_NR", $itemnr+1, $widget_data1);
			$widget_data3 = str_replace("LINK1", $url . "/?page_id=" . $guestbook_id_part1[1] . "&amp;from=$itemnr&amp;widget_gb_step=1&amp;select=1&amp;widget=1&amp;itemnr=$itemnr2", $widget_data2);
			$widget_data4 = str_replace("LINK2", $url . "/?p=" . $guestbook_id_part1[1] . "&amp;from=$itemnr&amp;widget_gb_step=1&amp;select=1&amp;widget=1&amp;itemnr=$itemnr2", $widget_data3);
			$widget_data5 = str_replace("SHOW_DATE", strftime($dateformat, $result->date), $widget_data4);
			$widget_data6 = str_replace("SHOW_NAME", stripslashes(htmlspecialchars($result->name, ENT_QUOTES)), $widget_data5);

			$itemnr++;

			$message = str_replace("[html]", "", $result->message);
			$message = str_replace("[/html]", "", $message);

			if($wordcut!=0) {
			$gbtext = substr(str_replace("\\","",stripslashes(strip_tags($message))), 0, $wordcut) . "...";
			}
			else {
			     $gbtext = strip_tags(str_replace("\\","",$message));
			     }

		$widget_data7 = str_replace("SHOW_MESSAGE", stripslashes($gbtext), $widget_data6);
		echo $widget_data7;
		}

		echo $footer;

	echo $after_widget;
	echo "\t<!-- Stop DMSGuestbook widget -->\n";
	}


register_sidebar_widget('DMSGuestbook', 'widget_dmsguestbook');
register_widget_control('DMSGuestbook', 'widget_dmsguestbook_control', 600, 800);
}
add_action('plugins_loaded', 'widget_dmsguestbook_init');
?>