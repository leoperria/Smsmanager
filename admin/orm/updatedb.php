<?php
  require_once('../core/bootstrap.php');
  $var=Zend_Registry::get('doctrine_config');
  Doctrine::generateModelsFromDb($var['MODELS_PATH']);
  print "MODEL AGGIORNATO";