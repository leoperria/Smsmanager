<?
  /*require("../core/bootstrap.php");
  require 'Zend/XmlRpc/Client.php';*/
  require("../config/config.php");
  require $app_config["BASE_PATH"].'/lib/xmlrpc-2.2.1/lib/xmlrpc.inc';

  /*$client = new Zend_XmlRpc_Client($app_config["BASE_URL"]."/rpc.php");
  $result = $client->call('utilities.md5Value', array("CIAO"));
  var_dump($result);*/

  
  $client = new xmlrpc_client($app_config["BASE_URL"]."/rpc.php");
  $client->return_type = 'phpvals';
  $message = new xmlrpcmsg("utilities.md5Value", array(new xmlrpcval("CIAO", "string")));
  $resp = $client->send($message);
  if ($resp->faultCode()) {
    echo 'KO. Error: '.$resp->faultString(); 
  }else{
    echo 'OK: got '.$resp->value();
  }

  