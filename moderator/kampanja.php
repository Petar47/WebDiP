<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Kampanja</title>
        <meta charset="UTF-8">
        <meta name="keywords" content="Uređivanje kampanje, Uređivanje proizvoda">
        <meta name="author" content="Petar Martinović">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="../css/pmartinov.css"/>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
        <script src="//code.jquery.com/jquery-1.12.4.js"></script>
        <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    </head>
    <header>
        <div id="sadrzaj">
            <?php require '../privatno/funkcije.php'; ?>
        </div>
    </header>
    <h1>Kampanja</h1>
    <?php
    $x=new Baza();
    $x->spojiDB();
    $korisnik=Sesija::dajKorisnika();
    $korime=$korisnik["korisnik"];
    $rezultat=$x->selectDB("SELECT * FROM korisnik WHERE korisnicko_ime='$korime'")->fetch_assoc();
    $id=$rezultat["korisnik_id"];
    $popiskampanja=$x->selectDB("SELECT * FROM kampanja WHERE moderator_korisnik_id='$id'");
    if($popiskampanja->num_rows>0){
        while($red=$popiskampanja->fetch_assoc()){
            $idkampanje= $red["kampanja_id"];
            echo $red["naziv"] . "<br>";
            echo "Od: " . $red["datum_vrijeme_pocetka"] . " Do: " . $red["datum_vrijeme_zavrsetka"]; 
            $sqlproizvodi="SELECT p.proizvod_id, p.naziv, p.opis, p.cijena, p.kolicina, p.bodovi_cijena, bodovi_kupnjom
                            FROM kampanja c
                            JOIN kampanja_proizvod cp ON c.kampanja_id = cp.kampanja_kampanja_id
                            JOIN proizvod p ON cp.proizvod_proizvod_id = p.proizvod_id
                            WHERE c.kampanja_id = '$idkampanje'";
            $sqlnemaproizvoda="SELECT p.proizvod_id, p.naziv
                                FROM proizvod p
                                LEFT JOIN kampanja_proizvod cp ON cp.proizvod_proizvod_id = p.proizvod_id AND cp.kampanja_kampanja_id = '$idkampanje'
                                WHERE p.moderator_korisnik_id = '$id' AND cp.kampanja_kampanja_id IS NULL";
            $proizvodi=$x->selectDB($sqlnemaproizvoda);
            echo "<form method=\"post\">
                    <select name=\"id_$idkampanje\">";
            while ($dropdown = $proizvodi->fetch_assoc()) {
                $iddropdown = $dropdown["proizvod_id"];
                $nazivdropdown = $dropdown["naziv"];
                echo "<option value='$iddropdown' name='$iddropdown'>$nazivdropdown</option>";
            }
            echo "</select>
                <input type=\"hidden\" name=\"novo_$idkampanje\" value=$idkampanje>
                    <input type=\"submit\" name=\"dodajProizvod_$idkampanje\" value=\"Dodaj proizvod u kampanju\" /><br>";
            $proizvodii=$x->selectDB($sqlproizvodi);
            if($proizvodii->num_rows>0){
                while($redd =$proizvodii->fetch_assoc()){
                    $idproizvoda=$redd["proizvod_id"];
                    echo "Naziv: " . $redd["naziv"];
                    echo " Opis: " . $redd["opis"];
                    echo " Količina: " . $redd["kolicina"];
                    echo " Cijena: " . $redd["cijena"] . "<br>";
                }
                echo "<br>";
            } else{
                echo "Ova kampanja nema proizvoda<br><br>";
            }
        }
    } else{
        echo "Nemate ni jedne kampanje";
    }
    $danasnjiDatum= virtualnoVrijeme();
    echo "<br><br><h3>Nova kampanja</h3> <br>";
    echo "<form method=\"post\">
        <label for=\"naziv\">Naziv: </label>
        <input type=\"text\" id=\"naziv\" placeholder=\"Naziv\" name=\"naziv\">
        <label for=\"opis\">Opis: </label>
        <input type=\"text\" id=\"opis\" placeholder=\"Opis\" name=\"opis\">
        <label for=\"od\">Od: </label>
        <input type=\"date\" id=\"od\" name=\"od\" min=\"$danasnjiDatum\" value=\"$danasnjiDatum\">
        <label for=\"do\">Do: </label>
        <input type=\"date\" id=\"do\" name=\"do\" min=\"$danasnjiDatum\" value=\"$danasnjiDatum\">
        <input type=\"submit\" name=\"novaKampanja\" value=\"Kreiraj novu kampanju\" />";
    echo "<br><h3>Uredi proizvod</h3>";
    $formaa=$x->selectDB("SELECT * FROM proizvod p WHERE p.moderator_korisnik_id='$id'");
    if($formaa->num_rows>0){
        while($red=$formaa->fetch_assoc()){
            $idproizvoda=$red["proizvod_id"];
            echo "Naziv: " . $red["naziv"];
            echo " Opis: " . $red["opis"];
            echo " Količina: " . $red["kolicina"];
            echo " Cijena: " . $red["cijena"]; 
            $bodoviKupnjom=$red["bodovi_kupnjom"];
            $bodoviCijena=$red["bodovi_cijena"];
            echo "<form method=\"post\">
                    <input type=\"hidden\" name=\"idproizvoda\" value=$idproizvoda>
                    <label for=\"bodoviKupnjom\">Bodovi kupnjom: </label>
                    <input type=\"text\" id=\"bodoviKupnjom\" value='$bodoviKupnjom' name=\"bodoviKupnjom_$idproizvoda\">
                    <label for=\"bodoviCijena\">Bodovi cijena: </label>
                    <input type=\"text\" id=\"bodoviCijena\" value='$bodoviCijena' name=\"bodoviCijena_$idproizvoda\">
                    <input type=\"submit\" name=\"urediProizvod_$idproizvoda\" value=\"Uredi proizvod\" /><br>";
        }
    }
    $formaaa=$x->selectDB("SELECT p.proizvod_id FROM proizvod p WHERE p.moderator_korisnik_id='$id'");
    if($formaaa->num_rows>0){
        while($red=$formaaa->fetch_assoc()){
            $i=$red["proizvod_id"];
            if(null !== filter_input(INPUT_POST,"urediProizvod_" . $i)){
                $datumVrijeme= virtualnoVrijeme();
                $bodoviK=filter_input(INPUT_POST,"bodoviKupnjom_" . $i);
                $bodoviC=filter_input(INPUT_POST,"bodoviCijena_" . $i);
                var_dump($bodoviK);
                var_dump($bodoviC);
                $sqluredi="UPDATE proizvod SET bodovi_kupnjom = '$bodoviK', bodovi_cijena = '$bodoviC' WHERE proizvod_id='$i'";
                $dnevnikuredi="INSERT INTO dnevnik (radnja, datum_vrijeme, tip_dnevnika_tip_id, korisnik_korisnik_id)
                                VALUES ('Uređivanje proizvoda', '$datumVrijeme', 3, '$id')";
                $x->updateDB($sqluredi);
                $x->updateDB($dnevnikuredi);
            }
        }
    }
    if(null !== filter_input(INPUT_POST,'novaKampanja')){
        $naziv=filter_input(INPUT_POST,'naziv');
        $opis=filter_input(INPUT_POST,'opis');
        $datumVrijeme = virtualnoVrijeme();
        if($naziv!==""&&$opis!==""){
            $od=filter_input(INPUT_POST,'od');
            $do=filter_input(INPUT_POST,'do');
            $sqlnovakampanja="INSERT INTO kampanja (naziv,opis,datum_vrijeme_pocetka,datum_vrijeme_zavrsetka,moderator_korisnik_id) VALUES ('$naziv','$opis','$od','$do','$id')";
            $dnevniknovakampanja="INSERT INTO dnevnik (radnja, datum_vrijeme, tip_dnevnika_tip_id, korisnik_korisnik_id)
                        VALUES ('Nova kampanja', '$datumVrijeme', 3, '$id')";
            $x->updateDB($sqlnovakampanja);
            $x->updateDB($dnevniknovakampanja);
        } else{
            echo "Unesite podatke!";
        } 
    }
    $forma=$x->selectDB("SELECT * FROM kampanja WHERE moderator_korisnik_id='$id'");
    if($forma->num_rows>0){
        while($red=$forma->fetch_assoc()){
            $i=$red["kampanja_id"];
            if(null !== filter_input(INPUT_POST,'dodajProizvod_' . $i)){
                $datumVrijeme= virtualnoVrijeme();
                $iddropdown=filter_input(INPUT_POST,'id_' . $i);
                $novo=filter_input(INPUT_POST,'novo_' . $i);
                $sqlproizvodkampanja="INSERT INTO kampanja_proizvod (kampanja_kampanja_id,proizvod_proizvod_id) VALUES('$novo','$iddropdown')";
                $dnevnikunosproizvodaukampanju="INSERT INTO dnevnik (radnja, datum_vrijeme, tip_dnevnika_tip_id, korisnik_korisnik_id)
                                VALUES ('Unos proizvoda u kampanju', '$datumVrijeme', 3, '$id')";
                $x->updateDB($sqlproizvodkampanja);
                $x->updateDB($dnevnikunosproizvodaukampanju);
            }
        }
    }
    ?>
</html>