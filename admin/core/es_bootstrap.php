<?php
/**
 * Set up include path and require core libraries (Zend and Doctrine)
 */
$base_path = realpath(dirname(__FILE__).'/../');
$library_path = $base_path.DIRECTORY_SEPARATOR.'library';
$doctrine_path = $library_path.DIRECTORY_SEPARATOR.'Doctrine';
set_include_path($library_path . PATH_SEPARATOR . $doctrine_path.PATH_SEPARATOR. get_include_path());
$appincpath = str_replace('library','application/includes',$library_path);
if(file_exists($appincpath) && is_dir($appincpath)) {
  set_include_path($appincpath.PATH_SEPARATOR.get_include_path());
}
/**
 * Check for debug Doctrine directory
 */
require_once 'Zend/Loader.php';
require_once('Zend/Registry.php');

Zend_Registry::set('base_path', $base_path);

require_once('Doctrine.php');
spl_autoload_register(array('Doctrine', 'autoload'));

require_once('Crystal/CDoctrine/Validator/Strings.php');
require_once('Zend/Config/Ini.php');

/**
 * Check for config file
 * If not found, check for installation directory and redirect.  Else exit.
 */
if(!file_exists($base_path.DIRECTORY_SEPARATOR.'config.ini')) {
  if(file_exists($base_path.DIRECTORY_SEPARATOR.'htdocs'.DIRECTORY_SEPARATOR.'install')) {
    header('Location: install/');
    exit;
  }
  die('No config.ini file found.  Please see the installation documentation.');
}


/**
 * Load env settings
 */
try {
  $envconfig = new Zend_Config_Ini($base_path.DIRECTORY_SEPARATOR.'config.ini','env');
  foreach($envconfig as $k=>$v) {
    putenv("$k=$v");
  }
} catch (Exception $x) {
  
}


/**
 * Load the database configuration and set it up
 */
$dbconfig = new Zend_Config_Ini($base_path.DIRECTORY_SEPARATOR.'config.ini','database');
/**
 * Set up Doctrine and register it as our database to use.
 * Once we're done setting up all databases, unset dbconfig so it's not sitting around.
 */
