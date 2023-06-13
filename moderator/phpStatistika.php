<?php
require '../privatno/baza.class.php';
require '../privatno/sesija.class.php';
$x = new Baza();
$x->spojiDB();
$korisnik = Sesija::dajKorisnika();
$korime = $korisnik["korisnik"];
$kor = $x->selectDB("SELECT * FROM korisnik WHERE korisnicko_ime='$korime'")->fetch_assoc();
$id = $kor["korisnik_id"];
$sql = "SELECT c.naziv AS kampanja_naziv, p.naziv AS proizvod_naziv, COUNT(kp.proizvod_proizvod_id) AS kolicina_kupljeno
            FROM kampanja c
            INNER JOIN kampanja_proizvod kp ON c.kampanja_id = kp.kampanja_kampanja_id
            INNER JOIN proizvod p ON kp.proizvod_proizvod_id = p.proizvod_id
            INNER JOIN kupnja k ON kp.kampanja_kampanja_id = k.kampanja_id AND kp.proizvod_proizvod_id = k.proizvod_id
            WHERE c.moderator_korisnik_id = $id
            GROUP BY c.kampanja_id, p.proizvod_id;";
$rez = $x->selectDB($sql);
$data = array();
while ($red = $rez->fetch_assoc()) {
    $data[] = array(
        'kampanje' => $red['kampanja_naziv'],
        'proizvodi' => $red['proizvod_naziv'],
        'kolicinaKupljeno' => $red['kolicina_kupljeno']
    );
}
$x->zatvoriDB();
header('Content-Type: application/json');
echo json_encode($data);