<?php

class RicaricheController extends Ext_Controller_Action {

  public function preDispatch() {
    try {
      $this->aaac->authCheck("ricariche.access");
    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }
    $this->db=Zend_Registry::get('db');
  }

  public function listAction() {
    $req = $this->getRequest();
    $orgid = ($this->aaac->getCurrentUser()->ID_role == Constants::AMMINISTRATORE) ? (int) $req->getParam('orgid') : A::orgid();
    $query = Doctrine_Query::create()
        ->from('Ricariche r')
        ->where('r.orgid=?')
        ->orderBy('r.data_ricarica DESC');
    list($ricariche, $total) = DBUtils::pageQuery($query, array($orgid), $this->getLimit(), $this->getOffset());
    $this->emitTableResult($ricariche, $total);
  }

  public function saveAction() {
    $req = $this->getRequest();
    if ($req->getParam('id') == 'new') {
      $record = Doctrine::getTable('Ricariche')->create();
      $mov = Doctrine::getTable('Movimenti')->create();
      $mov->orgid = ($this->isAdmin() == true) ? $req->getParam('orgid') : A::orgid();
    } else {
      $query = Doctrine_Query::create()->from('Ricariche')->where('ID=?', $req->getParam('id'));
      if ($this->isAdmin() == false) {
        $query->addWhere('orgid=?', A::orgid());
      }
      $record = $query->fetchOne();
      $mov = Doctrine_Query::create()->from('Movimenti')->where('ID_ricarica=?', $req->getParam('id'))->fetchOne();
      /** TO DO CONTROLLI: variando il numero degli sms si creano incongruenze con quelli gi&agrave; utilizzati? * */
    }
    if ($record == false) {
      $this->errors->addError("Ricarica non trovata.");
      $this->emitSaveData();
      return;
    }
    $record->merge(array(
      "data_ricarica" => $req->getParam('data_ricarica'),
      "numero_sms" => (int) $req->getParam('numero_sms'),
      "importo" => (float) $req->getParam('importo'),
      "orgid" => ($this->isAdmin() == true) ? $req->getParam('orgid') : A::orgid()
    ));
    if (!$record->trySave()) {
      $this->errors->addValidationErrors($record);
      $this->emitSaveData();
      return;
    }
    if ($req->getParam('id') == 'new') {
      $mov->ID_ricarica = $record->ID;
    }
    $mov->merge(array(
      "data_movimento" => $record->data_ricarica,
      "qnt" => $record->numero_sms)
    );
    $mov->save();
    $this->emitSaveData();
  }

  public function getAction() {
    $q = Doctrine_Query::create()
        ->from('Ricariche')
        ->where('ID=?', $this->getRequest()->getParam('id'));
    if ($this->isAdmin() == false) {
      $q->addWhere('orgid=?', A::orgid());
    }
    $record = $q->fetchOne();
    $this->emitLoadData($record->toArray());
  }

  public function deleteAction() {
     $req = $this->getRequest();
    /*$control = $this->db->fetchOne("SELECT COUNT(1)as cnt FROM pubblicazioni WHERE ID_categoria=?", $req->getParam('id'));
    if ((int) $control > 0) {
      $this->errors->addError("Esiste una o pi&ugrave; pubblicazioni con questa categoria, impossibile eliminare la categoria selezionata");
      $this->emitSaveData();
      return;
    }*/
    $this->db->delete("movimenti", "ID_ricarica=" . $req->getParam('id'));
    $this->db->delete("ricariche", "ID=" . $req->getParam('id'));
    $this->emitSaveData();
  }

  public function remainingsmsAction() {
    $q = Doctrine_Query::create()
        ->select('SUM(m.qnt) AS SMS')
        ->from('Movimenti m')
        ->where('m.orgid=?', A::orgid())
        ->execute(null, Doctrine::HYDRATE_ARRAY);

    $this->emitLoadData(array("SMS" => (int) $q[0]['SMS']));
  }

  public function costiricaricheAction() {
    try {
      if (!$this->isAdmin()) {
        throw new Exception("ACCESSO NEGATO");
      }
    } catch (Exception $e) {
      echo $e->getMessage();
      return;
    }
    $q = Doctrine_Query::create()->from('ImportiRicariche i')->where("orgid=?", $this->getRequest()->getParam('orgid'));
    list($ricariche, $total) = DBUtils::PageQuery($q, array(), $this->getLimit(), $this->getOffset());
    $this->emitTableResult($ricariche, $total);
  }

  public function deletecostoAction() {
    try {
      if (!$this->isAdmin()) {
        throw new Exception("ACCESSO NEGATO");
      }
    } catch (Exception $e) {
      echo $e->getMessage();
      return;
    }
    $needle = Doctrine::getTable('ImportiRicariche')->find($this->getRequest()->getParam('id'));
    if ($needle != false) {
      $needle->delete();
      $this->emitSaveData();
    } else {
      $this->errors->addError('Importo non trovato');
      $this->emitSaveData();
    }
  }

  public function savecostoricaricaAction() {
    try {
      if (!$this->isAdmin()) {
        throw new Exception("ACCESSO NEGATO");
      }
    } catch (Exception $e) {
      echo $e->getMessage();
      return;
    }
    $q = Doctrine::getTable('ImportiRicariche');
    $record = ($this->getRequest()->getParam('id') == 'new') ? $q->create() : $q->find($this->getRequest()->getParam('id'));
    $record->merge(array(
      "orgid" => (int) $this->getRequest()->getParam('orgid'),
      "numero_sms" => (int) $this->getRequest()->getParam('numero_sms'),
      "importo" => (float) $this->getRequest()->getParam('importo')
    ));
    if (!$record->trySave()) {
      $this->errors->addValidationErrors($record);
    }
    $this->emitSaveData();
  }

  public function getcostoricaricaAction() {
    try {
      if (!$this->isAdmin()) {
        throw new Exception("ACCESSO NEGATO");
      }
    } catch (Exception $e) {
      echo $e->getMessage();
      return;
    }
    $record = Doctrine::getTable('ImportiRicariche')->find($this->getRequest()->getParam('id'));
    $this->emitLoadData($record->toArray());
  }

  public function listcomboAction() {
    $req = $this->getRequest();
    $query = Doctrine_Query::create()->from('ImportiRicariche')->where("orgid=?", $req->getParam('orgid'));
    $importiRicariche = $query->execute(null, Doctrine::HYDRATE_ARRAY);
    $this->emitTableResult($importiRicariche);
  }

  private function isAdmin() {
    return ($this->aaac->getCurrentUser()->ID_role == Constants::AMMINISTRATORE) ? true : false;
  }

}