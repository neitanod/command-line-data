<?php
if(!defined("WORKING_PATH")) define("WORKING_PATH", getcwd());

require_once(dirname(__FILE__).'/DataConfig.class.php');
require_once(dirname(__FILE__).'/traitMultipleton.trait.php');
require_once(dirname(__FILE__).'/traitDataInstance.trait.php');

class DataServer {
  use traitMultipleton;
  use traitDataInstance;

  public function runList(){
    $servers = $this->config()->get('server');
    if(is_array($servers) && !empty($servers)) {
      foreach ($servers as $k => $server){
        echo $k."\n";
      }
    }
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
