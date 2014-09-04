<?php
  require(dirname(__FILE__)."/../config/config.php");
  

  //**********************  Inizializzazioni
  /*error_reporting(E_ALL); 
  ini_set('display_startup_errors', true); 
  ini_set('display_errors', true);*/
  

  
  //**********************  Zend Framework & Extensions
  require "Zend/Loader/Autoloader.php";
  $autoloader = Zend_Loader_Autoloader::getInstance();
  require 'ExtControllerAction.php';
  require 'DBUtils.php';
  require 'FileManager.php';
  require 'ErrorStack.php';
  require 'ReportManager.php';
  require 'functions.php'; 
  require 'GenPassword.php';
  require 'MultiTenant.php';
  
  date_default_timezone_set('Europe/Rome');
 // disable_gpc(); 
  
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
    
    
  
  //**********************  Logging
  try {
    try {
      $logger = new Zend_Log();
    } catch (Exception $x) {
      echo $x->getMessage();
    }
    if(isset($logger)) {
      Zend_Registry::set('logger',$logger);
    }
    if ( !isset($app_config["LOG_DESTINATIONS"]) || 
         !is_array($app_config["LOG_DESTINATIONS"]) || 
         count($app_config["LOG_DESTINATIONS"])==0    
    ) {
      $logger->addWriter(new Zend_Log_Writer_Null());
    }else{
      if (in_array("firebug",$app_config["LOG_DESTINATIONS"])){
        $logger->addWriter(new Zend_Log_Writer_Firebug());
      }
      if (in_array("file",$app_config["LOG_DESTINATIONS"])){
        $logger->addWriter(new Zend_Log_Writer_Stream($app_config["BASE_PATH"]."/log/app.log"));
      }
      if (in_array("stream",$app_config["LOG_DESTINATIONS"])){
        $logger->addWriter(new Zend_Log_Writer_Stream('php://output'));
      }
   }
  } catch (Exception $x) {
    echo $x->getMessage();
  }
    
  if ($app_config["DB_PROFILER"]){
    require 'LoggerProfiler.php';
    $queryLogger = new Zend_Log();
    $queryLogger->addWriter(new Zend_Log_Writer_Stream($app_config["BASE_PATH"]."/log/sql.log"));
    if (isset($_SERVER['REDIRECT_URL'])){
      $queryLogger->log("****************************************** ".$_SERVER['REDIRECT_URL'],Zend_Log::INFO);
    }
    $profiler = new LoggerProfiler($queryLogger,Zend_Log::INFO);
    $profiler->setEnabled(true);
    $db->setProfiler($profiler);
    Zend_Registry::set('db_profiler',$profiler);
  }
  
  

   
  /* 
   * NOTA: Contenuto del Zend_Registry:
   * 
   * "app_config"            -> l'array di configurazione in config.php
   * "db"                    -> database Zend_Db
   * "logger"                -> il Zend_Logger
   * "db_profiler"           -> l'eventuale oggetto Zend_Profiler (solo se attivo il profiling dei db)
   * "report_config"         -> la configurazione delle directory per i reports
   */
