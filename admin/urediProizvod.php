<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Uredi proizvod</title>
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
    $id= filter_input(INPUT_POST, 'id');
    $proizvod=$x->selectDB("SELECT * FROM proizvod WHERE proizvod_id='$id'")->fetch_assoc();
    $naziv=$proizvod["naziv"];
    $opis=$proizvod["opis"];
    $kolicina=$proizvod["kolicina"];
    $cijena=$proizvod["cijena"];
    echo "<form id=\"uredii\" method=\"post\" name=\"uredii\" novalidate>
        <input type=\"hidden\" id=\"idd\"  name=\"idd\" value=\"$id\">
        <label for=\"naziv\">Naziv: </label>
        <input type=\"text\" id=\"naziv\"  name=\"naziv\" value=\"$naziv\">
        <label for=\"opis\">Opis: </label>
        <input type=\"text\" id=\"opis\"  name=\"opis\" value=\"$opis\">
        <label for=\"kolicina\">Kolicina: </label>
        <input type=\"text\" id=\"kolicina\"  name=\"kolicina\" value=\"$kolicina\">
        <label for=\"cijena\">Cijena: </label>
        <input type=\"text\" id=\"cijena\"  name=\"cijena\" value=\"$cijena\">
        <input type=\"submit\" name=\"uredii\" value=\"Uredi\"><br><br>
    </form>";
    if(null !== filter_input(INPUT_POST, 'uredii')){
        $datumVrijeme= virtualnoVrijeme();
        $idd=filter_input(INPUT_POST,'idd');
        $nazivv=filter_input(INPUT_POST, 'naziv');
        $opiss=filter_input(INPUT_POST, 'opis');
        $kolicinaa=filter_input(INPUT_POST, 'kolicina');
        $cijenaa=filter_input(INPUT_POST, 'cijena');
        echo "UPDATE proizvod SET naziv='$nazivv', opis='$opiss', kolicina='$kolicinaa', cijena='$cijenaa' WHERE proizvod_id='$idd'";
        $x->updateDB("UPDATE proizvod SET naziv='$nazivv', opis='$opiss', kolicina='$kolicinaa', cijena='$cijenaa' WHERE proizvod_id='$idd'");
        $x->updateDB("INSERT INTO dnevnik (radnja, datum_vrijeme, tip_dnevnika_tip_id, korisnik_korisnik_id) VALUES ('Uređivanje proizvoda', '$datumVrijeme', 3, 2)");
        $x->zatvoriDB();
        header("Location: ./proizvodstatistika.php");
        exit();
    }
    ?>
</html>

