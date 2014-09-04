<?php
  require(dirname(__FILE__)."/../core/xmlrpc_bootstrap.php");

  
  class SMS_data{
    
    public function __construct($originator,$recipient,$body,$type,$validityperiod){
      $this->originator=$originator;
      $this->recipient=$recipient;
      $this->body=$body;
      $this->type=$type;
      $this->validityperiod=$validityperiod;
      
    }
    
    public $originator;
    public $recipient;
    public $body;
    public $type;
    public $validityperiod;
  }
  
  $client = new SoapClient(
    "https://www.esendex.com/secure/messenger/soap/SendService.asmx?wsdl",
    array('classmap' => array('messagesubmission' => "SMS_data", "trace"=>true))
  );
  
  
  $client->__setSoapHeaders(
    new SOAPHeader("com.esendex.ems.soapinterface", 'MessengerHeader', array(
      "Username"=>"leonardo.perria@kinesistemi.com",
      "Password"=>"NDX7272",
      "Account"=>"EX0059643"
    ))
  );



  $result=(array)$client->SendMessageBatch(array(
    "messages"=>array(
       new SMS_data("LEO1","393284347808",utf8_encode("Prova microfono aeiou ιη°§*^?^!\"£%&/()=?^§"),"Text",72),
       new SMS_data("LEO2","393284347808",utf8_encode("Prova microfono aeiou ιη°§*^?^!\"£%&/()=?^§"),"Text",72)
    )
  ));
  
  var_dump($result);
  
  /*$result=(array)$client->SendMessageFull(array(
    "originator"=>"LEO1",
    "recipient"=>"393284347808",
    "body"=>"Ciao come stai io bene e tu ?",
    "type"=>"Text",
    "validityperiod"=>"72"
  ));
  
  
  var_dump($result);*/
  
