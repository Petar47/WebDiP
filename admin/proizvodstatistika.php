<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Proizvodi i statistika</title>
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
    <input type="button" value="Kreiraj novi proizvod" onclick="location='noviProizvod.php'" /><br>
    <?php
    $x=new Baza();
    $x->spojiDB();
    $popisproizvoda=$x->selectDB("SELECT * FROM proizvod");
    if($popisproizvoda->num_rows>0){
        while($red=$popisproizvoda->fetch_assoc()){
            $id=$red["proizvod_id"];
            echo "Naziv:" . $red["naziv"] . " Opis:" . $red["opis"] . " Količina:" . $red["kolicina"] . " Cijena:" . $red["cijena"] . " Status:" . $red["status"];
            echo "<form action=\"urediProizvod.php\" method=\"post\" novalidate>
                       <input type=\"hidden\" name=\"id\" value=$id> 
                        <input type=\"submit\" name=\"uredi\" value=\"Uredi proizvod\"><br><br>
                        </form>";
        }
    }
    ?>
    <h2>Statistika proizvoda po moderatoru</h2>
    <form id="sortt" method="post" name="sortt" novalidate>
            <label for="soritiraj">Sortiraj po proizvodima:</label>
            <select name="sortiraj" id="sortiraj">
                <option value="silazno">Silazno</option>
                <option value="uzlazno">Uzlazno</option>
                </select>
            <label for="zbroj">Najmanje proizvoda:</label>
            <input type="text" id="zbroj" name="zbroj" value="0"></input>
            <input type="submit" name="sortt" value="Sortiraj i/ili filtriraj">
    </form>
    <?php
        $sql="SELECT k.korisnicko_ime, COUNT(ku.kampanja_id) AS broj_prodanih_proizvoda
                FROM korisnik k
                LEFT JOIN kampanja ka ON k.korisnik_id = ka.moderator_korisnik_id
                LEFT JOIN kupnja ku ON ka.kampanja_id = ku.kampanja_id
                WHERE k.tip_korisnika_tip_id = 2
                GROUP BY k.korisnicko_ime
                ORDER BY broj_prodanih_proizvoda DESC";
        if (null !== filter_input(INPUT_POST, 'sortt')) {
            $datumVrijeme = virtualnoVrijeme();
            $zbroj= filter_input(INPUT_POST, 'zbroj');
            if(filter_input(INPUT_POST, 'sortiraj')=='uzlazno'){
                $sql="SELECT k.korisnicko_ime, COUNT(ku.kampanja_id) AS broj_prodanih_proizvoda
                        FROM korisnik k
                        LEFT JOIN kampanja ka ON k.korisnik_id = ka.moderator_korisnik_id
                        LEFT JOIN kupnja ku ON ka.kampanja_id = ku.kampanja_id
                        WHERE k.tip_korisnika_tip_id = 2
                        GROUP BY k.korisnicko_ime
                        HAVING COUNT(ku.kampanja_id) >= '$zbroj'";
                $dnevnikfilter="INSERT INTO dnevnik (upit,datum_vrijeme,tip_dnevnika_tip_id) VALUES ('SELECT k.korisnicko_ime, COUNT(ku.kampanja_id) AS broj_prodanih_proizvoda
                        FROM korisnik k
                        LEFT JOIN kampanja ka ON k.korisnik_id = ka.moderator_korisnik_id
                        LEFT JOIN kupnja ku ON ka.kampanja_id = ku.kampanja_id
                        WHERE k.tip_korisnika_tip_id = 2
                        GROUP BY k.korisnicko_ime
                        HAVING COUNT(ku.kampanja_id) >= \'$zbroj\'','$datumVrijeme',2)";
            } else{
                $sql="SELECT k.korisnicko_ime, COUNT(ku.kampanja_id) AS broj_prodanih_proizvoda
                        FROM korisnik k
                        LEFT JOIN kampanja ka ON k.korisnik_id = ka.moderator_korisnik_id
                        LEFT JOIN kupnja ku ON ka.kampanja_id = ku.kampanja_id
                        WHERE k.tip_korisnika_tip_id = 2
                        GROUP BY k.korisnicko_ime
                        HAVING COUNT(ku.kampanja_id) >= '$zbroj'
                        ORDER BY broj_prodanih_proizvoda DESC";
                $dnevnikfilter="INSERT INTO dnevnik (upit,datum_vrijeme,tip_dnevnika_tip_id) VALUES ('SELECT k.korisnicko_ime, COUNT(ku.kampanja_id) AS broj_prodanih_proizvoda
                        FROM korisnik k
                        LEFT JOIN kampanja ka ON k.korisnik_id = ka.moderator_korisnik_id
                        LEFT JOIN kupnja ku ON ka.kampanja_id = ku.kampanja_id
                        WHERE k.tip_korisnika_tip_id = 2
                        GROUP BY k.korisnicko_ime
                        HAVING COUNT(ku.kampanja_id) >= \'$zbroj\'
                        ORDER BY broj_prodanih_proizvoda DESC','$datumVrijeme',2)";
            }
            $x->selectDB($dnevnikfilter);
        }
        $rezultat = $x->selectDB($sql);
        echo "<table>";
        echo "<tr><th>Korisničko ime</th><th>Broj prodanih proizvoda</th></tr>";
        while ($red = $rezultat->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $red['korisnicko_ime'] . "</td>";
            echo "<td>" . $red['broj_prodanih_proizvoda'] . "</td>";
            echo "</tr>";
        }

        echo "</table>";
        $x->zatvoriDB();
    ?>
</html>