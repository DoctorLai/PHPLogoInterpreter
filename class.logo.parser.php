<?php

  // class.logo.parser.php
  // @url https://github.com/DoctorLai/PHPLogoInterpreter

  // Define Directory
  define('LOGO_DIR', dirname(__FILE__).'/');
  
  // Include
  include(LOGO_DIR.'class.logo.php');
  require_once(LOGO_DIR.'utils.php');
  require_once(LOGO_DIR.'class.storage.php');
  
  // Constants
  define('LOGO_OK_PROC_RETURN',-1);
  define('LOGO_OK',0);
  define('LOGO_ERROR_UNFINISHED_COMMENTS',1);
  define('LOGO_ERROR_MISSING_COMMAND',2);
  define('LOGO_WARNING_NOT_FULLY_IMPLEMENTED',3);
  define('LOGO_WARNING_NUMBER_ROUNDED',4);
  define('LOGO_ERROR_MISSING_ARG',5);
  define('LOGO_ERROR_BAD_COMMAND',6);
  define('LOGO_WARNING_UNSUPPORTED_BG_CHANGE',7);
  define('LOGO_ERROR_NO_REPEAT_TIME',8);
  define('LOGO_ERROR_UNSUPPORTED_GROUP_ACTION',9);
  define('LOGO_ERROR_TOO_MANY_LEFT',10);
  define('LOGO_ERROR_TOO_MANY_RIGHT',11);
  define('LOGO_INFORMATION_TOO_MANY_WARNING',12);
  define('LOGO_ERROR_TIME_OUT',13);
  define('LOGO_WARNING_MISSING_REPEAT_BODY',14);
  define('LOGO_ERROR_STOP_NOT_IN_PROC',15);
  define('LOGO_ERROR_END_NOT_FOUND',16);
  define('LOGO_ERROR_KEYWORD_IN_USE',17);
  define('LOGO_ERROR_NESTED_PROCEDURE_NOT_ALLOWED',18);
  define('LOGO_ERROR_INVALID_PROC_NAME',19);
  define('LOGO_ERROR_PROC_DEFINED',20);
  define('LOGO_ERROR_NO_TO',21);
  define('LOGO_ERROR_INVALID_SYMBOL',22);
  define('LOGO_ERROR_TOO_MANY_PROC',23);
  define('LOGO_WARNING_TURTLE_OUT',24);
  define('LOGO_ERROR_REPEAT_PROC_ARG',25);
  define('LOGO_ERROR_TOO_MANY_LEVELS',26);
  define('LOGO_WARNING_LOCAL_MAKE_GLOBALUSE',27);
  define('LOGO_ERROR_MAKE_NEED_QUOTE',28);
  define('LOGO_ERROR_MAKE_INVALID_NAME',29);
  define('LOGO_ERROR_MAKE_INVALID_VALUE',30);
  define('LOGO_ERROR_UNKNOWN_GROUP',31);
  define('LOGO_ERROR_MISSING_BOOLEAN',32);
  define('LOGO_WARNING_UNSUPPORTED_COLOR',33);
  
  class LogoParser
  {
      var $_logo;
      var $_s;
      var $_vars;
      var $_makevars;
      var $_localmake=array();
      var $_coms=array();
      var $_args=array();
      var $_lineX;
      var $_lineY;
      var $_warnings=array();
      var $_lineW;
      var $_maxW;
      var $_repeat=array();
      var $_repeatT=array();
      var $_maxT;
      var $_runningT;
      var $_maxProcLevel;
      var $_endT;
      var $_maxProc;
      var $_inProc;
      var $_rlvl;
      var $_to_names=array();
      var $_to_start=array();
      var $_to_end=array();
      var $_to_args=array();
      var $_to_counting_args;
      var $_var_stack=array();
      var $_src_line;
      var $_istruecolor;      
      var $_runningProc=array();      
      var $Logo_Color_R=array(0,0,0,0,255,255,165,211,153,173,127,224,250,221,255,255);
      var $Logo_Color_G=array(0,0,255,255,0,0,42,211,153,216,255,255,128,221,255,255);
      var $Logo_Color_B=array(0,255,0,255,0,255,42,211,153,230,212,255,114,221,0,255);      
      var $_keywords_args=array(
      'FORWARD','FD', 'WALK', 'JUMP', 'TURN', 'JMP',
      'BACK','BK',
      'RIGHT','RT',
      'LEFT','LT',
      'SETPC','SETPENCOLOR','SETPENCOLOUR',
      'SETSCREENCOLOR','SETSC','SETSCREENCOLOUR',
      'REPEAT',
      'SETXY','SETPOS','DOTXY',
      'SETX',
      'SETA',
      'SETY',
      'SLEEP',
      'WAIT',
      'IMGW',
      'IMGH',
      'PRINT',
      'TO',
      'ARC','ARC2',
      'MAKE',
      'LOCALMAKE',
      'LOCAL',
      'IF',
      'FOREVER',
      //'IFELSE',
      'SETFC','SETFLOODCOLOR','SETFLOODCOLOUR'
      );      
      var $_keywords=array(
      'HT','HIDETURTLE',
      'ST','SHOWTURTLE',
      'PU','PENUP',
      'PE','PENERASER',
      'PD','PENDOWN','PENNORMAL',
      'CS','CLEANSCREEN','CLEAR',
      'HOME',
      'FENCE',
      'WINDOW',
      'WRAP',
      'FILL',
      'END',
      'STOP',
      'DOT'
      );
      /* Constructor */
      function LogoParser(&$lgo, $s='')
      {
          $this->_logo=&$lgo;
          $this->_lineX=0;
          $this->_lineY=0;
          $this->_lineW='';
          $this->_maxW=25;
          $this->_maxT=3;
          $this->_maxProcLevel=999;
          $this->_maxProc=999;
          $this->_inProc=false;
          $this->_rlvl=0;
          $this->_to_counting_args=false;
          $this->_makevars=new Storage();
          $this->_vars=new Storage();
          $this->_vars->_setVar('REPCOUNT',0);
          $this->_vars->_setVar('SCREENCOLOUR',($lgo->getSC()));
          $this->_vars->_setVar('FLOODCOLOUR',($lgo->getFC()));
          $this->_vars->_setVar('PENCOLOUR',($lgo->getPC()));
          $this->_vars->_setVar('SCREENCOLOR',($lgo->getSC()));
          $this->_vars->_setVar('FLOODCOLOR',($lgo->getFC()));
          $this->_vars->_setVar('PENCOLOR',($lgo->getPC()));
          $this->_vars->_setVar('PI',3.1415926535897932384626433832795);
          $this->_vars->_setVar('WIDTH', $lgo->getImageX());
          $this->_vars->_setVar('HEIGHT', $lgo->getImageY());
          $this->_vars->_setVar('MAXX', $lgo->getImageHX());
          $this->_vars->_setVar('MAXY', $lgo->getImageHY());
          $this->_vars->_setVar('MINX', -$lgo->getImageHX());
          $this->_vars->_setVar('MINY', -$lgo->getImageHY());
          $this->_istruecolor=$lgo->isTrueColor()?1:0;
          $this->_vars->_setVar('ISTRUECOLOR', $this->_istruecolor);
          $this->_vars->_setVar('ISTRUECOLOUR', $this->_istruecolor);          
          $this->_vars->_setVar('BLACK',0);
          $this->_vars->_setVar('BLUE',1);
          $this->_vars->_setVar('GREEN',2);
          $this->_vars->_setVar('CYAN',3);
          $this->_vars->_setVar('RED',4);
          $this->_vars->_setVar('MAGENTA',5);
          $this->_vars->_setVar('BROWN',6);
          $this->_vars->_setVar('LIGHTGRAY',7);
          $this->_vars->_setVar('DARKGRAY',8);
          $this->_vars->_setVar('LIGHTGREY',7);
          $this->_vars->_setVar('DARKGREY',8);
          $this->_vars->_setVar('LIGHTBLUE',9);
          $this->_vars->_setVar('LIGHTGREEN',10);
          $this->_vars->_setVar('LIGHTCYAN',11);
          $this->_vars->_setVar('LIGHTRED',12);
          $this->_vars->_setVar('LIGHTMAGENTA',13);
          $this->_vars->_setVar('YELLOW',14);
          $this->_vars->_setVar('WHITE',15);          
          $this->_vars->_setVar('POSX',0);
          $this->_vars->_setVar('POSY',0);
          $this->_vars->_setVar('DEGREE',0);
      }
      
      function setMaxTimeout($d)
      {
          $d=(integer)$d;
          if ($d<1) $d=1;
          if ($d>5) $d=5;
          $this->_maxT=$d;
      }
      
      function getMaxTimeout()
      {
          return ($this->_maxT);
      }
      
      function getMsg($code, $x=0, $y=0, $w='')
      {
          $code=(integer)$code;
          $m='';
          switch ($code)
          {
              case LOGO_OK:
              case LOGO_OK_PROC_RETURN: $m='OK - EXECUTED'; break;
              case LOGO_ERROR_UNFINISHED_COMMENTS: $m='ERROR - UNFINISHED COMMENTS'; break;
              case LOGO_ERROR_MISSING_COMMAND: $m='ERROR - MISSING COMMAND'; break;
              case LOGO_WARNING_NUMBER_ROUNDED: $m='WARNING - FLOAT ROUNDED AS INTEGER'; break;
              case LOGO_ERROR_MISSING_ARG: $m='ERROR - MISSING ARGUMENTS'; break;
              case LOGO_ERROR_BAD_COMMAND: $m='ERROR - UNKNOWN COMMAND OR PROCEDURE'; break;
              case LOGO_WARNING_UNSUPPORTED_BG_CHANGE: $m='WARNING - UNABLE TO CHANGE SCREEN COLOR IN PROCESS'; break;
              case LOGO_ERROR_TOO_MANY_LEFT: $m='ERROR - MISSING ]'; break;
              case LOGO_ERROR_TOO_MANY_RIGHT: $m='ERROR - MISSING ['; break;
              case LOGO_INFORMATION_TOO_MANY_WARNING: $m='MSG - TOO MANY WARNINGS, THE REST IGNORED'; break;
              case LOGO_ERROR_TIME_OUT: $m='ERROR - TIME OUT'; break;
              case LOGO_WARNING_MISSING_REPEAT_BODY: $m='WARNING - MISSING A GROUP STATMENT'; break;
              case LOGO_WARNING_NOT_FULLY_IMPLEMENTED: $m='WARNING - NOT FULLY IMPLEMENTED'; break;
              case LOGO_ERROR_STOP_NOT_IN_PROC: $m='ERROR - CAN ONLY USE STOP IN PROCEDURE'; break;
              case LOGO_ERROR_END_NOT_FOUND: $m='ERROR - NEED A END TO FINISH DEFINITION OF TO';break;
              case LOGO_ERROR_KEYWORD_IN_USE: $m='ERROR - PROCEDURE NAME IS A RESERVED KEYWORD'; break;
              case LOGO_ERROR_NESTED_PROCEDURE_NOT_ALLOWED: $m='ERROR - NESTED PROCEDURE NOT ALLOWED'; break;
              case LOGO_ERROR_INVALID_PROC_NAME: $m='ERROR - INVALID PROCEDURE NAME'; break;
              case LOGO_ERROR_PROC_DEFINED: $m='ERROR - PROCEDURE CAN NOT BE RE-DEFINED'; break;
              case LOGO_ERROR_NO_TO: $m='ERROR - CANNOT FIND TO TO START DEFINING PROCEDURE'; break;
              case LOGO_ERROR_INVALID_SYMBOL: $m='ERROR - NOT A VALID PROCEDURE NAME'; break;
              case LOGO_ERROR_TOO_MANY_PROC: $m='ERROR - EXCEED MAXIMUM '.$this->_maxProc.' DEFINED PROCEDURES '; break;
              case LOGO_WARNING_TURTLE_OUT: $m='WARNING - TURTLE IS OUT OF BOUND'; break;
              case LOGO_ERROR_REPEAT_PROC_ARG: $m='ERROR - DUPLICATION OF PROCEDURE ARGUMENTS'; break;
              case LOGO_ERROR_TOO_MANY_LEVELS: $m='ERROR - TOO MANY LEVELS OF PROCEDURE'; break;
              case LOGO_WARNING_LOCAL_MAKE_GLOBALUSE: $m='WARNING - LOCALMAKE GLOBALLY USE (NOT DELETED)'; break;
              case LOGO_ERROR_MAKE_NEED_QUOTE: $m='ERROR - MAKE VARIABLE NEEDS DOUBLE QUOTA'; break;
              case LOGO_ERROR_MAKE_INVALID_NAME: $m='ERROR - MAKE VARIABLE IS NOT A VALID KEYWORD'; break;
              case LOGO_ERROR_MAKE_INVALID_VALUE: $m='ERROR - MAKE DOES NOT HAVE A VALID VALUE'; break;
              case LOGO_ERROR_UNKNOWN_GROUP: $m='ERROR - UNKNOWN ACTION TO START A GROUP ['; break;
              case LOGO_ERROR_NO_REPEAT_TIME: $m='ERROR - NO ENOUGH INPUT TO REPEAT'; break;
              case LOGO_ERROR_UNSUPPORTED_GROUP_ACTION: $m='ERROR - UNSUPPORTED GROUP ACTION'; break;
              case LOGO_ERROR_MISSING_BOOLEAN: $m='ERROR - IF STATEMENT MISSING BOOLEAN EXPRESSION'; break;
              case LOGO_WARNING_UNSUPPORTED_COLOR: $m='WARNING - UNSUPPORTED COLOR'; break;
              default: $m='ERROR - UNKNOWN REASONS FAILED THE PROCESS';break;
          }
          $s='';
          $extra='';
          if (strlen($w)>8) $w=substr($w,0,8).'[..]';
          if ($x&&$y)
          {
              if ($w)
              {
                  $extra=' - ('.$w.')';
              }
              $s=$m."$extra AT ROW ".$y.' COL '.$x;
          }
          else
          {
              if ($this->_lineW)
              {
                  $extra=' - ( '.$this->_lineW.' )';
              }
              $s=$m."$extra AT ROW ".$this->_lineY.' COL '.$this->_lineX;
          }
          if (($this->_src_line)&&($code!=LOGO_OK))
          {
              $extra=' (__LINE__:'.$this->_src_line.')';
          }
          return ($s.$extra);
      }
      
      function getMemAndTime()
      {
          return ('[IN '.round($this->_endT,5).' SECONDS, '.(memory_get_usage()).' BYTES ALLOCATED]');
      }
      
      public static function isSymbol($s)
      {
          $__tmp = strlen($s);
          if ($__tmp==0) return (false);
          $ch=$s[0];
          if (isDigit($ch)) return (false);
          for ($i=0;$i<$__tmp;$i++)
          {
              $ch=$s[$i];
              if (!((isChar($ch))||(isDigit($ch))||($ch=='_')))
              {
                  return (false);
              }
          }
          return (true);
      }
      
      function isGood($s)
      {
          return ( (LogoParser::isSymbol($s)) && (!in_array($s, $this->_keywords))
          &&(!in_array($s, $this->_keywords_args))
          &&(!in_array($s, $this->_to_names))
          );
      }
      
      public static function str2num($str, &$changes)
      {
          $t=strlen($str);
          $changes=true;
          $str = preg_replace('`([^+\-*=/\(\)0-9<>&|\.%]*)`','',$str);
          if (strlen($str)==0)
          {
              $str = '0';
          }
          else
          {
              if ($t==strlen($str))
              {
                  eval("\$str = $str;");
                  $changes=false;
              }
              else
              {
                  $str = '0';
              }
          }
          return ($str);
      }
      
      function isNumber($s, &$realnum, &$value)
      {
          if (strpos($s, '==')===false)
          {
              $s=str_replace('=','==',$s);
          }
          foreach ($this->_makevars->keys() as $keys)
          {
              $s=str_replace(':'.$keys, $this->_makevars->getVar($keys), $s);
          }
          foreach ($this->_vars->keys() as $keys)
          {
              $s=str_replace(':'.$keys, $this->_vars->getVar($keys), $s);
          }
          //$s=str_replace($this->_vars->keys(), $this->_vars->values(), $s);
          if (count($this->_runningProc) > 0)
          {
              $procVarIdx=array_search($this->_runningProc[count($this->_runningProc)-1], $this->_to_names);
              if ($procVarIdx!==false)
              {
                foreach ($this->_to_args[$procVarIdx]->keys() as $key)
                {
                    $s=str_replace(':'.$key, $this->_to_args[$procVarIdx]->getVar($key), $s);
                }
              }
          }
          $s=str_replace(':RANDOM',mt_rand(0,32767), $s);
          $s=str_replace(':YEAR',date('Y'), $s);
          $s=str_replace(':MONTH',date('m'), $s);
          $s=str_replace(':DAY',date('d'), $s);
          $s=str_replace(':HOUR',date('H'), $s);
          $s=str_replace(':MINUTE',date('i'), $s);
          $s=str_replace(':SECOND',date('s'), $s);
          $s=str_replace(':CURRENTPIXEL',$this->_logo->pixel(), $s);
          $value=LogoParser::str2num($s, $c);
          $realnum=false;
          if (!$c)
          {
              $realnum=is_float($value);
              //$value=$value;
              return (true);
          }
          $value=(integer)$value;
          return (false);
      }
      
      public static function getNextWord($s, $i, $U, &$j)
      {
          $_k=$i;
          while (($i<=$U)&&(isSpace($s[$i]))) 
          { 
            $i++; 
          }
          if ($i>$U)
          {
              $j=$U;
              return (trim(substr($s, $_k)));
          }
          $ch = $s[$i];          
          if (($ch=='[') || ($ch==']') || ($ch==';') || ($ch=='#'))
          {
              $j=$i;
              return ($ch);
          }
          $_k = $i;
          while ( ( ($i<=$U) ) && 
            ($s[$i]!='[') && ($s[$i]!=']') &&
            ($s[$i]!=';') && ($s[$i]!='#')
          )
          {
              if ($i+1<=$U)
              {
                if ($s[$i]=='/')
                {
                  $ch=$s[$i+1];
                  if (($ch=='/')||($ch=='*'))
                  {
                    break;
                  }
                }
              }
              if (isSpace($s[$i]))
              {
                break;      
              }                                            
              $i++;
          }
          $j=$i-1;
          return trim((substr($s,$_k, $i-$_k)));
      }
            
      function printWarnings()
      {
          $i=0;
          $j=-$this->_logo->getImageHY();
          $t=1;
          $maxcount = count($this->_warnings);
          while ($i<$maxcount)
          {
              $j+=$this->_logo->printText(-$this->_logo->getImageHX(), $j,
              $this->getMsg($this->_warnings[$i],
              $this->_warnings[$i+1],
              $this->_warnings[$i+2],
              $this->_warnings[$i+3])
              );
              $t++;
              if ($t>$this->_maxW)
              {
                  $j+=$this->_logo->printText(-$this->_logo->getImageHX(), $j,
                  $this->getMsg(LOGO_INFORMATION_TOO_MANY_WARNING)
                  );
                  break;
              }
              $i+=4;
          }
          return ($this->_logo->getIYd($j));
      }
      
      function pushWarn($code, $x, $y, $w)
      {
          $t=(integer)(((count($this->_warnings)+1)/4)-1);
          if ($t>$this->_maxW)
          {
              return;
          }
          $same=false;
          $maxcount = count($this->_warnings);
          for ($i=0;$i<$maxcount;$i+=4)
          {
              if (  ($code==$this->_warnings[$i]) &&
              ($x==$this->_warnings[$i+1]) &&
              ($y==$this->_warnings[$i+2])
              )
              {
                  $same=true;
                  break;
              }
          }
          if (!$same)
          {            
              array_push($this->_warnings, $code, $x, $y, $w);
          }
      }
      
      function getX()
      {
          return ($this->_lineX);
      }
      
      function getY()
      {
          return ($this->_lineY);
      }
      
      function getW()
      {
          return ($this->_lineW);
      }
      
      function _getXY($s, $d, $w='')
      {
          $i=0;
          $x=0;
          $j=1;
          while ($i<$d)
          {
              $x++;
              if (($s[$i]=="\n"))
              {
                  $j++;
                  $x=0;
              }
              $i++;
          }
          $this->_lineX=$x+1;
          $this->_lineY=$j;
          $this->_lineW=$w;
      }
      
      function removeLocalVars()
      {
          $__tmp = count($this->_runningProc);
          if ($__tmp == 0) return;
          $k=array_search($this->_runningProc[$__tmp-1], $this->_to_names);
          if ($k===false) return;
          //$s=$this->_localmake;
          foreach ($this->_localmake as $key=>$v)
          {
              if ($v[0]==$this->_to_names[$k])
              {
                  $this->_to_args[$k]->delVar($v[1]);
                  unset($this->_localmake[$key]);
              }
          }
      }
      
      function _parse($s, $i, $U)
      {          
          if ($i > $U) 
          {
              return (LOGO_OK);
          }
          
          $i--;
          while ($i <= $U)
          {
              $i++;
              if ($i > $U)
              {
                  break;
              }
          
              /* Check if Time Out */
              $this->_endT=abs(round(microtime()-$this->_runningT,5));
              if ($this->_endT>$this->_maxT)
              {
                  $this->_getXY($s, $i+1);
                  $this->_src_line=__LINE__;
                  return (LOGO_ERROR_TIME_OUT);
              }
                            
              /* Skip Spaces */
              if (isSpace($s[$i])) continue;
              
              /* Comments Start */
              if ($i+1<$U)
              {
                if (($s[$i]=='/')&&($s[$i+1]=='*'))
                {
                    $i+=2;
                    while ( ($i+1<$U) &&
                    !(($s[$i]=='*')&&($s[$i+1]=='/'))
                    )
                    {
                        $i++;
                    }
                    if (($i+1<$U)&&($s[$i]=='*')&&($s[$i+1]=='/'))
                    {
                        $i++;
                        continue;
                    }
                    else
                    {
                        $this->_getXY($s, $i);
                        $this->_src_line=__LINE__;
                        return (LOGO_ERROR_UNFINISHED_COMMENTS);
                    }
                }
                if (($s[$i]=='/')&&($s[$i+1]=='/'))
                {
                    $i+=2;
                    while (($i<$U)&&($s[$i]!="\n")) { $i++; }
                    if (($i<$U) && ($s[$i]=="\n"))
                    {
                        continue;
                    }
                    break;
                }
              }
              if (($s[$i]==';')||($s[$i]=='#'))
              {
                  $i++;
                  while (($i<$U)&&($s[$i]!="\n")) { $i++; }
                  if (($i<$U) && ($s[$i]=="\n"))
                  {
                      continue;
                  }
                  break;
              }
              /* Coments Ends */            
              
              /* Group */
              if (!$this->_inProc)
              {
                  
                  if ($s[$i]=='[')
                  {
                      if (count($this->_coms)==0)
                      {
                          $this->_getXY($s, $i);
                          $this->_src_line=__LINE__;
                          return (LOGO_ERROR_UNKNOWN_GROUP);
                      }
                      $this->_repeat[] = $i;
                      //array_push($this->_repeat, $i);
                      continue;
                  }
                  
                  if ($s[$i]==']')
                  {
                      if (count($this->_repeat)==0)
                      {
                          $this->_getXY($s, $i);
                          $this->_src_line=__LINE__;
                          return (LOGO_ERROR_TOO_MANY_RIGHT);
                      }
                      $groupstart=array_pop($this->_repeat);
                      if (count($this->_repeat)>0)
                      {
                          continue;
                      }
                      $k=array_pop($this->_coms);
                      if (($k=='REPEAT'))
                      {
                          if (count($this->_repeatT)==0)
                          {
                              $this->_getXY($s, $i);
                              $this->_src_line=__LINE__;
                              return (LOGO_ERROR_NO_REPEAT_TIME);
                          }
                          $lastRT=array_pop($this->_repeatT);
                          $t=0;
                          for ($tt=0;$tt<($lastRT);$tt++)
                          {
                              $this->_vars->_setVar('REPCOUNT',$tt+1);
                              $t=$this->_parse($s, $groupstart+1, $i-1);
                              if (($t!=LOGO_OK)&&($t!=LOGO_OK_PROC_RETURN)) break;
                          }
                          if ($t==LOGO_OK_PROC_RETURN)
                          {
                              if ($this->_rlvl==0)
                              {
                                  $this->_rlvl=1;
                                  return ($t);
                              }
                              continue;
                          }
                          if (($t==LOGO_OK))
                          {
                              continue;
                          }
                          else
                          {
                              return ($t);
                          }
                      }
                      else
                      if (($k=='IF')||($k=='FOREVER'))
                      {
                          if (count($this->_repeatT)==0)
                          {
                              $this->_getXY($s, $i);
                              $this->_src_line=__LINE__;
                              return (LOGO_ERROR_MISSING_BOOLEAN);
                          }
                          $lastRT=array_pop($this->_repeatT);
                          if ($lastRT>0)
                          {
                              $lastRT=1;
                          }
                          else
                          {
                              $lastRT=0;
                          }
                          $t=0;
                          if (strlen($k)==2)
                          {
                              if ($lastRT)
                              {
                                  $t=$this->_parse($s, $groupstart+1, $i-1);
                              }
                          }
                          else
                          {
                              if ($lastRT)
                              {
                                  //for (;;)
                                  //{
                                      $t=$this->_parse($s, $groupstart+1, $i-1);
                                      //if (($t!=LOGO_OK)&&($t!=LOGO_OK_PROC_RETURN)) break;
                                  //}   
                                  // Not implemented in Web-version
                                  $this->_getXY($s, $i, $w);
                                  $this->_src_line=__LINE__;
                                  $this->pushWarn( LOGO_WARNING_NOT_FULLY_IMPLEMENTED,
                                  $this->_lineX,
                                  $this->_lineY,
                                  $k.' '.$w
                                  );                                                                
                              }                              
                          }
                          if ($t==LOGO_OK_PROC_RETURN)
                          {
                              if ($this->_rlvl==0)
                              {
                                  $this->_rlvl=1;
                                  return ($t);
                              }
                              continue;
                          }
                          if (($t==LOGO_OK))
                          {
                              continue;
                          }
                          else
                          {
                              return ($t);
                          }
                      }  
                      $this->_getXY($s, $i);
                      $this->_src_line=__LINE__;
                      return (LOGO_ERROR_UNSUPPORTED_GROUP_ACTION);
                  }
                  
                  /* Skip parsing until complete group statement */
                  if (count($this->_repeat)>0)
                  {
                      continue;
                  }
                  
              } // Not _inProc
              
              /* Get a word */
              $w=(strtoupper(LogoParser::getNextWord($s, $i, $U, $j)));
              if (strlen($w)==0)
              {
                  break;
              }
              
              if ($this->_inProc)
              {
                  if ($w=='TO')
                  {
                      $this->_getXY($s, $i);
                      $this->_src_line=__LINE__;
                      return (LOGO_ERROR_NESTED_PROCEDURE_NOT_ALLOWED);
                  }
                  else
                  if ($w=='END')
                  {
                      $this->_inProc=false;
                      $this->_to_end[] = $i-1;
                      //array_push($this->_to_end,$i);
                      $i=$j;
                      $this->_to_counting_args=false;
                      continue;
                  }
                  else
                  {
                      if ($this->_to_counting_args)
                      {
                          $ww=substr($w,1);
                          if (($w[0]==':')&&($this->isGood($ww)))
                          {
                              $counttoargs=count($this->_to_args);
                              if ($counttoargs == 0)
                              {
                                  $this->_to_args[] = new Storage();
                                  $counttoargs = 0;
                              }
                              else
                              {
                                  $counttoargs--;
                              }
                              //$counttoargs = count($this->_to_args);
                              if ($this->_to_args[$counttoargs]->isVar($ww))
                              {
                                  $this->_getXY($s, $i);
                                  $this->_src_line=__LINE__;
                                  return (LOGO_ERROR_REPEAT_PROC_ARG);
                              }
                              else
                              {
                                  $this->_to_args[$counttoargs]->_setVar($ww, 0);
                              }
                          }
                          else
                          {
                              $this->_to_counting_args=false;
                          }
                      }
                      $i=$j;
                      continue;
                  }
              } // Not _inProc
              else
              {
                  if ($w=='END')
                  {
                      $this->_getXY($s, $i);
                      $this->_src_line=__LINE__;
                      return (LOGO_ERROR_NO_TO);
                  }
              }
              /*
              if (count($this->_repeatT)>0)
              {
                  if (($w=='REPEAT')||($w=='IF')||($w=='FOREVER'))
                  {
                      array_push($this->_repeatT,-1);
                  }
                  $i=$j;
                  continue;
              }
              
              */
              
              if (count($this->_coms)>0)
              {
                  $k=array_pop($this->_coms);
                  if ($k=='TO')
                  {
                      if (  in_array($w, $this->_keywords) ||
                      in_array($w, $this->_keywords_args) )
                      {
                          $this->_getXY($s, $i, $w);
                          $this->_src_line=__LINE__;
                          return (LOGO_ERROR_KEYWORD_IN_USE);
                      }
                      if ( in_array($w, $this->_to_names))
                      {
                          $this->_getXY($s, $i, $w);
                          $this->_src_line=__LINE__;
                          return (LOGO_ERROR_PROC_DEFINED);
                      }
                      if (!LogoParser::isSymbol($w))
                      {
                          $this->_getXY($s, $i, $w);
                          $this->_src_line=__LINE__;
                          return (LOGO_ERROR_INVALID_SYMBOL);
                      }
                      if (count($this->_to_names)+1>$this->_maxProc)
                      {
                          $this->_getXY($s, $i, $w);
                          $this->_src_line=__LINE__;
                          return (LOGO_ERROR_TOO_MANY_PROC);
                      }
                      $this->_inProc=true;
                      //array_push($this->_to_names, $w);
                      $this->_to_names[] = $w;
                      //array_push($this->_to_start, $i+strlen($w));
                      $this->_to_start[] = $i + strlen($w);
                      $this->_to_counting_args=true;
                      //array_push($this->_to_args, new Storage());
                      $this->_to_args[] = new Storage();
                      $i=$j;
                      continue;
                  }
                  //array_push($this->_coms, $k);
                  $this->_coms[] = $k;
              }
              
              // Commands with at least one arg
              if (in_array($w, $this->_keywords_args))
              {
                  if (count($this->_coms)>0)
                  {
                      $this->_getXY($s, $i);
                      $this->_src_line=__LINE__;
                      return (LOGO_ERROR_MISSING_ARG);
                  }
                  //array_push($this->_coms, $w);
                  $this->_coms[] = $w;
                  $i=$j;
                  continue;
              }
              
              $procpos=array_search($w, $this->_to_names);
              if ($procpos!==false)
              {                  
                  $argnum=$this->_to_args[$procpos]->getSize();
                  if ($argnum>0)
                  {
                      //array_push($this->_coms, $w);
                      $this->_coms[] = $w;
                      $i=$j;
                      continue;
                  }
                  /* Procedure with no arguments */
                  //array_push($this->_runningProc, $w);
                  $this->_runningProc[] = $w;
                  if (count($this->_runningProc)>$this->_maxProcLevel)
                  {
                      $this->_getXY($s, $i, $w);
                      $this->_src_line=__LINE__;
                      return (LOGO_ERROR_TOO_MANY_LEVELS);
                  }
                  //array_push($this->_var_stack, $this->_to_args[$procpos]);
                  $this->_var_stack[] = clone $this->_to_args[$procpos];
                  $this->removeLocalVars();
                  $ret=$this->_parse($s, $this->_to_start[$procpos], $this->_to_end[$procpos]);
                  $this->_to_args[$procpos]=array_pop($this->_var_stack);
                  array_pop($this->_runningProc);
                  if ($ret==LOGO_OK_PROC_RETURN)
                  {
                      if ($this->_rlvl==0)
                      {
                          $this->_rlvl=1;
                          return ($ret);
                      }
                      $i=$j;
                      continue;
                  }
                  if (($ret==LOGO_OK)||($ret==LOGO_OK_PROC_RETURN))
                  {
                      $i=$j;
                      continue;
                  }
                  else
                  {
                      return ($ret);
                  }
              }
              
              // Commands with no args
              if (in_array($w, $this->_keywords))
              {
                  if ($w=='STOP')
                  {
                      if (($this->_inProc)||(count($this->_runningProc)>0))
                      {
                          $this->_rlvl=0;
                          return (LOGO_OK_PROC_RETURN);
                      }
                      else
                      {
                          $this->_getXY($s, $i, $w);
                          $this->_src_line=__LINE__;
                          return (LOGO_ERROR_STOP_NOT_IN_PROC);
                      }
                  }
                  if (($w=='PU')||($w=='PENUP'))
                  {
                      if (count($this->_coms)>0)
                      {
                          $this->_getXY($s, $i, $w);
                          $this->_src_line=__LINE__;
                          return (LOGO_ERROR_MISSING_ARG);
                      }
                      $this->_logo->pu();
                      $i=$j;
                      continue;
                  }
                  if (($w=='PE')||($w=='PENERASER'))
                  {
                      if (count($this->_coms)>0)
                      {
                          $this->_getXY($s, $i, $w);
                          $this->_src_line=__LINE__;
                          return (LOGO_ERROR_MISSING_ARG);
                      }
                      $this->_logo->pe();
                      $i=$j;
                      continue;
                  }                  
                  if (($w=='PD')||($w=='PENDOWN')||($w=='PENNORMAL'))
                  {
                      if (count($this->_coms)>0)
                      {
                          $this->_getXY($s, $i, $w);
                          $this->_src_line=__LINE__;
                          return (LOGO_ERROR_MISSING_ARG);
                      }
                      $this->_logo->pd();
                      $i=$j;
                      continue;
                  }
                  if (($w=='ST')||($w=='SHOWTURTLE'))
                  {
                      if (count($this->_coms)>0)
                      {
                          $this->_getXY($s, $i, $w);
                          $this->_src_line=__LINE__;
                          return (LOGO_ERROR_MISSING_ARG);
                      }
                      $this->_logo->st();
                      $i=$j;
                      continue;
                  }
                  if ($w=='DOT')
                  {
                      if (count($this->_coms)>0)
                      {
                          $this->_getXY($s, $i, $w);
                          $this->_src_line=__LINE__;
                          return (LOGO_ERROR_MISSING_ARG);
                      }
                      $this->_logo->dot();
                      $i=$j;
                      continue;
                  }                  
                  if ($w=='FILL')
                  {
                      if (count($this->_coms)>0)
                      {
                          $this->_getXY($s, $i, $w);
                          $this->_src_line=__LINE__;
                          return (LOGO_ERROR_MISSING_ARG);
                      }
                      $this->_logo->fill();
                      $i=$j;
                      continue;
                  }
                  if ($w=='HOME')
                  {
                      if (count($this->_coms)>0)
                      {
                          $this->_getXY($s, $i, $w);
                          $this->_src_line=__LINE__;
                          return (LOGO_ERROR_MISSING_ARG);
                      }
                      $this->_logo->home();
                      $this->_vars->_setVar('POSX', $this->_logo->getX());
                      $this->_vars->_setVar('POSY', $this->_logo->getY());
                      $this->_vars->_setVar('DEGREE', $this->_logo->getD());
                      $i=$j;
                      continue;
                  }
                  if (($w=='HT')||($w=='HIDETURTLE'))
                  {
                      if (count($this->_coms)>0)
                      {
                          $this->_getXY($s, $i, $w);
                          $this->_src_line=__LINE__;
                          return (LOGO_ERROR_MISSING_ARG);
                      }
                      $this->_logo->ht();
                      $i=$j;
                      continue;
                  }
                  if (($w=='WINDOW')||($w=='FENCE')||($w=='WRAP'))
                  {
                      if (count($this->_coms)>0)
                      {
                          $this->_getXY($s, $i, $w);
                          $this->_src_line=__LINE__;
                          return (LOGO_ERROR_MISSING_ARG);
                      }
                      if ($w[1]=='I')
                      {
                          $this->_logo->setWrap(LOGO_WINDOW);
                      }
                      else
                      if ($w[1]=='E')
                      {
                          $this->_logo->setWrap(LOGO_FENCE);
                      }
                      else
                      {
                          $this->_logo->setWrap(LOGO_WRAP);
                      }
                      $this->_vars->_setVar('POSX', $this->_logo->getX());
                      $this->_vars->_setVar('POSY', $this->_logo->getY());
                      $this->_vars->_setVar('DEGREE', $this->_logo->getD());
                      $i=$j;
                      continue;
                  }
                  if (($w=='CS')||($w=='CLEANSCREEN')||($w=='CLEAR'))
                  {
                      if (count($this->_coms)>0)
                      {
                          $this->_getXY($s, $i, $w);
                          $this->_src_line=__LINE__;
                          return (LOGO_ERROR_MISSING_ARG);
                      }
                      if (strlen($w)==5)
                      {
                          $this->_logo->clear();
                      }
                      else
                      {
                          $this->_logo->cs();
                          $this->_vars->_setVar('DEGREE', $this->_logo->getD());
                          $this->_vars->_setVar('POSX', $this->_logo->getX());
                          $this->_vars->_setVar('POSY', $this->_logo->getY());                                                    
                      }
                      /*
                      $this->_getXY($s, $i, $w);
                      $this->_src_line=__LINE__;
                      $this->pushWarn( LOGO_WARNING_NOT_FULLY_IMPLEMENTED,
                      $this->_lineX,
                      $this->_lineY,
                      $w
                      );
                      */
                      $i=$j;
                      continue;
                  }
              } // end of Commands with no args
              
              if (count($this->_coms)>0)
              {
                  $k=array_pop($this->_coms);
                  if ($k=='LOCAL')
                  {
                      if (($w[0]!='"'))
                      {
                          $this->_getXY($s, $i, $w);
                          $this->_src_line=__LINE__;
                          return (LOGO_ERROR_MAKE_NEED_QUOTE);
                      }
                      $ww=substr($w,1);
                      if (!$this->isGood($ww))
                      {
                          $this->_getXY($s, $i, $w);
                          $this->_src_line=__LINE__;
                          return (LOGO_ERROR_MAKE_INVALID_NAME);
                      }
                      if (!(($this->_inProc)||(count($this->_runningProc)>0)))
                      {
                          $this->_getXY($s, $i, $w);
                          $this->_src_line=__LINE__;
                          $this->pushWarn( LOGO_WARNING_LOCAL_MAKE_GLOBALUSE,
                          $this->_lineX,
                          $this->_lineY,
                          $w
                          );
                      }
                      else
                      {
                          $counttoargs=count($this->_runningProc);
                          if ($counttoargs > 0)
                          //array_push($this->_localmake, array($this->_runningProc[$counttoargs-1],$ww));
                          $this->_localmake[] = array($this->_runningProc[$counttoargs-1],$ww); 
                      }
                      $i=$j;
                      continue;
                  }
                  else
                  if (($k=='MAKE')||($k=='LOCALMAKE'))
                  {
                      if (count($this->_args)==0)
                      {
                          if (($w[0]!='"'))
                          {
                              $this->_getXY($s, $i, $w);
                              $this->_src_line=__LINE__;
                              return (LOGO_ERROR_MAKE_NEED_QUOTE);
                          }
                          $ww=substr($w,1);
                          if (!$this->isGood($ww))
                          {
                              $this->_getXY($s, $i, $w);
                              $this->_src_line=__LINE__;
                              return (LOGO_ERROR_MAKE_INVALID_NAME);
                          }
                          //array_push($this->_coms, $k);
                          $this->_coms[] = $k;
                          //array_push($this->_args, $ww);
                          $this->_args[] = $ww;
                          $i=$j;
                          continue;
                      }
                      else
                      {
                          $t=array_pop($this->_args);
                          if ($w[0]=="\"")
                          {
                              $ww=substr($w,1);
                              if ($k=='MAKE')
                              {
                                  $this->_makevars->_setVar($t, $ww);
                                  $i=$j;
                                  continue;
                              }
                              else
                              {
                                  if (!(($this->_inProc)||(count($this->_runningProc)>0)))
                                  {
                                      $this->_getXY($s, $i, $w);
                                      $this->_src_line=__LINE__;
                                      $this->pushWarn( LOGO_WARNING_LOCAL_MAKE_GLOBALUSE,
                                      $this->_lineX,
                                      $this->_lineY,
                                      $w
                                      );
                                      $this->_makevars->_setVar($t, $ww);
                                      $i=$j;
                                      continue;
                                  }
                                  else
                                  {
                                      $counttoargs=count($this->_runningProc);
                                      if ($counttoargs > 0)
                                      {
                                          //array_push($this->_localmake, array(
                                          //$this->_runningProc[$counttoargs-1],
                                          //$t
                                          //));
                                          $this->_localmake[] = array(
                                              $this->_runningProc[$counttoargs-1],
                                              $t
                                          );
                                          $procpos=array_search($this->_runningProc[$counttoargs-1], $this->_to_names);
                                          if ($procpos!==false)
                                          {
                                              $this->_to_args[$procpos]->_setVar($t, $ww);
                                          }
                                          $i=$j;
                                          continue;
                                      }
                                  }
                              }
                          }
                          else
                          {
                              if ($this->isNumber($w, $_real, $value))
                              {
                                  if ($k=='MAKE')
                                  {
                                      $this->_makevars->_setVar($t, $value);
                                      $i=$j;
                                      continue;
                                  }
                                  else
                                  {
                                      if (!(($this->_inProc)||(count($this->_runningProc)>0)))
                                      {
                                          $this->_getXY($s, $i, $w);
                                          $this->_src_line=__LINE__;
                                          $this->pushWarn( LOGO_WARNING_LOCAL_MAKE_GLOBALUSE,
                                          $this->_lineX,
                                          $this->_lineY,
                                          $w
                                          );
                                          $this->_makevars->_setVar($t, $value);
                                          $i=$j;
                                          continue;
                                      }
                                      else
                                      {
                                          $counttoargs=count($this->_runningProc);
                                          if ($counttoargs > 0)
                                          {
                                              $this->_localmake[] = array(
                                              $this->_runningProc[$counttoargs-1],
                                              $t
                                              );
                                              $procpos=array_search($this->_runningProc[$counttoargs-1], $this->_to_names);
                                              if ($procpos!==false)
                                              {
                                                  $this->_to_args[$procpos]->_setVar($t, $value);
                                              }
                                              $i=$j;
                                              continue;
                                          }
                                      }
                                  }
                              }
                              else
                              {
                                  $this->_getXY($s, $i, $w);
                                  $this->_src_line=__LINE__;
                                  return (LOGO_ERROR_MAKE_INVALID_VALUE);
                              }
                          }
                      }
                  }// end of make, local make
                  //array_push($this->_coms, $k);
                  $this->_coms[] = $k;
              }
              
              if ($this->isNumber($w, $realnum, $value))
              {
                  if (count($this->_coms)==0)
                  {
                      $this->_getXY($s, $i, $w);
                      $this->_src_line=__LINE__;
                      return (LOGO_ERROR_MISSING_COMMAND);
                  }
                  /*
                  if ($realnum)
                  {
                      $this->_getXY($s, $i, $w);
                      $this->_src_line=__LINE__;
                      $this->pushWarn( LOGO_WARNING_NUMBER_ROUNDED,
                      $this->_lineX,
                      $this->_lineY,
                      $w
                      );
                  }
                  */
                  $k=array_pop($this->_coms);
                  
                  $procpos=array_search($k, $this->_to_names);
                  if ($procpos!==false)
                  {                      
                      $argnum=$this->_to_args[$procpos]->getSize();
                      if ($argnum>count($this->_args)+1)
                      {
                          //array_push($this->_coms, $k);
                          $this->_coms[] = $k;
                          //array_push($this->_args, $w);
                          $this->_args[] = $w; 
                          $i=$j;
                          continue;
                      }
                      //array_push($this->_args, $w);
                      $this->_args[] = $w;
                      $u=0;
                      //array_push($this->_var_stack, $this->_to_args[$procpos]);
                      $this->_var_stack[] = clone $this->_to_args[$procpos]; 
                      $this->removeLocalVars();
                      $varlist=$this->_to_args[$procpos]->keys();
                      foreach ($varlist as $key)
                      {
                          $this->isNumber($this->_args[$u++], $real, $value);
                          $this->_to_args[$procpos]->_setVar($key,$value);
                      }
                      $usa=$this->_to_start[$procpos]+1;
                      for ($sk=0;$sk<$argnum;$sk++)
                      {
                          $t=LogoParser::getNextWord($s, $usa, $this->_to_end[$procpos], $usa);
                          $usa++;
                      }
                      //array_push($this->_runningProc, $k);
                      $this->_runningProc[] = $k;
                      if (count($this->_runningProc)>$this->_maxProcLevel)
                      {
                          $this->_getXY($s, $i, $w);
                          $this->_src_line=__LINE__;
                          return (LOGO_ERROR_TOO_MANY_LEVELS);
                      }
                      for (; $u>0; $u--)
                      {
                          array_pop($this->_args);
                      }
                      
                      // recursion
                      $ret=false;                      
                      $ret=$this->_parse($s, $usa, $this->_to_end[$procpos]);
                      $this->_to_args[$procpos]=array_pop($this->_var_stack);
                      array_pop($this->_runningProc);
                      if ($ret==LOGO_OK_PROC_RETURN)
                      {
                          if ($this->_rlvl==0)
                          {
                              $this->_rlvl=1;
                              return ($ret);
                          }
                          $i=$j;
                          continue;
                      }
                      if (($ret==LOGO_OK)||($ret==LOGO_OK_PROC_RETURN))
                      {
                          $i=$j;
                          continue;
                      }
                      else
                      {
                          return ($ret);
                      }
                      
                  }
                  else
                  if (($k=='BK')||($k=='BACK')||($k=='FD')||($k=='FORWARD')||($k=='WALK')||($k=='JUMP')||($k=='JMP'))
                  {
                      if ($this->_logo->isWrap()==LOGO_FENCE)
                      {
                          $x1=$this->_logo->getX();
                          $y1=$this->_logo->getY();
                          $x2=round($x1+$value*sin($this->_logo->getD()*0.01745329251994329576923690768489));
                          $y2=round($y1-$value*cos($this->_logo->getD()*0.01745329251994329576923690768489));
                          if ($this->_logo->isOutXY($x2,$y2))
                          {
                              $this->_getXY($s, $i, $w);
                              $this->_src_line=__LINE__;
                              $this->pushWarn( LOGO_WARNING_TURTLE_OUT,
                              $this->_lineX,
                              $this->_lineY,
                              $w
                              );
                          }
                      }
                      if ($k[0]=='J')
                      {
                          $this->_logo->jump($value);
                      }
                      else
                      if ($k[0]=='B')
                      {
                          $this->_logo->fd(-$value);
                      }
                      else
                      {
                          $this->_logo->fd($value);
                      }
                      $this->_vars->_setVar('POSX', $this->_logo->getX());
                      $this->_vars->_setVar('POSY', $this->_logo->getY());
                  }
                  else
                  if (($k=='RT')||($k=='RIGHT')||($k=='TURN'))
                  {
                      $this->_logo->rt($value);
                      $this->_vars->_setVar('DEGREE', $this->_logo->getD());
                  }
                  else
                  if (($k=='LT')||($k=='LEFT'))
                  {
                      $this->_logo->lt($value);
                      $this->_vars->_setVar('DEGREE', $this->_logo->getD());
                  }
                  else
                  if (($k=='SETPC')||($k=='SETPENCOLOR')||($k=='SETPENCOLOUR'))
                  {
                      $value=(integer)$value;
                      if (($value>=0)&&($value<=15))
                      {
                          $this->_logo->setPCrgb($this->Logo_Color_R[$value],
                          $this->Logo_Color_G[$value],
                          $this->Logo_Color_B[$value]);
                      }
                      else
                      {
                          if ($this->_istruecolor)
                          {
                              $this->_logo->setPC($value);
                          }
                          else
                          {
                              $this->_src_line=__LINE__;
                              $this->pushWarn(
                              LOGO_WARNING_UNSUPPORTED_COLOR,
                              $this->_lineX,
                              $this->_lineY,
                              $w
                              );
                          }
                      }
                      $this->_vars->_setVar('PENCOLOR',$value);
                      $this->_vars->_setVar('PENCOLOUR',$value);
                  }
                  else
                  if (($k=='SETFC')||($k=='SETFLOODCOLOR')||($k=='SETFLOODCOLOUR'))
                  {
                      $value=(integer)$value;
                      if (($value>=0)&&($value<=15))
                      {
                          $this->_logo->setFCrgb($this->Logo_Color_R[$value],
                          $this->Logo_Color_G[$value],
                          $this->Logo_Color_B[$value]);
                      }
                      else
                      {
                          if ($this->_istruecolor)
                          {
                              $this->_logo->setFC($value);
                          }
                          else
                          {
                              $this->_src_line=__LINE__;
                              $this->pushWarn(LOGO_WARNING_UNSUPPORTED_COLOR,
                              $this->_lineX,
                              $this->_lineY,
                              $w
                              );
                          }
                      }
                      $this->_vars->_setVar('FLOODCOLOR',$value);
                      $this->_vars->_setVar('FLOODCOLOUR',$value);
                  }
                  else
                  if (($k=='SETSC')||($k=='SETSCREENCOLOR')||($k=='SETSCREENCOLOUR'))
                  {
                      $this->_getXY($s, $i, $w);
                      $this->_src_line=__LINE__;
                      $this->pushWarn( LOGO_WARNING_UNSUPPORTED_BG_CHANGE,
                      $this->_lineX,
                      $this->_lineY,
                      $w
                      );
                      if (($value>=0)&&($value<=15))
                      {
                          $this->_logo->setSCrgb($this->Logo_Color_R[$value],
                          $this->Logo_Color_G[$value],
                          $this->Logo_Color_B[$value]);
                      }
                      else
                      {
                          if ($this->_istruecolor)
                          {
                              $this->_logo->setSC($value);
                          }
                          else
                          {
                              $this->_src_line=__LINE__;
                              $this->pushWarn(LOGO_WARNING_UNSUPPORTED_COLOR,
                              $this->_lineX,
                              $this->_lineY,
                              $w
                              );
                          }
                      }
                      $this->_vars->_setVar('SCREENCOLOR',$value);
                      $this->_vars->_setVar('SCREENCOLOUR',$value);
                  }
                  else
                  if (($k=='REPEAT')||($k=='IF')||($k=='FOREVER'))
                  {
                      //array_push($this->_repeatT, round($value));
                      $this->_repeatT[] = round($value);
                      //array_push($this->_coms, $k);
                      $this->_coms[] = $k;
                  }
                  else
                  if (($k=='SETXY')||($k=='SETPOS'))
                  {
                      if (count($this->_args)==0)
                      {
                          //array_push($this->_coms, $k);
                          $this->_coms[] = $k;
                          //array_push($this->_args, $value);
                          $this->_args[] = $value;
                      }
                      else
                      {
                          $t=array_pop($this->_args);
                          $this->_logo->lineTo($t, -$value, true);
                          if ($this->_logo->isWrap()==LOGO_FENCE)
                          {
                              if ($this->_logo->isOutXY($t, -$value))
                              {
                                  $this->_getXY($s, $i, $w);
                                  $this->_src_line=__LINE__;
                                  $this->pushWarn( LOGO_WARNING_TURTLE_OUT,
                                  $this->_lineX,
                                  $this->_lineY,
                                  $w
                                  );
                              }
                          }
                      }
                      $this->_vars->_setVar('POSX', $this->_logo->getX());
                      $this->_vars->_setVar('POSY', $this->_logo->getY());
                  }
                  else
                  if (($k=='ARC')||($k=='ARC2'))
                  {
                      if (count($this->_args)==0)
                      {
                          //array_push($this->_coms, $k);
                          $this->_coms[] = $k;
                          //array_push($this->_args, $value);
                          $this->_args[] = $value;
                      }
                      else
                      {
                          $t=array_pop($this->_args);
                          $this->_logo->arc($t, $value);
                      }
                  }
                  else
                  if (($k=='DOTXY'))
                  {
                      if (count($this->_args)==0)
                      {
                          //array_push($this->_coms, $k);
                          $this->_coms[] = $k;
                          //array_push($this->_args, $value);
                          $this->_args[] = $value;
                      }
                      else
                      {
                          $t=array_pop($this->_args);
                          $this->_logo->dotxy($t, -$value);
                      }
                  }                  
                  else
                  if (($k=='SLEEP')||($k=='PRINT')||($k=='IMGW')||($k=='IMGH')||($k=='WAIT')||($k=='FOREVER'))
                  {
                      // Not implemented in Web-version
                      $this->_getXY($s, $i, $w);
                      $this->_src_line=__LINE__;
                      $this->pushWarn( LOGO_WARNING_NOT_FULLY_IMPLEMENTED,
                      $this->_lineX,
                      $this->_lineY,
                      $k.' '.$w
                      );                      
                  }
                  else
                  if ($k=='SETA')
                  {
                      $this->_logo->setD($value);
                      $this->_vars->_setVar('DEGREE', $this->_logo->getD());
                  }
                  else
                  if ($k=='SETX')
                  {
                      $this->_logo->lineTo($value, $this->_logo->getY(), true);
                      if ($this->_logo->isWrap()==LOGO_FENCE)
                      {
                          if (($value>$this->_logo->getImageHX())||($value<-$this->_logo->getImageHX()))
                          {
                              $this->_getXY($s, $i, $w);
                              $this->_src_line=__LINE__;
                              $this->pushWarn( LOGO_WARNING_TURTLE_OUT,
                              $this->_lineX,
                              $this->_lineY,
                              $w
                              );
                          }
                      }
                      $this->_vars->_setVar('POSX', $this->_logo->getX());
                      $this->_vars->_setVar('POSY', $this->_logo->getY());
                  }
                  else
                  if ($k=='SETY')
                  {
                      $this->_logo->lineTo($this->_logo->getX(), -$value, true);
                      if ($this->_logo->isWrap()==LOGO_FENCE)
                      {
                          if ((-$value>$this->_logo->getImageHY())||(-$value<-$this->_logo->getImageHY()))
                          {
                              $this->_getXY($s, $i, $w);
                              $this->_src_line=__LINE__;
                              $this->pushWarn( LOGO_WARNING_TURTLE_OUT,
                              $this->_lineX,
                              $this->_lineY,
                              $w
                              );
                          }
                      }
                      $this->_vars->_setVar('POSX', $this->_logo->getX());
                      $this->_vars->_setVar('POSY', $this->_logo->getY());
                  }
                  else
                  if ($k=='TO')
                  {
                      $this->_getXY($s, $i, $w);
                      $this->_src_line=__LINE__;
                      return (LOGO_ERROR_INVALID_PROC_NAME);
                  }
                  else
                  if (($procpos=array_search($w, $this->_to_names))!==false) 
                  {                      
                      $argnum=$this->_to_args[$procpos]->getSize();
                      if ($argnum>0)
                      {
                          //array_push($this->_coms, $w);
                          $this->_coms[] = $w;
                          $i=$j;
                          continue;
                      }
                      //array_push($this->_runningProc, $w);
                      $this->_runningProc[] = $w;
                      if (count($this->_runningProc)>$this->_maxProcLevel)
                      {
                          $this->_getXY($s, $i, $w);
                          $this->_src_line=__LINE__;
                          return (LOGO_ERROR_TOO_MANY_LEVELS);
                      }
                      //array_push($this->_var_stack, $this->_to_args[$procpos]);
                      $this->_var_stack[] = clone $this->_to_args[$procpos]; 
                      $this->removeLocalVars();
                      $ret=$this->_parse($s, $this->_to_start[$procpos], $this->_to_end[$procpos]);
                      $this->_to_args[$procpos]=array_pop($this->_var_stack);
                      array_pop($this->_runningProc);
                      if ($ret==LOGO_OK_PROC_RETURN)
                      {
                          if ($this->_rlvl==0)
                          {
                              $this->_rlvl=1;
                              return ($ret);
                          }
                          $i=$j;
                          continue;
                      }
                      if (($ret==LOGO_OK)||($ret==LOGO_OK_PROC_RETURN))
                      {
                          $i=$j;
                          continue;
                      }
                      else
                      {
                          return ($ret);
                      }
                  }
                  $i=$j;
                  continue;
              }  // end of isNumber
              
              
              /* The word is Not Recongized */
              if ($w)
              {
                  $this->_getXY($s, $i, $w);
                  $this->_src_line=__LINE__;
                  return (LOGO_ERROR_BAD_COMMAND);
              }
              
          }// end of processing
          
          
          /* Error Handling */
          $this->_getXY($s, $U);
          if ($this->_inProc)
          {
              $this->_src_line=__LINE__;
              return (LOGO_ERROR_END_NOT_FOUND);
          }
          else
          if (count($this->_repeat) > 0)
          {
              $this->_src_line=__LINE__;
              return (LOGO_ERROR_TOO_MANY_LEFT);
          }
          else
          if (count($this->_repeatT) > 0)
          {
              $this->_getXY($s, $i);
              $this->_src_line=__LINE__;
              $this->pushWarn( LOGO_WARNING_MISSING_REPEAT_BODY,
              $this->_lineX,
              $this->_lineY,
              '');
          }
          else
          if (count($this->_coms) > 0)
          {
              $this->_src_line=__LINE__;
              return (LOGO_ERROR_MISSING_ARG);
          }
          // Return OK
          return (LOGO_OK);
      }
      
      function parse($s='')
      {
          $this->_runningT = microtime();
          $t = false;
          $len = strlen($s);
          if ($len > 0)
          {
              $t = $this->_parse($s, 0, $len - 1);
          }
          $this->_logo->drawTurtle();
          return ($t);
      }    
  }	
?>
