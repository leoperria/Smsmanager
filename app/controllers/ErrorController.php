<?php

class ErrorController extends Zend_Controller_Action
{

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        
        switch ($errors->type) { 
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Pagina non trovata';
                break;
            default:
                // application error 
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Errore dell\'applicazione';
                break;
        }
        
        $this->_helper->layout->setLayout('errlayout');
        $this->view->exception = $errors->exception;
        $this->view->request   = $errors->request;
    }


}

