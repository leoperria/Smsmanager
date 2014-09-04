<?php

class IndexController extends Zend_Controller_Action {


    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function servizioAction()
    {
        // action body
    }
    
    public function offertaAction()
    {
        // action body
    }
    
    public function demoAction()
    {
        // action body
    }
    
    public function contattiAction()
    {
        // action body
    }
    
    public function mailAction(){
      $params=$this->getRequest()->getParams();
      $this->view->errors=array();
      if (trim($params["contact_nome"])==""){ $this->view->errors[]="Il campo 'Nome' non può essere vuoto";  }
      if (trim($params["contact_email"])==""){ $this->view->errors[]="Il campo 'Email' non può essere vuoto"; }
      if (count($this->view->errors)>0){
        // Gestisce gli errori  
        $this->view->contact_nome=$params["contact_nome"];
        $this->view->contact_email=$params["contact_email"];
      }else{
        // Invia la mail
        $mail = new Zend_Mail();
        $mail->setBodyText(
              "Mister SMS: nuovo contatto:\n\n".
              "Nome: {$params["contact_nome"]}\n".
              "Email: {$params["contact_email"]}\n".
              "Messaggio: ".strip_tags($params["contact_messaggio"])."\n"
             )
             ->setFrom($params["contact_email"])
             ->addTo("info@mistersms.it")
             ->setSubject("Mister SMS: nuovo contatto Sig. {$params["contact_nome"]}")
             ->send();     
      }
      
    }
}