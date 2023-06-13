<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Popis kampanja i korisnika</title>
        <meta charset="UTF-8">
        <meta name="keywords" content="Popis kampanja, Popis korisnika">
        <meta name="author" content="Petar Martinović">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="../css/pmartinov.css"/>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
        <script src="//code.jquery.com/jquery-1.12.4.js"></script>
        <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    </head>
    <header>
        <?php require '../privatno/funkcije.php'; ?>
    </header>
    <section>
        <h1>Popis kampanja i korisnika</h1>
        <p>Popis kampanja</p>
        <form id="datum" method="post" name="datum" novalidate>
            <label for="od">Od:</label>
            <input type="date" id="od" name="od">
            <label for="do">Do:</label>
            <input type="date" id="do" name="do">
            <input type="submit" name="filterkampanje" value="Filtriraj">
        </form>
        <script>
        document.getElementById("datum").addEventListener("submit", function(event) {
        var datumOd = new Date(document.getElementById("od").value);
        var datumDo = new Date(document.getElementById("do").value);

        if (datumOd > datumDo) {
          alert("Datum od ne može biti veći od datuma do!");
          event.preventDefault();
        }
        });    
        </script>
        <?php
        $x = new Baza();
        $x->spojiDB();
        $sql = "SELECT k.naziv AS naziv_kampanje, COALESCE(SUM(p.kolicina), 0) AS ukupna_kolicina_proizvoda, k.datum_vrijeme_pocetka, k.datum_vrijeme_zavrsetka
            FROM kampanja k
            LEFT JOIN kampanja_proizvod kp ON k.kampanja_id = kp.kampanja_kampanja_id
            LEFT JOIN proizvod p ON kp.proizvod_proizvod_id = p.proizvod_id
            GROUP BY k.naziv";
        if (null !== filter_input(INPUT_POST, 'filterkampanje')) {
            $datumVrijeme = virtualnoVrijeme();
            $od=filter_input(INPUT_POST, 'od');
            $do=filter_input(INPUT_POST, 'do');
            $sql = "SELECT k.naziv AS naziv_kampanje, COALESCE(SUM(p.kolicina), 0) AS ukupna_kolicina_proizvoda, k.datum_vrijeme_pocetka, k.datum_vrijeme_zavrsetka
                    FROM kampanja k
                    LEFT JOIN kampanja_proizvod kp ON k.kampanja_id = kp.kampanja_kampanja_id
                    LEFT JOIN proizvod p ON kp.proizvod_proizvod_id = p.proizvod_id
                    WHERE (k.datum_vrijeme_pocetka <= '$od' OR '$od'='') 
                        AND (k.datum_vrijeme_zavrsetka >= '$od' OR '$od'='') 
                        AND (k.datum_vrijeme_pocetka <= '$do' OR '$do'='') 
                        AND (k.datum_vrijeme_zavrsetka >= '$do' OR '$do'='')
                    GROUP BY k.naziv";
            $dnevnikfilter = "INSERT INTO dnevnik (radnja,upit,datum_vrijeme,tip_dnevnika_tip_id) VALUES ('Filter po datumu','SELECT k.naziv AS naziv_kampanje, COALESCE(SUM(p.kolicina), 0) AS ukupna_kolicina_proizvoda, k.datum_vrijeme_pocetka, k.datum_vrijeme_zavrsetka
                    FROM kampanja k
                    LEFT JOIN kampanja_proizvod kp ON k.kampanja_id = kp.kampanja_kampanja_id
                    LEFT JOIN proizvod p ON kp.proizvod_proizvod_id = p.proizvod_id
                    WHERE (k.datum_vrijeme_pocetka <= \'$od\' OR \'$od\'=\'\') 
                        AND (k.datum_vrijeme_zavrsetka >= \'$od\' OR \'$od\'=\'\') 
                        AND (k.datum_vrijeme_pocetka <= \'$do\' OR \'$do\'=\'\') 
                        AND (k.datum_vrijeme_zavrsetka >= \'$do\' OR \'$do\'=\'\')
                    GROUP BY k.naziv','$datumVrijeme',2)";
            $x->selectDB($dnevnikfilter);
        }
        $rezultat = $x->selectDB($sql);
        if ($rezultat->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Naziv kampanje</th><th>Ukupna količina proizvoda</th><th>Početak</th><th>Kraj</th></tr>";

            while ($red = $rezultat->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $red["naziv_kampanje"] . "</td>";
                    echo "<td>" . $red["ukupna_kolicina_proizvoda"] . "</td>";
                    echo "<td>" . $red["datum_vrijeme_pocetka"] . "</td>";
                    echo "<td>" . $red["datum_vrijeme_zavrsetka"] . "</td>";
                    echo "</tr>";
               }
               echo "</table>";
        } else {
            echo "Nema podataka za prikaz.";
        }
        echo "<p>Popis registriranih korisnika</p>
            <form id=\"sortiranje\" method=\"post\" name=\"sortiranje\" novalidate>
            <label for=\"soritraj\">Sortiraj nadimke:</label>
            <select name=\"sortiraj\" id=\"sortiraj\">
                <option value=\"silazno\">Silazno</option>
                <option value=\"uzlazno\">Uzlazno</option>
                </select>
            <label for=\"tip\">Tip korisnika:</label>
            <select name=\"tip\" id=\"tip\">
                <option value=\"4\">Svi</option>
                <option value=\"1\">Registrirani</option>
                <option value=\"2\">Moderator</option>
                <option value=\"3\">Administrator</option>
                </select>
            <input type=\"submit\" name=\"sortiranjekorisnika\" value=\"Sortiraj i/ili filtriraj\">
            </form>";
        $sqll = "SELECT ime, prezime, nadimak FROM korisnik WHERE status_racuna != 0 ORDER BY nadimak";
        if (null !== filter_input(INPUT_POST, 'sortiranjekorisnika')) {
            $datumVrijeme = virtualnoVrijeme();
	    $tip=filter_input(INPUT_POST, 'tip');
            if(filter_input(INPUT_POST, 'sortiraj')==='silazno'){
                $dnevnikfilter;
                if($tip==='4'){
                    $sqll = "SELECT ime, prezime, nadimak FROM korisnik WHERE status_racuna != 0 ORDER BY nadimak";
                    $dnevnikfilter = "INSERT INTO dnevnik (upit,datum_vrijeme,tip_dnevnika_tip_id) VALUES ('SELECT ime, prezime, nadimak FROM korisnik WHERE status_racuna != 0 ORDER BY nadimak','$datumVrijeme',2)";
                    
                } else{
                    $sqll = "SELECT ime, prezime, nadimak FROM korisnik WHERE status_racuna != 0 AND tip_korisnika_tip_id='$tip' ORDER BY nadimak";
                    $dnevnikfilter = "INSERT INTO dnevnik (upit,datum_vrijeme,tip_dnevnika_tip_id) VALUES ('SELECT ime, prezime, nadimak FROM korisnik WHERE status_racuna != 0 AND tip_korisnika_tip_id=\'$tip\' ORDER BY nadimak','$datumVrijeme',2)";
                }
            } else{
                if($tip==='4'){
                    $sqll = "SELECT ime, prezime, nadimak FROM korisnik WHERE status_racuna != 0 ORDER BY nadimak DESC";
                    $dnevnikfilter = "INSERT INTO dnevnik (upit,datum_vrijeme,tip_dnevnika_tip_id) VALUES ('SELECT ime, prezime, nadimak FROM korisnik WHERE status_racuna != 0 ORDER BY nadimak DESC' ORDER BY nadimak','$datumVrijeme',2)";
                } else{
                    $sqll = "SELECT ime, prezime, nadimak FROM korisnik WHERE status_racuna != 0 AND tip_korisnika_tip_id='$tip' ORDER BY nadimak DESC";
                    $dnevnikfilter = "INSERT INTO dnevnik (upit,datum_vrijeme,tip_dnevnika_tip_id) VALUES ('SELECT ime, prezime, nadimak FROM korisnik WHERE status_racuna != 0 AND tip_korisnika_tip_id=\'$tip\' ORDER BY nadimak DESC','$datumVrijeme',2)";
                }
            }
            $x->selectDB($dnevnikfilter);
        }
        $rez = $x->selectDB($sqll);     
        if ($rez->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Ime</th><th>Prezime</th><th>Nadimak</th></tr>";
            while ($red = $rez->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $red["ime"] . "</td>";
                echo "<td>" . $red["prezime"] . "</td>";
                echo "<td>" . $red["nadimak"] . "</td>";
                echo "</tr>";
               }
        echo "</table>";
        } else {
            echo "Nema podataka za prikaz.";
        }
        $x->zatvoriDB();
        ?>
    </section>
</html>
