<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Postavke admina</title>
        <meta charset="UTF-8">
        <meta name="keywords" content="ADMIN">
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
    <h3>Virtualno vrijeme</h3>
    <input type="button" value="Uključi virtualno vrijeme" onclick="location = 'ukljuciVirtualno.php'" />
    <input type="button" value="Isključi virtualno vrijeme" onclick="location = 'iskljuciVirtualno.php'" /><br>
    <h3>Otključavanje korisničkih računa</h3> 
    <form method="post">
        <select name="id">
            <?php
            $x = new Baza();
            $x->spojiDB();
            $rezultat = $x->selectDB("SELECT korisnik_id,korisnicko_ime FROM korisnik WHERE status_racuna=0");
            while ($dropdown = $rezultat->fetch_assoc()) {
                $iddropdown = $dropdown["korisnik_id"];
                $nazivdropdown = $dropdown["korisnicko_ime"];
                echo "<option value='$iddropdown' name='$iddropdown'>$nazivdropdown</option>";
            }
            echo "</select>
        <input type=\"submit\" name=\"odblok\" value=\"Odblokiraj\" /><br>";
            if (null != filter_input(INPUT_POST, 'odblok')) {
                $datumVrijeme = virtualnoVrijeme();
                $id = filter_input(INPUT_POST, 'id');
                $x->updateDB("UPDATE korisnik SET status_racuna=1,broj_unosa=0 WHERE korisnik_id='$id'");
                $x->updateDB("INSERT INTO dnevnik (radnja,datum_vrijeme,tip_dnevnika_tip_id,korisnik_korisnik_id) VALUES ('Deblokiranje računa','$datumVrijeme',3,'$id')");
            }
            $blokirati = $x->selectDB("SELECT korisnik_id,korisnicko_ime FROM korisnik WHERE status_racuna=1 AND tip_korisnika_tip_id < 3");
            echo "<h3>Blokiranje korisničkih računa</h3> 
            <form method=\"post\">
            <select name=\"idzablok\">";
            while ($dropdown = $blokirati->fetch_assoc()) {
                $iddropdown = $dropdown["korisnik_id"];
                $nazivdropdown = $dropdown["korisnicko_ime"];
                echo "<option value='$iddropdown' name='$iddropdown'>$nazivdropdown</option>";
            }
            echo "</select>
        <input type=\"submit\" name=\"blok\" value=\"Blokiraj\" /><br>";
            if (null != filter_input(INPUT_POST, 'blok')) {
                $datumVrijeme = virtualnoVrijeme();
                $id = filter_input(INPUT_POST, 'idzablok');
                $x->updateDB("UPDATE korisnik SET status_racuna=0 WHERE korisnik_id='$id'");
                $x->updateDB("INSERT INTO dnevnik (radnja,datum_vrijeme,tip_dnevnika_tip_id,korisnik_korisnik_id) VALUES ('Blokiranje računa','$datumVrijeme',3,'$id')");
            }
            echo '<h3>Promocija za moderatora</h3>'
            . '<form method="post">
                <select name="idzapromociju">';
            $promocija = $x->selectDB("SELECT korisnik_id,korisnicko_ime FROM korisnik WHERE tip_korisnika_tip_id=1");
            while ($dropdown = $promocija->fetch_assoc()) {
                $iddropdown = $dropdown["korisnik_id"];
                $nazivdropdown = $dropdown["korisnicko_ime"];
                echo "<option value='$iddropdown' name='$iddropdown'>$nazivdropdown</option>";
            }
            echo "</select>
        <input type=\"submit\" name=\"promocija\" value=\"Promoviraj u moderatora\" /><br>";
            if (null != filter_input(INPUT_POST, 'promocija')) {
                $datumVrijeme = virtualnoVrijeme();
                $id = filter_input(INPUT_POST, 'idzapromociju');
                $x->updateDB("UPDATE korisnik SET tip_korisnika_tip_id=2 WHERE korisnik_id='$id'");
                $x->updateDB("INSERT INTO dnevnik (radnja,datum_vrijeme,tip_dnevnika_tip_id,korisnik_korisnik_id) VALUES ('Promocija korisnika','$datumVrijeme',3,'$id')");
            }
	    echo '<h3>Pridruži korisnika kampanji</h3>'
            . '<form method="post">
                <select name="idkorisnikzakampanju">';
            $korisnikkampanja = $x->selectDB("SELECT korisnik_id,korisnicko_ime FROM korisnik WHERE tip_korisnika_tip_id=1");
            while ($dropdown = $korisnikkampanja->fetch_assoc()) {
                $iddropdown = $dropdown["korisnik_id"];
                $nazivdropdown = $dropdown["korisnicko_ime"];
                echo "<option value='$iddropdown' name='$iddropdown'>$nazivdropdown</option>";
            }
            echo '</select> 
	    <select name="idkampanjazakorisnika">';
            $kampanjakorisnik = $x->selectDB("SELECT kampanja_id,naziv FROM kampanja");
            while ($dropdown = $kampanjakorisnik->fetch_assoc()) {
                $iddropdown = $dropdown["kampanja_id"];
                $nazivdropdown = $dropdown["naziv"];
                echo "<option value='$iddropdown' name='$iddropdown'>$nazivdropdown</option>";
            }
            echo "</select>
        <input type=\"submit\" name=\"pridruzivanje\" value=\"Pridruži kampanji\" /><br>";
            if (null != filter_input(INPUT_POST, 'pridruzivanje')) {
                $datumVrijeme = virtualnoVrijeme();
                $idkorisnik = filter_input(INPUT_POST, 'idkorisnikzakampanju');
                $idkampanja = filter_input(INPUT_POST, 'idkampanjazakorisnika');
                $x->updateDB("INSERT INTO kampanja_korisnik(korisnik_korisnik_id,kampanja_kampanja_id) VALUES('$idkorisnik','$idkampanja')");
                $x->updateDB("INSERT INTO dnevnik (radnja,datum_vrijeme,tip_dnevnika_tip_id,korisnik_korisnik_id) VALUES ('Ubacivanje korisnika u kampanju','$datumVrijeme',3,'$idkorisnik')");
            }
            echo '<h3>Dnevnik rada</h3>';
            echo "<form method=\"POST\">
                <label for=\"tip\">Tip korisnika:</label>
                <select name=\"tip\" id=\"tip\">
                    <option value=\"4\">Svi</option>
                    <option value=\"1\">Registrirani</option>
                    <option value=\"2\">Moderator</option>
                    <option value=\"3\">Administrator</option>
                    <option value=\"5\">Neregistrirani</option>
                </select>
                <label for=\"radnja\">Tip radnje:</label>
                <select name=\"radnja\" id=\"tip\">
                    <option value=\"4\">Sve</option>
                    <option value=\"1\">Prijava/odjava</option>
                    <option value=\"2\">Rad s bazom</option>
                    <option value=\"3\">Ostale radnje</option>
                </select>
                    <input type=\"submit\" name=\"dnevnik\" value=\"Pretrazi\"></button>
                </form>";
            $sqldnevnik = "SELECT d.radnja, d.upit, d.datum_vrijeme, td.naziv AS naziv_dnevnika,
                            COALESCE(tk.naziv, 'Neregistrirani korisnik') AS tip_korisnika,
                            k.korisnicko_ime
                            FROM dnevnik d
                            JOIN tip_dnevnika td ON d.tip_dnevnika_tip_id = td.tip_id
                            LEFT JOIN korisnik k ON d.korisnik_korisnik_id = k.korisnik_id
                            LEFT JOIN tip_korisnika tk ON k.tip_korisnika_tip_id = tk.tip_id;";
            if(null != filter_input(INPUT_POST, 'dnevnik')){
                $tipKorisnikaFilter= filter_input(INPUT_POST, 'tip');
                $tipRadnjeFilter= filter_input(INPUT_POST, 'radnja');
                if($tipRadnjeFilter==4){
                    if($tipKorisnikaFilter==5){
                    $sqldnevnik="SELECT d.radnja, d.upit, d.datum_vrijeme, td.naziv AS naziv_dnevnika,
                                'Neregistrirani korisnik' AS tip_korisnika,
                                NULL AS korisnicko_ime
                                FROM dnevnik d
                                JOIN tip_dnevnika td ON d.tip_dnevnika_tip_id = td.tip_id
                                LEFT JOIN korisnik k ON d.korisnik_korisnik_id = k.korisnik_id
                                WHERE k.korisnik_id IS NULL";
                    }
                    else if($tipKorisnikaFilter!=4){
                        $sqldnevnik="SELECT d.radnja, d.upit, d.datum_vrijeme, td.naziv AS naziv_dnevnika,
                            COALESCE(tk.naziv, 'Neregistrirani korisnik') AS tip_korisnika,
                            k.korisnicko_ime
                            FROM dnevnik d
                            JOIN tip_dnevnika td ON d.tip_dnevnika_tip_id = td.tip_id
                            LEFT JOIN korisnik k ON d.korisnik_korisnik_id = k.korisnik_id
                            LEFT JOIN tip_korisnika tk ON k.tip_korisnika_tip_id = tk.tip_id
                            WHERE tk.tip_id = '$tipKorisnikaFilter'";
                    }
                }
                else{
                    if($tipKorisnikaFilter==5){
                    $sqldnevnik="SELECT d.radnja, d.upit, d.datum_vrijeme, td.naziv AS naziv_dnevnika,
                                'Neregistrirani korisnik' AS tip_korisnika,
                                NULL AS korisnicko_ime
                                FROM dnevnik d
                                JOIN tip_dnevnika td ON d.tip_dnevnika_tip_id = td.tip_id
                                LEFT JOIN korisnik k ON d.korisnik_korisnik_id = k.korisnik_id
                                WHERE k.korisnik_id IS NULL AND d.tip_dnevnika_tip_id = '$tipRadnjeFilter'";
                    }
                    else if($tipKorisnikaFilter==4){
                        $sqldnevnik="SELECT d.radnja, d.upit, d.datum_vrijeme, td.naziv AS naziv_dnevnika,
                            COALESCE(tk.naziv, 'Neregistrirani korisnik') AS tip_korisnika,
                            k.korisnicko_ime
                            FROM dnevnik d
                            JOIN tip_dnevnika td ON d.tip_dnevnika_tip_id = td.tip_id
                            LEFT JOIN korisnik k ON d.korisnik_korisnik_id = k.korisnik_id
                            LEFT JOIN tip_korisnika tk ON k.tip_korisnika_tip_id = tk.tip_id
                            WHERE d.tip_dnevnika_tip_id = '$tipRadnjeFilter'";
                    }
                    else{
                        $sqldnevnik="SELECT d.radnja, d.upit, d.datum_vrijeme, td.naziv AS naziv_dnevnika,
                            COALESCE(tk.naziv, 'Neregistrirani korisnik') AS tip_korisnika,
                            k.korisnicko_ime
                            FROM dnevnik d
                            JOIN tip_dnevnika td ON d.tip_dnevnika_tip_id = td.tip_id
                            LEFT JOIN korisnik k ON d.korisnik_korisnik_id = k.korisnik_id
                            LEFT JOIN tip_korisnika tk ON k.tip_korisnika_tip_id = tk.tip_id
                            WHERE tk.tip_id = '$tipKorisnikaFilter' AND d.tip_dnevnika_tip_id = '$tipRadnjeFilter'";
                    }
                }
            }
            $rez = $x->selectDB($sqldnevnik);
            if ($rez->num_rows > 0) {
                echo "<table>";
                echo "<tr>
                        <th>Radnja</th>
                        <th>Upit</th>
                        <th>Datum i vrijeme</th>
                        <th>Naziv dnevnika</th>
                        <th>Korisničko ime</th>
                        <th>Tip korisnika</th>
                      </tr>";

                while ($red = $rez->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $red["radnja"] . "</td>";
                    echo "<td>" . $red["upit"] . "</td>";
                    echo "<td>" . $red["datum_vrijeme"] . "</td>";
                    echo "<td>" . $red["naziv_dnevnika"] . "</td>";
                    echo "<td>" . $red["korisnicko_ime"] . "</td>";
                    echo "<td>" . $red["tip_korisnika"] . "</td>";
                    echo "</tr>";
                }

                echo "</table>";
            } else {
                echo "Nema rezultata.";
            }
            $x->zatvoriDB();
            ?>
    <canvas id="myChart"></canvas>
    <script>
        var xhttp = new XMLHttpRequest();

        xhttp.onreadystatechange = function () {
            console.log(this.responseText);
            if (this.readyState === 4 && this.status === 200) {
                var response = JSON.parse(this.responseText);
                var naziv = [];
                var zbroj = [];
                for (var i = 0; i < response.length; i++) {
                    naziv.push(response[i].naziv);
                    zbroj.push(response[i].broj);
                }
                graf(naziv, zbroj);
            }
        };

        xhttp.open("GET", "./tipDnevnika.php", true);
        xhttp.send();

        function graf(naziv, zbroj) {
            var canvas = document.getElementById("myChart");
            var ctx = canvas.getContext("2d");

            canvas.width = 400;
            canvas.height = 300;

            var brojStupaca = naziv.length;
            var maxKolicina = Math.max.apply(null, zbroj);
            var stupacSirina = canvas.width / brojStupaca;
            console.log(brojStupaca);
            console.log(maxKolicina);
            for (var i = 0; i < brojStupaca; i++) {
                var x = i * stupacSirina;
                var y = canvas.height - (zbroj[i] / maxKolicina) * canvas.height;
                var sirina = stupacSirina - 10;
                var visina = (zbroj[i] / maxKolicina) * canvas.height;

                ctx.fillStyle = "blue";
                ctx.fillRect(x, y, sirina, visina);

                ctx.fillStyle = "black";
                ctx.textAlign = "center";

                var tekst = naziv[i] + ', ' + zbroj[i];
                ctx.fillText(tekst, x + stupacSirina / 2, canvas.height - 10);
            }

        }
        </script>
</html>
