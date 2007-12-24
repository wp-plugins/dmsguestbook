<?
############################################################################################
// be free to change what you want... @ your own risk :-)

// static variable list:
// $gb_bordercolor1            =            outside border color
// $gb_bordercolor2            =            text fields border color
// $gb_navicolor                 =            navigation char color
// $gb_fontcolor                 =            overall font color
// $gb_hairlinecolor             =            guestbook message hairline color
// $gb_position                  =            position in pixel from left to right
// $gb_width                     =            guestbook width in percent
// $gb_width2                   =            guestbook message hairline width in percent

// Stylesheet (CSS) Help & Tutorials:
// English: http://www.html.net/tutorials/css/
// German: http://www.css4you.de/
############################################################################################

# position of the guestbook
$guestbook_position = "
 position:relative;
 top:0px;
 left:$gb_position;
";

# overall guestbook color
$guestbook_font_color = "
 color:$gb_fontcolor1;
";

# name text field
$namefield = "
 border:1px solid $gb_bordercolor2;
 width:150px;
";

#email text field
$emailfield = "
 border:1px solid $gb_bordercolor2;
 width:150px;
";

#url text field
$urlfield = "
 border:1px solid $gb_bordercolor2;
 width:150px;
";

# define space between each text fields
$textfieldspace = "
 text-align:left;
 padding:5px 0px 0px 0px;
 margin:0px 0px;
";

# message text field
$messagefield = "
 border:1px solid $gb_bordercolor2;
 width:80%;
 height:150px;
";

# antispam informationmessage
$antispamtext = "
 text-align:center;
";

# antispam image or mathematic figures
$antispamcontent = "
 border:1px solid $gb_bordercolor2;
";

# antispam image or mathematic figures position
$antispamcontent_position = "
 text-align:center;
 padding:5px 0px;
 margin:0px 0px;
";

# antispam input text field
$antispam_inputfield = "
 width:60px;
 border:1px solid $gb_bordercolor2;
";

# submit button
$submit = "";

# submit button position
$submit_position = "
 text-align:center;
 padding:20px 0px 10px 0px;
";

# wrong input text error message
$errormessage = "
 color:#bb0000;
 font-size: 11px;
 text-decoration: none;
 font-weight:bold;
";

# success input text message
$successmessage = "
 color:#00bb00;
 font-size: 11px;
 text-decoration: none;
 font-weight:bold;
";

# visible if the guestbook form is set to "bottom"
$guestbookform_link = "
 font-size:11px;
 position:relative;
 top:0px;
 left:0;
";

# total guestbook entrys (nr)
$navigation_totalcount = "
 font-size:11px;
 text-align:center;
";

# guestbook pages (1 2 3 4 [..])
$navigation_overview = "
 width:$gb_width;
 text-align:center;
";

# selected guestbook page
$navigation_select = "
 color:#bb1100;
 text-decoration:none;
";

# not selected guestbook page
$navigation_notselect = "
 color:#000000;
 text-decoration:none;
";

# navigation char e.g. < >
$navigation_char = "
 color:$gb_navicolor;
 font-size:$gb_arrowsize;
 text-decoration:none;
 font-weight:bold;
";

# navigation char position
$navigation_char_position = "
 width:$gb_width;
 padding:0px 0px;
 margin:0px 0px 20px 0px;
 text-align:center;
";

# guestbook message number e.g. (24)
$guestbook_message_nr_name = "
 font-size:11px;
 height:15px;
";

# guestbook email container
$guestbook_message_email = "
 width:20px;
 height:15px;
";

# guestbook url container
$guestbook_message_url = "
 width:20px;
 height:15px;
";

# guestbook date & ip address
$guestbook_message_date_ip = "
 font-size:11px;
 height:15px;
";

# email image
$guestbook_email = "
 height:15px;
 width:15px;
 border:0px;
";

# email image path
$guestbook_email_image = "$url/wp-content/plugins/dmsguestbook/img/mail_generic.gif";

# url image
$guestbook_url = "
 height:15px;
 width:15px;
 border:0px;
";

# url image path
$guestbook_url_image = "$url/wp-content/plugins/dmsguestbook/img/gohome.gif";

# guestbook hairline (separator between guestbook header and body)
$guestbook_message_hairline = "
 border: 1px solid $gb_hairlinecolor;
 height:1px; width:$gb_width2;
 text-align:left;
 margin:5px 0px;
";

# content in guestbook body (written text by homepage visitors)
$guestbook_message_body = "
 font-size:11px;
 margin:0px; 0px;
";

# guestbook input data container
$embedded1 = "
 width:$gb_width;
 border:1px solid $gb_bordercolor1;
 font-size:12px;
 text-align:left;
 padding:0px 10px;
 margin:0px 0px 0px 0px;
 line-height:1.4em;
";

# guestbook display post container
$embedded2 = "
 width:$gb_width;
 border:1px solid $gb_bordercolor1;
 font-size:12px;
 text-align:left;
 padding:10px 10px;
 margin:0px 0px 0px 0px;
 line-height:1.4em;
";

############################################################################################
// end of user css settings
?>