$manager = Doctrine_Manager::getInstance();
if(extension_loaded('apc')) {
  //$manager->setAttribute(Doctrine::ATTR_QUERY_CACHE, new Doctrine_Cache_Apc());
  $manager->setAttribute(Doctrine::ATTR_RESULT_CACHE, new Doctrine_Cache_Apc());
  $manager->setAttribute(Doctrine::ATTR_RESULT_CACHE_LIFESPAN, 3600);
}
$manager->setAttribute(Doctrine::ATTR_USE_NATIVE_ENUM, true);
$dbarray = $dbconfig->toArray();
$dbarray = array_keys($dbarray);
function getPDO($dsn,$user,$pass) {
  try {
    $pdo = new PDO($dsn,$user,$pass);
  } catch (PDOException $e) {
    return false;
  }
  return $pdo;
}
require_once('Zend/Db.php');
foreach($dbarray as $dbname) {
  if($dbname != 'debug') {
    $dbengine = isset($dbconfig->$dbname->dbengine) ? $dbconfig->$dbname->dbengine : 'pdo';
    $dbtype = isset($dbconfig->$dbname->dbtype) ? $dbconfig->$dbname->dbtype : 'mysql';
    switch(strtolower($dbconfig->$dbname->dbclass)) {
      case 'zend':
        if(!isset($zdb)) {
          $zdb = array();
        }
        if($dbconfig->$dbname->dbtype == 'odbc') {
          require_once('Crystal/Db/Odbc.php');
        }
        $dbtype = $dbconfig->$dbname->dbengine == 'pdo' ? 'Pdo_'.ucfirst($dbconfig->$dbname->dbtype) : ucfirst($dbconfig->$dbname->dbengine);
        $zdb[$dbname] = Zend_Db::factory($dbtype,array(
          'host'=>$dbconfig->$dbname->dbhost,
          'username'=>$dbconfig->$dbname->dbuser,
          'password'=>$dbconfig->$dbname->dbpass,
          'dbname'=>$dbconfig->$dbname->dbname
        ));
        if(isset($dbconfig->$dbname->profile)) {
          switch($dbconfig->$dbname->profile) {
            case 'firebug':
              require_once 'Zend/Db/Profiler/Firebug.php';
              $profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
              $zdb[$dbname]->setProfiler($profiler);
              break;
            default:
              break;
          }
          $zdb[$dbname]->getProfiler()->setEnabled(true);
        }
        Zend_Registry::set('ZendDb',$zdb);
        break;
      case 'doctrine':
        switch($dbengine) {
          case 'pdo':
            $pdo = false;
            $tries = 0;
            while(!$pdo) {
              $pdo = getPDO("{$dbtype}:host={$dbconfig->$dbname->dbhost};dbname={$dbconfig->$dbname->dbname}", $dbconfig->$dbname->dbuser, $dbconfig->$dbname->dbpass);
              if($dbtype == 'mysql') {
                $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,true);
                $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,1);
              }
              if(!$pdo) {
                sleep(1);
              }
            }
            $conn = Doctrine_Manager::connection($pdo, $dbname);
            break;
          default:
            $dsn = "{$dbtype}://{$dbconfig->$dbname->dbuser}:{$dbconfig->$dbname->dbpass}@{$dbconfig->$dbname->dbhost}/{$dbconfig->$dbname->dbname}";
            $conn = Doctrine_Manager::connection($dsn);
            break;
        }
        $conn->setAttribute(Doctrine::ATTR_USE_NATIVE_ENUM, true);
        break;
      default: // both
        if(!isset($zdb)) {
          $zdb = array();
        }
        $dbtype = $dbconfig->$dbname->dbengine == 'pdo' ? 'Pdo_'.ucfirst($dbconfig->$dbname->dbtype) : ucfirst($dbconfig->$dbname->dbengine);
        $zdb[$dbname] = Zend_Db::factory($dbtype,array(
          'host'=>$dbconfig->$dbname->dbhost,
          'username'=>$dbconfig->$dbname->dbuser,
          'password'=>$dbconfig->$dbname->dbpass,
          'dbname'=>$dbconfig->$dbname->dbname
        ));
        $pdo = $zdb[$dbname]->getConnection();
        if($dbtype == 'mysql') {
          $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,true);
          $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,1);
        }
        $conn = Doctrine_Manager::connection($pdo, $dbname);
        $conn->setAttribute(Doctrine::ATTR_USE_NATIVE_ENUM, true);
        if(isset($dbconfig->$dbname->profile)) {
          switch($dbconfig->$dbname->profile) {
            case 'firebug':
              require_once 'Zend/Db/Profiler/Firebug.php';
              $profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
              $zdb[$dbname]->setProfiler($profiler);
              break;
            default:
              break;
          }
          $zdb[$dbname]->getProfiler()->setEnabled(true);
        }
        Zend_Registry::set('ZendDb',$zdb);
        break;
    }
//$profiler = new Doctrine_Connection_Profiler();
//$conn->setListener($profiler);
    if(isset($dbconfig->debug) && isset($conn)) {
      require_once('Crystal/CDoctrine/Debug.php');
      $debugger = new Doctrine_Debug();
      $debugger->_config = $dbconfig->debug;
      $conn->addListener($debugger);
    }
  }
}
unset($dbconfig);
unset($dbarray);

/**
 * Load base model
 */
require_once('Crystal/Record.php');

// Set up the controllers paths.  Core last in case the app has a version of an item to use specifically.
$controller_path = array(
  'default' => $base_path.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'controllers',
  'core' => $base_path.DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'controllers'
);
Zend_Registry::set('controller_paths', $controller_path);

// Set up the css paths
$css_paths = array(
  'app' => $base_path.DIRECTORY_SEPARATOR.'htdocs'.DIRECTORY_SEPARATOR.'css',
  'core' => $base_path.DIRECTORY_SEPARATOR.'library'.DIRECTORY_SEPARATOR.'htdocs'.DIRECTORY_SEPARATOR.'css',
  'ext' => $library_path.DIRECTORY_SEPARATOR.'htdocs'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'ext'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'css',
  'jquery' => $library_path.DIRECTORY_SEPARATOR.'htdocs'.DIRECTORY_SEPARATOR.'js'
);
Zend_Registry::set('css_paths', $css_paths);

