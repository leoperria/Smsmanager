<?php

/*************************************************************
 * 
 * File di configurazione
 * 
 * 
 */
$app_config=array(
  "PRODUCT_NAME"=>"Mister SMS",
  "PRODUCT_VERSION"=>"0.4",
  "PRODUCTION"=>true,
  "DEFAULT_COUNTRY_CODE"=>"39",
  "SMS_USER"=>"",
  "SMS_PASSWORD"=>"",
  "SMS_ACCOUNT"=>"",
  "SMS_SOAP_WDSL"=>"https://www.esendex.com/secure/messenger/soap/SendService.asmx?wsdl",
  "SMS_VALIDITY_PERIOD"=>72,
  "SMS_TYPE"=>"Text",
  "SMS_MAX_LENGTH"=>160,
  "SMS_CHUNK_SIZE"=>10
);

if (getenv("windir")!= "") {
  set_include_path(
     dirname(__FILE__)."/../../../lib/ZendFramework/library".PATH_SEPARATOR.
     dirname(__FILE__)."/../../../lib/doctrine".PATH_SEPARATOR.
     dirname(__FILE__)."/../../../FW2/"
  );
  $app_config["MYSQL_HOST"]="";
  $app_config["MYSQL_USER"]="";
  $app_config["MYSQL_PASS"]="";
  $app_config["MYSQL_DB"]="";
  $app_config["DB_PROFILER"]=true;
  $app_config["BASE_PATH"]=dirname(__FILE__)."/..";
  $app_config["BASE_URL"]="http://localhost/smsmanager/admin/public";
  $app_config["BYPASS_LOGIN"]=array(false,"admin","");
  $app_config["LOG_DESTINATIONS"]=array("firebug"); 
  $app_config["DOCTRINE_PATH"]=dirname(__FILE__)."/../../../lib/doctrine"; 
}else{
  set_include_path(
    "/var/www/ZendFramework/library".PATH_SEPARATOR.
    "/var/www/doctrine".PATH_SEPARATOR. 
    "/var/www/FW2"  
  );
  $app_config["MYSQL_HOST"]="";
  $app_config["MYSQL_USER"]="";
  $app_config["MYSQL_PASS"]="";
  $app_config["MYSQL_DB"]="";
  $app_config["DB_PROFILER"]=true;
  $app_config["BASE_PATH"]=dirname(__FILE__)."/..";
  $app_config["BASE_URL"]="http://admin.mistersms.it";
  $app_config["BYPASS_LOGIN"]=array(false,"admin","");
  $app_config["LOG_DESTINATIONS"]=array("file");
  $app_config["DOCTRINE_PATH"]="/var/www/doctrine"; 
}