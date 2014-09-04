<?php
  require("../core/bootstrap.php");
  require("../config/loader_config.php");
  
  header("Content-type: text/javascript");
  header("Vary: Accept-Encoding");  
  
  if (isset($_GET["ndx"])){
    if (!$app_config["PRODUCTION"]){
      echo file_get_contents($app_config["BASE_PATH"]."/app/".$LOADER_CONFIG["JAVASCRIPT_FILES"][(int)$_GET["ndx"]]);
    }else{
      echo "Permission denied";
    }
  }else{
    foreach($LOADER_CONFIG["JAVASCRIPT_FILES"] as $jsFile){
      echo file_get_contents($app_config["BASE_PATH"]."/app/".$jsFile);
      echo "\n\n";
    }
  }
  