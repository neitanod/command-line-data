<?php
/**
 * @author:  Sebastián Grignoli <grignoli@gmail.com>
 */

class DFDB {

  private static $connections = array();
  private $pdo = array();
  private $setUtf8 = false;

  protected function __construct($dsn, $user, $pass) {
    $this->pdo['data'] = array('dsn' => $dsn, 'user' => $user, 'pass' => $pass);
    return $this;
  }

  public static function getInstance($dsn, $user, $pass) {
    $id = md5($user . ':' . $pass . '@' . $dsn);

    if (!empty(self::$connections[$id])) {
      return self::$connections[$id];
    } else {
      $classname = __CLASS__;
      self::$connections[$id] = new $classname($dsn, $user, $pass);
      return self::$connections[$id];
    }
  }

  public static function getNamedInstance($name) {
    $conn = DFConfig::get('conexiones', $name);
    return DFDB::getInstance($conn['dsn'], $conn['user'], $conn['pass']);
  }

  public function __call($method_name, $args) {
    // Every call to an unknown method gets forwarded to the PDO object.
    // If PDO object does not exist it gets instantiated now.

    if (!method_exists($this->pdo(), $method_name))
      throw new Exception("No existe el metodo 'DFDB::$method_name'");

    return call_user_func_array(array($this->pdo['pdo'], $method_name), $args);
  }

  public function setUtf8($utf8 = true)
  {
    $this->setUtf8 = $utf8;
  }

  protected function createPDO() 
  {
    $this->pdo['pdo'] = new PDO($this->pdo['data']['dsn'], $this->pdo['data']['user'], $this->pdo['data']['pass'], 
                                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION) );
    
    if($this->setUtf8){
      $this->queryOrException("SET NAMES 'utf8'");
    }

  }

  public function throwError() {
    if ($this->errorCode())
      throw new Exception(implode(" - ", $this->errorInfo()));
  }

  public function queryOrException($q){
    $args = func_get_args();
    $result = call_user_func_array(array($this->pdo(), 'query'), $args);

    if((int)$this->pdo()->errorCode())
      throw new Exception(implode(" - ",$this->pdo()->errorInfo())." \nQuery:\n".$q);

    return $result;
  }

  public function lastInsertId()
  {
    return $this->pdo()->lastInsertId();
  }

  public function pdo(){
    if (empty($this->pdo['pdo']))
      $this->createPDO();
    
    return $this->pdo['pdo'];
  }

  public function executeOrException($query, $params = array(), $driver_options = array()){
    
    $statement = $this->prepare($query, $driver_options);

    $statement->execute($params);
  
    $err = $statement->errorInfo();

    if(is_array($err) && isset($err[1]) && $err[1]) {
      $e = new Exception(implode(" - ", $err) . "  \nQUERY: " . $query . "  \nParameters: " . var_export($params, 1));
      $e->errorInfo = $err;
      throw $e;
      //throw new Exception(implode(" - ", $err) . "  \nQUERY: " . $query . "  \nParameters: " . var_export($params, 1));
    }

    return $statement; 

  }

}
