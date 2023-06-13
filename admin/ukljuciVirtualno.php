<?php
include '../privatno/baza.class.php';
$xmlData = file_get_contents('http://barka.foi.hr/WebDiP/pomak_vremena/pomak.php?format=xml');
$xml = simplexml_load_string($xmlData);
$virutalno=$xml->vrijeme->pomak->brojSati;
$x=new Baza();
$x->spojiDB();
$x->updateDB("UPDATE konfiguracija SET pomak = '$virutalno' WHERE konfiguracija_id='1'");
$x->zatvoriDB();
header("Location: ./admin.php");
exit();