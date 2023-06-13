<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Statistika moderatora</title>
        <meta charset="UTF-8">
        <meta name="keywords" content="Statistika, Kampanje">
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
    <h1>Statistika moderatora</h1>
    <canvas id="myChart"></canvas>
    <script>
        var xhttp = new XMLHttpRequest();

        xhttp.onreadystatechange = function () {
            console.log(this.responseText);
            if (this.readyState === 4 && this.status === 200) {
                var response = JSON.parse(this.responseText);
                var kampanje = [];
                var proizvodi = [];
                var kolicinaKupljeno = [];
                for (var i = 0; i < response.length; i++) {
                    kampanje.push(response[i].kampanje);
                    proizvodi.push(response[i].proizvodi);
                    kolicinaKupljeno.push(response[i].kolicinaKupljeno);
                }
                graf(kampanje, kolicinaKupljeno, proizvodi);
            }
        };

        xhttp.open("GET", "./phpStatistika.php", true);
        xhttp.send();

        function graf(kampanje, kolicinaKupljeno, proizvodi) {
            var canvas = document.getElementById("myChart");
            var ctx = canvas.getContext("2d");

            canvas.width = 400;
            canvas.height = 300;

            var brojStupaca = kampanje.length;
            var maxKolicina = Math.max.apply(null, kolicinaKupljeno);
            var stupacSirina = canvas.width / brojStupaca;

            for (var i = 0; i < brojStupaca; i++) {
                var x = i * stupacSirina;
                var y = canvas.height - (kolicinaKupljeno[i] / maxKolicina) * canvas.height;
                var sirina = stupacSirina - 10;
                var visina = (kolicinaKupljeno[i] / maxKolicina) * canvas.height;

                ctx.fillStyle = "blue";
                ctx.fillRect(x, y, sirina, visina);

                ctx.fillStyle = "black";
                ctx.textAlign = "center";

                var tekst = kampanje[i] + ', ' + proizvodi[i] + '= '+ kolicinaKupljeno[i];
                ctx.fillText(tekst, x + stupacSirina / 2, canvas.height - 10);
            }

        }
    </script>
    <?php
    $x=new Baza();
    $x->spojiDB();
    $korisnik = Sesija::dajKorisnika();
    $korime = $korisnik["korisnik"];
    $kor = $x->selectDB("SELECT * FROM korisnik WHERE korisnicko_ime='$korime'")->fetch_assoc();
    $id = $kor["korisnik_id"];
    $sql = "SELECT c.naziv AS kampanja_naziv, p.naziv AS proizvod_naziv, COUNT(kp.proizvod_proizvod_id) AS kolicina_kupljeno
            FROM kampanja c
            RIGHT JOIN kampanja_proizvod kp ON c.kampanja_id = kp.kampanja_kampanja_id
            LEFT JOIN proizvod p ON kp.proizvod_proizvod_id = p.proizvod_id
            INNER JOIN kupnja k ON kp.kampanja_kampanja_id = k.kampanja_id AND kp.proizvod_proizvod_id = k.proizvod_id
            WHERE c.moderator_korisnik_id = '$id'
            GROUP BY c.kampanja_id, p.proizvod_id";
    $rez = $x->selectDB($sql);
    if ($rez->num_rows > 0) {
        echo "<table>
                <tr>
                    <th>Kampanja Naziv</th>
                    <th>Proizvod Naziv</th>
                    <th>Količina Kupljeno</th>
                </tr>";
        while ($red = $rez->fetch_assoc()) {
            echo "<tr>
                    <td>" . $red['kampanja_naziv'] . "</td>
                    <td>" . $red['proizvod_naziv'] . "</td>
                    <td>" . $red['kolicina_kupljeno'] . "</td>
                  </tr>";
        }

        echo "</table>";
    } else {
        echo "Nema rezultata.";
    }
    $x->zatvoriDB();
    ?>
</html>