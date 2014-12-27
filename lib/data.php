<?php 
require_once("constants.php");

$arguments = $argv;
array_shift($arguments);

require_once(LIB_PATH.'/DataRunner.class.php');
DataRunner::run($arguments);

