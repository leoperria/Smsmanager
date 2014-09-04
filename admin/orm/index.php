<?php
require_once('config.php');
class Query extends Doctrine_Query{
  
   private $placeOrgIdFilter;
      
   
   public static function  create($placeOrgIdFilter=true,Doctrine_Connection $conn = null){
    $q=new Query($conn);
    $q->setPlaceOrgIdFitler($placeOrgIdFilter);
    return $q;
   }
  
   public function setPlaceOrgIdFitler($p){
     $this->placeOrgIdFilter=$p;
   }
   
   public function preQuery(){
       $this->addWhere("orgid=?",array(100));
   }
}