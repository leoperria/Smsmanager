<?php

class ContattiController extends Ext_Controller_Action {

  public function listAction() {
    try {
      $this->aaac->authCheck("archiviocontatti.access");
    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }
    $q = Doctrine_Query::create()
        ->from('ArchivioContatti c')
        ->leftJoin('c.Gruppi g')
        ->where('c.orgid=?');
    $params = array(A::orgid());
    if ($this->isDataentry()) {
      $q->addWhere("c.ID_dataentry=?");
      $params[] = $this->getUserId();
    }
    if (self::isInternalResource() == false && $this->aaac->isDeveloper() == false) {
      $q->addWhere('pubblicato=?');
      $params[] = Constants::PUBBLICATO;
    }
    $q->orderBy("ID");
    list($users, $total) = DBUtils::pageQuery($q, $params, $this->getLimit(), $this->getOffset());
    $this->emitTableResult($users, $total);
  }

  public function getAction() {
    try {
      $this->aaac->authCheck("archiviocontatti.edit");
    } catch (Exception $e) {
      $this->errors->addError($e->getMessage());
      $this->emitSaveData();
      return;
    }
    $contatto = Doctrine_Query::create()
        ->from("ArchivioContatti c")
        ->addWhere("c.ID=?", $this->getRequest()->getParam('id'));
    if ($this->aaac->getCurrentUser()->ID_role != Constants::AMMINISTRATORE) {
      $contatto->addWhere("c.orgid=?", A::orgid());
    }
    if ($this->isDataentry()) {
      $contatto->addWhere("c.ID_dataentry=?", $this->getUserId());
    }
    $res = $contatto->fetchOne(null, Doctrine::HYDRATE_ARRAY);
    $this->emitLoadData($res);
  }

  public function saveAction() {
    try {
      $this->aaac->authCheck("archiviocontatti.edit");
    } catch (Exception $e) {
      $this->errors->addError($e->getMessage());
      $this->emitSaveData();
      return;
    }
    $req = $this->getRequest();
    if ($req->getParam('id') === 'new') {
      $record = Doctrine::getTable('ArchivioContatti')->create();
      $id_dataentry = 0;
    } else {
      $r = Doctrine_Query::create()
          ->from("ArchivioContatti c")
          ->addWhere("c.ID=?", $this->getRequest()->getParam('id'));
      if ($this->aaac->getCurrentUser()->ID_role != Constants::AMMINISTRATORE) {
        $r->addWhere("c.orgid=?", A::orgid());
      }
      $record = $r->fetchOne();
      $id_dataentry = $record->ID_dataentry;
    }
    if ($req->getParam('id') === 'new') {
      $record->orgid = ($this->aaac->getCurrentUser()->ID_role != Constants::AMMINISTRATORE) ? A::orgid() : $req->getParam('orgid');
      $record->data_creazione = new Doctrine_Expression('NOW()');
    } else {
      $record->data_creazione = $record->data_creazione;
    }
    $data_n = $req->getParam('data_nascita');
    if (self::isInternalResource() && $req->getParam('id') == 'new') {
      $pubblicato = Constants::NON_PUBBLICATO;
    } else if ($req->getParam('id') != 'new') {
      $pubblicato = $record->pubblicato;
    } else {
      $pubblicato = Constants::PUBBLICATO;
    }
    $record->merge(array(
      "nome" => $req->getParam("nome"),
      "cognome" => $req->getParam("cognome"),
      "ID_gruppo" => (int) $req->getParam("ID_gruppo") > 0 ? (int) $req->getParam("ID_gruppo") : null,
      "data_nascita" => (isset($data_n) && $data_n != '') ? $data_n : null,
      "indirizzo" => $req->getParam('indirizzo'),
      "localita" => $req->getParam('localita'),
      "cap" => $req->getParam('cap'),
      "ID_provincia" => $req->getParam('ID_provincia') != "" ? $req->getParam('ID_provincia') : null,
      "sesso" => $req->getParam('sesso'),
      "telefono" => str_replace(" ", "", $req->getParam('telefono')),
      "email" => $req->getParam('email'),
    //  "ID_dataentry" => ($this->isDataentry()) ? $this->getUserId() : $id_dataentry,
      "pagato" => ($req->getParam('id') == 'new') ? Constants::NON_PAGATO : $record->pagato,
      "pubblicato" => $pubblicato
    ));
    if (!$record->trySave()) {
      $this->errors->addValidationErrors($record);
    }
    $this->emitSaveData();
  }


