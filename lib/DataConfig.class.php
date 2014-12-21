<?php 
require_once(dirname(__FILE__).'/traitMultipleton.trait.php');

class DataConfig {
  use traitMultipleton; 

  protected $config = array();

  public static function run($arg, $options = array()){
    $base_path = empty($options['base_path'])?dirname(__FILE__):$options['base_path'];
    $command = empty($arg[0])?'help':strtolower($arg[0]);
    
    /*
    $instance_name = empty($options['instance'])?'default':$options['instance'];
    $instance = static::getInstance();
    */

    switch($command){
      case 'help':
        echo file_get_contents($base_path.'/data-config-help.txt');

      break; case 'set':
        if(count($arg)!=3) return self::showError('Invalid number of arguments');
        static::getInstance()->set($arg[1],$arg[2]);

      break; case 'view':
        if(count($arg)>2) return self::showError('Invalid number of arguments');
        static::getInstance()->view();

      break; case 'get':
        if(count($arg)>2) return self::showError('Invalid number of arguments');
        if(count($arg)==2) static::getInstance()->get($arg[1]);
        if(count($arg)==1) static::getInstance()->view();

      break; case 'replace':
      break; case 'remove':
        if(count($arg)!=2) return self::showError('Invalid number of arguments');
        static::getInstance()->remove($arg[1]);

      break;
    }

    return static::getInstance();
  }

  public function set($path, $value){
    $this->loadCurrentConfig();
    $this->setConfigKey($path, $value);
    $this->saveCurrentConfig();
  }

  public function get($path = array()){
    $this->loadCurrentConfig();
    $value = $this->getConfigKey($path);
    if(is_string($value)) {
      echo $value."\n";
    } else {
      echo json_encode($value, JSON_PRETTY_PRINT)."\n";
    }
  }
  
  public function remove($path){
    $this->loadCurrentConfig();
    $this->unsetConfigKey($path);
    $this->saveCurrentConfig();
  }
  
  public function view($path = NULL){
    $this->loadCurrentConfig();
    echo json_encode($this->config, JSON_PRETTY_PRINT)."\n";
  }

  public function currentConfigFileName(){
    return $_SERVER['HOME'].'/.data_util';
  }
  
  public function loadCurrentConfig(){
    if(is_readable($this->currentConfigFileName())) {
      $current = json_decode(file_get_contents($this->currentConfigFileName()), true);
    } else {
      $current = array();
    }
    if(!is_array($current)) $current = array();
    $this->config = $current;
  }

  public function saveCurrentConfig(){
    $filename = $this->currentConfigFileName();
    if( (file_exists($filename) && !is_dir($filename) && is_writable($filename)) ||
        (!file_exists($filename) && is_writable(dirname($filename)))) {
      file_put_contents($filename, json_encode($this->config, JSON_PRETTY_PRINT));
    } else {
      return $this->showError("'". $filename ."' is not writable.");
    }
  }

  public function showError($error){
    echo "ERROR: ".$error."\n";
    return 1;
  }

  protected function getConfigKey($keys) {
    $keys = explode(".", $keys);
    $path = &$this->config;
    foreach($keys as $key) {
      if(!isset($path[$key])) return NULL;
      $path = &$path[$key];
    }
      return $path;
  }

  protected function setConfigKey($keys, $value) {
    $keys = explode(".", $keys);
    $path = &$this->config;
    foreach($keys as $key) {
      if(is_string($path)) {
        return $this->showError("Can't set subkey of a string");
      }
      if(!isset($path[$key])) $path[$key] = array(); 
      $path = &$path[$key];
    }
    $path = $value;
  }
  
  protected function unsetConfigKey($keys) {
    $keys = explode(".", $keys); 

    $this->unsetConfigKey_($this->config, $keys);
  }

  protected function unsetConfigKey_(&$array, $keys) {
    $localkeys = $keys;
    $local_key = array_shift($localkeys);
    
    if(count($localkeys) < 1 ) { // This is the value we want to remove
      unset($array[$local_key]);
      return;
    } 
    
    $this->unsetConfigKey_($array[$local_key], $localkeys);  // Recurse into
  
    if(count($array[$local_key]) < 1 ) {  // Is the branch empty now?
      unset($array[$local_key]);
      return;
    }
  }
}
