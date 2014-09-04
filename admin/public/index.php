<?php
 
  require("../core/bootstrap.php");
 
  $front = Zend_Controller_Front::getInstance()
           -> setParam('app_config', $app_config)
           -> setControllerDirectory($app_config["BASE_PATH"].'/app/controllers')
           -> dispatch();
  if ($app_config["DB_PROFILER"]){
    Zend_Registry::get("db_profiler")->summarize();
    Zend_Registry::get("doctrine_profiler")->summarize();
  }