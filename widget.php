<?php
/*
Plugin Name: DMSGuestbook widget
Plugin URI: http://DanielSchurter.net
Description: Add a DMSGuestbook widget to your sidebar.
Author: Daniel M. Schurter
Version: 2.00
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
				'namecss1'=> '<b style="font-weight:bold;">(SHOW_NR) ',
				'namecss2'=> '</b>',
				'messagecss1'=> '<b style="font-weight:normal;"><a href=\"LINK1\">',
				'messagecss2'=> '</a></b>',
				'frame1'=> '<div style="background-color:#F7CDC1; padding:5px; border:1px dashed #dd8888;">',
				'frame2'=> '</div>');

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
				$newoptions['namecss1'] = mysql_real_escape_string(preg_replace("/$remove_tags/", "", $POSTVARIABLE['DMSGuestbook_namecss1']));
				$newoptions['namecss2'] = mysql_real_escape_string(preg_replace("/$remove_tags/", "", $POSTVARIABLE['DMSGuestbook_namecss2']));
				$newoptions['messagecss1'] = mysql_real_escape_string(preg_replace("/$remove_tags/", "", $POSTVARIABLE['DMSGuestbook_messagecss1']));
				$newoptions['messagecss2'] = mysql_real_escape_string(preg_replace("/$remove_tags/", "", $POSTVARIABLE['DMSGuestbook_messagecss2']));
				$newoptions['frame1'] = mysql_real_escape_string(preg_replace("/$remove_tags/", "", $POSTVARIABLE['DMSGuestbook_frame1']));
				$newoptions['frame2'] = mysql_real_escape_string(preg_replace("/$remove_tags/", "", $POSTVARIABLE['DMSGuestbook_frame2']));
				}

			if ($options != $newoptions) {
				$options = $newoptions;
				update_option('widget_dmsguestbook', $options);
				}


$options0 = get_option('DMSGuestbook_options');
$part3 = explode("<page_id>", $options0);
$part4 = explode("</page_id>", $part3[1]);
$multi_page_id = explode(",", $part4[0]);

for($m=0; $m<count($multi_page_id); $m++) {
	if($options['guestbook_id'] != $m) {
	$data .= "<option value='$m,$multi_page_id[$m]'>$multi_page_id[$m]</option>";
	}
	else {
	     $dataS .= "<option value='$m,$multi_page_id[$m]' selected>$multi_page_id[$m]</option>";
	     }
}
$data = $dataS . $data;



		echo '<table><tr>';
		echo '<td>Title:</td>
		<td><input style="width:150px;" id="DMSGuestbook_title" name="DMSGuestbook_title" type="text" value="'.str_replace("\\", "", $options['title']).'" /> This will be shown on top of you widget.</td><tr>';
		echo '<td>Page id:</td>
		<td><select style="width:150px;" id="DMSGuestbook_guestbook_id" name="DMSGuestbook_guestbook_id">' . $data . '</select> Which guestbook would you like to display on your sidebar?
		</td><tr>';
		echo '<tr><td>Number:</td>
		<td><input style="width:30px;" type="text" name="DMSGuestbook_entries" value="'.$options['entries'].'"> How many guestbook entries do you want to see on your widget?</td></tr>';
		echo '<tr><td>Lenght:</td>
		<td>Cut message text after <input style="width:30px;" type="text" name="DMSGuestbook_wordcut" value="'.$options['wordcut'].'"> characters.</td></tr>';

		echo "<tr><td></td><td><br />Use HTML & CSS elements to customize your settings.<br />
		Don't forget to close all tags!<br />
		</td></tr>";

		echo '<td>Frame Header:</td>
		<td><textarea style="width:400px;height:80px;background-color:#C4D3FF;" name="DMSGuestbook_frame1" />'.str_replace("\\","",$options['frame1']).'</textarea>
		</td><tr>';

		echo '<td>Name Header:</td>
		<td><textarea style="width:400px;height:80px;background-color:#DCFFC4;" name="DMSGuestbook_namecss1" />'.str_replace("\\","",$options['namecss1']).'</textarea></td></tr>
		<tr><td>Name Footer:</td>
		<td><textarea style="width:400px;height:40px;background-color:#DCFFC4;" name="DMSGuestbook_namecss2" />'.str_replace("\\","",$options['namecss2']).'</textarea>
		</td><tr>';

		echo '<td>Message Header:</td>
		<td><textarea style="width:400px;height:80px;background-color:#FFF3C4;" name="DMSGuestbook_messagecss1" />'.str_replace("\\","",$options['messagecss1']).'</textarea></td></tr>
		<tr><td>Message Footer:</td>
		<td><textarea style="width:400px;height:40px;background-color:#FFF3C4;" name="DMSGuestbook_messagecss2" />'.str_replace("\\","",$options['messagecss2']).'</textarea>
		</td><tr>';

		echo '<td>Frame Footer:</td>
		<td><textarea style="width:400px;height:40px;background-color:#C4D3FF;" name="DMSGuestbook_frame2" />'.str_replace("\\","",$options['frame2']).'</textarea>
		</td><tr>';

		echo '<input type="hidden" id="submit" name="submit" value="1" />';

		/* describe options */
		$url=get_bloginfo('wpurl');
		echo "<tr><td></td><td><br /><b style='font-size:16px;text-decoration: underline;'>Options</b><br />
		<b style='font-weight:normal;font-size:11px;'>Use this tags in the Name and Message fields<br /><br />

		<b>LINK1</b> - Auto link to a guestbook post<br />
	    Example: <i style='color:#dd0000;'>&lt;a href=\"LINK1\"&gt;</i><br />
		LINK1 is trying to generate a link to guestbook with the \"page_id=id\" statement where is defined under page id.<br />
		Don't forget to close the &lt;a href=\"\"... tag with &lt;/a&gt; in the Name or Message Footer.<br />
		<br />
		<b>LINK2</b> - Auto link to a guestbook post<br />
	    Example: <i style='color:#dd0000;'>&lt;a href=\"LINK2\"&gt;</i><br />
		LINK2 is trying to generate a link to guestbook with the \"p=id\" statement where is defined under page id.<br />
		Don't forget to close the &lt;a href=\"\"... tag with &lt;/a&gt; in the Name or Message Footer.<br />
		<br />
		<b>SHOW_POST</b> - Link to the guestbook post (manually)<br />
		Example 1: <i style='color:#dd0000;'>&lt;a href=\"$url/?page_id=YourGuestbookId&SHOW_POST\"&gt;</i><br />
		<br />
		Example 2: <i style='color:#dd0000;'>&lt;a href=\"$url/?p=YourGuestbookId&SHOW_POST\"&gt;</i><br />
		<br />
		Example 3: <i style='color:#dd0000;'>&lt;a href=\"$url/YourGuestbookPageName/?SHOW_POST\"&gt;</i><br />
		<br />
		Use SHOW_POST when do you want to connect from an other webpage to your guestbook page, when do you want to define your own path or when do you have problems with LINK1 or LINK2<br />
		<br />
		<b>SHOW_NR</b> - Count guestbook entries<br />
		Example: <i style='color:#dd0000;'>&lt;b&gt;(SHOW_NR)&lt;/b&gt;</i><br />
		Show the guestbook number.<br />
		<br />
		<b>SHOW_ID</b> - Show the database unique id<br />
		Example: <i style='color:#dd0000;'>&lt;b&gt;(SHOW_ID)&lt;/b&gt;</i><br />
		Show the internal database id.<br />
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
	$namecss1 = str_replace("\\", "", $options['namecss1']);
	$namecss2 = str_replace("\\", "", $options['namecss2']);
	$messagecss1 = str_replace("\\", "", $options['messagecss1']);
	$messagecss2 = str_replace("\\", "", $options['messagecss2']);
	$frame1 = str_replace("\\", "", $options['frame1']);
	$frame2 = str_replace("\\", "", $options['frame2']);

	echo "<!-- Start DMSGuestbook widget -->\n";
	echo $before_widget . $before_title . $title . $after_title . "<br />";


		global $wpdb;
		$table_name = $wpdb->prefix . "dmsguestbook";
			/* read options, use ASC or DESC */
			$options = get_option('DMSGuestbook_options');
			$part1 = explode("<sortitem>", $options);
			$part2 = explode("</sortitem>", $part1[1]);

			$guestbook_id_part1 = explode(",", $guestbook_id);

		$query = $wpdb->get_results("SELECT id, name, message FROM $table_name WHERE flag != '1' && guestbook = '" . sprintf("%d", $guestbook_id_part1[0]) . "' ORDER BY id
		" . sprintf("%s", $part2[0]) . " LIMIT " . sprintf("%d", $entries) . "") or die("Database not available!");

		echo $frame1;
		$itemnr=0;
		$itemnr2=0;
		foreach ($query as $result) {

		$itemnr2++;
			/* rewrite tags */
			$namecss11		=str_replace("SHOW_POST", "from=$itemnr&amp;widget_gb_step=1&amp;select=1&amp;widget=1&amp;itemnr=$itemnr2", $namecss1);
			$messagecss11 	=str_replace("SHOW_POST", "from=$itemnr&amp;widget_gb_step=1&amp;select=1&amp;widget=1&amp;itemnr=$itemnr2", $messagecss1);

			$namecss12 		=str_replace("SHOW_ID", "$result->id", $namecss11);
			$messagecss12 	=str_replace("SHOW_ID", "$result->id", $messagecss11);

			$namecss13		=str_replace("SHOW_NR", $itemnr+1, $namecss12);
			$messagecss13	=str_replace("SHOW_NR", $itemnr+1, $messagecss12);

			$url=get_bloginfo('wpurl');
			$namecss14		=str_replace("LINK1", $url . "/?page_id=" . $guestbook_id_part1[1] . "&amp;from=$itemnr&amp;widget_gb_step=1&amp;select=1&amp;widget=1&amp;itemnr=$itemnr2", $namecss13);
			$messagecss14	=str_replace("LINK1", $url . "/?page_id=" . $guestbook_id_part1[1] . "&amp;from=$itemnr&amp;widget_gb_step=1&amp;select=1&amp;widget=1&amp;itemnr=$itemnr2", $messagecss13);

			$namecss15		=str_replace("LINK2", $url . "/?p=" . $guestbook_id_part1[1] .  "&amp;from=$itemnr&amp;widget_gb_step=1&amp;select=1&amp;widget=1&amp;itemnr=$itemnr2", $namecss14);
			$messagecss15	=str_replace("LINK2", $url . "/?p=" . $guestbook_id_part1[1] .  "&amp;from=$itemnr&amp;widget_gb_step=1&amp;select=1&amp;widget=1&amp;itemnr=$itemnr2", $messagecss14);

			$itemnr++;
			/* end rewrite tags */

			echo $namecss15 . stripslashes($result->name) . $namecss2 . "<br />";

			$message = str_replace("[html]", "", $result->message);
			$message = str_replace("[/html]", "", $message);

			if($wordcut!=0) {
			echo $messagecss15 . substr(str_replace("\\","",stripslashes(strip_tags($message))), 0, $wordcut) . $messagecss2 . "..." ."<br /><br />";
			} else {echo $messagecss15 . strip_tags(str_replace("\\","",$message)) . $messagecss2 . "<br /><br />";}
		}
		echo $frame2;

	echo $after_widget;
	echo "\t<!-- Stop DMSGuestbook widget -->\n";
	}


register_sidebar_widget('DMSGuestbook', 'widget_dmsguestbook');
register_widget_control('DMSGuestbook', 'widget_dmsguestbook_control', 600, 800);
}
add_action('plugins_loaded', 'widget_dmsguestbook_init');
?>