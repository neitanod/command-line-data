<?php
require_once("constants.php");

require_once(LIB_PATH.'/DataConfig.class.php');
require_once(LIB_PATH.'/traitMultipleton.trait.php');
require_once(LIB_PATH.'/traitDataInstance.trait.php');

require_once(LIB_PATH.'/vendor/DFDB.class.php');

class DataDB {
  use traitMultipleton;
  use traitDataInstance;

  public function db(){
    if(empty($this->db)) {
      $servers = $this->config()->get('server');
      if(empty($servers)) return $this->showError("No servers configured.");
      $server = $this->instanceData()->get('server-using');
      if(empty($server)) return $this->showError("Must select a server to use:\n  data server use <server>");
      if(empty($servers[$server])) return $this->showError("Selected server not configured: '$server'");
      if(empty($servers[$server]['dsn'])) return $this->showError("Selected server 'dsn' string not configured:  data config set server.$server.dsn <dsn>");
      if(empty($servers[$server]['user'])) return $this->showError("Selected server 'user' not configured:  data config set server.$server.user <dsn>");
      if(empty($servers[$server]['pass'])) return $this->showError("Selected server 'pass' not configured:  data config set server.$server.pass <dsn>");

      $this->db = DFDB::getInstance(
        $servers[$server]['dsn'],
        $servers[$server]['user'],
        $servers[$server]['pass']
      );
    }
    return $this->db;
  }

  public function runList(){
    echo implode($this->getDBList(),"\n")."\n";
  }

  public function getDBList(){
    return $this->db()->executeOrException("SELECT schema_name FROM information_schema.SCHEMATA")->fetchAll(PDO::FETCH_COLUMN, 0);
  }

  public function runUse($db = NULL){
    $dbs = $this->getDBList();
    if (!in_array($db, $dbs)) return $this->showError("Database '$db' not found.");
    $this->instanceData()->set('db-using', $db);
  }

  public function runUsing(){
    echo $this->using()."\n";
  }

  public function runShow(){
    $using = $this->using();
    if(empty($using))  return $this->showError("No database selected.  Use 'data db use <name>'");
    echo json_encode($this->details(), JSON_PRETTY_PRINT)."\n";
  }

  public function runLeave(){
    $this->leave();
  }

  public function leave(){
    $this->instanceData()->remove('db-using');
  }

  public function using(){
    return $this->instanceData()->get('db-using');
  }

  public function details($db = NULL){
    if(is_null($db)) $db = $this->using();
    $info = $this->db()->executeOrException("SELECT * FROM information_schema.SCHEMATA WHERE schema_name = ?", array($this->using()))->fetchAll(PDO::FETCH_ASSOC);
    $info[0]['TABLES'] = $this->db()->executeOrException('SELECT CONCAT(TABLE_NAME, " (", TABLE_ROWS ," rows)") as COL FROM information_schema.TABLES WHERE table_schema = ?', array($this->using()))->fetchAll(PDO::FETCH_COLUMN, 0);

    return $info[0];
  }

  public function showError($error){
    echo "ERROR: ".$error."\n";
    return 1;
  }

}
