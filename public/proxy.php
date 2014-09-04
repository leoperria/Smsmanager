<?php

function proxyWeb($url){
  $userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/532.0 (KHTML, like Gecko) Chrome/3.0.195.38 Safari/532.0';

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
  curl_setopt($ch, CURLOPT_URL,$url);
  curl_setopt($ch, CURLOPT_FAILONERROR, true);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_AUTOREFERER, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
  curl_setopt($ch, CURLOPT_TIMEOUT, 10);
  $html= curl_exec($ch);
  return $html;
}


echo proxyWeb($_GET["url"]);