// Set up the image paths
$image_paths = array(
  'app' => $base_path.DIRECTORY_SEPARATOR.'htdocs'.DIRECTORY_SEPARATOR.'images',
  'core' => $library_path.DIRECTORY_SEPARATOR.'htdocs'.DIRECTORY_SEPARATOR.'images',
  'ext' => $library_path.DIRECTORY_SEPARATOR.'htdocs'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'ext'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'images',
  'jquery' => $library_path.DIRECTORY_SEPARATOR.'htdocs'.DIRECTORY_SEPARATOR.'js'
);
Zend_Registry::set('image_paths', $image_paths);

// Set up the js paths
$js_paths = array(
  'app' => $base_path.DIRECTORY_SEPARATOR.'htdocs'.DIRECTORY_SEPARATOR.'js',
  'core' => $library_path.DIRECTORY_SEPARATOR.'htdocs'.DIRECTORY_SEPARATOR.'js'
);
Zend_Registry::set('js_paths', $js_paths);

/**
 * Fire up the controller and view
 */
require_once('Crystal/Controller/Front.php');
require_once('Crystal/View.php');
require_once('Crystal/Form.php');
require_once('Crystal/Input.php');

require_once('Crystal/Layout.php');
Zend_Registry::set('layout',Crystal_Layout::startMvc());

$view = new Crystal_View();
/**
 * Add core views directory
 */
$view->addScriptPath($library_path.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'views');
/**
 * Add application views directory
 */
$view->addScriptPath($base_path.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'views');
/**
 * Add our crystal view helper dir
 */
$view->addHelperPath($library_path.DIRECTORY_SEPARATOR.'Crystal/View/Helper','Crystal_View_Helper');

$files = new Zend_Config_Ini($base_path.DIRECTORY_SEPARATOR.'config.ini','files');
$files = $files->toArray();
if(isset($files['css'])) {
  foreach($files['css'] as $file) {
    $view->addCSSFile($file);
  }
}
if(isset($files['js_file'])) {
  foreach($files['js_file'] as $name => $file) {
    $view->addJSFile($file);
  }
}
if(isset($files['js_library'])) {
  foreach($files['js_library'] as $name => $lib) {
    $view->addJSLibrary($lib);
  }
}
Zend_Registry::set('view',$view);
/**
 * Set up the application's models path (modules will define their own model directories
 */
$model_path = array();
$model_path['basemodels']= $base_path.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'generated';
$model_path['core']= $base_path.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'models';
Zend_Registry::set('model_path',$model_path);

/**
 * Set up the router here in case any modules add to it
 */
require_once('Zend/Controller/Router/Rewrite.php');
require_once('Zend/Controller/Router/Route.php');
$router = new Zend_Controller_Router_Rewrite();
/*
$router->addRoute(
  'default',
  new Zend_Controller_Router_Route(':controller/:action/*', array('controller' => 'index', 'action' => 'index'))
);
*/
$router->addRoute(
    'css',
    new Zend_Controller_Router_Route('css/*', array('module' => 'core', 'controller' => 'css', 'action' => 'display'))
);
$router->addRoute(
    'image',
    new Zend_Controller_Router_Route('image/*', array('module' => 'core', 'controller' => 'image', 'action' => 'display'))
);
$router->addRoute(
    'image',
    new Zend_Controller_Router_Route('images/*', array('module' => 'core', 'controller' => 'image', 'action' => 'display'))
);
$router->addRoute(
    'js',
    new Zend_Controller_Router_Route('js/*', array('module' => 'core', 'controller' => 'js', 'action' => 'display'))
);
Zend_Registry::set('router',$router);

/**
 * Grab any modules and run their bootstrap (to set up observers, plugins, add extra controller dirs, etc)
 */
