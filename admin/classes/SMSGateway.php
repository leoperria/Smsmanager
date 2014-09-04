<?php

class SMS_data {

  public function __construct($originator, $recipient, $body, $type, $validityperiod) {
    $this->originator = $originator;
    $this->recipient = $recipient;
    $this->body = $body;
    $this->type = $type;
    $this->validityperiod = $validityperiod;
  }

  public $originator;
  public $recipient;
  public $body;
  public $type;
  public $validityperiod;

}

class SMSGateway {

  public static function getBalance($orgid) {
    return (int) Zend_Registry::get("db")->fetchOne("SELECT SUM(qnt) as sms_rimasti FROM movimenti WHERE orgid=?", $orgid);
  }

  /**
   * 
   * @author DON CHUNK CASTORO
   *
   * STUB: per il momento la creazione e la spedizione dei messaggi coincidono... da separare
   * 
   * - Riceve un array di ID_contatto, il messaggio, l'sms_sender, orgid ed eventualmente un ID_campagna
   * - Controlla se l'organization ha abbastanza SMS altrimenti restituisce un errore
   * - Carica tutti i contatti dall'array
   * - Crea un SMS job
   * - Crea tutti il primo blocco di SMS da inviare in un'array in memoria   
   * - Invia a ESENDEX il blocco di SMS 
   * - salva gli SMS nel DB marchiati come inviati e con l'UID restituito da ESENDEX
   * - Scala il bilancio del orgid di un pari numero di SMS
   * - Passa al blocco successivo
   * - Restitusice true/false
   *       
   */
  public static function sendMessages($orgid, $contattiIds, $msgText, $replaceFields=false, $ID_campagna=null, $smsSender=null) {

    $appConf = Zend_Registry::get("app_config");
    $db = Zend_Registry::get("db");

    $CHUNKSIZE = $appConf["SMS_CHUNK_SIZE"];

    // Prepara il client di ESENDEX
    $client = new SoapClient(
        $appConf["SMS_SOAP_WDSL"],
        array('classmap' => array('messagesubmission' => "SMS_data", "trace" => true))
    );

    $client->__setSoapHeaders(
      new SOAPHeader("com.esendex.ems.soapinterface", 'MessengerHeader', array(
        "Username" => $appConf["SMS_USER"],
        "Password" => $appConf["SMS_PASSWORD"],
        "Account" => $appConf["SMS_ACCOUNT"]
      ))
    );

    // Carica l'organization
    $org = $db->fetchRow("SELECT * FROM organizations WHERE orgid=?", $orgid);

    // Carica i contatti e li splitta in chunk da $CHUNKSIZE elementi
    $ids = join(",", $contattiIds);
    $contatti = $db->fetchAll("SELECT * FROM archivio_contatti WHERE ID in ($ids)");
    $nContatti = count($contatti);
    $contatti = array_chunk($contatti, $CHUNKSIZE, false);

    // Controlla il balance
    $balance = self::getBalance($orgid);
    if ($balance < $nContatti) {
      throw new Exception("L'organizzazione $orgid non ha abbastanza SMS (balance=$balance)");
    }

    // Crea l'SMS job
    $db->insert("smsjobs", array());
    $smsjid = $db->lastInsertId();

    $msgText = trim($msgText);

    if ($smsSender === null) {
      $smsSender = $org["sms_sender"];
    }

    // Cicla su tutti CHUNK
    for ($chn = 0; $chn < count($contatti); $chn++) {

      // Crea l'elenco dei messaggi da spedire per questo CHUNK
      $chunk = array();
      for ($i = 0; $i < count($contatti[$chn]); $i++) {

        $tel = trim($contatti[$chn][$i]["telefono"]);

        //if (substr($tel, 0, 2) != $appConf["DEFAULT_COUNTRY_CODE"]) {
        $tel = $appConf["DEFAULT_COUNTRY_CODE"] . $tel;
        /*}else{
          if
        }*/

        if ($replaceFields) {
          //TODO: rimpiazza i placeholder con il contenuto dei campi
          $mText = $msgText;
        } else {
          $mText = $msgText;
        }

        if (strlen(utf8_decode($msgText)) > $appConf["SMS_MAX_LENGTH"]) {
          $msgText = trunc($msgText, $appConf["SMS_MAX_LENGTH"]);
        }

        $chunk[] = new SMS_data(
            $smsSender,
            $tel,
            $mText,
            $appConf["SMS_TYPE"],
            $appConf["SMS_VALIDITY_PERIOD"]
        );
      }// Ciclo for interno


      /* Spedisce il chunk */
      $result = $client->SendMessageBatch(array("messages" => $chunk));

      /* Inserirsce gli SMS in archivio*/
      $insertSql = "INSERT INTO sms (ID_smsjob, orgid, ID_contatto, ID_campagna, originator,
        type, validity_period, recipient, messaggio, inviato, UID) VALUES \n";

      $nSMSaddebitati=0;
      $ins = array();
      for ($i = 0; $i < count($contatti[$chn]); $i++) {
        $ID_smsjob = (int) $smsjid;
        $ID_contatto = (int) $contatti[$chn][$i]["ID"];
        $ID_campagna = (int) $ID_campagna;
        $originator = $db->quote($chunk[$i]->originator);
        $recipient = $db->quote($chunk[$i]->recipient);
        $type = $db->quote($chunk[$i]->type);
        $validity_period = $db->quote($chunk[$i]->validityperiod);
        $body = $db->quote($chunk[$i]->body);
        if (is_array($result->SendMessageBatchResult->string)) {
          $UID = $result->SendMessageBatchResult->string[$i];
        } else {
          $UID = $result->SendMessageBatchResult->string;
        }
        if ($UID!=""){
          $nSMSaddebitati++;
        }
        $UID = $db->quote($UID);
        $ins[] = "($ID_smsjob,$orgid,$ID_contatto,$ID_campagna,$originator,$type,$validity_period,$recipient,$body,1,$UID)";
      }
      $insertSql.=join(",\n", $ins);

       // Aggiunge un movimento al conto
      $db->insert("movimenti",array(
        "orgid"=>(int)$orgid,
        "ID_campagna"=>(int)$ID_campagna,
        "qnt"=> -$nSMSaddebitati
      ));

      // Aggiunge i messaggi
      $db->query($insertSql);

     


      /*  Risultato della chiamata:
       * 
       * 
       * array(1) {
        ["SendMessageBatchResult"]=>
        object(stdClass)#25 (1) {
        ["string"]=>
        array(2) {
        [0]=>
        string(36) "d2fe60c9-3a8e-49e0-9e8f-753931d8ad11"
        [1]=>
        string(36) "f8f67d8b-0119-49ab-a641-77d28f663381"
        }
        } */


    }  // Ciclo FOR esterno
  }

}

//class




