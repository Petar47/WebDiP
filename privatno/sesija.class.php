<?php
class Sesija {

    const KORISNIK = "korisnik";
    const ULOGA = "uloga"; // konstanta uloga
    const KOSARICA = "kosarica";
    const SESSION_NAME = "prijava_sesija";

    static function kreirajSesiju() {
         if (session_id() == "") {
            session_name(self::SESSION_NAME);
            session_start();
        }
    }

    static function kreirajKorisnika($korisnik, $uloga = 4) { // prosljedujemo ulogu korisnika
        self::kreirajSesiju();
        $_SESSION[self::KORISNIK] = $korisnik;
        $_SESSION[self::ULOGA] = $uloga;
    }

    static function kreirajKosaricu($kosarica) {
        self::kreirajSesiju();
        $_SESSION[self::KOSARICA] = $kosarica;
    }

    static function dajKorisnika() {
        self::kreirajSesiju();
        if (isset($_SESSION[self::KORISNIK])) {
            $korisnik[self::KORISNIK] = $_SESSION[self::KORISNIK];
            $korisnik[self::ULOGA] = $_SESSION[self::ULOGA];
        } else {
            return null;
        }
        return $korisnik; // vraca asocijativni niz korisnik
    }

    static function dajKosaricu() {
        self::kreirajSesiju();
        if (isset($_SESSION[self::KOSARICA])) {
            $kosarica = $_SESSION[self::KOSARICA];
        } else {
            return null;
        }
        return $kosarica;
    }

    /**
     * Odjavljuje korisnika tj. briše sesiju
     */
    static function obrisiSesiju() {
        session_name(self::SESSION_NAME);

        if (session_id() != "") {
            session_unset();
            session_destroy();
        }
    }

}