  public function importAction(){
    $req = $this->getRequest();
    $db=Zend_Registry::get("db");
    try {
      $this->aaac->authCheck("archiviocontatti.edit");
    } catch (Exception $e) {
      $this->errors->addError($e->getMessage());
      $this->emitSaveData();
      return;
    }

    $sep=$req->getParam("separator");
    $text=$req->getParam("text");
    $ID_gruppo=$req->getParam("ID_gruppo");

    if ($sep==""){
      $sep=";";
    }

    if ($sep=="\\n"){
      $sep="\n";
    }

    if ($text==""){
      $this->errors->addError("Nulla da importare");
      $this->emitSaveData();
      return;
    }

    if ($ID_gruppo==""){
      $this->errors->addError("Specificare il gruppo di destinazione");
      $this->emitSaveData();
      return;
    }

    $MAX_NUMBERS=2000;
    $numbers=explode($sep, $text, $MAX_NUMBERS);
    
    if (count($numbers)>$MAX_NUMBERS){
      $this->errors->addError("Non &egrave; possibile importare pi&ugrave; di $MAX_NUMBERS numeri alla volta. <br/>Nessun numero importato.");
      $this->emitSaveData();
      return;
    }

    $i=1;
    foreach($numbers as $number){
      $number=trim($number);
      if ($number=="") continue;
      if (substr($number, 0, 2)=="39" && strlen($number)>=11){
        $number=substr($number,2);
      }
      $check=0; //$db->fetchOne("SELECT COUNT(ID) FROM  archivio_contatti WHERE orgid=? AND telefono=?",array((int)A::orgid(),$number));
      if ($check==0){
        $db->insert("archivio_contatti",array(
          "orgid"=>A::orgid(),
          "nome"=>"Anonimo $number",
          "ID_gruppo"=>(int)$ID_gruppo,
          "ID_dataentry"=>(int)$this->getUserId(),
          "pubblicato"=>1,
          "telefono"=>$number
        ));
        $i++;
      }
    }

    $this->emitSaveData();
  }
  
  public function listbydataentryAction() {
    if ($this->aaac->getCurrentUser()->ID_role == Constants::AMMINISTRATORE) {
      $orgid = $this->getRequest()->getParam('orgid');
    } else {
      $orgid = A::orgid();
    }
    $q = Doctrine_Query::create()
        ->from('ArchivioContatti c')
        ->where('c.ID_dataentry=?')
        ->addWhere('c.orgid=?');
    list($users, $total) = DBUtils::pageQuery($q, array($this->getRequest()->getParam('id'), $orgid), $this->getLimit(), $this->getOffset());
    $this->emitTableResult($users, $total);
  }

  public function listbyorganizationAction() {
    if ($this->aaac->getCurrentUser()->ID_role == Constants::AMMINISTRATORE) {
      $orgid = $this->getRequest()->getParam('orgid');
    } else {
      $orgid = A::orgid();
    }
    $q = Doctrine_Query::create()
        ->from('ArchivioContatti c')
        ->addWhere('c.orgid=?');
    list($users, $total) = DBUtils::pageQuery($q, array($orgid), $this->getLimit(), $this->getOffset());
    $this->emitTableResult($users, $total);
  }

