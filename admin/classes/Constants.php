<?php
class Constants {
    //Utenti
  const SUPERUSER=1;
  const DEVELOPER=1;
  const INTERNAL_RESOURCE=1;
  const UTENTE_NON_PRIVILEGIATO=0;
  const GRUPPO_DEVELOPER="SUPER MEGA DIREZIONE GENERALE";
  const UTENTE_ATTIVO=1;
  const UTENTE_NON_ATTIVO=0;
  //Password
  const PASSWORD_LENGHT=6;
  
  // ID RUOLI
  const AMMINISTRATORE=1;
  const ESERCENTE=2;
  const DATAENTRY=3;
  const DATAENTRY_INTERNO=4;
  
  // contatti
  const PUBBLICATO=1;
  const NON_PUBBLICATO=0;
  const PAGATO=1;
  const NON_PAGATO=0;
  
  //SMS
  const MAX_CHARS_SMS=160;
  
  //Campagne
  const CAMPAGNA_INVIATA=1;
  const CAMPAGNA_NON_INVIATA=0;
}
?>