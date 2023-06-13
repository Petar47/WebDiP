<?php
require 'sesija.class.php';
require 'baza.class.php';
Sesija::kreirajSesiju();
$korime = $_SESSION["korisnik"];
$x = new Baza();
$x->spojiDB();
$rez=$x->selectDB("SELECT korisnik_id FROM korisnik WHERE korisnicko_ime='$korime'")->fetch_assoc();
$id=intval($rez["korisnik_id"]);
$datumVrijeme = date('Y-m-d H:i:s');
$x->updateDB("INSERT INTO dnevnik (radnja,datum_vrijeme,tip_dnevnika_tip_id,korisnik_korisnik_id) VALUES ('Odjava','$datumVrijeme',1,'$id')");
$x->zatvoriDB();
Sesija::obrisiSesiju();
header("Location: ../index.php");
exit();

