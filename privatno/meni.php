<?php
$putanja = "/WebDiP/2022_projekti/WebDiP2022x030";
echo "<nav class=\"navigacija\"><ul>
        <li><a href=\"$putanja/index.php\">Početna stranica</a></li>    
        <li><a href=\"$putanja/o_autoru.php\">O autoru</a></li>
        <li><a href=\"$putanja/ostalo/popis.php\">Popis kampanja i korisnika</a></li>
    ";
if (!isset($_SESSION["uloga"])){
    echo "<li><a href=\"$putanja/autentifikacija/registracija.php\">Registracija i prijava</a></li>";
}
if (isset($_SESSION["uloga"]) && intval($_SESSION["uloga"]) > 1) {
    echo "<li><a href=\"$putanja/moderator/kampanja.php\">Kampanja</a></li>";
}
if (isset($_SESSION["uloga"]) && intval($_SESSION["uloga"]) == 2) {
    echo "<li><a href=\"$putanja/moderator/statistika.php\">Statistika (moderator)</a></li>";
}
if (isset($_SESSION["uloga"]) && intval($_SESSION["uloga"]) > 2) {
    echo "<li><a href=\"$putanja/admin/proizvodstatistika.php\">Proizvodi i statistika</a></li>";
    echo "<li><a href=\"$putanja/admin/admin.php\">Postavke admina</a></li>";
}
if (isset($_SESSION["uloga"]) && intval($_SESSION["uloga"]) > 0) {
    echo "<li><a href=\"$putanja/korisnik/proizvodi.php\">Otvorene kapmanje i proizvodi</a></li>";
    echo "<li><a href=\"$putanja/korisnik/profil.php\">Moj profil</a></li>";
    echo "<li><a href=\"$putanja/privatno/odjava.php\">Odjava</a></li>";
}
echo "</ul></nav>";
