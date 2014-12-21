<?php 
define("WORKING_PATH", getcwd());
define("BASE_PATH", dirname(__FILE__));
$command = isset($argv[1]) ? $argv[1] : 'help';

$arguments = $argv;
array_shift($arguments);
array_shift($arguments);

switch($command){
  case 'config':
    require_once(BASE_PATH.'/DataConfig.class.php');
    DataConfig::run($arguments);

  break; case 'server':
    require_once(BASE_PATH.'/DataServer.class.php');
    DataServer::run($arguments);

  break; case 'db':
    require_once(BASE_PATH.'/DataDB.class.php');
    DataDB::run($arguments);

  break; case 'help':
    echo file_get_contents(BASE_PATH.'/data-help.txt');
  
  break; default:
    if(isset($command)) { echo "data: '".$command."' is not a data command. "; }
    echo "See: 'data help'.\n";
}
