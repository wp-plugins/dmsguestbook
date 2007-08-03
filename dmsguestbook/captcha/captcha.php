<?php
   @session_start();
   unset($_SESSION['captcha_spam']);

   function randomString($len) {
      srand(date("s"));
      //Der String $possible enthält alle Zeichen, die verwendet werden sollen
      $possible="ABCDEFGHJKLMNPRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789";
      $str="";
      while(strlen($str)<$len) {
        $str.=substr($possible,(rand()%(strlen($possible))),1);
      }
   return($str);
   }

	//Konvertier HEX in RGB
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
   
   $text = randomString(5);  //Die Zahl bestimmt die Anzahl stellen
   $_SESSION['captcha_spam'] = $text;

   header('Content-type: image/png');
   $img = ImageCreateFromPNG('captcha.png'); //Backgroundimage
   $color = ImageColorAllocate($img, $rgb[0], $rgb[1], $rgb[2]); //Farbe
   $ttf = "XFILES.TTF"; //Schriftart
   $ttfsize = 25; //Schriftgrösse
   $angle = rand(0,5);
   $t_x = rand(5,30);
   $t_y = 35;
   imagettftext($img, $ttfsize, $angle, $t_x, $t_y, $color, $ttf, $text);
   imagepng($img);
   imagedestroy($img);
?>
