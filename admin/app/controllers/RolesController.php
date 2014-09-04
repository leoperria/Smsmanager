<?php
/**
 * Omicronmedia(C) 2009
 * In questa applicazione sparisce il Role collegato ad una sola orgid, adesso i role sono uguali per tutti
 * Questo controller è accessibile solo all'utente developer pertanto è priva di permessi
*/

class RolesController extends Ext_Controller_Action {

  public function preDispatch(){
    try{
  	  if(!$this->aaac->isDeveloper()){throw new Exception("ACCESSO NEGATO"); }
    } catch (Exception $e){
      echo $e->getMessage();
      die();
    }
  }
  
  public function listAction(){
    $query=Doctrine_Query::create()->from("Roles r");
    list($roles,$total)=DBUtils::pageQuery($query,array(),$this->getLimit(),$this->getOffset());
    $this->emitTableResult($roles,$total);
  }
  
  public function getAction(){
  	$role=Doctrine_Query::create()
  		->from("Roles r")
  		->addWhere("r.ID=?",$this->getRequest()->getParam('id'))
  		->fetchOne(null,Doctrine::HYDRATE_ARRAY);
    $this->emitLoadData($role);
  }
  
  public function getpermissionsAction(){
    $p=Doctrine_Query::create()
      	->from("Areas a")
        ->leftJoin("a.Permissions p")
        ->leftJoin("p.RolesPermissions r WITH r.ID_role=?",$this->getRequest()->getParam("id"))
        ->orderBy("a.UIname")
        ->addOrderBy("p.sortid")
        ->execute(null,Doctrine::HYDRATE_SCALAR);
        $this->emitTableResult($p);
  }
  
  public function saveAction(){
   $r=$this->getRequest();
   $record=Doctrine::getTable('Roles')->create();
   if($r->getParam('id')=='new'){
     $ruolo=$record;
   }else{
     $ruolo=$record->find($r->getParam('id'));
   }
   // POSSIBILITA' di aggiungere altri ruoli con supeuser?
   $ruolo->merge(array(
     "role"=>$r->getParam('role'),
     "superuser"=>Constants::UTENTE_NON_PRIVILEGIATO
   ));
   if(!$ruolo->trySave()){
     $this->errors->addValidationErrors($ruolo); 
   }
   $this->emitSaveData();
  }
 
  public function deleteAction(){
    $req=$this->getRequest();
    $role=Doctrine::getTable('Roles')->find($req->getParam('id'));    
    if ($role->superuser || $role->developer){
      $this->errors->addError("Il ruolo Developer/Esercente non pu&ograve; essere cancellato.");
      $this->emitSaveData();
      return;
    }
    $role->delete();
    $this->emitJson(array("success"=>true));
  }
  
  public function modpermessoAction(){
    $r=$this->getRequest();
    /* Esiste già ?*/
    $record=Doctrine_Query::create()
    ->from('RolesPermissions')
    ->where('ID_role=?',$r->getParam('idRole'))
    ->addWhere('ID_permission=?',$r->getParam('idPermission'))
    ->fetchOne();
    if(!$record){
      /*Nuovo*/
      $record=Doctrine::getTable('RolesPermissions')->create();
      $record->ID_role=$r->getParam('idRole');
      $record->ID_permission=$r->getParam('idPermission');
    }
    if((int)$r->getParam('type')!=0){
      $record->value=$r->getParam('value');
    }
    if($r->getParam('allow')=="true"){
      $record->save();
    }else{
      $record->delete();
    }    
    $this->emitSaveData();
  }
    
}