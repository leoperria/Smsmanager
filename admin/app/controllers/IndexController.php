<?php

class IndexController extends Zend_Controller_Action {

  public function init(){
    $this->aaac=Zend_Registry::get('aaac');
    $this->app_config=Zend_Registry::get('app_config');
  }

  public function preDispatch(){
    if (!$this->aaac->isLogged()) {
      if ('login' != $this->getRequest()->getActionName() && 'logout'!=$this->getRequest()->getActionName()) {
        $this->_helper->redirector('login','index');
      }
    }
  }
  /**
  * L'azione di index deve riconoscere i tre ruoli e reindirizzare l'utente alla applicazione giusta.
  * Esempio: Amministratore->applicazione amministratore, Esercente->applicazione esercente, data - entry-> applicazione data-entry.
  **/
  public function indexAction(){
    require($this->app_config["BASE_PATH"]."/config/loader_config.php");
    
    switch($this->aaac->getCurrentUser()->ID_role){
      case Constants::AMMINISTRATORE:
       $LOADER_CONFIG=$LOADER_CONFIG_ADMINISTRATOR;
      break;
      case Constants::ESERCENTE:
       $orgid=self::getOrgid();
       A::setOrgid((int)$orgid['orgid']);
       $LOADER_CONFIG=$LOADER_CONFIG_ESERCENTE;
      break;
      case Constants::DATAENTRY:
      case Constants::DATAENTRY_INTERNO:
       $LOADER_CONFIG=$LOADER_CONFIG_DATAENTRY;
      break; 
    }
    $this->view->loader_config=$LOADER_CONFIG;
    $this->view->app_config=$this->app_config;
  }
  
  public function loginAction(){
    $req=$this->getRequest();
    $this->view->app_config=$this->app_config;
    
    //***************************************************** BYPASS LOGIN
    if ($this->app_config["BYPASS_LOGIN"][0]){
       $res=$this->aaac->login($this->app_config["BYPASS_LOGIN"][1],$this->app_config["BYPASS_LOGIN"][2]);
       $this->_helper->redirector('index','index');
       return;
    }
    //***************************************************** BYPASS LOGIN
        
    if($req->isPost()){
      if ($req->getParam('username')=='' || $req->getParam('password')=='') {
        $this->view->result='Inserire user e password.';
      }else{
        $user=$req->getParam('username');
        $password=$req->getParam('password');
        $res=$this->aaac->login($user,$password);
        if ($res){
          if($this->aaac->isLogged() && ($this->aaac->getCurrentUser()->ID_role==Constants::DATAENTRY || $this->aaac->getCurrentUser()->ID_role==Constants::DATAENTRY_INTERNO) && A::orgid()==null){
             $this->view->lista_organizzazioni=$this->getOrgid();
          }else{
          	$this->_helper->redirector('index','index');
          }
        } else {
          $this->view->result='Accesso negato';
        }
      }
    }
  }

  public function logoutAction(){
    $this->aaac->logout();
    //***************************************************** BYPASS LOGIN
    if (!$this->app_config["BYPASS_LOGIN"][0]){
      $this->_helper->redirector('login','index');
    }else{
      $this->_helper->viewRenderer->setNoRender(true);
    }
  }
  
  public function setorgidAction(){
   $req=$this->getRequest();
   $orgid=(int)$req->getParam('organization');
   $control=Doctrine_Query::create()
    ->from('UsersOrganizations')
    ->where('ID_user=?',$this->aaac->getCurrentUser()->ID)
    ->addWhere('orgid=?',$orgid)
    ->addWhere('active=1')
    ->fetchOne();
    if($control!=false){
     A::setOrgid($orgid);
     $this->_helper->redirector('index','index');
    }else{
      $this->view->result='Accesso negato';
    }
  }
  
  public function getOrgid(){
  	$orgid=false;
  	$org=Doctrine_Query::create()
  	 ->from('Organizations o')
  	 ->leftJoin('o.UsersOrganizations u')
  	 ->where('u.ID_user=?',$this->aaac->getCurrentUser()->ID)
  	 ->addWhere('u.active=?',Constants::UTENTE_ATTIVO);
    switch($this->aaac->getCurrentUser()->ID_role){
      case Constants::ESERCENTE:
        $orgids=$org->fetchOne();
        if($orgids!==false){
          return $orgid=array("orgid"=>$orgids->orgid,"rag_soc"=>$orgids->rag_soc);
        }
      break;
      case Constants::DATAENTRY:
      case Constants::DATAENTRY_INTERNO:
      	$orgids=$org->execute();
      	$orgid=array();
      	if($orgids!==false){
      	  foreach($orgids as $o){
      	   $orgid[]=array("orgid"=>$o->orgid,"rag_soc"=>$o->rag_soc);
      	 }
      	}
      break; 
  	}
  	return $orgid;  	 
  }
}