  public function deleteAction() {
    try {
      $this->aaac->authCheck("archiviocontatti.edit");
    } catch (Exception $e) {
      $this->errors->addError($e->getMessage());
      $this->emitSaveData();
      return;
    }
    /**
     * USER:DATAENTRY
     * NO, se � pagato oppure � stata impostata/inviata una campagna che include l'utente
     */
    $query = Doctrine_Query::create()
        ->from('ArchivioContatti')
        ->where('orgid=?', A::orgid())
        ->addWhere('ID=?', $this->getRequest()->getParam('id'));
    if ($this->isDataentry()) {
      $query->addWhere('ID_dataentry=?', $this->getUserId())->addWhere('pagato=?', Constants::NON_PAGATO);
      $control = Doctrine_Query::create()->select('COUNT(ID) as cnt')->from('Sms')->where('ID_contatto=?', $this->getRequest()->getParam('id'))->fetchOne();
      if ((int) $control->cnt > 0) {
        $this->errors->addError("Non si dispone dei permessi per eseguire l'operazione.");
        $this->emitSaveData();
        return;
      }
    }
    $res = $query->fetchOne();
    if ($res != false) {
      $res->delete();
    } else {
      $this->errors->addError("Impossibile procedere con l'eliminazione di questo contatto.<br/>Contattare l'amministratore");
      $this->emitSaveData();
      return;
    }
    $this->emitSaveData();
  }

  public function listbycriteriaAction() {
    try {
      if ($this->aaac->getCurrentUser()->ID_role != Constants::AMMINISTRATORE) {
        throw new Exception('PERMESSO NEGATO');
      }
    } catch (Exception $e) {
      $this->errors->adError($e->getMessage());
      $this->emitSaveData();
      return;
    }
    $req = $this->getRequest();
    $q = Doctrine_Query::create()
        ->from('ArchivioContatti c')
        ->leftJoin('c.Organizations o')
        ->where('c.ID_dataentry=?');
    $params = array($req->getParam('id_dataentry'));
    if ((int) $req->getParam('orgid') > 0) {
      $q->addWhere('c.orgid=?');
      $params[] = $req->getParam('orgid');
    }
    switch ((int) $req->getParam('filtro')) {
      case 1:
        $q->addWhere("c.pagato=?")->addWhere("c.pubblicato=?");
        $params[] = Constants::PAGATO;
        $params[] = Constants::PUBBLICATO;
        break;
      case 2:
        $q->addWhere('c.pagato=?')->addWhere('c.pubblicato=?');
        $params[] = Constants::PAGATO;
        $params[] = Constants::NON_PUBBLICATO;
        break;
      case 3:
        $q->addWhere('c.pagato=?')->addWhere('c.pubblicato=?');
        $params[] = Constants::NON_PAGATO;
        $params[] = Constants::PUBBLICATO;
        break;
      case 4:
        $q->addWhere('c.pagato=?')->addWhere('c.pubblicato=?');
        $params[] = Constants::NON_PAGATO;
        $params[] = Constants::NON_PUBBLICATO;
        break;
    }
    list($contatti, $total) = DBUtils::pageQuery($q, $params, $this->getLimit(), $this->getOffset());
    $this->emitTableResult($contatti, $total);
  }

  public function setpubblicatoAction() {
    try {
      if ($this->aaac->getCurrentUser()->ID_role != Constants::AMMINISTRATORE) {
        throw new Exception('PERMESSO NEGATO');
      }
    } catch (Exception $e) {
      $this->errors->adError($e->getMessage());
      $this->emitSaveData();
      return;
    }
    $req = $this->getRequest();
    $q = Doctrine_Query::create()
        ->update('ArchivioContatti')
        ->set('pubblicato', Constants::PUBBLICATO)
        ->where('ID_dataentry=?', (int) $req->getParam('id_dataentry'));
    if ((int) $req->getParam('id_selected') > 0) {
      $q->addWhere('ID=?', $req->getParam('id_selected'))->execute();
      $this->emitSaveData();
      return;
    }
    if ((int) $req->getParam('orgid') > 0) {
      $q->addWhere('orgid=?', $req->getParam('orgid'));
    }
    switch ((int) $req->getParam('filtro')) {
      case 1:
        $q->addWhere("pagato=?", Constants::PAGATO)->addWhere("pubblicato=?", Constants::PUBBLICATO);
        break;
      case 2:
        $q->addWhere('pagato=?', Constants::PAGATO)->addWhere('pubblicato=?', Constants::NON_PUBBLICATO);
        break;
      case 3:
        $q->addWhere('pagato=?', Constants::NON_PAGATO)->addWhere('pubblicato=?', Constants::PUBBLICATO);
        break;
      case 4:
        $q->addWhere('pagato=?', Constants::NON_PAGATO)->addWhere('pubblicato=?', Constants::NON_PUBBLICATO);
        break;
    }
    $q->execute();
    $this->emitSaveData();
  }

