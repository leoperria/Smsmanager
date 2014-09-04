<?php
  try{
    require("../core/bootstrap.php");
     
    $logger->log("MIAO",Zend_Log::DEBUG);
    
  }catch(Exception $ex){
    $msg=@file_get_contents("php://input");
    $logger->log("esendex: exception=".$ex->getMessage(),Zend_Log::CRIT);
    $logger->log("LOG:\n\n".$msg,Zend_Log::CRIT);
  }
