<?php
  require(dirname(__FILE__)."/../config/config.php");
  require 'function.php'; 
  require 'bootstrap_base.php';
  require 'bootstrap_zend.php';
  require 'bootstrap_log.php';
  require 'bootstrap_doctrine.php';
  require 'bootstrap_db_aaac.php';
  require 'bootstrap_report.php';
  
  
  require $app_config["BASE_PATH"]."/classes/MultiTenant.php";
  require $app_config["BASE_PATH"]."/classes/Constants.php";
  require $app_config["BASE_PATH"]."/classes/SMSGateway.php";
  require "GenPassword.class.php";
  //require "FWDateTime.class.php";
   
  /* 
   * NOTA: Contenuto del Zend_Registry:
   * 
   * "app_config"            -> l'array di configurazione in config.php
   * "db"                    -> database Zend_Db
   * "doctrine_connection"   -> la connessione Doctrine
   * "doctrine_config"       -> la configurazioen di Doctrine
   * "logger"                -> il Zend_Logger
   * "auth"                  -> l'oggetto Zend_Auth
   * "db_profiler"           -> l'eventuale oggetto Zend_Profiler (solo se attivo il profiling dei db)
   * "doc_profiler"          -> l'eventuale oggetto Doctrne_Profiler (solo se attivo il profiling dei db)
   * "report_config"         -> la configurazione delle directory per i reports
   */
