<?
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
    require 'ZendExtensions/LoggerProfiler.php';
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