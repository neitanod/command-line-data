<?php 
require_once("constants.php");

require_once(LIB_PATH.'/DataConfig.class.php');
require_once(LIB_PATH.'/DataServer.class.php');
require_once(LIB_PATH.'/DataDB.class.php');

class DataRunner {

  public static function run($arg, $options = array()){
    $program = array_shift($arg);

    switch($program){
      case 'config':
        static::config($arg, $options);
      case 'help':
      default:
        echo file_get_contents(DOC_PATH.'/data-help.txt');
      break;
    }
  }

  public static function config($arg, $options = array()){
    $command = empty($arg[0])?'help':strtolower($arg[0]);
    $instance = DataConfig::getInstance();

    if(!empty($options['working_file'])) $instance->working_file = $options['working_file'];

    switch($command){
      case 'help':
        echo file_get_contents(DOC_PATH.'/data-config-help.txt');

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
}
