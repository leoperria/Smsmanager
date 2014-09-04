<?php

/**
 * Omicronmedia(C) 2009
 * AMMINISTRAZIONE:
 * Questo controller è accessibile solo agli utenti Developer
*/

class UsersadminController extends Ext_Controller_Action {

  
  public function preDispatch(){
    try {
      if(!$this->aaac->isDeveloper()){
        throw new Exception('ACCESSO NEGATO'); 
      }
    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }
  }
  
  /**
   * Elenca tutti gli utenti dell'organizzazione selezionata
   *
   */
  public function listAction(){
    $r=$this->getRequest();
    $query=Doctrine_Query::create()
      ->select("uo.orgid, u.ID, u.nome,u.cognome,u.user,u.active,u.data_iscrizione,r.role")
      ->from("Users u")
      ->leftJoin("u.Role r")
      ->leftJoin("u.UsersOrganizations uo")
      ->where("uo.orgid=".$r->getParam('orgid'))
      ->addWhere("uo.active=".Constants::UTENTE_ATTIVO);
    list($users,$total)=DBUtils::pageQuery($query,array(),$this->getLimit(),$this->getOffset());
    $this->emitTableResult($users,$total);
  }
  
  public function listdataentryAction(){
    $res=Doctrine_Query::create()
    ->from('Users u')
    ->where('ID_role=?',Constants::DATAENTRY_INTERNO);
    list($de,$t)=DBUtils::pageQuery($res,array(),$this->getLimit(),$this->getOffset());
    $this->emitTableResult($de,$t);
  }
  
  
  /**
   * Ottieni i dati di un certo utente
   *
   */
  public function getAction(){
  	$user=Doctrine_Query::create()
		  ->select("u.ID, u.nome,u.cognome,u.user,u.active,u.data_iscrizione,u.ID_role")
		  ->from("Users u")
		  ->addWhere("u.ID=?",$this->getRequest()->getParam('id'))
		  ->fetchOne(null,Doctrine::HYDRATE_ARRAY);
    $this->emitLoadData($user);
  }
  
  
  /**
   * Aggiorna i dati di un certo utente 
   *
   */
  public function saveAction(){
    
    $req=$this->getRequest();
    
    if ($req->getParam('id') === 'new') {
      $record = Doctrine::getTable('Users')->create();
      $usersOrg = Doctrine::getTable('UsersOrganizations')->create();
    } else {
      $record =	Doctrine_Query::create()
		    ->from("Users u")
		    ->addWhere("u.ID=?",$this->getRequest()->getParam('id'))
		    ->fetchOne();
    }
       
    //Aggiorna 
    $password=szGenPass::generatePassword(Constants::PASSWORD_LENGHT); 
    if ($req->getParam('id') === 'new') {
      $record->password=md5($password);
      $record->data_iscrizione=new Doctrine_Expression('NOW()');
    }else{
      $record->data_iscrizione=$req->getParam("data_iscrizione");
    }
    $record->merge(array(
      "nome"=> $req->getParam("nome"),
      "cognome" => $req->getParam("cognome"),
      "user" => $req->getParam("user"),
      "active" => $req->getParam("active"),
      "ID_role" =>$req->getParam("ID_role")
    ));
    
    if(!$record->trySave()){
      $this->errors->addValidationErrors($record);      
    }
    //TO DO: se l'utente è nuovo devo conoscere la password
     if ($req->getParam('id') === 'new') {
      if($req->getParam('orgid')!=''){
     	$usersOrg->merge(array(
          "orgid"=>$req->getParam('orgid'),
          "ID_user"=>$record->ID,
          "active"=>Constants::UTENTE_ATTIVO
        ));
        $usersOrg->save();
      }
      $this->emitSaveData(array("success"=>true,"message"=>"Password: ".$password));
     }else{
       $this->emitSaveData();
     }
  }
  
  
  
  /**
   * Cancella un certo utente
   */
  public function deleteAction(){
    
    $req=$this->getRequest();
    $user=Doctrine::getTable('Users')->find($req->getParam('id'));   

    // Gli utenti developer non possono essere eliminati 
    if ($user->Role->developer){
      $this->errors->addError("L'utente Developer non pu&ograve; essere cancellato.");
      $this->emitSaveData();
      return;
    }
    
    $q=Doctrine_Query::create()
       ->delete()
       ->from('Users')
       ->addWhere('ID = ?',$this->getRequest()->getParam('id'))
       ->execute();
       
    $this->emitJson(array("success"=>true));
  }
  /** restituisce tutti i ruoli**/
  public function loadrolesAction(){
    $roles=Doctrine_Query::create()
	   ->select("r.ID, r.role")
	   ->from("Roles r")
  	 ->execute(null,Doctrine::HYDRATE_ARRAY);
    $this->emitTableResult($roles);
  }
  /**restituisce solo il ruolo dataentry interno**/
  public function loadroledataentryAction(){
    $roles=Doctrine_Query::create()
	  ->select("r.ID, r.role")
	  ->from("Roles r")
	  ->where("r.ID=?",Constants::DATAENTRY_INTERNO)
  	 ->execute(null,Doctrine::HYDRATE_ARRAY);
    $this->emitTableResult($roles);
  }

  /**
   * Permette di cambiare la password
   *
   */
  public function setpasswordAction(){
    $req=$this->getRequest();
    $cu=$this->aaac->getCurrentUser();
    $password=$this->getRequest()->getParam('password');
    $record=Doctrine_Query::create()->from('Users')->addWhere('ID=?')->fetchOne(array($req->getParam('id')));        
    $record->password=md5($password);
    if(!$record->trySave()){
      $this->errors->addValidationErrors($record);
    }
    $this->emitSaveData(); 
  }
}