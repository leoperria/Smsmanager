<?php
class CampagneController extends Ext_Controller_Action {
    /**
     * struttura oggetto json del filtro target
     * {
     *  "sesso":"M"|"F"|false,
     *  "ID_provincia":"XX"|false,
     *  "eta":{"comparazione":"<"|">"|"=","value":int eta}|false,
     *  "cap":"XXXXX"|false,
     *  "ID_gruppo":int |false
     * }
     * 
     */
	
    public function preDispatch(){
      try{
    	$this->aaac->authCheck("campagne.access");
      } catch (Exception $e){
        echo $e->getMessage();
        die();
      } 
    }
    
    
	public function listAction(){
	 $req=$this->getRequest();
	 $orgid=($this->aaac->getCurrentUser()->ID_role==Constants::AMMINISTRATORE)? (int)$req->getParam('orgid'):A::orgid();
	 $query=Doctrine_Query::create()
	  ->from('Campagne c')
	  ->where('c.orgid=?')
	  ->orderBy('c.data_campagna DESC');
	 list($campagne,$total)=DBUtils::pageQuery($query,array($orgid),$this->getLimit(),$this->getOffset());
	 $cam=array();
	 if(count($campagne)>0){
	  foreach($campagne as $c){

      $crit=$this->unserializeCriteria($c['c_criteri']);
      $subQuery=Doctrine_Query::create()
      ->from('ArchivioContatti')
      ->where('orgid=?',$c['c_orgid'])
      ->addWhere('pubblicato=?',Constants::PUBBLICATO);
      if(isset($crit['sesso']) && $crit['sesso']!=false){$subQuery->addWhere('sesso=?',$crit['sesso']);}
      if(isset($crit['ID_provincia']) && $crit['ID_provincia']!=false){$subQuery->addWhere('ID_provincia=?',$crit['ID_provincia']);}
      if(isset($crit['ID_gruppo']) && $crit['ID_gruppo']!=false){
        $subQuery->addWhere('ID_gruppo=?',$crit['ID_gruppo']);
        $gruppo=Doctrine::getTable('Gruppi')->find($crit['ID_gruppo']);
        $crit['ID_gruppo']=$gruppo->descrizione;
      }
      if(isset($crit['cap']) && $crit['cap']!=false){$subQuery->addWhere('cap=?',$crit['cap']);}
      if(isset($crit['eta']) && $crit['eta']!=false){
       $subQuery->addWhere("((TO_DAYS(NOW())-TO_DAYS(data_nascita)) DIV 365){$crit['eta']['comparazione']}{$crit['eta']['value']}");
      }
             
	  	if((int)$c['c_inviata']==Constants::CAMPAGNA_INVIATA){
	  	  $inviati=Doctrine_Query::create()
	  	   ->select("COUNT(ID) AS inviati")
	  	   ->from('Sms s')
	  	   ->where('ID_campagna=?',$c['c_ID'])
         ->andWhere('inviato=1')
	  	   ->fetchOne();
        $consegnati=Doctrine_Query::create()
	  	   ->select("COUNT(ID) AS consegnati")
	  	   ->from('Sms s')
	  	   ->where('ID_campagna=?',$c['c_ID'])
         ->andWhere('inviato=1')
         ->andWhere('delivery=1')
	  	   ->fetchOne();

        $numm=Doctrine_Query::create()
	  	   ->select("COUNT(ID) AS numero_messaggi")
	  	   ->from('Sms s')
	  	   ->where('ID_campagna=?',$c['c_ID'])
	  	   ->fetchOne();

        $num_messaggi=$numm->numero_messaggi;
        
	  	}else{
        $contatti=$subQuery->execute();
        $num_messaggi=$contatti->count();
      }
	  	
	    $cam[]=array(
	     "c_ID"=>$c['c_ID'],
	     "c_orgid"=>$c['c_orgid'],
	     "c_inviata"=>(int)$c['c_inviata'],
	     "c_data_campagna"=>$c['c_data_campagna'],
	     "c_data_invio"=>$c['c_data_invio'],
	     "c_nome_campagna"=>$c['c_nome_campagna'],
	     "c_testo"=>$c['c_testo'],
	     "c_criteri"=>$this->filterMaker($crit),
	     "n_messaggi"=>$num_messaggi,
	     "inviati"=>((int)$c['c_inviata']==Constants::CAMPAGNA_INVIATA)? (int)$inviati->inviati :0,
       "consegnati"=>((int)$c['c_inviata']==Constants::CAMPAGNA_INVIATA)? (int)$consegnati->consegnati :0
	    );
	  }
	 }
     $this->emitTableResult($cam,$total);
	}
	
