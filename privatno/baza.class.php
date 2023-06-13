<?php

class Baza {

    const server = "localhost";
    const korisnik = "admin";
    const lozinka = "admin";
    const baza = "WebDiP2022x030";

    private $veza = null;
    private $greska = '';

    function spojiDB() {
        $this->veza = new mysqli(self::server, self::korisnik, self::lozinka, self::baza);
        if ($this->veza->connect_errno) {
            echo "Neuspješno spajanje na bazu: " . $this->veza->connect_errno . ", " .
            $this->veza->connect_error;
            $this->greska = $this->veza->connect_error;
        }
        $this->veza->set_charset("utf8");
        if ($this->veza->connect_errno) {
            echo "Neuspješno postavljanje znakova za bazu: " . $this->veza->connect_errno . ", " .
            $this->veza->connect_error;
            $this->greska = $this->veza->connect_error;
        }
        return $this->veza;
    }

    function selectDB($upit) {
        $rezultat = $this->veza->query($upit);
        if ($this->veza->connect_errno) {
            echo "Greška kod upita: {$upit} - " . $this->veza->connect_errno . ", " .
            $this->veza->connect_error;
            $this->greska = $this->veza->connect_error;
        }
        if (!$rezultat) {
            $rezultat = null;
        }
        return $rezultat;
    }

    function updateDB($upit, $skripta = '') {
        $rezultat = $this->veza->query($upit);
        if ($this->veza->connect_errno) {
            echo "Greška kod upita: {$upit} - " . $this->veza->connect_errno . ", " .
            $this->veza->connect_error;
            $this->greska = $this->veza->connect_error;
        } else {
            if ($skripta != '') {
                header("Location: $skripta");
            }
        }

        return $rezultat;
    }

    function registracija($ime, $prezime, $korisnickoIme, $lozinka, $email, $uvjeti_koristenja) {
        $slucajniBroj = mt_rand(10000, 99999);
        $upit = "SELECT * FROM korisnik WHERE email='$email'";
        $rezultat = $this->selectDB($upit);
        if ($rezultat->num_rows == 0) {
            $idKorisnika = "SELECT COUNT(*) FROM korisnik";
            $suma = $this->selectDB($idKorisnika)->fetch_assoc();
            $id= intval($suma["COUNT(*)"])+1;
            $datumVrijemeRegistracije = virtualnoVrijeme();
            $lozinkaHash = hash('sha256', $lozinka);
            $sql = "INSERT INTO korisnik (korisnik_id,ime, prezime, korisnicko_ime, lozinka, lozinkasha256, email, uvjeti_koristenja, datum_vrijeme_registracije, broj_unosa, status_racuna, tip_korisnika_tip_id, aktivacijskiKod) 
          VALUES ('$id','$ime', '$prezime', '$korisnickoIme', '$lozinka', '$lozinkaHash', '$email', '$uvjeti_koristenja', '$datumVrijemeRegistracije', 0, 0, 1, '$slucajniBroj')";
            $dnevnikreg= "INSERT INTO dnevnik (radnja,datum_vrijeme,tip_dnevnika_tip_id,korisnik_korisnik_id) VALUES ('Registracija','$datumVrijemeRegistracije',1,'$id')";
            $rezultat = $this->veza->query($sql);
            if ($this->veza->connect_errno) {
                echo "Greška kod upita: {$sql} - " . $this->veza->connect_errno . ", " .
                $this->veza->connect_error;
                $this->greska = $this->veza->connect_error;
            } else {
                $this->veza->query($dnevnikreg);
                echo 'Uspješno ste se registrirali, provjerite mail za aktivaciju računa!';
                $naslov = "Aktivacija korisničkog računa";
                $sadrzaj = "Vaš aktivacijski kod: " . $slucajniBroj;
                mail($email, $naslov, $sadrzaj);
            }
        } else {
            echo "Mail već postoji u bazi";
        }
    }

