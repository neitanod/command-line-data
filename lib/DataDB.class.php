<?php 
if(!defined("WORKING_PATH")) define("WORKING_PATH", getcwd());

require_once(dirname(__FILE__).'/DataConfig.class.php');
require_once(dirname(__FILE__).'/traitMultipleton.trait.php');
require_once(dirname(__FILE__).'/traitDataInstance.trait.php');

class DataDB {
  use traitMultipleton; 
  use traitDataInstance; 

  public static function run($arg, $options = array()){
    $instance = static::getInstance();

    $instance->working_path = empty($options['working_path'])?WORKING_PATH:$options['working_path'];
    $base_path = empty($options['base_path'])?dirname(__FILE__):$options['base_path'];
    $command = empty($arg[0])?'help':strtolower($arg[0]);
    
    switch($command){
      case 'help':
        echo file_get_contents($base_path.'/data-db-help.txt');
      
      break; case 'list':
        if(count($arg)!=1) return $instance->showError('Invalid number of arguments');
        $instance->runList();

      break; case 'use':
        if(count($arg)!=2) return $instance->showError('Invalid number of arguments');
        $instance->runUse($arg[1]);

      break; case 'using':
        if(count($arg)!=1) return $instance->showError('Invalid number of arguments');
        $instance->runUsing();

      break; case 'show':
        if(count($arg)!=1) return $instance->showError('Invalid number of arguments');
        $instance->runShow();

      break;
    }

  }

  public function connect(){
    $servers = $this->config()->get('server');
    if(empty($servers)) return $this->showError("No servers configured.");
    $server = $this->instanceData()->get('server-using');
    if(empty($server)) return $this->showError("Must select a server to use:\n  data server use <server>");
    if(empty($servers[$server])) return $this->showError("Selected server not configured: '$server'");
    if(empty($servers[$server]['dsn'])) return $this->showError("Selected server 'dsn' string not configured:  data config set server.$server.dsn <dsn>");
    if(empty($servers[$server]['user'])) return $this->showError("Selected server 'user' not configured:  data config set server.$server.user <dsn>");
    if(empty($servers[$server]['pass'])) return $this->showError("Selected server 'pass' not configured:  data config set server.$server.pass <dsn>");
 
    echo "Keep Calp and Connect to Database\n"; die();

  }

  public function runList(){
    $this->connect();
  }

  public function runUse($server = NULL){
    $servers = $this->config()->get('server');
    if (empty($servers[$server])) return $this->showError("Server '$server' not configured.");
    $this->instanceData()->set('server-using', $server);
  }

  public function runUsing(){
    echo $this->using()."\n";
  }

  public function runShow(){
    echo json_encode($this->details(), JSON_PRETTY_PRINT)."\n";
  }

  public function runLeave(){
    $this->leave();
  }

  public function leave(){
    $this->instanceData()->remove('server-using');
  }

  public function using(){
    return $this->instanceData()->get('server-using');
  }
  
  public function details(){
    return $this->config()->get('server.'.$this->using());
  }

  public function showError($error){
    echo "ERROR: ".$error."\n";
    return 1;
  }

  
}
