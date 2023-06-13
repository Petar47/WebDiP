<?php
function virtualnoVrijeme() {
    $x=new Baza();
    $x->spojiDB();
    $upit=$x->selectDB("SELECT pomak FROM konfiguracija")->fetch_assoc();
    $trenutnoVrijeme = time();
    $pomaknutoVrijeme = $trenutnoVrijeme + ($upit["pomak"] * 3600);
    $formatiranoVrijeme = date('Y-m-d H:i:s', $pomaknutoVrijeme);
    return $formatiranoVrijeme;
}
echo "Trenutno vrijeme: " . virtualnoVrijeme();
