<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Početna</title>
        <meta charset="UTF-8">
        <meta name="keywords" content="Početna stranica, Kolačići">
        <meta name="author" content="Petar Martinović">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="css/pmartinov.css"/>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
        <script src="//code.jquery.com/jquery-1.12.4.js"></script>
        <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    </head>
    <header>
        <div id="sadrzaj" style="display: none;">
            <?php require './privatno/funkcije.php'; ?>
        </div>
    </header>
    <section>
        <h1>Početna</h1>
        <p><a href="dokumentacija.html">Dokumentacija</a></p>
        <p id="uvjeti">Prihvaćate li uvjete korištenja?</p>
        <button id="prihvatiUvjete">Prihvati</button>
        
        <script>
            function postaviKolacic(naziv, vrijednost, trajanje) {
                var datum = new Date();
                datum.setTime(datum.getTime() + (trajanje * 24 * 60 * 60 * 1000));
                var istjece = "expires=" + datum.toUTCString();
                document.cookie = naziv + "=" + vrijednost + "; " + istjece + "; path=/";
            }
            function provjeriPristanak() {
                var korisnikPrihvatio = document.cookie.includes("uvjetiPrihvaceni=true");
                if (korisnikPrihvatio) {
                    console.log("Korisnik je već prihvatio uvjete korištenja.");
                    prikaziSadrzaj();
                }
            }
            function prihvatiUvjete() {
                postaviKolacic("uvjetiPrihvaceni", "true", 2);
                console.log("Korisnik je prihvatio uvjete korištenja.");
                prikaziSadrzaj();
                sakrijUvjete();
            }
            function prikaziSadrzaj() {
                document.getElementById("sadrzaj").style.display = "block";
                sakrijUvjete();
            }
            function sakrijUvjete() {
                document.getElementById("prihvatiUvjete").style.display = "none";
                document.getElementById("uvjeti").style.display = "none";
            }
            provjeriPristanak();
            document.getElementById("prihvatiUvjete").addEventListener("click", prihvatiUvjete);
            /*Funkcija za brisanje kolačića
            function obrisiKolacic(naziv) {
              document.cookie = naziv + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            }
            obrisiKolacic("uvjetiPrihvaceni");*/
        </script>
    </section>
</html>
