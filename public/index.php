<?php
 
  require("../core/bootstrap.php");
  
  $front = Zend_Controller_Front::getInstance()
           -> setParam('app_config', $app_config)
           -> setControllerDirectory($app_config["BASE_PATH"].'/app/controllers');
           
  $router = $front->getRouter(); 

  $router->addRoute(
            'all',
            new Zend_Controller_Router_Route('*',
                array('controller' => 'index',
                      'action'     => 'index')
            )
        );


  $router->addRoute(
    'default',new Zend_Controller_Router_Route('/:action/*',array('module' => 'default','controller'=>'index')
  ));
  
  $router->addRoute(
    'default2',new Zend_Controller_Router_Route('/main/:action/*',array('module' => 'default','controller'=>'index')
  ));
  
  Zend_Layout::startMvc(array(
    "layoutPath"=>$app_config["BASE_PATH"].'/app/views/layout'
  ));
  
  $front->dispatch();
  
  if ($app_config["DB_PROFILER"]){
    Zend_Registry::get("db_profiler")->summarize();
  }
