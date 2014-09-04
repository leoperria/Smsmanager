<?
  //********************* Doctrine
  Zend_Registry::set("doctrine_config",array(
    'SANDBOX_PATH'       => $app_config["BASE_PATH"]."/orm",
    'PATH'               => $app_config["DOCTRINE_PATH"],
    'DATA_FIXTURES_PATH' => $app_config["BASE_PATH"]."/orm/data/fixtures",
    'MODELS_PATH'        => $app_config["BASE_PATH"]."/app/models",
    'MIGRATIONS_PATH'    => $app_config["BASE_PATH"]."/orm/migrations",
    'SQL_PATH'           => $app_config["BASE_PATH"]."/orm/data/sql",
    'YAML_SCHEMA_PATH'   => $app_config["BASE_PATH"]."/orm/schema"
  ));
  require($app_config["DOCTRINE_PATH"]."/Doctrine.php");
  spl_autoload_register(array('Doctrine', 'autoload'));
  
  //TODO: ripristinare l'autoloader dei models
  include $registry["doctrine_config"]["MODELS_PATH"]."/Users.php";
  include $registry["doctrine_config"]["MODELS_PATH"]."/Roles.php";
  include $registry["doctrine_config"]["MODELS_PATH"]."/Permissions.php";
  include $registry["doctrine_config"]["MODELS_PATH"]."/Areas.php";
  include $registry["doctrine_config"]["MODELS_PATH"]."/RolesPermissions.php";
  include $registry["doctrine_config"]["MODELS_PATH"]."/Organizations.php";
  include $registry["doctrine_config"]["MODELS_PATH"]."/UsersOrganizations.php";
  include $registry["doctrine_config"]["MODELS_PATH"]."/ArchivioContatti.php";
  include $registry["doctrine_config"]["MODELS_PATH"]."/Campagne.php";
  include $registry["doctrine_config"]["MODELS_PATH"]."/Movimenti.php";
  include $registry["doctrine_config"]["MODELS_PATH"]."/Sms.php";
  include $registry["doctrine_config"]["MODELS_PATH"]."/Province.php";
  include $registry["doctrine_config"]["MODELS_PATH"]."/Ricariche.php";
  include $registry["doctrine_config"]["MODELS_PATH"]."/ImportiRicariche.php";
  include $registry["doctrine_config"]["MODELS_PATH"]."/Gruppi.php";
  
  
  
  $conn = Doctrine_Manager::connection(Zend_Registry::get("db")->getConnection());
  Zend_Registry::set("doctrine_connection",$conn);
  Doctrine_Manager::getInstance()->setAttribute(Doctrine::ATTR_VALIDATE, Doctrine::VALIDATE_ALL);
  
  if ($app_config["DB_PROFILER"]){
    require("DoctrineExtensions/CustomProfiler.php");
    $doc_profiler = new Doctrine_Connection_Profiler_Custom($queryLogger);
    $conn->setListener($doc_profiler);
    Zend_Registry::set('doctrine_profiler',$doc_profiler );
  }