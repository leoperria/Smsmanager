<?php

/**
 * Omicronmedia(C) 2009
 * AMMINISTRAZIONE: Il metodo preDispatch blocca tutto se l'utente non � developer.
 * 
 */

class OrganizationsController extends Ext_Controller_Action {

  
  public function preDispatch(){
  	try{
     if(!$this->aaac->isDeveloper()){
       throw new Exception("ACCESSO NEGATO");
      }
  	}catch (Exception $e){
  	  echo $e->getMessage();
  	  die();
  	}
    $this->db=Zend_Registry::get('db');
  }

  public function listAction(){
    $sel=$this->db->select()
      ->from(array("o"=>"organizations"))
      ->columns(array("balance"=>"IFNULL((SELECT SUM(qnt) FROM movimenti m WHERE m.orgid=o.orgid),0)"));

    
    list($organizations,$total)=ZendDBUtils::pageQuery($sel,$this->getLimit(),$this->getOffset());
    $this->emitTableResult($organizations,$total);
  }
  
  public function listcomboAction(){
    $o=Doctrine_Query::create()
    ->select('orgid,rag_soc')
    ->from('Organizations')
    ->orderBy('rag_soc')
    ->execute(null,Doctrine::HYDRATE_ARRAY);
    $this->emitTableResult($o);
  }
  
  public function listbydataentryAction(){
   $q=Doctrine_Query::create()
   ->select('o.orgid,o.rag_soc')
   ->from('Organizations o')
   ->leftJoin('o.UsersOrganizations u')
   ->where('u.ID_user=?',$this->getRequest()->getParam('id'))
   ->addWhere('u.active=?',Constants::UTENTE_ATTIVO);
   list($res,$total)=DBUtils::pageQuery($q,array(),$this->getLimit(),$this->getOffset());
   $this->emitTableResult($res,$total);
  }
  
  public function listautdataentryAction(){
   $q=Doctrine_Query::create()
   ->select('o.orgid,o.rag_soc')
   ->from('Organizations o')
   ->where('o.orgid NOT IN (SELECT p.orgid FROM UsersOrganizations p WHERE p.ID_user=? AND active=1)',$this->getRequest()->getParam('id'));
   list($res,$total)=DBUtils::pageQuery($q,array(),$this->getLimit(),$this->getOffset());
   $this->emitTableResult($res,$total);
  }

  public function getAction(){
  	$organization=Doctrine_Query::create()
  		 ->from("Organizations o")
  		 ->addWhere("o.orgid=?",$this->getRequest()->getParam('id'))
  		 ->fetchOne(null,Doctrine::HYDRATE_ARRAY);
    $this->emitLoadData($organization);
  }

  public function saveAction(){
    
    $req=$this->getRequest();
    if ($req->getParam('id') === 'new') {
      $record = Doctrine::getTable('Organizations')->create();
    } else {
      $record =	Doctrine_Query::create()
    		    ->from("Organizations o")
    		    ->addWhere("o.orgid=?",$this->getRequest()->getParam('id'))
    		    ->fetchOne();
    }
    $record->merge(array(
      "rag_soc"=> $req->getParam("rag_soc"),
      "data_iscrizione" => $req->getParam("data_iscrizione"),
      "sms_sender" => $req->getParam("sms_sender"),
      "p_iva" => $req->getParam("p_iva"),
      "codfis" => $req->getParam("codfis"),
      "tel" =>$req->getParam("tel")
    ));
   
    if(!$record->trySave()){
      $this->errors->addValidationErrors($record);      
    }
    $this->emitSaveData();
  }

  public function deleteAction(){
   $req=$this->getRequest();
   $this->emitJson(array("success"=>true));
  }
  
  public function enabledataentryAction(){
   $req=$this->getRequest();
   $q=Doctrine_Query::create()->from('UsersOrganizations')->where('orgid=?',$req->getParam('orgid'))->addWhere('ID_user=?',$req->getParam('ID_user'))->fetchOne();
   if($q==false){
    $rel=Doctrine::getTable('UsersOrganizations')->create();
    $rel->merge(array(
      "orgid"=>(int)$req->getParam('orgid'),
      "ID_user"=>(int)$req->getParam('ID_user')
    ));
   }else{
    $rel=$q;
   }
   $rel->active=Constants::UTENTE_ATTIVO;
   $rel->save();
   $this->emitSaveData();
  }
  
  public function disabledataentryAction(){
   $req=$this->getRequest();
   $q=Doctrine_Query::create()->from('UsersOrganizations')->where('orgid=?',$req->getParam('orgid'))->addWhere('ID_user=?',$req->getParam('ID_user'))->fetchOne();
   if($q!=false){
     $q->active=Constants::UTENTE_NON_ATTIVO;
     $q->save();
   }
   $this->emitSaveData();
  }
  
  public function listresourcesAction(){
   $res=Doctrine_Query::create()
    ->select('u.ID,u.nome,u.cognome,u.user')
    ->from('Users u')
    ->leftJoin('u.UsersOrganizations o')
    ->where('o.orgid=?',$this->getRequest()->getParam('orgid'))
    ->addWhere('o.active=?',Constants::UTENTE_ATTIVO)
    ->addWhere('u.ID_role=?',Constants::DATAENTRY_INTERNO)
    ->execute(null,Doctrine::HYDRATE_ARRAY);
    $this->emitTableResult($res,0);
  }
  
  public function getresourcesAction(){
   $res=Doctrine_Query::create()
   ->select('u.ID,u.nome,u.cognome,u.user')
   ->from('Users u')
   ->addWhere('u.ID_role=?',Constants::DATAENTRY_INTERNO)
   ->addWhere("u.ID NOT IN (SELECT o.ID_user FROM UsersOrganizations o WHERE o.orgid={$this->getRequest()->getParam('orgid')} AND o.active=1)")
   ->execute(null,Doctrine::HYDRATE_ARRAY);
    $this->emitTableResult($res,0);
  }
}