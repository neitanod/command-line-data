<?php 
trait traitDataInstance {

  public function config(){
    if(!isset($this->config)) $this->config = DataConfig::getInstance();
    return $this->config;
  }
  
  public function instanceData(){
    if(!isset($this->instanceData)) $this->instanceData = DataConfig::getInstance('instance');
    $this->instanceData->currentConfigFilePath(WORKING_PATH.'/.data.instance');
    return $this->instanceData;
  }


}
