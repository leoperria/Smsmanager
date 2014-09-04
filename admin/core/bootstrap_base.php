<?
  
  error_reporting(E_ALL); 
  ini_set('display_startup_errors', true); 
  ini_set('display_errors', true); 
  date_default_timezone_set('Europe/Rome'); 
  
  //********************  Framework 2
  require "functions.php";
  require "Timer.class.php";

  $globalTimer=new Timer();
  $t1=new Timer();
  disable_gpc(); 
  