    function prijava($korime, $lozinka, $zapamti_me, $aktivacijskikod) {
        $upit = "SELECT korisnik_id,lozinkasha256,status_racuna,broj_unosa,aktivacijskiKod,datum_vrijeme_registracije,tip_korisnika_tip_id FROM korisnik WHERE korisnicko_ime='$korime'";
        $rezultat = $this->selectDB($upit);
        $lozinkaHash = hash('sha256', $lozinka);
        if ($rezultat->num_rows > 0) {
            $datumVrijeme = virtualnoVrijeme();
            $blokiran=0;
            $sifra = $rezultat->fetch_assoc();
            $id=$sifra["korisnik_id"];
            $trajanjekoda="SELECT trajanje_aktivacijskog_koda FROM konfiguracija";
            $trajanjeaktkoda=$this->selectDB($trajanjekoda)->fetch_assoc();
            $provjeraVremena = strtotime('-' .$trajanjeaktkoda["trajanje_aktivacijskog_koda"] . ' hours', strtotime(virtualnoVrijeme()));
            if ($sifra["aktivacijskiKod"] === $aktivacijskikod && strtotime($sifra["datum_vrijeme_registracije"]) > $provjeraVremena && $sifra["lozinkasha256"] === $lozinkaHash) {
                $aktivacija = "UPDATE korisnik SET status_racuna = 1 WHERE korisnicko_ime = '$korime'";
                $brisanjekoda = "UPDATE korisnik SET aktivacijskiKod = NULL WHERE korisnicko_ime = '$korime'";
                $dnevnikaktivacija = "INSERT INTO dnevnik (radnja,datum_vrijeme,tip_dnevnika_tip_id,korisnik_korisnik_id) VALUES ('Aktivacija računa','$datumVrijeme',1,'$id')";
                $this->updateDB($dnevnikaktivacija);
                $this->updateDB($aktivacija);
                $this->veza->query($brisanjekoda);
            }

            else if ($sifra["status_racuna"] === "0") {
                $dnevnikblokirani = "INSERT INTO dnevnik (radnja,datum_vrijeme,tip_dnevnika_tip_id,korisnik_korisnik_id) VALUES ('Pokušaj login blokiranog korisnika','$datumVrijeme',1,'$id')";
                $this->veza->query($dnevnikblokirani);
                $blokiran=1;
                echo "Blokiran vam je račun";
            } 
            if ($sifra["lozinkasha256"] !== $lozinkaHash&&!$blokiran) {
                echo "Kriva lozinka";
                $greska = "UPDATE korisnik SET broj_unosa = broj_unosa + 1 WHERE korisnicko_ime = '$korime'";
                $dnevnikkrivasifra = "INSERT INTO dnevnik (radnja,datum_vrijeme,tip_dnevnika_tip_id,korisnik_korisnik_id) VALUES ('Unos krive lozinke','$datumVrijeme',1,'$id')";
                $this->veza->query($dnevnikkrivasifra);
                $this->updateDB($greska);
                $maksimalnibrojprijava="SELECT maksimalni_broj_prijava FROM konfiguracija";
                $max=$this->selectDB($maksimalnibrojprijava)->fetch_assoc();
                if ($sifra["broj_unosa"] + 1 >= $max["maksimalni_broj_prijava"]) {
                    $blokirati = "UPDATE korisnik SET status_racuna = 0 WHERE korisnicko_ime = '$korime'";
                    $dnevnikblokada = "INSERT INTO dnevnik (radnja,datum_vrijeme,tip_dnevnika_tip_id,korisnik_korisnik_id) VALUES ('Blokiranje korisnika (previse unosa)','$datumVrijeme',1,'$id')";
                    $this->veza->query($dnevnikblokada);
                    $this->updateDB($blokirati);
                }
            } else if(!$blokiran) {
                if ($zapamti_me === "on") {
                    setcookie('korisnicko_ime', $korime, time() + (86400 * 30), '/');
                }
                $dnevniklogin = "INSERT INTO dnevnik (radnja,datum_vrijeme,tip_dnevnika_tip_id,korisnik_korisnik_id) VALUES ('Prijava','$datumVrijeme',1,'$id')";
                $this->veza->query($dnevniklogin);
                $reset = "UPDATE korisnik SET broj_unosa = 0 WHERE korisnicko_ime = '$korime'";
                $this->updateDB($reset);
                Sesija::kreirajKorisnika($korime, $sifra["tip_korisnika_tip_id"]);
                header("Location: ../korisnik/profil.php");
                exit();
            }
        } else {
            echo "Krivo korisničko ime";
        }
    }
    function generirajLozinku() {
        $znakovi = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=';
        $lozinka = '';

        for ($i = 0; $i < 10; $i++) {
            $index = rand(0, strlen($znakovi) - 1);
            $lozinka .= $znakovi[$index];
        }
        return $lozinka;
    }

    function zaboravljena($korime){
        $upit = "SELECT korisnik_id,email FROM korisnik WHERE korisnicko_ime='$korime'";
        $rezultat = $this->selectDB($upit);
        if($rezultat->num_rows>0){
            $idd=$rezultat->fetch_assoc();
            $id=$idd["korisnik_id"];
	    $email=$idd["email"];
            $datumVrijeme = virtualnoVrijeme();
            $novalozinka= $this->generirajLozinku();
            $hash=hash('sha256',$novalozinka);
            $novo = "UPDATE korisnik SET lozinka='$novalozinka',lozinkasha256='$hash' WHERE korisnicko_ime = '$korime'";
            $this->updateDB($novo);
            $dnevnikzaboravljena = "INSERT INTO dnevnik (radnja,datum_vrijeme,tip_dnevnika_tip_id,korisnik_korisnik_id) VALUES ('Reset lozinke','$datumVrijeme',1,'$id')";
            $this->veza->query($dnevnikzaboravljena);
            $naslov = "Nova lozinka";
            $sadrzaj = "Vaša nova lozinka: " . $novalozinka;
            mail($email, $naslov, $sadrzaj); 
            echo '<script>';
            echo 'window.location.href = "#";';
            echo '</script>';
            echo "Nova lozinka je poslana na mail!";
        } else{
            echo "Ne postoji taj username";
        }
    }

    function pogreskaDB() {
        if ($this->greska != '') {
            return true;
        } else {
            return false;
        }
    }

    function zatvoriDB() {
        $this->veza->close();
    }

}
