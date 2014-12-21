<?php 
trait traitMultipleton {

  protected static $instances = array();

  public static function getInstance($name = 'default'){
    if(isset(self::$instances[$name])) return self::$instances[$name];

    $classname = __CLASS__;
    self::$instances[$name] = new $classname();
    return self::$instances[$name];
  }

}
