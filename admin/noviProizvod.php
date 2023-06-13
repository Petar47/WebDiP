<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Novi proizvod</title>
        <meta charset="UTF-8">
        <meta name="keywords" content="Proizvodi, Statistika">
        <meta name="author" content="Petar Martinović">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="../css/pmartinov.css"/>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
        <script src="//code.jquery.com/jquery-1.12.4.js"></script>
        <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    </head>
    <header>
        <div id="sadrzaj"">
            <?php require '../privatno/funkcije.php'; ?>
        </div>
    </header>
    <?php
    $x=new Baza();
    $x->spojiDB();
    $moderatori=$x->selectDB("SELECT * from korisnik WHERE tip_korisnika_tip_id>1");
    echo "<form id=\"novi\" method=\"post\" name=\"novi\" novalidate>
        <label for=\"naziv\">Naziv: </label>
        <input type=\"text\" id=\"naziv\"  name=\"naziv\">
        <label for=\"opis\">Opis: </label>
        <input type=\"text\" id=\"opis\"  name=\"opis\">
        <label for=\"kolicina\">Kolicina: </label>
        <input type=\"text\" id=\"kolicina\"  name=\"kolicina\">
        <label for=\"cijena\">Cijena: </label>
        <input type=\"text\" id=\"cijena\"  name=\"cijena\">
        <select name=\"id_moderatora\">";
            while ($dropdown = $moderatori->fetch_assoc()) {
                $iddropdown = $dropdown["korisnik_id"];
                $imedropdown = $dropdown["korisnicko_ime"];
                echo "<option value='$iddropdown' name='$iddropdown'>$imedropdown</option>";
            }
        echo "<input type=\"submit\" name=\"novi\" value=\"Kreiraj proizvod\"><br><br>
                </form>";
    if(null !== filter_input(INPUT_POST, 'novi')){
        $datumVrijeme= virtualnoVrijeme();
        $naziv=filter_input(INPUT_POST, 'naziv');
        $opis=filter_input(INPUT_POST, 'opis');
        $kolicina=filter_input(INPUT_POST, 'kolicina');
        $cijena=filter_input(INPUT_POST, 'cijena');
        $idmoderator=filter_input(INPUT_POST, 'id_moderatora');
        $x->updateDB("INSERT INTO proizvod(naziv,opis,kolicina,cijena,status,moderator_korisnik_id) VALUES('$naziv','$opis','$kolicina','$cijena',1,'$idmoderator')");
        $x->updateDB("INSERT INTO dnevnik (radnja, datum_vrijeme, tip_dnevnika_tip_id, korisnik_korisnik_id) VALUES ('Novi proizvod', '$datumVrijeme', 3, 2)");
        $x->zatvoriDB();
        header("Location: ./proizvodstatistika.php");
        exit();
    }
    ?>
</html>