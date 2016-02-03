<?php 
  // class.storage.php
  
  class Storage 
  { 
    private $_pairs = array(); 
    
    public function Storage($pairs=array()) 
    { 
      //$this->_paris = array(); 
      foreach ($pairs as $keys=>$v) 
      { 
          $this->setVar($keys,$v); 
      } 
    } 
    
    public function getSize() 
    { 
      return (count($this->_pairs)); 
    } 
    
    public function keys() 
    { 
      return (array_keys($this->_pairs)); 
    } 
    
    public function values() 
    { 
      return (array_values($this->_pairs)); 
    } 
    
    public function isVar($name) 
    { 
      return (array_key_exists($name, $this->_pairs)); 
    } 
    
    public function getVar($name) 
    { 
      return (isset($this->_pairs[$name])) ? ($this->_pairs[$name]) : (NULL); 
    } 
    
    public function delVar($name) 
    { 
      if (isset($this->_pairs[$name])) 
      { 
        unset($this->_pairs[$name]); 
      } 
    } 
    
    public function setVar($name, $value) 
    { 
      $this->delVar($name); 
      if (!is_null($value)) 
      { 
        $this->_pairs[$name] = $value; 
      } 
      /* 
      if ($name) 
      { 
        $this->_pairs[$name] = $value; 
      } 
      */ 
    } 
    
    public function _setVar($name, $value) 
    { 
      if ($name) 
      { 
        $this->delVar($name); 
        $this->_pairs[$name] = $value; 
      } 
    } 
    
    public function getAll() 
    { 
      return ($this->_pairs); 
    } 
    
    public function _print() 
    { 
      print_r($this->_pairs); 
    } 
    
    public function toQs() 
    { 
      $_q=''; 
      foreach ($this->_pairs as $key=>$pair) 
      { 
        $key=urlencode(trim($key)); 
        $pair=urlencode(trim($pair)); 
        if (($key) && ($pair)) 
        { 
           $_q.="($key=$pair)"; 
          } 
      } 
      $_q=str_replace(')(','&amp;',$_q); 
      $_q=str_replace('(','',$_q); 
      $_q=str_replace(')','',$_q); 
      $_q='?'.$_q; 
      return ($_q); 
    } 
    
    public function toQs2() 
    { 
      $_q=''; 
      foreach ($this->_pairs as $key=>$pair) 
      { 
        $key=urlencode(trim($key)); 
        $pair=urlencode(trim($pair)); 
        if (($key) && ($pair)) 
        { 
           $_q.="($key=$pair)"; 
          } 
      } 
      $_q=str_replace(')(','&',$_q); 
      $_q=str_replace('(','',$_q); 
      $_q=str_replace(')','',$_q); 
      $_q='?'.$_q; 
      return ($_q); 
    } 
  }; 
  
?> 
