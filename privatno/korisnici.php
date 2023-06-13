<?php
include 'baza.class.php';
$x = new Baza();
$x->spojiDB();
$rez=$x->selectDB("SELECT * FROM korisnik");
while ($red = $rez->fetch_assoc()){
	echo 'Korisničko ime: ' . $red['korisnicko_ime'] . '<br>';
        echo 'Prezime: ' . $red['prezime'] . '<br>';
        echo 'Ime: ' . $red['ime'] . '<br>';
        echo 'Email: ' . $red['email'] . '<br>';
        echo 'Lozinka: ' . $red['lozinka'] . '<br><br>';
}
$x->zatvoriDB();
