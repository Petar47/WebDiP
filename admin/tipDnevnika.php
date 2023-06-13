<?php
include '../privatno/baza.class.php';
$x=new Baza();
$x->spojiDB();
$upit = "SELECT td.naziv AS naziv, COUNT(*) AS broj FROM dnevnik d
          JOIN tip_dnevnika td ON d.tip_dnevnika_tip_id = td.tip_id
          GROUP BY d.tip_dnevnika_tip_id";
$rez = $x->selectDB($upit);

$tipDnevnikaData = [];

while ($red = $rez->fetch_assoc()) {
    $tipDnevnikaData[] = [
        'naziv' => $red['naziv'],
        'broj' => $red['broj']
    ];
}
$x->zatvoriDB();
header('Content-Type: application/json');
echo json_encode($tipDnevnikaData);