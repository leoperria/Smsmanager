<?php
class GruppiController extends Ext_Controller_Action{
  
  public function listAction(){
    $req=$this->getRequest();
    $orgid=($this->aaac->getCurrentUser()->ID_role!=Constants::AMMINISTRATORE)?A::orgid():$req->getParam('orgid');
    $query=Doctrine_Query::create()->from('Gruppi g')->where("orgid=?",$orgid);
    list($gruppi,$total)=DBUtils::pageQuery($query,array(),$this->getLimit(),$this->getOffset());
    $this->emitTableResult($gruppi,$total);
  }
  
  public function listcomboAction(){
    $req=$this->getRequest();
    $orgid=($this->aaac->getCurrentUser()->ID_role!=Constants::AMMINISTRATORE)? A::orgid() : $req->getParam('orgid');
    $query=Doctrine_Query::create()->from('Gruppi')->where("orgid=?",$orgid);
    $gruppi=$query->orderBy("descrizione")->execute(null,Doctrine::HYDRATE_ARRAY);
    $this->emitTableResult($gruppi);
  }
  
  public function saveAction(){
	 $req=$this->getRequest();
	 $orgid=($this->aaac->getCurrentUser()->ID_role!=Constants::AMMINISTRATORE)?A::orgid():$req->getParam('orgid');
	 if($req->getParam('id')=='new'){
	   $record=Doctrine::getTable('Gruppi')->create();
	 }else{
	   $query=Doctrine_Query::create()
	    ->from('Gruppi')
	    ->where('ID_gruppo=?',$req->getParam('id'))
	    ->addWhere('orgid=?',$orgid);
	   $record=$query->fetchOne();
	 }
	 if($record==false){
	   $this->errors->addError("Campagna non trovata.");
       $this->emitSaveData();
	   return;
	 }
	 $record->merge(array("orgid"=>$orgid,"descrizione"=>$req->getParam('descrizione')));
	 if(!$record->trySave()){
       $this->errors->addValidationErrors($record);
     }
     $this->emitSaveData();
	}
	
	public function getAction(){
	 $orgid=($this->aaac->getCurrentUser()->ID_role!=Constants::AMMINISTRATORE)?A::orgid():$this->getRequest()->getParam('orgid');
	 $q=Doctrine_Query::create()
	   ->from('Gruppi')
	   ->where('ID_gruppo=?',$this->getRequest()->getParam('id'))
	   ->addWhere('orgid=?',$orgid);
	 $record=$q->fetchOne(null,Doctrine::HYDRATE_ARRAY);
	 $this->emitLoadData($record);
	}
	
	public function deleteAction(){
	  $orgid=($this->aaac->getCurrentUser()->ID_role!=Constants::AMMINISTRATORE)?A::orgid():$this->getRequest()->getParam('orgid');
	  $record=Doctrine_Query::create()
      ->from('Gruppi')
      ->where('orgid=?',$orgid)
      ->addWhere('ID_gruppo=?',$this->getRequest()->getParam('id'))
      ->fetchOne();
      if($record!=false){
       $record->delete();
      }
      $this->emitSaveData();
	}
  
}