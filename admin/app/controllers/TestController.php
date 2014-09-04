<?php
/**
 * Omicronmedia(C) 2009
*/

class TestController extends Ext_Controller_Action {

  public function testAction(){
    var_dump(Account::getAccountByParameters(array("stato"=>Account::WHAITING_ACTIVATION)));
  }
    
}