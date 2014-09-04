<?
class A{
  
  public static function orgid(){
    $a=Zend_Registry::get('aaac')->getAuth();
    $storage=$a->getStorage()->read();
    return (isset($storage->orgid)) ? $storage->orgid:null;
  }
  
  public static function setOrgid($orgid){
  	$a=Zend_Registry::get('aaac')->getAuth();
  	$arr=$a->getStorage()->read();
  	$arr->orgid=$orgid;
    $storage=$a->getStorage()->write($arr);
  }
  
}
