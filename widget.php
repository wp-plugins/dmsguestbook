<?php
/*
Plugin Name: DMSGuestbook widget
Plugin URI: http://DanielSchurter.net
Description: Adds a DMSGuestbook widget to your sidebar.
Author: Daniel M. Schurter
Version: 1.0
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
				'title' => 'Guestbook',
				'entries'=> '5',
				'wordcut'=> '25',
				'orderby'=> 'DESC',
				'showname'=> '1',
				'namecss1'=> '<b style="font-weight:bold;">',
				'namecss2'=> '</b>',
				'messagecss1'=> '<b style="font-weight:normal;">',
				'messagecss2'=> '</b>',
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
				$newoptions['title'] = strip_tags(stripslashes($POSTVARIABLE['DMSGuestbook_title']));
				$newoptions['entries'] = stripslashes($POSTVARIABLE['DMSGuestbook_entries']);
				$newoptions['wordcut'] = stripslashes($POSTVARIABLE['DMSGuestbook_wordcut']);
				$newoptions['orderby'] = stripslashes($POSTVARIABLE['DMSGuestbook_orderby']);
				$newoptions['showname'] = $POSTVARIABLE['DMSGuestbook_showname'];
				$newoptions['namecss1'] = stripslashes($POSTVARIABLE['DMSGuestbook_namecss1']);
				$newoptions['namecss2'] = stripslashes($POSTVARIABLE['DMSGuestbook_namecss2']);
				$newoptions['messagecss1'] = stripslashes($POSTVARIABLE['DMSGuestbook_messagecss1']);
				$newoptions['messagecss2'] = stripslashes($POSTVARIABLE['DMSGuestbook_messagecss2']);
				$newoptions['frame1'] = stripslashes($POSTVARIABLE['DMSGuestbook_frame1']);
				$newoptions['frame2'] = stripslashes($POSTVARIABLE['DMSGuestbook_frame2']);
				}
			if ($options != $newoptions) {
				$options = $newoptions;
				update_option('widget_dmsguestbook', $options);
				}


		echo '<table><tr>';
		echo '<td>Title:</td>
		<td><input style="width:150px;" id="DMSGuestbook_title" name="DMSGuestbook_title" type="text" value="'.htmlspecialchars($options['title'], ENT_QUOTES).'" /></td><tr>';
		echo '<tr><td>Number of post to show:</td>
		<td><input style="width:30px;" type="text" name="DMSGuestbook_entries" value="'.$options['entries'].'"></td></tr>';
		echo '<tr><td>Cut message text after </td>
		<td><input style="width:30px;" type="text" name="DMSGuestbook_wordcut" value="'.$options['wordcut'].'"> characters</td></tr>';
		echo '<tr><td>Order by:</td>
		<td><select style="width:100px;" name="DMSGuestbook_orderby">
		<option>'.$options['orderby'].'</option>
		<option>ASC</option>
		<option>DESC</option>
		</select>
		</td></tr>';

		if($options['showname']==1) {$check1="checked='checked'";} else {$check1="";}
		echo '<tr><td>Show Name:</td>
		<td><input type="checkbox" name="DMSGuestbook_showname" value="1" '.$check1.'></td></tr>';

		echo '<tr><td></td><td><br />Use HTML & CSS elements to customize your settings.<br />
		</td></tr>';

		echo '<td>Header frame:</td>
		<td><textarea style="width:400px;height:50px;background-color:#C4D3FF;" name="DMSGuestbook_frame1" />'.$options['frame1'].'</textarea>
		</td><tr>';

		echo '<td>Name CSS:</td>
		<td><textarea style="width:400px;height:80px;background-color:#DCFFC4;" name="DMSGuestbook_namecss1" />'.$options['namecss1'].'</textarea>
		<textarea style="width:400px;height:20px;background-color:#DCFFC4;" name="DMSGuestbook_namecss2" />'.$options['namecss2'].'</textarea>
		</td><tr>';

		echo '<td>Message CSS:</td>
		<td><textarea style="width:400px;height:80px;background-color:#FFF3C4;" name="DMSGuestbook_messagecss1" />'.$options['messagecss1'].'</textarea>
		<textarea style="width:400px;height:20px;background-color:#FFF3C4;" name="DMSGuestbook_messagecss2" />'.$options['messagecss2'].'</textarea>
		</td><tr>';

		echo '<td>Footer frame:</td>
		<td><textarea style="width:400px;height:50px;background-color:#C4D3FF;" name="DMSGuestbook_frame2" />'.$options['frame2'].'</textarea>
		</td><tr>';

		echo '<input type="hidden" id="submit" name="submit" value="1" />';
		echo '</table>';
	}

/* what you see in the side of your webpage */
function widget_dmsguestbook($args) {
	extract($args);
	$options = get_option('widget_dmsguestbook');
	$title = $options['title'];
	$entries = $options['entries'];
	$wordcut = $options['wordcut'];
	$orderby = $options['orderby'];
	$showname = $options['showname'];
	$namecss1 = $options['namecss1'];
	$namecss2 = $options['namecss2'];
	$messagecss1 = $options['messagecss1'];
	$messagecss2 = $options['messagecss2'];
	$frame1 = $options['frame1'];
	$frame2 = $options['frame2'];

	echo "<!-- Start DMSGuestbook widget -->\n";
	echo $before_widget . $before_title . $title . $after_title . "<br />";

		global $wpdb;
		$table_name = $wpdb->prefix . "dmsguestbook";
		$query = $wpdb->get_results("SELECT * FROM $table_name WHERE flag != '1' ORDER BY id $orderby LIMIT $entries");

		echo $frame1;
		foreach ($query as $result) {
			if($showname==0) {
			} else {echo $namecss1 . $result->name . $namecss2 ."<br />";}
			if($wordcut!=0) {
			echo $messagecss1 . substr($result->message, 0, $wordcut) . $messagecss2 . "..." ."<br /><br />";
			} else {echo $messagecss1 . $result->message . $messagecss2 . "<br /><br />";}
		}
		echo $frame2;

	echo $after_widget;
	echo "\t<!-- Stop DMSGuestbook widget -->\n";
	}


register_sidebar_widget('DMSGuestbook', 'widget_dmsguestbook');
register_widget_control('DMSGuestbook', 'widget_dmsguestbook_control', 600, 650);
}
add_action('plugins_loaded', 'widget_dmsguestbook_init');
?>