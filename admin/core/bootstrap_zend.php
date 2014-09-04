<?
  //**********************  Zend Framework

  require $app_config["BASE_PATH"]."/classes/ClassLoader.php";
  ClassLoader::registerAutoload();
  
  require 'ZendExtensions/ExtControllerAction.php';
  require 'ZendExtensions/FileManager.php';
  require 'ZendExtensions/DBUtils.php';
  require 'ZendExtensions/ErrorStack.php';
  require 'ZendExtensions/ReportManager.php';
  
  $registry=Zend_Registry::getInstance();
  Zend_Registry::set("app_config",$app_config);  
  
  $pdoParams = array(
    PDO::ATTR_PERSISTENT => true,
    PDO::ATTR_EMULATE_PREPARES=>true
  );
  $db=Zend_Db::factory('Pdo_Mysql', array(
    'host'     => $app_config["MYSQL_HOST"],
    'username' => $app_config["MYSQL_USER"],
    'password' => $app_config["MYSQL_PASS"],
    'dbname'   => $app_config["MYSQL_DB"],
    'driver_options' => $pdoParams
  ));
  Zend_Registry::set("db",$db);
  