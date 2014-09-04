<?php
  try{
    require("../core/bootstrap.php");
    
        
    class notifica {
      
      private $Username;
      
      public function MessengerHeader($data){
          $logger=Zend_Registry::get("logger");
          $logger->log("MessengerHeader: Username={$data->Username} Password={$data->Password} Account={$data->Account}",Zend_Log::DEBUG);
          $this->Username=$data->Username;
      }
        
      public function MessageEvent($event){
          $logger=Zend_Registry::get("logger");
          $db=Zend_Registry::get("db");
          $logger->log("MessageEvent: [{$this->Username}] id={$event->id} eventype={$event->eventtype} occurredat={$event->occurredat} ",Zend_Log::DEBUG);
          //TODO: registrare tutti gli eventi su una tabella aopposita
          $UID=$event->id;
          $occurred=str_replace("T"," ",$event->occurredat);
          $db->insert("delivery",array(
            "delivery_id"=>$event->id,
            "occurred"=>$event->occurredat
          ));
          $db->update("sms",array("delivery"=>1,"data_delivery"=>$occurred)," UID='$UID' ");
          
      }
      
    }
    
    $logger->log("Nuova notifica",Zend_Log::DEBUG);
    $server = new SoapServer("https://www.esendex.com/secure/messenger/soap/AccountEventHandler.asmx?wsdl");
    $server->setClass("notifica");
    $server->handle();
    
  }catch(Exception $ex){
    $msg=@file_get_contents("php://input");
    $logger->log("esendex: exception=".$ex->getMessage(),Zend_Log::CRIT);
    $logger->log("LOG:\n\n".$msg,Zend_Log::CRIT);
  }
