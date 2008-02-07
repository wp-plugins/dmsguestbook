<?php
   @session_start();
   unset($_SESSION['captcha_spam']);

   function randomString($len) {
      srand(date("U"));
      // all figures were captcha can use
      $possible="ABCDEFGHJKLMNPRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789";
      unset($str);
      while(strlen($str)<$len) {
        $str.=substr($possible,(rand()%(strlen($possible))),1);
      }
   return($str);
   }

	//convert hex to rgb
	function html2rgb($color)
	{
    if ($color[0] == '#')
        $color = substr($color, 1);

    if (strlen($color) == 6)
        list($r, $g, $b) = array($color[0].$color[1],
                                 $color[2].$color[3],
                                 $color[4].$color[5]);
    elseif (strlen($color) == 3)
        list($r, $g, $b) = array($color[0], $color[1], $color[2]);
    else
        return false;

    $r = hexdec($r); $g = hexdec($g); $b = hexdec($b);

    return array($r, $g, $b);
	}

   $rgb=html2rgb($_SESSION[gb_captcha_color]);

   $text = randomString(5);  // how many figures will be display
   $_SESSION['captcha_spam'] = $text;

   $abspath = getcwd();
   $abspath = str_replace("\\","/", $abspath);

   //header('Content-type: image/png');
   //error_reporting(E_ALL);
   $img = ImageCreateFromPNG('captcha.png'); // background image
   $color = ImageColorAllocate($img, $rgb[0], $rgb[1], $rgb[2]); // color
   $ttf = "$abspath/xfiles.ttf"; //font type
   $ttfsize = 24; // font size
   $angle = rand(0,5);
   $t_x = rand(5,30);
   $t_y = 32;
   imagettftext($img, $ttfsize, $angle, $t_x, $t_y, $color, $ttf, $text);
   imagepng($img);
   imagedestroy($img);




?>