  public function unsetpubblicatoAction() {
    try {
      if ($this->aaac->getCurrentUser()->ID_role != Constants::AMMINISTRATORE) {
        throw new Exception('PERMESSO NEGATO');
      }
    } catch (Exception $e) {
      $this->errors->adError($e->getMessage());
      $this->emitSaveData();
      return;
    }
    $req = $this->getRequest();
    $q = Doctrine_Query::create()
        ->update('ArchivioContatti')
        ->set('pubblicato', Constants::NON_PUBBLICATO)
        ->where('ID_dataentry=?', (int) $req->getParam('id_dataentry'));
    if ((int) $req->getParam('id_selected') > 0) {
      $q->addWhere('ID=?', $req->getParam('id_selected'))->execute();
      $this->emitSaveData();
      return;
    }
    if ((int) $req->getParam('orgid') > 0) {
      $q->addWhere('orgid=?', $req->getParam('orgid'));
    }
    switch ((int) $req->getParam('filtro')) {
      case 1:
        $q->addWhere("pagato=?", Constants::PAGATO)->addWhere("pubblicato=?", Constants::PUBBLICATO);
        break;
      case 2:
        $q->addWhere('pagato=?', Constants::PAGATO)->addWhere('pubblicato=?', Constants::NON_PUBBLICATO);
        break;
      case 3:
        $q->addWhere('pagato=?', Constants::NON_PAGATO)->addWhere('pubblicato=?', Constants::PUBBLICATO);
        break;
      case 4:
        $q->addWhere('pagato=?', Constants::NON_PAGATO)->addWhere('pubblicato=?', Constants::NON_PUBBLICATO);
        break;
    }
    $q->execute();
    $this->emitSaveData();
  }

  public function setpagatoAction() {
    try {
      if ($this->aaac->getCurrentUser()->ID_role != Constants::AMMINISTRATORE) {
        throw new Exception('PERMESSO NEGATO');
      }
    } catch (Exception $e) {
      $this->errors->adError($e->getMessage());
      $this->emitSaveData();
      return;
    }
    $req = $this->getRequest();
    $q = Doctrine_Query::create()
        ->update('ArchivioContatti')
        ->set('pagato', Constants::PAGATO)
        ->where('ID_dataentry=?', (int) $req->getParam('id_dataentry'));
    if ((int) $req->getParam('id_selected') > 0) {
      $q->addWhere('ID=?', $req->getParam('id_selected'))->execute();
      $this->emitSaveData();
      return;
    }
    if ((int) $req->getParam('orgid') > 0) {
      $q->addWhere('orgid=?', $req->getParam('orgid'));
    }
    switch ((int) $req->getParam('filtro')) {
      case 1:
        $q->addWhere("pagato=?", Constants::PAGATO)->addWhere("pubblicato=?", Constants::PUBBLICATO);
        break;
      case 2:
        $q->addWhere('pagato=?', Constants::PAGATO)->addWhere('pubblicato=?', Constants::NON_PUBBLICATO);
        break;
      case 3:
        $q->addWhere('pagato=?', Constants::NON_PAGATO)->addWhere('pubblicato=?', Constants::PUBBLICATO);
        break;
      case 4:
        $q->addWhere('pagato=?', Constants::NON_PAGATO)->addWhere('pubblicato=?', Constants::NON_PUBBLICATO);
        break;
    }
    $q->execute();
    $this->emitSaveData();
  }