	private function serializeCriteria($arr){
	  return json_encode($arr);
	}
	
	private function unserializeCriteria($jsoncode){
	  return json_decode($jsoncode,true);
	}
	
	private function filterMaker($arr){
	 $struct=array("sesso"=>false,"ID_provincia"=>false,"eta"=>false,"cap"=>false,"ID_gruppo"=>false);
	 foreach($arr as $k=>$v){
	  if(array_key_exists($k,$struct)){
	   if($k=="eta" && (int)$v>0 && isset($arr['comparazione'])){
	    $v=array("comparazione"=>$arr['comparazione'],"value"=>$arr['eta']);
	   }
	   $struct[$k]=$v;
	  }
	 }
	 return $struct;
	}
	
	public function saveAction(){
	 $req=$this->getRequest();
	 if( strlen(utf8_decode($req->getParam('testo'))) > Constants::MAX_CHARS_SMS ){
	  $this->errors->addError('Un SMS non pu&ograve; contenere pi&ugrave; di '.Constants::MAX_CHARS_SMS.' caratteri');
	  $this->emitSaveData();
	  return;
	 }
	 $criteri=$this->filterMaker(array(
	   "sesso"=>$req->getParam('sesso'),
	   "ID_provincia"=>$req->getParam('ID_provincia'),
	   "eta"=>((int)$req->getParam('eta')>0)?(int)$req->getParam('eta'):false,
	   "comparazione"=>$req->getParam('comparazione'),
	   "cap"=>$req->getParam('cap'),
	   "ID_gruppo"=>((int)$req->getParam('ID_gruppo')>0)?$req->getParam('ID_gruppo'):false
	 ));
	 if($req->getParam('id')=='new'){
	   $record=Doctrine::getTable('Campagne')->create();
	 }else{
	   $query=Doctrine_Query::create()
	    ->from('Campagne')
	    ->where('ID=?',$req->getParam('id'));
	   if($this->isAdmin()==false){
	     $query->addWhere('orgid=?',A::orgid());
	   }
	   $record=$query->fetchOne();
	   if($record->inviata==Constants::CAMPAGNA_INVIATA){
	    $this->errors->addError('Le campagne gi&agrave; inviate non sono midificabili');
	    $this->emitSaveData();
	    return;
	   }
	 }
	 if($record==false){
	   $this->errors->addError("Campagna non trovata.");
       $this->emitSaveData();
	   return;
	 }
	 $record->merge(array(
	   "data_campagna"=>$req->getParam('data_campagna'),
	   "nome_campagna"=>$req->getParam('nome_campagna'),
	   "testo"=>$req->getParam('testo'),//CONTARE IL NUMERO DI CARATTERI
	   "criteri"=>$this->serializeCriteria($criteri),
	   "inviata"=>Constants::CAMPAGNA_NON_INVIATA,
	   "orgid"=>($this->isAdmin()==true)? $req->getParam('orgid'):A::orgid()
	 ));
	 if(!$record->trySave()){
       $this->errors->addValidationErrors($record);
     }
     $this->emitSaveData();
	}
	
