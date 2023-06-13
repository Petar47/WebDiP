<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Profil</title>
        <meta charset="UTF-8">
        <meta name="keywords" content="Profil">
        <meta name="author" content="Petar Martinović">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="../css/pmartinov.css"/>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
        <script src="//code.jquery.com/jquery-1.12.4.js"></script>
        <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="javascript/pmartinov_jquery.js"></script>
    </head>
    <header class="sakrij">
        <?php require '../privatno/funkcije.php';?>
    </header>
    <?php
    $x=new Baza();
    $x->spojiDB();
    $korisnik=Sesija::dajKorisnika();
    $korime=$korisnik["korisnik"];
    $rezultat=$x->selectDB("SELECT * FROM korisnik WHERE korisnicko_ime='$korime'")->fetch_assoc();
    
    echo "<h1>Moj profil</h1>";
    echo "<p>Slika: <img src='data:image/jpeg;base64," . base64_encode($rezultat['slika']) . "' alt='Slika'><br>Ime : " . $rezultat['ime'] . "<br>Prezime : " . $rezultat['prezime'] . "<br>Korisničko ime : " . $rezultat['korisnicko_ime'] . "<br>Email: " . $rezultat["email"] . "<br>Nadimak: " . $rezultat["nadimak"];
    if($rezultat['slika']==""||$rezultat['nadimak']==""){
        echo '<style>
                .sakrij {
                    display: none;
                }
            </style>';
        echo "<br>Morate unijeti sliku i nadimak da bi ste mogli kupovati proizvode";
        echo "<form id=\"aktivacija\" method=\"post\" name=\"aktivacija\" novalidate enctype=\"multipart/form-data\">"
        . "<label for=\"nadimak\">Nadimak: </label>
                <input type=\"text\" id=\"nadimak\" placeholder=\"Nadimak\" name=\"nadimak\"><br>
                <label for=\"slika\">Slika: </label>
                <input type=\"file\" id=\"slika\" name=\"slika\"><br><input type=\"submit\" name=\"aktivacija\" value=\"Aktiviraj\">
            </form>";
        $nadimak=filter_input(INPUT_POST, 'nadimak');
        if (isset($_FILES['slika'])){
            $slika=$_FILES["slika"]['name'];
        }
        $id=$rezultat["korisnik_id"];
        if (null !== filter_input(INPUT_POST, 'aktivacija')&&""!==$nadimak&&isset($_FILES['slika'])){
            $datumVrijeme = virtualnoVrijeme();
            $sql="UPDATE korisnik SET nadimak = '$nadimak', slika = '$slika' WHERE korisnicko_ime = '$korime'";
            $dnevnikakt="INSERT INTO dnevnik (radnja,datum_vrijeme,tip_dnevnika_tip_id,korisnik_korisnik_id) VALUES ('Unos slike i nadimka','$datumVrijeme',1,'$id')";
            $x->selectDB($sql);
            $x->selectDB($dnevnikakt);
            echo "Uspješno ste unijeli podatke";
            echo '<style>
                .sakrij {
                    display: block;
                }
            </style>';
        }
    }
    $x->zatvoriDB();
    ?>
</html>