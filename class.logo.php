<?php
  // class.logo.php
  // This class is used to do simple Logo Command to a 
  // Image in PHP
  
  error_reporting(E_ALL);

  define('LOGO_WRAP',0);
  define('LOGO_FENCE',1);
  define('LOGO_WINDOW',2);
  
  class Logo
  {
      // X,Y Coordinates
      var $_x;
      var $_y;
      // Image Handler
      var $_im;
      // Width & Height
      var $_imagex;
      var $_imagey;
      var $_half_ix;
      var $_half_iy;
      // Colors
      var $_pc;
      var $_fc;
      var $_sc;
      // Turtle status
      var $_draw;
      var $_d;
      var $_st;
      // Fonts
      var $_font;
      var $_fontw;
      var $_fonth;
      // Window
      var $_win;
      
      function Logo(&$im, $r=255, $g=255, $b=255, $width=720, $height=600)
      {
          if (!$im)
          {
              $im=imagecreate($width,$height);
          }
          $this->_im=$im;
          $this->_imagex=imagesx($im);
          $this->_imagey=imagesy($im);
          $this->_half_ix=$this->_imagex*0.5;
          $this->_half_iy=$this->_imagey*0.5;
          $this->_d=0;
          $this->_draw=true;
          $this->_sc=imagecolorallocate($im, $r, $g, $b);
          imagefill($im, 0, 0, $this->_sc);
          $this->_fc=imagecolorallocate($im, 0, 0, 255);
          $this->_pc=imagecolorallocate($im, 0, 0, 0);
          $this->_win=LOGO_WINDOW;
          $this->_x=0;
          $this->_y=0;
          $this->_st=true;
          $this->_font=4;
          $this->_fontw=imagefontwidth(4);
          $this->_fonth=imagefontheight(4);
      }
      
      function getFont()
      {
          return ($this->_font);
      }
      
      function isTrueColor()
      {
          return (imageistruecolor($this->_im));
      }
      
      function setFont($d)
      {
          $d=(integer)$d;
          if ($d<1) $d=1;
          if ($d>5) $d=5;
          $this->_font=$d;
          $this->_fontw=imagefontwidth($d);
          $this->_fonth=imagefontheight($d);
      }
      
      public static function getRGB($rgb, &$r, &$g, &$b)
      {
          $rgb=(integer)$rgb;
          $r = ($rgb >> 16) & 0xFF;
          $g = ($rgb >> 8) & 0xFF;
          $b = $rgb & 0xFF;
      }
      
      function printText($x, $y, $s, $font=0)
      {
          $x=$this->getIXd($x);
          $y=$this->getIYd($y);
          if ($font)
          {
              imagestring($this->_im, $font, $x, $y, $s, $this->_pc);
          }
          else
          {
              imagestring($this->_im, $this->_font, $x, $y, $s, $this->_pc);
          }
          return ($this->_fonth);
      }
      
      function printTextUp($x, $y, $s, $font=0)
      {
          $x=$this->getIXd($x);
          $y=$this->getIYd($y);
          if ($font)
          {
              imagestringup($this->_im, $font, $x, $y, $s, $this->_pc);
          }
          else
          {
              imagestringup($this->_im, $this->_font, $x, $y, $s, $this->_pc);
          }
          return ($this->_fontw);
      }
      
      function getImageX()
      {
          return $this->_imagex;
      }
      
      function getImageY()
      {
          return $this->_imagey;
      }
      
      function getImageHX()
      {
          return $this->_half_ix;
      }
      
      function getImageHY()
      {
          return $this->_half_iy;
      }
      
      function st()
      {
          $this->_st=true;
      }
      
      function ht()
      {
          $this->_st=false;
      }
      
      function getTurtle()
      {
          return ($this->_st);
      }
      
      function drawTurtle()
      {
          if ($this->_st)
          {
              $t=$this->_draw;
              $this->_draw=true;
              $this->arc(360, 3);
              $this->_draw=$t;
          }
      }
      
      function home()
      {
          $this->_d=0;
          $this->lineTo(0,0,true);
      }
      
      function getD()
      {
          return ($this->_d);
      }
      
      function setD($d)
      {
//          $d=round($d);
          $this->_d=$d;
      }
      
      function dot()
      {
          if ($this->_draw)
          {
              imagesetpixel($this->_im, $this->getIX(), $this->getIY(), $this->_pc);
          }
      }
      
      function dotxy($x, $y)
      {
          if (!$this->_draw) return;
          $dr=$this->_draw;
          $dx=$this->_x;
          $dy=$this->_y;
          $this->pu();
          $this->setXY($x, $y);
          $this->_draw=$dr;
          $this->dot();
          $this->_x=$dx;
          $this->_y=$dy;
      }
      
      function pixelxy($x, $y)
      {
          $xx=$this->getIXd($x);
          $yy=$this->getIYd($y);
          if (($xx>0)&&($yy>0)&&($xx<$this->_imagex)&&($yy<$this->_imagey))
            return imagecolorat($this->_im, $xx, $yy);
          return (0); 
      }
      
      function pixel()
      {
          return ($this->pixelxy($this->_x, $this->_y));
      } 
      
      function cs()
      {
          $this->clean();
          $this->setXY(0,0);
          $this->_d=0;
          //$this->getRGB($this->_sc, $r, $g, $b);
          //$this->_sc=imagecolorallocate($this->_im, $r, $g, $b);
      }
      
      function clear()
      {
          $this->clean();          
          //$this->getRGB($this->_sc, $r, $g, $b);
          //$this->_sc=imagecolorallocate($this->_im, $r, $g, $b);      
      }
      
      function setWrap($d)
      {
          $d=(integer)$d;
          switch ($d)
          {
              case LOGO_FENCE: $this->_win=LOGO_FENCE; break;
              case LOGO_WINDOW: $this->_win=LOGO_WINDOW; break;
              case LOGO_WRAP: $this->_win=LOGO_WRAP; break;
              default: $this->_win=LOGO_WINDOW; break;
          }
          if (($d == LOGO_WRAP)||($d == LOGO_FENCE))
          {
              if ($this->isOut())
              {
                  $this->_x=0;
                  $this->_y=0;
              }
              return;
          }
      }
      
      function isWrap()
      {
          return ($this->_win);
      }
      
      function wrapX($x)
      {
          $this->_crossPoint($this->_x, $this->_y, $x, $this->_y, $nx, $ny);
          return ($nx);
      }
      
      function wrapY($y)
      {
          $this->_crossPoint($this->_x, $this->_y, $this->_x, $y, $nx, $ny);
          return ($ny);
      }
      
      function setX($x)
      {
          $x=round($x);
          $this->_x=$this->wrapX($x);
      }
      
      function setY($y)
      {
          $y=round($y);
          $this->_y=$this->wrapY($y);
      }
      
      function setXY($x,$y)
      {
          $this->_crossPoint($this->_x, $this->_y, $x, $y, $this->_x, $this->_y);
      }
      
      function getX()
      {
          return ($this->wrapX($this->_x));
      }
      
      function getY()
      {
          return ($this->wrapY($this->_y));
      }
      
      function getIXd($d)
      {
          $d=round($d);
          return ($this->wrapX($d)+$this->_half_ix);
      }
      
      function getIYd($d)
      {
          $d=round($d);
          return ($this->wrapY($d)+$this->_half_iy);
      }
      
      function getIX()
      {
          return ($this->getX()+$this->_half_ix);
      }
      
      function getIY()
      {
          return ($this->getY()+$this->_half_iy);
      }
      
      function togglePenStatus()
      {
          $this->_draw=!$this->_draw;
      }
      
      function penStatus()
      {
          return ($this->_draw);
      }
      
      function drawLine($x1,$y1,$x2,$y2)
      {
          $k=$this->_draw;
          $this->pd();
          $ox=$this->_x;
          $oy=$this->_y;
          $this->setXY($x1,$y1);
          $this->lineTo($x2,$y2);
          $this->setXY($ox,$oy);
          $this->_draw=$k;
      }
      
      function jump($d)
      {
          $k=$this->_draw;
          $this->pu();
          $this->fd($d);
          $this->_draw = $k;          
      }
      
      function jmp($d)
      {
          $this->jump($d);
      }
      
      function turn($d)
      {
          $this->rt($d);
      }
      
      function arc($a, $r)
      {
          $a=round($a) % 360;
          $r=round($r);
          $s=90;
          $e=$s+$a;
          if ($r<0)
          {
              $r=-$r;
              $s=270;
              $e=$s+$a;
          }          
          if ($e<$s)
          {
              $t=$e;
              $e=$s;
              $s=$t;
          }
          $s+=$this->_d;
          $e+=$this->_d;
          if ($this->_draw)
          {
              imagearc($this->_im, $this->getIX(), $this->getIY(),
              $r*2, $r*2, $s, $e, $this->_pc);
          }
      }
      
      function isOutXY($x, $y)
      {
          return ( ($x>$this->_half_ix) || ($x<-$this->_half_ix) ||
          ($y>$this->_half_iy) || ($y<-$this->_half_iy));
      }
      
      function isOut()
      {
          return ($this->isOutXY($this->_x, $this->_y));
      }
      
      function isIn()
      {
          return (!$this->isOut());
      }
      
      function isInXY($x, $y)
      {
          return (!$this->isOutXY($x, $y));
      }
      
      function _crossPoint($sx, $sy, $ex, $ey, &$fx, &$fy)
      {
          if ($this->_win == LOGO_WINDOW)
          {
              $fx=$ex;
              $fy=$ey;
              return;
          }
          if (!$this->isOutXY($ex, $ey))
          {
              $fx=$ex;
              $fy=$ey;
              return;
          }
          $k2=$ey-$sy;
          $k1=$ex-$sx;
          if ($this->_win == LOGO_FENCE)
          {
              if ($k2 == 0)
              {
                  $fy=$ey;
                  if ($ex>=$sx)
                  {
                      $fx=$this->_half_ix;
                  }
                  else
                  {
                      $fx=-$this->_half_ix;
                  }
                  return;
              }
              if ($k1 == 0)
              {
                  $fx=$ex;
                  if ($ey>=$sy)
                  {
                      $fy=$this->_half_iy;
                  }
                  else
                  {
                      $fy=-$this->_half_iy;
                  }
                  return;
              }
              $k=$k2/$k1;
              $x1=$this->_half_ix;
              $x2=-$this->_half_ix;
              $y1=$k*($x1-$sx)+$sy;
              $y2=$k*($x2-$sx)+$sy;
              $y3=$this->_half_iy;
              $y4=-$this->_half_iy;
              $x3=($y3-$sy)/$k+$sx;
              $x4=($y4-$sy)/$k+$sx;
              if (($ey>=$sy)&&($ex>=$sx))
              {
                  if (!$this->isOutXY($x1, $y1))
                  {
                      $fx=$x1;
                      $fy=$y1;
                      return;
                  }
                  else
                  {
                      $fx=$x3;
                      $fy=$y3;
                      return;
                  }
              }
              if (($ey>=$sy)&&($ex<=$sx))
              {
                  if (!$this->isOutXY($x2, $y2))
                  {
                      $fx=$x2;
                      $fy=$y2;
                      return;
                  }
                  else
                  {
                      $fx=$x3;
                      $fy=$y3;
                      return;
                  }
              }
              if (($ey<=$sy)&&($ex<=$sx))
              {
                  if (!$this->isOutXY($x2, $y2))
                  {
                      $fx=$x2;
                      $fy=$y2;
                      return;
                  }
                  else
                  {
                      $fx=$x4;
                      $fy=$y4;
                      return;
                  }
              }
              if (($ey<=$sy)&&($ex>=$sx))
              {
                  if (!$this->isOutXY($x1, $y1))
                  {
                      $fx=$x1;
                      $fy=$y1;
                      return;
                  }
                  else
                  {
                      $fx=$x4;
                      $fy=$y4;
                      return;
                  }
              }
          }
          else
          if ($this->_win == LOGO_WRAP)
          {
              if ($k2 == 0)
              {
                  $ny=$ey;
                  if ($ex>=$sx)
                  {
                      $nx=$this->_half_ix;
                      $eex=$ex-$this->_imagex;
                  }
                  else
                  {
                      $nx=-$this->_half_ix;
                      $eex=$ex+$this->_imagex;
                  }
                  $this->_crossPoint(-$nx, $ny, $eex, $ny, $fx, $fy);
                  return;
              }
              if ($k1 == 0)
              {
                  $nx=$ex;
                  if ($ey>=$sy)
                  {
                      $ny=$this->_half_iy;
                      $eey=$ey-$this->_imagey;
                  }
                  else
                  {
                      $ny=-$this->_half_iy;
                      $eey=$ey+$this->_imagey;
                  }
                  $this->_crossPoint($nx, -$ny, $nx, $eey, $fx, $fy);
                  return;
              }
              $k=$k2/$k1;
              $x1=$this->_half_ix;
              $x2=-$this->_half_ix;
              $y1=$k*($x1-$sx)+$sy;
              $y2=$k*($x2-$sx)+$sy;
              $y3=$this->_half_iy;
              $y4=-$this->_half_iy;
              $x3=($y3-$sy)/$k+$sx;
              $x4=($y4-$sy)/$k+$sx;
              if (($ey>=$sy)&&($ex>=$sx))
              {
                  if (!$this->isOutXY($x1, $y1))
                  {
                      $nx=$x1;
                      $ny=$y1;
                      $this->_crossPoint(-$nx, $ny, $ex-$this->_imagex, $ny, $fx, $fy);
                      return;
                  }
                  else
                  {
                      $nx=$x3;
                      $ny=$y3;
                      $this->_crossPoint($nx, -$ny, $nx, $ey-$this->_imagey, $fx, $fy);
                      return;
                  }
              }
              if (($ey>=$sy)&&($ex<=$sx))
              {
                  if (!$this->isOutXY($x2, $y2))
                  {
                      $nx=$x2;
                      $ny=$y2;
                      $this->_crossPoint(-$nx, $ny, $ex+$this->_imagex, $ny, $fx, $fy);
                      return;
                  }
                  else
                  {
                      $nx=$x3;
                      $ny=$y3;
                      $this->_crossPoint($nx, -$ny, $nx, $ey-$this->_imagey, $fx, $fy);
                      return;
                  }
              }
              if (($ey<=$sy)&&($ex<=$sx))
              {
                  if (!$this->isOutXY($x2, $y2))
                  {
                      $nx=$x2;
                      $ny=$y2;
                      $this->_crossPoint(-$nx, $ny, $ex+$this->_imagex, $ny, $fx, $fy);
                      return;
                  }
                  else
                  {
                      $nx=$x4;
                      $ny=$y4;
                      $this->_crossPoint($nx, -$ny, $nx, $ey+$this->_imagey, $fx, $fy);
                      return;
                  }
              }
              if (($ey<=$sy)&&($ex>=$sx))
              {
                  if (!$this->isOutXY($x1, $y1))
                  {
                      $nx=$x1;
                      $ny=$y1;
                      $this->_crossPoint(-$nx, $ny, $ex-$this->_imagex, $ny, $fx, $fy);
                      return;
                  }
                  else
                  {
                      $nx=$x4;
                      $ny=$y4;
                      $this->_crossPoint($nx, -$ny, $nx, $ey+$this->_imagey, $fx, $fy);
                      return;
                  }
              }
          }
      }
      
      function lineTo($x,$y, $moveToPlace=false)
      {
          if (($x == $this->_x)&&($y == $this->_y))
          {
              return;
          }
          if ($this->_draw)
          {
              if ($this->_win == LOGO_WINDOW)
              {
                  imageline($this->_im, $this->getIX(), $this->getIY(),
                  $this->getIXd($x), $this->getIYd($y), $this->_pc);
              }
              else
              {
                  if ($this->_win == LOGO_FENCE)
                  {
                      $this->_crossPoint($this->_x, $this->_y, $x, $y, $nx, $ny);
                      imageline($this->_im, $this->getIX(), $this->getIY(),
                      $this->getIXd($nx), $this->getIYd($ny), $this->_pc);
                  }
                  else
                  {
                      $x1=$this->_x;
                      $y1=$this->_y;
                      $ix1=$this->getIX();
                      $iy1=$this->getIY();
                      $this->_win=LOGO_FENCE;
                      $this->_crossPoint($x1, $y1, $x, $y, $x3, $y3);
                      $this->_win=LOGO_WRAP;
                      imageline($this->_im, $ix1, $iy1, $this->getIXd($x3), $this->getIYd($y3), $this->_pc);
                      $d=sqrt(($y-$y1)*($y-$y1)+($x-$x1)*($x-$x1));
                      $d2=($d)-sqrt(($y3-$y1)*($y3-$y1)+($x3-$x1)*($x3-$x1));
                      if ($d2>0)
                      {
                          if (($x3 == $this->_half_ix))
                          {
                              $nx=-$x3;
                              $ny=$y3;
                              $this->_x=$nx;
                              $this->_y=$ny;
                              $this->lineTo($x-$this->_imagex, $y, true);
                          }
                          else
                          if (($x3 == -$this->_half_ix))
                          {
                              $nx=-$x3;
                              $ny=$y3;
                              $this->_x=$nx;
                              $this->_y=$ny;
                              $this->lineTo($x+$this->_imagex, $y, true);
                          }
                          else
                          if (($y3 == $this->_half_iy))
                          {
                              $nx=$x3;
                              $ny=-$ny;
                              $this->_x=$nx;
                              $this->_y=$ny;
                              $this->lineTo($x, $y-$this->_imagey, true);
                          }
                          else
                          if (($y3 == -$this->_half_iy))
                          {
                              $nx=$x3;
                              $ny=-$ny;
                              $this->_x=$nx;
                              $this->_y=$ny;
                              $this->lineTo($x, $y+$this->_imagey, true);
                          }
                      }
                  }
              }
          }
          if ($moveToPlace)
          {
              $this->setXY($x,$y);
          }
      }
      
      function setPC($d)
      {
          $this->getRGB($d, $r, $g, $b);
          imagecolordeallocate($this->_im, $this->_pc);
          $this->_pc=imagecolorallocate($this->_im, $r, $g, $b);
      }
      
      function setPCrgb($r, $g, $b)
      {
          imagecolordeallocate($this->_im, $this->_pc);
          $this->_pc=imagecolorallocate($this->_im, $r, $g, $b);
      }
      
      function setFC($d)
      {
          $this->getRGB($d, $r, $g, $b);
          imagecolordeallocate($this->_im, $this->_fc);
          $this->_fc=imagecolorallocate($this->_im, $r, $g, $b);
      }
      
      function setFCrgb($r, $g, $b)
      {
          imagecolordeallocate($this->_im, $this->_fc);
          $this->_fc=imagecolorallocate($this->_im, $r, $g, $b);
      }
      
      function setSC($d)
      {
          $this->getRGB($d, $r, $g, $b);
          imagecolordeallocate($this->_im, $this->_sc);
          $this->_sc=imagecolorallocate($this->_im, $r, $g, $b);
      }
      
      function setSCrgb($r, $g, $b)
      {
          imagecolordeallocate($this->_im, $this->_sc);
          $this->_sc=imagecolorallocate($this->_im, $r, $g, $b);
      }
      
      function getPC()
      {
          return ((integer)$this->_pc);
      }
      
      function getFC()
      {
          return ((integer)$this->_fc);
      }
      
      function getSC()
      {
          return ((integer)$this->_sc);
      }
      
      function pu()
      {
          $this->_draw=false;
      }
      
      function pd()
      {
          $this->_draw=true;
      }
      
      function rt($d)
      {
//          $d=round($d);
          $this->_d+=$d;
//          $this->_d%=360;
      }
      
      function lt($d)
      {
          $this->rt(-$d);
      }
      
      function walk($d)
      {
          $this->fd($d);  
      }
      
      function fd($d)
      {
          if ($d == 0)
          {
              return;
          }
          $x1=$this->_x;
          $y1=$this->_y;
          $ix1=$this->getIXd($x1);
          $iy1=$this->getIYd($y1);
          $x2=round($x1+$d*sin($this->_d*pi()/180));
          $y2=round($y1-$d*cos($this->_d*pi()/180));
          $ix2=$this->getIXd($x2);
          $iy2=$this->getIYd($y2);
          if (!$this->isOutXY($x2,$y2))
          {
              if ($this->_draw)
              {
                  imageline($this->_im, $ix1, $iy1, $ix2, $iy2, $this->_pc);
              }
              $this->_x=$x2;
              $this->_y=$y2;
              return;
          }
          
          
          if ($this->_win == LOGO_WINDOW)
          {
              if ($this->_draw)
              {
                  imageline($this->_im, $ix1, $iy1, $ix2, $iy2, $this->_pc);
              }
              $this->_x=$x2;
              $this->_y=$y2;
              return;
          }
          else
          if ($this->_win == LOGO_FENCE)
          {
              $this->_crossPoint($x1, $y1, $x2, $y2, $x3, $y3);
              if ($this->_draw)
              {
                  imageline($this->_im, $ix1, $iy1, $this->getIXd($x3), $this->getIYd($y3), $this->_pc);
              }
              $this->_x=$x3;
              $this->_y=$y3;
          }
          else
          if ($this->_win == LOGO_WRAP)
          {
              $this->_win=LOGO_FENCE;
              $this->_crossPoint($x1, $y1, $x2, $y2, $x3, $y3);
              if ($this->_draw)
              {
                  imageline($this->_im, $ix1, $iy1, $this->getIXd($x3), $this->getIYd($y3), $this->_pc);
              }
              $this->_win=LOGO_WRAP;
              $d2=abs($d)-sqrt(($y3-$y1)*($y3-$y1)+($x3-$x1)*($x3-$x1));
              if (($x3 == $this->_half_ix)||($x3 == -$this->_half_ix))
              {
                  $this->_x=-$x3;
                  $this->_y=$y3;
              }
              else
              {
                  $this->_x=$x3;
                  $this->_y=-$y3;
              }
              if ($d>0)
              {
                  $this->fd($d2);
              }
              else
              {
                  $this->fd(-$d2);
              }
          }
          
      }
      
      function clean()
      {
          for ($i=0; $i<=$this->_imagex; $i++)
          {
              for ($j=0; $j<=$this->_imagey; $j++)
              {
                  imagesetpixel($this->_im, $i, $j, $this->_sc);
              }
          }
      }
      
      function bk($d)
      {
          $this->fd(-$d);
      }
      
      function fill()
      {
          if ($this->isIn())
          {
              imagefill($this->_im, $this->getIX(), $this->getIY(), $this->_fc);
          }
      }
      
      function fillxy($x, $y)
      {
          if ($this->isInXY($x, $y))
          {
              imagefill($this->_im, $this->getIXd($x), $this->getIYd($y), $this->_fc);
          }
      }
      
      function fillxyc($x, $y, $c)
      {
          if ($this->InXY($x, $y))
          {
              imagefill($this->_im, $this->getIXd($x), $this->getIYd($y), $c);
          }
      }    
      
      function pe()
      {
          // Not supported yet      
      }  
  }  	
?>
