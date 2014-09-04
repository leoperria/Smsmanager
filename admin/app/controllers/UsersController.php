<?php

/**
 * Omicronmedia(C) 2009
 * Questo controller è accessibile in parte all'esercente, in parte al data-entry e totalmente al developer
 * naturalmente dotati di permesso access per l'area users.
 * 
 */

class UsersController extends Ext_Controller_Action {

  
  public function preDispatch(){
    try{$this->aaac->authCheck("users.access"); }catch(Exception $e){echo $e->getMessage();die();} 
  }
  
  /**
   * Elenca tutti gli utenti data-entry
   *
   */
  public function listAction(){
    try{
  	  $this->aaac->authCheck("users.list");
    }catch(Exception $e){
     echo $e->getMessage();
     return;
    }
    $query=Doctrine_Query::create()
       ->select("uo.orgid, u.ID, u.nome,u.cognome,u.user,u.active,u.data_iscrizione,r.role")
       ->from("Users u")
       ->leftJoin("u.Role r")
       ->leftJoin("u.UsersOrganizations uo")
       ->addWhere("uo.orgid=".A::orgid())
       ->addWhere("r.developer=".Constants::UTENTE_NON_PRIVILEGIATO)
       ->addWhere("r.superuser=".Constants::UTENTE_NON_PRIVILEGIATO)
       ->addWhere("r.internal_resource=".Constants::UTENTE_NON_PRIVILEGIATO);
    $params=array();
    list($users,$total)=DBUtils::pageQuery($query,$params,$this->getLimit(),$this->getOffset());
    $this->emitTableResult($users,$total);
  }
  
  
  /**
   * Ottieni i dati di un certo utente data-entry
   *
   */
  public function getAction(){
    
  	$user=Doctrine_Query::create()
		  ->select("u.ID, u.nome,u.cognome,u.user,u.active,u.data_iscrizione,u.ID_role")
		  ->from("Users u")
		  ->leftJoin("u.UsersOrganizations uo")
		  ->addWhere("u.ID=?",$this->getRequest()->getParam('id'))
		  ->addWhere("uo.orgid=?",A::orgid())
		  ->addWhere("u.ID_role!=?",Constants::AMMINISTRATORE)
		  ->addWhere("u.ID_role!=?",Constants::ESERCENTE)
		  ->addWhere("u.ID_role!=?",Constants::DATAENTRY_INTERNO)
		  ->fetchOne(null,Doctrine::HYDRATE_ARRAY);
    $this->emitLoadData($user);
  }
  
  
  /**
   * Aggiorna i dati di un certo utente 
   *
   */
  public function saveAction(){
    try{
  	  $this->aaac->authCheck("users.edit");
    }catch (Exception $e){
     $this->errors->addError($e->getMessage());
     $this->emitSaveData();
     return;
    }
    $req=$this->getRequest();
    
    // Il ruolo può essere solo quello del data-entry
    if ($req->getParam("ID_role")!=Constants::DATAENTRY){
     $this->errors->addError("PERMESSO NEGATO.");
     $this->errors->setCloseAfterErrors(true); //Chiede la chiusura della finestra dopo la visualizzazione dell'errore.
     $this->emitSaveData();
    }
    if ($req->getParam('id') === 'new') {
      $record = Doctrine::getTable('Users')->create();
      $usersOrg = Doctrine::getTable('UsersOrganizations')->create();
    } else {
      $record =	Doctrine_Query::create()
		    ->from("Users u")
		    ->leftJoin("u.UsersOrganization uo")
		    ->addWhere("u.ID=?",$this->getRequest()->getParam('id'))
		    ->addWhere("uo.orgid=?",A::orgid())
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
      "ID_role" =>Constants::DATAENTRY
    ));
    if(!$record->trySave()){
      $this->errors->addValidationErrors($record);      
    }
    //TO DO: se l'utente è nuovo devo conoscere la password
     if ($req->getParam('id') === 'new') {
       $usersOrg->merge(array(
       "orgid"=>A::orgid(),
       "ID_user"=>$record->ID,
       "active"=>Constants::UTENTE_ATTIVO
      ));
      $usersOrg->save();
       $this->emitSaveData(array("success"=>true,"message"=>"Password: ".$password));
     }else{
       $this->emitSaveData();
     }
  }
  
  
  
  /**
   * Cancella un certo utente
   */
  public function deleteAction(){
    try{$this->aaac->authCheck("users.edit");}catch (Exception $e){
     $this->errors->addError($e->getMessage());
     $this->emitSaveData();
     return;
    }
    $req=$this->getRequest();
    
    
    $user=Doctrine::getTable('Users')->find($req->getParam('id'));   

    // Gli utenti superuser o developer non possono essere eliminati 
    if ($user->Role->superuser || $user->Role->developer){
      $this->errors->addError("L'utente Amministratore non pu&ograve; essere cancellato.");
      $this->emitSaveData();
      return;
    }
    // TO DO L'UTENTE NON DEVE AVERE PENDENZE DI PAGAMENTO PER ESSERE ELIMINATO DAL DATABASE
    $q=Doctrine_Query::create()
       ->delete()
       ->from('Users')
       ->addWhere('ID = ?',$this->getRequest()->getParam('id'))
       ->execute();   
    $this->emitJson(array("success"=>true));
  }
  
  
  /**
   * Restituisce l'elenco dei ruoli
   * escluso quello administrator e developer
   */
  public function loadrolesAction(){
    
    $r=Doctrine_Query::create()
		 ->select("r.ID, r.role")
		 ->from("Roles r")
     ->addWhere("superuser=?",Constants::UTENTE_NON_PRIVILEGIATO)
     ->addWhere("developer=?",Constants::UTENTE_NON_PRIVILEGIATO)  
     ->addWhere("internal_resource=?",Constants::UTENTE_NON_PRIVILEGIATO);  
  	$roles=$r->execute(null,Doctrine::HYDRATE_ARRAY);
    $this->emitTableResult($roles);
  }

  /**
   * Permette di cambiare la password
   *
   */
  public function setpasswordAction(){
    try{$this->aaac->authCheck("users.edit_password");}catch(Exception $e){
     $this->errors->addError($e->getMessage());
     $this->emitSaveData();
     return;
    }
  	$req=$this->getRequest();
    $cu=$this->aaac->getCurrentUser();
    
    // Soltanto developer,superuser e l'utente stesso possono cambiare la password
    if ($cu->Role->developer || $cu->Role->superuser || $cu->ID==$req->getParam('id')){
      
      $password=$this->getRequest()->getParam('password');
      $record=Doctrine_Query::create()
        ->from('Users')
        ->addWhere('ID=?')
        ->fetchOne(array($this->getRequest()->getParam('id')));
              
      $record->password=md5($password);
      if(!$record->trySave()){
        $this->errors->addValidationErrors($record);
      }
      
    }else{
      $this->errors->addError("Operazione non consentita.");
    }
    $this->emitSaveData(); 
  }
  
  
  /**
   * Restituisce informazioni sull'utente corrente
   *
   */
  public function getinfoAction(){
    $cu=$this->aaac->getCurrentUser();
    if($cu->ID_role==Constants::DEVELOPER){
     $rag_soc=Constants::GRUPPO_DEVELOPER;
    }else {
       $organization=Doctrine_Query::create()
    	->from('Organizations')
    	->where('orgid=?',A::orgid())
    	->fetchOne();
      $rag_soc=$organization->rag_soc;
    }
    if($cu){
      $res=array(
         "ID"=>$cu->ID,
         "nome"=>$cu->nome,
         "cognome"=>$cu->cognome,
         "role"=>$cu->Role->role,
         "superuser"=>$cu->Role->superuser,
         "developer"=>$cu->Role->developer,
         "internal_resource"=>$cu->Role->internal_resource,
         "organization"=>$rag_soc
       );
      $this->emitLoadData($res);
    }
  }
}