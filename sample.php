<?php 
  // sample
  @include_once("class.logo.php"); 
  @include_once("class.logo.parser.php"); 
    
  // Header 
  header("Content-Type: image/png"); 
  $im = @imagecreatetruecolor(720,600); 
  $canvas=new Logo(&$im, 255, 255, 255); 
  $canvas->setWrap(LOGO_WINDOW); 
  $textcolor = ImageColorAllocate($im, 0 ,0, 0); 
  $canvas->setPC($textcolor); 
  $canvas->pd(); 
    
  // Draw a star 
  $src="repeat 5 [fd 100 rt 144]"; 
    
  $errmsg="https://SteakOverCooked.com"; 
  $parser=new LogoParser(&$canvas); 
  $ret=$parser->parse(($src)); 
  $w=$parser->printWarnings(); 
  $fs=$canvas->getFont(); 
  imagestring($im, $fs, 0, $w, $parser->getMsg($ret), $canvas->getPC()); 
  imagestring($im, $fs, 0, $w+imagefontheight($fs), $parser->getMemAndTime(), $canvas->getPC()); 
  imagestring($im, 4, 0, imagesy($im)-imagefontheight(4), "(C) 2007~ ZHIHUA, LAI", $textcolor); 
  imagepng($im); 
  imagedestroy($im); 
?> 
