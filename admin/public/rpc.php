<?
  require("../core/xmlrpc_bootstrap.php");
  require 'Zend/XmlRpc/Server.php';
  
  
  class Utilities {
    
    /**
     * Calcola l'MD5 di una stringa
     *
     * @param string $value Il valore
     * @return string
     */    
    public function md5Value($value)
    {
        return md5($value)." Io sono PHP ! ";
    }
  }
  
  $server = new Zend_XmlRpc_Server();
  $server->setClass('Utilities','utilities');
  echo $server->handle();
  //echo "CIAO";
