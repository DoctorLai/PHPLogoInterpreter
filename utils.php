<?php 
  // utils.php

    function isDigit($s) 
    { 
      $t=ord($s); 
      return (($t >= 48) && ($t <= 57)); 
    } 
    
    function isUpper($s) 
    { 
      $t=ord($s); 
      return (($t >= 65) && ($t <= 90)); 
    } 
    
    function isLower($s) 
    { 
      $t=ord($s); 
      return (($t >= 97) && ($t <= 122)); 
    } 
    
    function isChar($s) 
    { 
      return ((isLower($s)) || (isUpper($s))); 
    } 
    
    function isSpace($s) 
    { 
      $t=ord($s); 
      return (($t == 32)||($t == 9)||($t == 10)||($t == 11)||($t == 13)); 
    } 
    
    function isOperator($s) 
    { 
      return ( ($s == "+") || ($s == "<") || 
               ($s == "-") || ($s == ">") || 
               ($s == "*") || ($s == "%") || 
               ($s == "/") || ($s == "|") || 
               ($s == "(") || ($s == "&") || 
               ($s == ")") 
               ); 
    } 

?> 
