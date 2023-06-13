<?php
require '../privatno/baza.class.php';
$baza = new Baza();
$baza->spojiDB();

$tijeloZahtjeva = file_get_contents('php://input');
$jsonPodaci = json_decode($tijeloZahtjeva, true);

if ($jsonPodaci !== null && isset($jsonPodaci['korisnickoIme'])) {
    $korisnickoIme = $jsonPodaci['korisnickoIme'];

    $rezultatIzBaze = $baza->selectDB("SELECT ime FROM korisnik WHERE korisnicko_ime = '$korisnickoIme'");
    if ($rezultatIzBaze->num_rows > 0) {
        $odgovor = array('postoji' => true);
    } else {
        $odgovor = array('postoji' => false, 'korisnickoIme' => $korisnickoIme);
    }
} else {
    $odgovor = array('postoji' => false);
}

header('Content-Type: application/json');
echo json_encode($odgovor);
