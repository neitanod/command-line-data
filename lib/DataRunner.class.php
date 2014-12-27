<?php 
require_once("constants.php");

require_once(LIB_PATH.'/DataConfig.class.php');
require_once(LIB_PATH.'/DataServer.class.php');
require_once(LIB_PATH.'/DataDB.class.php');

class DataRunner {

  public static function run($arg, $options = array()){
    $program = array_shift($arg);

    switch($program){
      case 'autocomplete':
        static::autocomplete($arg, $options);
      break; case 'config':
        static::config($arg, $options);
      break; case 'server':
        static::server($arg, $options);
      break; case 'db':
        static::db($arg, $options);
      break; case 'help': default:
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

  public static function server($arg, $options = array()){
    $instance = DataServer::getInstance();

    $instance->working_path = empty($options['working_path'])?WORKING_PATH:$options['working_path'];
    $base_path = empty($options['base_path'])?dirname(__FILE__):$options['base_path'];
    $command = empty($arg[0])?'help':strtolower($arg[0]);
    
    switch($command){
      case 'help':
        echo file_get_contents(DOC_PATH.'/data-server-help.txt');

      break; case 'use':
        if(count($arg)!=2) return $instance->showError('Invalid number of arguments');
        $instance->runUse($arg[1]);

      break; case 'using':
        if(count($arg)!=1) return $instance->showError('Invalid number of arguments');
        $instance->runUsing();

      break; case 'leave':
        if(count($arg)!=1) return $instance->showError('Invalid number of arguments');
        $instance->runLeave();

      break; case 'list':
        if(count($arg)!=1) return $instance->showError('Invalid number of arguments');
        $instance->runList();

      break; case 'show':
        if(count($arg)!=1) return $instance->showError('Invalid number of arguments');
        $instance->runShow();

      break;
    }

  }

  public static function db($arg, $options = array()){
    $instance = DataDB::getInstance();

    $instance->working_path = empty($options['working_path'])?WORKING_PATH:$options['working_path'];
    $command = empty($arg[0])?'help':strtolower($arg[0]);
    
    switch($command){
      case 'list':
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

      break; case 'leave':
        if(count($arg)!=1) return $instance->showError('Invalid number of arguments');
        $instance->runLeave();

      break; case 'help': default:
        echo file_get_contents(DOC_PATH.'/data-db-help.txt');
      
      break; 
    }

  }

  public static function autocomplete($arg, $options = array()){
    $command = strtolower(array_shift($arg));
    while($command==='data' || $command==='autocomplete'){
      $command = strtolower(array_shift($arg));
    }
    $subcommand = array_shift($arg);
    $instance = DataConfig::getInstance();
    file_put_contents('out.txt', "COMMAND: ".$command . "\n". "SUBCOMMAND: ".$subcommand . "\n"."[".implode(',',$arg)."]\n");
    switch($command){

      case 'config':
        switch($subcommand){
          case 'remove':
          case 'get':
            $instance = DataConfig::getInstance();
            echo $instance->entries();
          break; case '': default:
            echo 'help show set get remove add';
          break; 
        }

      break; case 'server':
        switch($subcommand){
          case 'use':
            $instance = DataServer::getInstance();
            $instance->working_path = empty($options['working_path'])?WORKING_PATH:$options['working_path'];
            $instance->runList();
          break; case '': default:
            echo 'help list use using show leave';
        }

      break; case 'db':
        switch($subcommand){
          case 'use':
            $instance = DataDB::getInstance();
            $instance->working_path = empty($options['working_path'])?WORKING_PATH:$options['working_path'];
            $instance->runList();
          break; case '': default:
            echo 'help list use using show leave';
        }

      break; case '': default:
        echo 'config db server help';
      break;
    }

  } 
}
