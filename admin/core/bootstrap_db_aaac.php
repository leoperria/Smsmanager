<?
  //********************** Authentication, Authorization, and Access Control
  require 'ZendExtensions/AAAC.class.php';
  $aaac=new AAAC(array(
    'SESSION_NAMESPACE'=>'smsmanager_1.0'
  ));
  Zend_Registry::set('aaac',$aaac);
  