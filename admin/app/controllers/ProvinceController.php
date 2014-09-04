<?php
class ProvinceController extends Ext_Controller_Action{
  
  public function listcomboAction(){
    $req=$this->getRequest();
    $query=Doctrine_Query::create()->from('Province');
    if($req->getParam('query')!=''){
      $query->where("LOWER(provincia) LIKE :p",
          array(":p"=>strtolower($req->getParam('query'))."%")
      );
    }
    $province=$query->orderBy("provincia")->execute(null,Doctrine::HYDRATE_ARRAY);
    $this->emitTableResult($province);
  }
  
  public function provincepopulateAction(){
  	die();
    $province=array(
    	"Agrigento"=>"AG",
      "Alessandria"=>"AL",
      "Ancona"=>"AN",
      "Aosta"=>"AO",
      "Arezzo"=>"AR",
      "Ascoli Piceno"=>"AP",
      "Asti"=>"AT",
      "Avellino"=>"AV",
      "Bari"=>"BA",
      "Belluno"=>"BL",
      "Benevento"=>"BN",
      "Bergamo"=>"BG",
      "Biella"=>"BI",
      "Bologna"=>"BO",
      "Bolzano"=>"BZ",
      "Brescia"=>"BS",
      "Brindisi"=>"BR",
      "Cagliari"=>"CA",
      "Caltanissetta"=>"CL",
      "Campobasso"=>"CB",
      "Carbonia-Iglesias"=>"CI",
      "Caserta"=>"CE",
      "Catania"=>"CT",
      "Catanzaro"=>"CZ",
      "Chieti"=>"CH",
      "Como"=>"CO",
      "Cosenza"=>"CS",
      "Cremona"=>"CR",
      "Crotone"=>"KR",
      "Cuneo"=>"CN",
      "Enna"=>"EN",
      "Ferrara"=>"FE",
      "Firenze"=>"FI",
      "Foggia"=>"FG",
      "Forl�-Cesena"=>"FC",
      "Frosinone"=>"FR",
      "Genova"=>"GE",
      "Gorizia"=>"GO",
      "Grosseto"=>"GR",
      "Imperia"=>"IM",
      "Isernia"=>"IS",
      "La Spezia"=>"SP",
      "L'Aquila"=>"AQ",
      "Latina"=>"LT",
      "Lecce"=>"LE",
      "Lecco"=>"LC",
      "Livorno"=>"LI",
      "Lodi"=>"LO",
      "Lucca"=>"LU",
      "Macerata"=>"MC",
      "Mantova"=>"MN",
      "Massa-Carrara"=>"MS",
      "Matera"=>"MT",
      "Messina"=>"ME",
      "Milano"=>"MI",
      "Modena"=>"MO",
      "Napoli"=>"NA",
      "Novara"=>"NO",
      "Nuoro"=>"NU",
      "Olbia-Tempio"=>"OT",
      "Oristano"=>"OR",
      "Padova"=>"PD",
      "Palermo"=>"PA",
      "Parma"=>"PR",
      "Pavia"=>"PV",
      "Perugia"=>"PG",
      "Pesaro e Urbino"=>"PU",
      "Pescara"=>"PE",
      "Piacenza"=>"PC",
      "Pisa"=>"PI",
      "Pistoia"=>"PT",
      "Pordenone"=>"PN",
      "Potenza"=>"PZ",
      "Prato"=>"PO",
      "Ragusa"=>"RG",
      "Ravenna"=>"RA",
      "Reggio Calabria"=>"RC",
      "Reggio Emilia"=>"RE",
      "Rieti"=>"RI",
      "Rimini"=>"RN",
      "Roma"=>"RM",
      "Rovigo"=>"RO",
      "Salerno"=>"SA",
      "Medio Campidano"=>"VS",
      "Sassari"=>"SS",
      "Savona"=>"SV",
      "Siena"=>"SI",
      "Siracusa"=>"SR",
      "Sondrio"=>"SO",
      "Taranto"=>"TA",
      "Teramo"=>"TE",
      "Terni"=>"TR",
      "Torino"=>"TO",
      "Ogliastra"=>"OG",
      "Trapani"=>"TP",
      "Trento"=>"TN",
      "Treviso"=>"TV",
      "Trieste"=>"TS",
      "Udine"=>"UD",
      "Varese"=>"VA",
      "Venezia"=>"VE",
      "Verbano-Cusio-Ossola"=>"VB",
      "Vercelli"=>"VC",
      "Verona"=>"VR",
      "Vibo Valentia"=>"VV",
      "Vicenza"=>"VI",
      "Viterbo"=>"VT");
    foreach($province as $p=>$k){
      $pr=Doctrine::getTable('Province')->create();
      $pr->ID_provincia =$k;
      $pr->provincia =$p;
      $pr->save();
    }
  }
}