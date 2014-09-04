<?php
function elaboraImporto($value){
 $v=str_replace(",",".",$value);
 if(!is_numeric($v))$v=0;
 return  $v;
}
//print elaboraImporto("9,350");
/**
 * La funzione ricava i campi di ricerca giunti con la request
 * effettuando una sanitizzazione della stringa
 *
 * @param unknown_type $paramName
 * @param unknown_type $suffix
 * @return unknown
 */
function getSearchField($req,$suffix=false){
  $fields=split(",",$req);
  foreach($fields as $f){
    $f=str_replace('[',"",$f);
    $f=str_replace(']',"",$f);
    $f=str_replace('"',"",$f);
    if($suffix!==false){
      $f=str_replace($suffix,"",$f);
    }
    $res[]=$f;
  }
 return $res;
}


class ZendDBUtils{

    /**
     * Esegue una query paginata
     *
     * @param Zend_Db_Select $sel
     * @param unknown_type $limit
     * @param unknown_type $offset
     */
    public static function pageQuery(Zend_Db_Select $sel,$limit,$offset){
      $sel->limit($limit,$offset);
      $res=$sel->query()->fetchAll();
      $sel->reset(Zend_Db_Select::LIMIT_COUNT)
          ->reset(Zend_Db_Select::LIMIT_OFFSET)
          ->reset(Zend_Db_Select::ORDER)
          ->reset(Zend_Db_Select::COLUMNS)
          ->columns('COUNT(1)');
      return array($res,(int) $sel->getAdapter()->fetchOne($sel));
    }
}
?>