	public function getAction(){
	 $q=Doctrine_Query::create()
	   ->from('Campagne')
	   ->where('ID=?',$this->getRequest()->getParam('id'));
	 if($this->isAdmin()==false){
	   $q->addWhere('orgid=?',A::orgid());
	 }
	 $record=$q->fetchOne();
	 if($record->inviata==Constants::CAMPAGNA_INVIATA){
	  $this->errors->addError('Le campagne gi&agrave; inviate non sono midificabili');
	  $this->emitSaveData();
	  return;
	 }
	 $criteria=$this->unserializeCriteria($record->criteri);
	 $criteria=$this->filterMaker($criteria);
	 $result=array(
	   "ID"=>$record->ID,
	   "orgid"=>$record->orgid,
	   "nome_campagna"=>$record->nome_campagna,
	   "data_campagna"=>$record->data_campagna,
	   "data_invio"=>$record->data_invio,
	   "testo"=>$record->testo,
	   "sesso"=>$criteria['sesso'],
	   "eta"=>($criteria['eta']!=false)? $criteria['eta']['value']:"",
	   "comparazione"=>($criteria['eta']!=false)? $criteria['eta']['comparazione']:"",
	   "ID_provincia"=>($criteria['ID_provincia']!=false)?$criteria['ID_provincia']:"",
	   "cap"=>($criteria['cap']!=false)?$criteria['cap']:"",
	   "ID_gruppo"=>($criteria['ID_gruppo']!=false)?$criteria['ID_gruppo']:""
	 );
	 $this->emitLoadData($result);
	}
	
	public function inviaAction(){
	 $id=$this->getRequest()->getParam('id');
	 $q=Doctrine_Query::create()
	  ->from('Campagne')
	  ->where('ID=?',(int)$id)
	  ->addWhere('inviata=?',Constants::CAMPAGNA_NON_INVIATA);
	  if($this->isAdmin()!=true){
	   $q->addWhere('orgid=?',A::orgid());
	  }
	  $campagna=$q->fetchOne();
	  if($campagna!=false){
	  	/** seleziona il target**/
	  	$criteri=$this->unserializeCriteria($campagna->criteri);
	  	$query_contatti=Doctrine_Query::create()
	  	 ->select("ID")
	  	 ->from('ArchivioContatti')
	  	 ->where('orgid=?',$campagna->orgid);
	  	/** aggiungi i criteri **/
	    if($criteri['sesso']!=false){$query_contatti->addWhere('sesso=?',$criteri['sesso']);}
	    if($criteri['ID_provincia']!=false){$query_contatti->addWhere('ID_provincia=?',$criteri['ID_provincia']);}
	    if($criteri['cap']!=false){$query_contatti->addWhere('cap=?',$criteri['cap']);}
	    if($criteri['eta']!=false){
	      $query_contatti->addWhere("((TO_DAYS(NOW())-TO_DAYS(data_nascita)) DIV 365){$criteri['eta']['comparazione']}{$criteri['eta']['value']}");
	    }
	    if(isset($criteri['ID_gruppo']) && $criteri['ID_gruppo']!=false){$query_contatti->addWhere('ID_gruppo=?',$criteri['ID_gruppo']);}
	    /** I contatti **/
	    $contatti=$query_contatti->execute();
	    
	    /** Ho abbastanza credito SMS?**/
	    $balance=SMSGateway::getBalance($campagna->orgid);
	    if($balance<$contatti->count()){
	     $this->errors->addError('Non disponi di un numero sufficiente di SMS per avviare questa campagna.<br/>Puoi effettuare una ricarica ...facendo cos&igrave;...');
	     $this->emitSaveData();
	     return;
	    }
	    
	    $ids=array();
	    foreach($contatti->toArray() as $contatto){
	      $ids[]=$contatto["ID"];
	    }
	    $result=SMSGateway::sendMessages($campagna->orgid,$ids,$campagna->testo,false,$campagna->ID);
	    /**aggiorna campagna*/
	    $campagna->inviata=Constants::CAMPAGNA_INVIATA;
	    $campagna->data_invio=gmdate("Y-m-d H:i:s",time());
	    $campagna->save();
	    $this->emitSaveData();
	    return;
	  }else{
	   $this->errors->addError('CAMPAGNA NON TROVATA');
	   $this->emitSaveData();
	   return;
	  }
	}
	
	private function isAdmin(){
	 return ($this->aaac->getCurrentUser()->ID_role==Constants::AMMINISTRATORE)?true:false;
	}
}