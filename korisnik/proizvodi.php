<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Otvorene kampanje i proizvodi</title>
        <meta charset="UTF-8">
        <meta name="keywords" content="Kampanje, Proizvodi">
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
        $x = new Baza();
        $x->spojiDB();
        $datum = virtualnoVrijeme();
        $sql = "SELECT DISTINCT k.kampanja_id,k.naziv, k.datum_vrijeme_pocetka, k.datum_vrijeme_zavrsetka
                FROM kampanja k
                LEFT JOIN kampanja_proizvod kp ON k.kampanja_id = kp.kampanja_kampanja_id
                WHERE '$datum' BETWEEN k.datum_vrijeme_pocetka AND k.datum_vrijeme_zavrsetka";
        $rezultat=$x->selectDB($sql);
        if ($rezultat->num_rows > 0) {
        while ($red = $rezultat->fetch_assoc()) {
            $id=$red["kampanja_id"];
            $nazivKampanje = $red["naziv"];
            $datumPocetka = $red["datum_vrijeme_pocetka"];
            $datumZavrsetka = $red["datum_vrijeme_zavrsetka"];
            $sqlproizvodi="SELECT DISTINCT p.naziv,p.opis, p.cijena
                            FROM kampanja k
                            INNER JOIN kampanja_proizvod kp ON kp.kampanja_kampanja_id='$id'
                            INNER JOIN proizvod p ON kp.proizvod_proizvod_id = p.proizvod_id
                            WHERE p.status = 1";
            $rezultatproizvodi=$x->selectDB($sqlproizvodi)->fetch_all();
            $jsonData = json_encode($rezultatproizvodi);
            echo "<a href='#' onclick='openProductWindow(\"$nazivKampanje\",$jsonData)'>$nazivKampanje</a><br>";
            echo "Datum početka: " . $datumPocetka . "<br>";
            echo "Datum završetka: " . $datumZavrsetka . "<br><br><br>";
        }
    } else {
        echo "Nema kampanja koje odgovaraju kriterijima.";
    }
    echo "<h2>Moje kampanje</h2>";
    $korisnik=Sesija::dajKorisnika();
    $korime=$korisnik["korisnik"];
    $rez=$x->selectDB("SELECT * FROM korisnik WHERE korisnicko_ime='$korime'")->fetch_assoc();
    $id=$rez["korisnik_id"];
    $korisnikovibodovi=$rez["broj_bodova"];
    echo "Trenutno stanje vaših bodova je: " . $korisnikovibodovi . "<br>";
    $datumVrijeme = virtualnoVrijeme();
    $sqlkampanje="SELECT c.kampanja_id, c.naziv
		FROM korisnik k
		JOIN kampanja_korisnik ck ON k.korisnik_id = ck.korisnik_korisnik_id
		JOIN kampanja c ON ck.kampanja_kampanja_id = c.kampanja_id
		WHERE k.korisnik_id = '$id'
		AND c.datum_vrijeme_pocetka <= '$datumVrijeme'
		AND c.datum_vrijeme_zavrsetka >= '$datumVrijeme'";
    $kampanje=$x->selectDB($sqlkampanje);
    if($kampanje->num_rows>0){
        while($red = $kampanje->fetch_assoc()){
            echo $red["naziv"] . "<br>";
            $idkampanje= $red["kampanja_id"];
            $sqlproizvodi="SELECT p.proizvod_id, p.naziv, p.opis, p.cijena, p.bodovi_kupnjom, p.bodovi_cijena
                            FROM kampanja c
                            JOIN kampanja_proizvod cp ON c.kampanja_id = cp.kampanja_kampanja_id
                            JOIN proizvod p ON cp.proizvod_proizvod_id = p.proizvod_id
                            WHERE c.kampanja_id = '$idkampanje' AND p.kolicina > 0;";
            $proizvodi=$x->selectDB($sqlproizvodi);
            if($proizvodi->num_rows>0){
                while($redd =$proizvodi->fetch_assoc()){
                    echo $redd["naziv"] . "<br>";
                    echo $redd["opis"] . "<br>";
                    echo $redd["bodovi_cijena"] . " Bodova<br>";
                    $cijena=$redd["cijena"];
                    $idproizvoda=$redd["proizvod_id"];
                    $dobivenibodovi=$redd["bodovi_kupnjom"];
                    $kupnjabodovima=$redd["bodovi_cijena"];
                    echo "<form id=\"kupnja\" method=\"post\" name=\"kupnja\" novalidate>"
                        . "<label for=\"cijena\">Unesite cijenu: </label>
                                <input type=\"text\" id=\"cijena\" placeholder=\"Cijena\" name=\"cijena\">
                                <input type=\"hidden\" name=\"pravacijena\" value=$cijena>
                                <input type=\"hidden\" name=\"proizvod\" value=$idproizvoda>
                                <input type=\"hidden\" name=\"kampanja\" value=$idkampanje>
                                <input type=\"hidden\" name=\"brojbodova\" value=$dobivenibodovi>
                                <input type=\"submit\" name=\"kupi\" value=\"Kupi novcima\"><br> 
                                <input type=\"hidden\" name=\"kupnjabodovima\" value=$kupnjabodovima>
                                <label for=\"bodovi\">Kupnja bodovima: </label>
                                <input type=\"submit\" name=\"bodovi\" value=\"Kupi bodovima\"><br><br>
                            </form>";
                }
                echo "<br><br>";
            } else{
                echo "Ova kampanja nema proizvoda";
            }
        }
    } else{
        echo "Ne pripadate kampanji!";
    }
    if (null !== filter_input(INPUT_POST, 'kupi')){
        if(filter_input(INPUT_POST,'cijena')==filter_input(INPUT_POST,'pravacijena')){
            $proizvod=filter_input(INPUT_POST,'proizvod');
            $kampanja=filter_input(INPUT_POST,'kampanja');
            $dobivenibodovi=filter_input(INPUT_POST,'brojbodova');
            $datumVrijeme = virtualnoVrijeme();
            $sqlproizvod="UPDATE proizvod
                       SET kolicina = kolicina - 1
                       WHERE proizvod_id = '$proizvod'";
            $sqlkorisnik="UPDATE korisnik
                        SET broj_bodova = broj_bodova + '$dobivenibodovi'
                        WHERE korisnik_id = '$id'";
            $sqlkupnja="INSERT INTO kupnja (kampanja_id, korisnik_id, proizvod_id)
                        VALUES ('$kampanja', '$id', '$proizvod')";
            $sqldnevnik="INSERT INTO dnevnik (radnja, datum_vrijeme, tip_dnevnika_tip_id, korisnik_korisnik_id)
                        VALUES ('Kupnja novcem', '$datumVrijeme', 3, '$id')";
            $x->updateDB($sqlproizvod);
            $x->updateDB($sqlkorisnik);
            $x->updateDB($sqlkupnja);
            $x->updateDB($sqldnevnik);
            echo "<br>Uspješno ste kupili proizvod!";
        }
    }
    if(null !== filter_input(INPUT_POST, 'bodovi')){
        $bodovi=filter_input(INPUT_POST, 'kupnjabodovima');
        if($korisnikovibodovi>$bodovi){
            $proizvod=filter_input(INPUT_POST,'proizvod');
            $kampanja=filter_input(INPUT_POST,'kampanja');
            $datumVrijeme = virtualnoVrijeme();
            $sqlproizvod="UPDATE proizvod
                       SET kolicina = kolicina - 1
                       WHERE proizvod_id = '$proizvod'";
            $sqlkorisnik="UPDATE korisnik
                        SET broj_bodova = broj_bodova - '$bodovi'
                        WHERE korisnik_id = '$id'";
            $sqlkupnja="INSERT INTO kupnja (kampanja_id, korisnik_id, proizvod_id)
                        VALUES ('$kampanja', '$id', '$proizvod')";
            $sqldnevnik="INSERT INTO dnevnik (radnja, datum_vrijeme, tip_dnevnika_tip_id, korisnik_korisnik_id)
                        VALUES ('Kupnja bodovima', '$datumVrijeme', 3, '$id')";
            $x->updateDB($sqlproizvod);
            $x->updateDB($sqlkorisnik);
            $x->updateDB($sqlkupnja);
            $x->updateDB($sqldnevnik);
            echo "<br>Uspješno ste kupili proizvod!";
        } else{
            echo "<br>Nemate dovoljno bodova!";
        }
    }
    $x->zatvoriDB();
    ?>
    <script>
    function openProductWindow(naziv, proizvodi) {
        console.log(proizvodi);
        var windowFeatures = "width=400,height=300";
        var productWindow = window.open("", "Proizvod", windowFeatures);
        var content = "<h2>" + naziv + "</h2>";
        if(proizvodi.length===0) content+= "<p><strong>Nema dostupnih proizvoda</strong></p>";
        for (var i = 0; i < proizvodi.length; i++) {
            var x = proizvodi[i];
            content += "<p><strong>Naziv: </strong>" + x[0] + "</p>";
            content += "<p><strong>Opis: </strong>" + x[1] + "</p>";
            content += "<p><strong>Cijena: </strong>" + x[2] + "</p><br>";
        }
        productWindow.document.write(content);
    }
    </script>
</html>