$config = new Zend_Config_Ini($base_path.DIRECTORY_SEPARATOR.'config.ini','modules');
$modules = $config->toArray();
if(count($modules) > 0) {
  foreach($modules as $module_name => $module) {
    if(isset($module['bootstrap'])) {
      require_once($module['bootstrap']);
    } elseif(file_exists($base_path.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$module['name'].DIRECTORY_SEPARATOR.'bootstrap.php')) {
      require_once($base_path.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$module['name'].DIRECTORY_SEPARATOR.'bootstrap.php');
    }
  }
}

/**
 * Attach Auth object to view for easy checking
 * Also load the User model so the auth's user object can load
 */
//Zend_Loader::loadClass('Zend_Auth');
//Zend_Loader::loadClass('UserModel', Zend_Registry::get('model_path'));
//$view->auth = Zend_Auth::getInstance();

/**
 * Load ACL configuration
 */
$aclconfig = new Zend_Config_Ini($base_path.DIRECTORY_SEPARATOR.'config.ini','acl');
$aclconfig = $aclconfig->toArray();
if(!empty($aclconfig)) {
  /**
   * Check if there is a app-specific ACL class to use
   * This file should set itself up as the global ACL object for the application.
   * See documentation for specifics on the ACL API.
   */
  if(isset($aclconfig['useclass'])) {
    require_once($aclconfig['useclass']);
  } else {
    /**
     * Load our own Crystal_ACL
     */
    require_once('Crystal/ACL.php');
    /**
     * Load Configured ACL Items,
     * Register our class as the default ACL system.
     */
    $acl = new Crystal_ACL();
    $acl->load($aclconfig);
    Zend_Registry::set('ACL',$acl);
  }
}

/**
 * Set up logging
 */
try {
  $logconfig = new Zend_Config_Ini($base_path.DIRECTORY_SEPARATOR.'config.ini','logging');
  require_once 'Zend/Log.php';
  switch($logconfig->type) {
    case 'firebug':
      require_once 'Zend/Log/Writer/Firebug.php';
      $writer = new Zend_Log_Writer_Firebug();
      break;
    case 'file':
      // Note: you can also specify "php://output" or "php://stderr"
      if(!isset($logconfig->file)) {
        throw new Exception('No file specified in config.ini for logging.');
      }
      require_once 'Zend/Log/Writer/Stream.php';
      $writer = new Zend_Log_Writer_Stream($logconfig->file);
      break;
    default:
      // Log to php://output
      require_once 'Zend/Log/Writer/Stream.php';
      $writer = new Zend_Log_Writer_Stream('php://output');
      break;
  }
  if(isset($writer)) {
    try {
      $logger = new Zend_Log($writer);
    } catch (Exception $x) {
      var_dump($x);
    }
    if(isset($logger)) {
      Zend_Registry::set('logger',$logger);
    }
  }
} catch (Exception $x) {
}


/**
 * The bootstrap.php file in the application root will be extra configuration for the application.
 * This way we don't have to redo our main bootstrap file every time we have some tweaks to add for
 * a specific application.
 */
if(file_exists($base_path.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'bootstrap.php')) {
  require_once($base_path.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'bootstrap.php');
}

try {
  $controller = Crystal_Controller_Front::getInstance();
//  $viewRenderer = Zend_Controller_Action_HelperBroker::getExistingHelper('viewRenderer');
  /**
   * Add our Crystal controller action helpers
   */
  Zend_Controller_Action_HelperBroker::addPrefix('Crystal_Controller_Action_Helper');
  $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
  $viewRenderer->setView($view);
  $viewRenderer->setViewSuffix('php');
  $controller->setRouter($router);
  $controller->setParam('useGlobalDefault', true);
  $controller->setControllerDirectory($controller_path);
  $controller->throwExceptions(true);
  $controller->dispatch();
}catch (Exception $exception){
  print("An error of type ".get_class($exception)." has occured:<br>\n");
  print("Error Code: ".$exception->getCode()."<br>\nError Message: ".$exception->getMessage()."<br>\n");
  print("At line ".$exception->getLine()." of file ".$exception->getFile()."<br>\n");
  print(nl2br($exception->getTraceAsString()));
}