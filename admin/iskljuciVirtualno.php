<?php
include '../privatno/baza.class.php';
$x=new Baza();
$x->spojiDB();
$x->updateDB("UPDATE konfiguracija SET pomak = '0' WHERE konfiguracija_id='1'");
$x->zatvoriDB();
header("Location: ./admin.php");
exit();