  public function unsetpagatoAction() {
    try {
      if ($this->aaac->getCurrentUser()->ID_role != Constants::AMMINISTRATORE) {
        throw new Exception('PERMESSO NEGATO');
      }
    } catch (Exception $e) {
      $this->errors->adError($e->getMessage());
      $this->emitSaveData();
      return;
    }
    $req = $this->getRequest();
    $q = Doctrine_Query::create()
        ->update('ArchivioContatti')
        ->set('pagato', Constants::NON_PAGATO)
        ->where('ID_dataentry=?', (int) $req->getParam('id_dataentry'));
    if ((int) $req->getParam('id_selected') > 0) {
      $q->addWhere('ID=?', $req->getParam('id_selected'))->execute();
      $this->emitSaveData();
      return;
    }
    if ((int) $req->getParam('orgid') > 0) {
      $q->addWhere('orgid=?', $req->getParam('orgid'));
    }
    switch ((int) $req->getParam('filtro')) {
      case 1:
        $q->addWhere("pagato=?", Constants::PAGATO)->addWhere("pubblicato=?", Constants::PUBBLICATO);
        break;
      case 2:
        $q->addWhere('pagato=?', Constants::PAGATO)->addWhere('pubblicato=?', Constants::NON_PUBBLICATO);
        break;
      case 3:
        $q->addWhere('pagato=?', Constants::NON_PAGATO)->addWhere('pubblicato=?', Constants::PUBBLICATO);
        break;
      case 4:
        $q->addWhere('pagato=?', Constants::NON_PAGATO)->addWhere('pubblicato=?', Constants::NON_PUBBLICATO);
        break;
    }
    $q->execute();
    $this->emitSaveData();
  }

  private function isDataentry() {
    return ($this->aaac->getCurrentUser()->ID_role == Constants::DATAENTRY || $this->aaac->getCurrentUser()->ID_role == Constants::DATAENTRY_INTERNO) ? true : false;
  }

  private function isInternalResource() {
    return ((int) $this->aaac->getCurrentUser()->Role->internal_resource == Constants::INTERNAL_RESOURCE) ? true : false;
  }

  private function getUserId() {
    return $this->aaac->getCurrentUser()->ID;
  }

  public function populateAction() {
    return;
    $sesso = array("M", "F");
    $provincie = array("OR", "CA", "SS", "NU", "NA", "TO", "MI");
    $user = $this->aaac->getCurrentUser()->user;
    for ($i = 0; $i < 100; $i++) {
      $contatto = new ArchivioContatti();
      $contatto->nome = "$i ORGID: " . A::orgid();
      $contatto->cognome = "Inserito da: " . $user;
      $contatto->indirizzo = "Indirizzo $i, $i";
      $contatto->sesso = $sesso[array_rand($sesso)];
      $contatto->orgid = A::orgid();
      $contatto->ID_dataentry = ($this->isDataentry()) ? $this->getUserId() : 0;
      $contatto->data_creazione = date("Y-m-d H:i:s", time());
      $contatto->pubblicato = (self::isInternalResource()) ? Constants::NON_PUBBLICATO : Constants::PUBBLICATO;
      $contatto->pagato = Constants::NON_PAGATO;
      $contatto->data_nascita = mt_rand(1960, 1991) . "-" . mt_rand(1, 12) . "-" . mt_rand(1, 28) . " 00:00:00";
      $contatto->ID_provincia = $provincie[array_rand($provincie)];
      $contatto->localita = "Localita $i";
      $contatto->cap = "00$i";
      $contatto->telefono = $i . "123";
      $contatto->email = "nome$i.cognome$i@email$i.it";
      $contatto->save();
      unset($contatto);
      echo "fatto $i<br/>";
    }
  }

}
