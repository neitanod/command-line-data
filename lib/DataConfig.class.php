<?php 
if(!defined("WORKING_PATH")) define("WORKING_PATH", getcwd());

require_once(dirname(__FILE__).'/traitMultipleton.trait.php');

class DataConfig {
  use traitMultipleton; 

  protected $working_path = WORKING_PATH;
  protected $working_file = '.data.conf';
  protected $config = array();

  public static function run($arg, $options = array()){
    $instance = static::getInstance();

    $instance->working_path = empty($options['working_path'])?$instance->working_path:$options['working_path'];
    if(!empty($options['working_file'])) $instance->working_file = $options['working_file'];

    $base_path = empty($options['base_path'])?dirname(__FILE__):$options['base_path'];
    $command = empty($arg[0])?'help':strtolower($arg[0]);
    
    /*
    $instance_name = empty($options['instance'])?'default':$options['instance'];
    $instance = static::getInstance();
    */

    switch($command){
      case 'help':
        echo file_get_contents($base_path.'/data-config-help.txt');

      break; case 'show':
        if(count($arg)>2) return $instance->showError('Invalid number of arguments');
        $instance->runShow();

      break; case 'set':
        if(count($arg)!=3) return $instance->showError('Invalid number of arguments');
        $instance->runSet($arg[1],$arg[2]);

      break; case 'get':
        if(count($arg)>2) return $instance->showError('Invalid number of arguments');
        if(count($arg)==2) $instance->runGet($arg[1]);
        if(count($arg)==1) $instance->runShow();

      break; case 'add':
        if(count($arg)!=3) return $instance->showError('Invalid number of arguments');
        $instance->runAdd($arg[1],$arg[2]);

      break; case 'remove':
        if(count($arg)!=2) return self::showError('Invalid number of arguments');
        $instance->runRemove($arg[1]);

      break;
    }

  }

  protected function __construct(){
    $this->loadCurrentConfig();
  }

  public function runSet($path, $value){
    $this->set($path, $value);
  }

  public function runAdd($path, $value){
    $this->add($path, $value);
  }

  public function runGet($path = array()){
    $value = $this->get($path);
    if(is_string($value)) {
      echo $value."\n";
    } else {
      echo json_encode($value, JSON_PRETTY_PRINT)."\n";
    }
  }
  
  public function runRemove($path){
    $this->remove($path);
  }
  
  public function runShow($path = NULL){
    echo json_encode($this->config, JSON_PRETTY_PRINT)."\n";
  }

  public function currentConfigFileName($new_filename = NULL){
    if(!is_null($new_filename)) {
      $this->working_file = $new_filename;
    }
    return $this->working_file;
  }

  public function currentConfigFilePath($new_filepath = NULL){
    if(!is_null($new_filepath)) {
      $this->working_path = dirname($new_filepath);
      $this->working_file = basename($new_filepath);
      $this->loadCurrentConfig();
    }
    return $this->working_path.'/'.$this->working_file;
  }
  
  public function loadCurrentConfig(){
    if(is_readable($this->currentConfigFilePath())) {
      $current = json_decode(file_get_contents($this->currentConfigFilePath()), true);
    } else {
      $current = array();
    }
    if(!is_array($current)) $current = array();
    $this->config = $current;
  }

  public function saveCurrentConfig(){
    $filename = $this->currentConfigFilePath();
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

  public function get($keys) {
    $keys = explode(".", $keys);
    $path = &$this->config;
    foreach($keys as $key) {
      if(!isset($path[$key])) return NULL;
      $path = &$path[$key];
    }
    return $path;
  }

  public function set($keys, $value) {
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
    $this->saveCurrentConfig();
  }

  public function add($keys, $value) {
    $keys = explode(".", $keys);
    $path = &$this->config;
    foreach($keys as $key) {
      if(is_string($path)) {
        return $this->showError("Can't set subkey of a string");
      }
      if(!isset($path[$key])) $path[$key] = array(); 
      $path = &$path[$key];
    }
    if(is_array($path)) { 
      $path[] = $value;
    } else {
      return $this->showError("Element is not an array.  Can't add an entry.");
    }
    $this->saveCurrentConfig();
  }
  
  public function remove($keys) {
    $keys = explode(".", $keys); 

    $this->unset_($this->config, $keys);
    $this->saveCurrentConfig();
  }

  protected function unset_(&$array, $keys) {
    $localkeys = $keys;
    $local_key = array_shift($localkeys);
    
    if(count($localkeys) < 1 ) { // This is the value we want to remove
      unset($array[$local_key]);
      return;
    } 
    
    $this->unset_($array[$local_key], $localkeys);  // Recurse into
  
    if(count($array[$local_key]) < 1 ) {  // Is the branch empty now?
      unset($array[$local_key]);
      return;
    }
  }
}