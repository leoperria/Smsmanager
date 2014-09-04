<?php
/**
 * Omicronmedia(C) 2009
 *
 */
class PermissionsController extends Ext_Controller_Action {
  
  public function listAction(){
    try{
  	  $this->aaac->authCheck("permissions.list");
    } catch (Exception $e){
     $this->errors->addError($e->getMessage());
  	 $this->emitSaveData();
  	 return;
    }
    $query=Doctrine_Query::create()
      ->from('Areas a')
      ->leftJoin("a.Permissions p")
      ->orderBy("a.ID")
      ->addOrderBy("p.sortid");
    list($permissions,$total)=DBUtils::pageQuery($query,array(),$this->getLimit(),$this->getOffset());
    $this->emitTableResult($permissions,$total);
  }
  
  public function loadareasAction(){
    $areas=Doctrine_Query::create()
  		  ->select("a.ID, a.UIname")->from("Areas a")
  		  ->execute(null,Doctrine::HYDRATE_ARRAY);
    $this->emitTableResult($areas);
  }
  
  public function deleteAction(){
  	try{
      $this->aaac->authCheck("permissions.delete");
  	} catch (Exception $e){
  	  $this->errors->addError($e->getMessage());
  	  $this->emitSaveData();
  	  return;
  	}
    /*
     * Cancella a cascata anche tutte le roles_permission collegate
     */
    $req=$this->getRequest();
    $pTable = Doctrine::getTable('Permissions');
    $p=$pTable->find($req->getParam('id'));
    $p->delete();
    $this->emitSaveData();
  }
  
  public function getAction(){
  	try{
      $this->aaac->authCheck("permissions.load");
  	}catch (Exception $e){
  	  $this->errors->addError($e->getMessage());
  	  $this->emitSaveData();
  	  return;
  	}
    $p=Doctrine::getTable('Permissions')->find($this->getRequest()->getParam('id'));
    $this->emitLoadData($p->toArray());
  }
  
  public function saveAction(){
  	try{
      $this->aaac->authCheck("permissions.save");
  	} catch (Exception $e){
  	 $this->errors->addError($e->getMessage());
  	 $this->emitSaveData();
  	 return;
  	}
    $req=$this->getRequest();
    
    $record = Doctrine::getTable('Permissions');
    if ($req->getParam('id') === 'new') {
      $p=$record->create();
    } else {
      $p=$record->find($req->getParam('id'));
    }
    $p->merge(array(
      "ID_area"=>$req->getParam("ID_area"),
      "name"=> $req->getParam("name"),
      "UIname" => $req->getParam("UIname"),
      "type" => $req->getParam("type"),
      "default_value" => $req->getParam("default_value"),
      "sortid" => $req->getParam("sortid")
    ));
    
    if(!$p->trySave()){
      $this->errors->addValidationErrors($p);      
    }
    $this->emitSaveData();
  }
  
  public function jspermissionsAction(){
    $query=Doctrine_Query::create()
      ->select('a.name as area, p.name as permesso')
      ->from('Areas a')
      ->leftJoin('a.Permissions p');
    if(!$this->aaac->isSuperUser() && !$this->aaac->isDeveloper()){
      $query->leftJoin('p.RolesPermissions r')->where('r.ID_role=?',$this->aaac->getCurrentUser()->ID_role);
    }
    $record=$query->orderBy('a.name')->addOrderBy('p.sortid')->execute(null,Doctrine::HYDRATE_SCALAR);
    $res=array();
    foreach($record as $r){
      $res[]=$r['a_area'].".".$r['p_permesso'];
    }
    if($this->aaac->isDeveloper()){
      $res[]="superuser.developer";
    }
    if($this->aaac->isSuperUser()){
      $res[]="superuser.superuser";
    }
    $this->emitJson($res);
  }
  
  public function getareaAction(){
  
  }
  
  public function saveareaAction(){
  	try{
      $this->aaac->authCheck("permissions.save");
  	}catch (Exception $e){
  	 $this->errors->addError($e->getMessage());
  	 $this->emitSaveData();
  	 return;
  	}
    $req=$this->getRequest();
    
    $record = Doctrine::getTable('Areas');
    if ($req->getParam('id') === 'new') {
      $a=$record->create();
    } else {
      $a=$record->find($req->getParam('id'));
    }
    $a->merge(array(
      "name"=> $req->getParam("name"),
      "UIname" => $req->getParam("UIname")
    ));
    
    if(!$a->trySave()){
      $this->errors->addValidationErrors($a);      
    }
    $this->emitSaveData();
  }
}
