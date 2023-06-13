<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Registracija</title>
        <meta charset="UTF-8">
        <meta name="keywords" content="Registracija, Prijava">
        <meta name="author" content="Petar Martinović">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="../css/pmartinov.css"/>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <script src="../javascript/pmartinov.js"></script>
        <script src="//code.jquery.com/jquery-1.12.4.js"></script>
        <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    </head>
    <header>
        <?php include '../privatno/funkcije.php'; ?>
    </header>
    <section>
        <h1>Registracija korisnika</h1>
        <ul>
            <li>Ime i prezime treba početi sa velikim početnim slovom.</li>
            <li>Korisničko ime treba sadržavati samo mala slova i treba biti unikatno</li>
            <li>Email treba biti ispravan.</li>
            <li>Lozinka treba imati minimalno 10 znakova i maksimalno 25, treba sadržavati veliko, malo slovo, broj i poseban znak.</li>
            <li>Lozinke se trebaju podudarati.</li>
            <li>Morate prihvatiti uvjete korištenja.</li>
        </ul>
        <form id="registracija" method="post" name="registracija" novalidate>
            <p><label for="ime">Ime: </label>
                <input type="text" id="ime" placeholder="Ime" name="ime"><br>
                <label for="prezime">Prezime: </label>
                <input type="text" id="prezime" placeholder="Prezime" name="prezime"><br>
                <label for="korime">Korisničko ime: </label>
                <input type="text" id="korime" placeholder="Korisničko ime" name="korime"><br>
                <label for="email">Email: </label>
                <input type="email" id="email" placeholder="Email" name="email"><br>
                <label for="lozinka">Lozinka: </label>
                <input type="password" id="lozinka" placeholder="Lozinka" name="lozinka"><br>
                <label for="ponovljenalozinka">Ponovljena lozinka: </label>
                <input type="password" id="ponovljenalozinka" placeholder="Ponovljena lozinka" name="ponovljenalozinka"><br>
                <label>Uvjeti korištenja: </label>
                <input type="radio" id="osnovni" name="uvjeti" value="osnovni">
                <label for="osnovni">Osnovni</label>
                <input type="radio" id="sve" name="uvjeti" value="sve">
                <label for="sve">Sve</label><br>
            <div class="g-recaptcha" data-sitekey="6LfZHy0mAAAAAEJklbW0ipK1fkbPSY7Q6LgDOXC2"></div>
            <input type="submit" value="Registiraj se" name="submit" id="submit">
            <input type="button" value="Dalje" name="dalje" id="dalje" onclick="Dalje();">
            </p>
            <?php
            if (isset($_COOKIE['korisnicko_ime'])) {
                $prethodno_prijavljeni = $_COOKIE['korisnicko_ime'];
            } else {
                $prethodno_prijavljeni = '';
            }

            if (filter_input(INPUT_POST, 'prijavasubmit') !== null) {
                $baza = new Baza();
                $baza->spojiDB();
                $korisnickoimee = filter_input(INPUT_POST, 'korisnickoime');
                $sifraprijava = filter_input(INPUT_POST, 'lozinkaprijava');
                $zapamti_me = filter_input(INPUT_POST, 'zapamti_me');
                $aktivacijskikod=filter_input(INPUT_POST, 'aktivacijskikod');
                $baza->prijava($korisnickoimee, $sifraprijava, $zapamti_me,$aktivacijskikod);
                $baza->zatvoriDB();
            }
            if (filter_input(INPUT_POST, 'zaboravljenalozinka') !== null) {
                $baza = new Baza();
                $baza->spojiDB();
                $zkorisnickoime = filter_input(INPUT_POST, 'zkorime');
                $baza->zaboravljena($zkorisnickoime);
                $baza->zatvoriDB();
            }
            ?>
        </form>
        <div class="prijava">
                <p>Već imate račun? Prijava:</p>
                <form id="prijava" method="post" name="prijava" action="" novalidate>
                    <p><label for="korisnickoime">Korisničko ime: </label>
                        <input type="text" id="korisnickoime" name="korisnickoime" placeholder="Korisničko ime" value="<?php echo $prethodno_prijavljeni; ?>"><br>
                        <label for="lozinkaprijava">Lozinka: </label>
                        <input type="password" id="lozinkaprijava" name="lozinkaprijava" placeholder="Lozinka"><br>
                        <label for="aktivacijskikod">Aktivacijski kod (Ako se prvi put prijavljujete): </label>
                        <input type="password" id="aktivacijskikod" name="aktivacijskikod" placeholder="Kod je u mailu"><br>
                        <input type="checkbox" id="zapamti_me" name="zapamti_me" ><label for="zapamti_me">Zapamti me</label>
                        <input type="submit" value="Prijavi se" name="prijavasubmit">
                    </p>
                </form>
                <a class="button" href="#zaboravljena">Zaboravili ste lozinku?</a>
                <div id="zaboravljena" class="overlay">
                    <div class="popup">
                        <p>Zaboravljena lozinka:</p>
                        <form id="zaboravljena" method="post" name="zaboravljena" action="" novalidate=>
                            <p><label for="zkorime">Korisničko ime: </label>
                                <input type="text" id="zkorime" name="zkorime" placeholder="Korisničko ime"><br>
                                <input type="submit" value="Pošalji" name="zaboravljenalozinka">
                            </p>
                        </form>
                    </div>
                <a class="zatvori" href="#">Zatvori</a>
            </div>
        </div>

        <script>
            document.getElementById('prezime').style.visibility = 'hidden';
            document.getElementById('korime').style.visibility = 'hidden';
            document.getElementById('lozinka').style.visibility = 'hidden';
            document.getElementById('ponovljenalozinka').style.visibility = 'hidden';
            document.getElementById('email').style.visibility = 'hidden';
            document.getElementById('osnovni').style.visibility = 'hidden';
            document.getElementById('sve').style.visibility = 'hidden';
            document.getElementById('submit').style.visibility = 'hidden';
        </script>
        <?php
        if (null !== filter_input(INPUT_POST, 'submit')) {
            $ime = filter_input(INPUT_POST, 'ime');
            if (ucfirst($ime) !== $ime && $ime !== "") {
                $prolaz = 0;
                echo "Ime treba početi velikim početnim slovom!";
            } else {
                $prolaz = 1;
            }
            $prezime = filter_input(INPUT_POST, 'prezime');
            if (ucfirst($prezime) !== $prezime && $prezime !== "") {
                $prolaz = 0;
                echo "Prezime treba početi velikim početnim slovom!";
            }
            $korisnickoIme = filter_input(INPUT_POST, 'korime');
            if ($korisnickoIme !== "" && !ctype_lower($korisnickoIme)) {
                $prolaz = 0;
                echo "Korisničko ime treba sadržavati samo mala slova";
            }
            $emaill = filter_input(INPUT_POST, 'email');
            $regex = '/(?!.*[@.]{2,})(?!.*\.$)(?!.*\.\.)(?!.*[._-]{2,})[a-zA-Z0-9._+-]{1,64}@[a-zA-Z0-9.-]{1,253}\.(com|hr|info)$/';
            if (!preg_match($regex, $emaill)) {
                $prolaz = 0;
                echo "Email adresa nije ispravnog formata.";
            }
            $password = filter_input(INPUT_POST, 'lozinka');
            $passwordLength = strlen($password);
            if ($passwordLength < 10 || $passwordLength > 25) {
                $prolaz = 0;
                echo "Lozinka treba imati između 10 i 25 znakova";
            }
            if (!preg_match('/[A-Z]/', $password)) {
                $prolaz = 0;
                echo "Lozinka treba imati veliko slovo";
            }
            if (!preg_match('/[a-z]/', $password)) {
                $prolaz = 0;
                echo "Lozinka treba imati malo slovo";
            }
            if (!preg_match('/[0-9]/', $password)) {
                $prolaz = 0;
                echo "Lozinka treba imati broj";
            }
            if (preg_match('/^[=*\/%]|[\s]|[*\/%=]$/', $password)) {
                $prolaz = 0;
                echo "Lozinka treba imati specijalni znak";
            }
            $ponovljena = filter_input(INPUT_POST, 'ponovljenalozinka');
            if ($password !== $ponovljena) {
                $prolaz = 0;
                echo "Lozinke se ne podudaraju";
            }
            if (strlen($ime) !== 0 && !empty(filter_input(INPUT_POST, 'uvjeti')) && $prolaz !== 0) {
                $recaptcha_response = filter_input(INPUT_POST, 'g-recaptcha-response');
                $site_key = '6LfZHy0mAAAAAEJklbW0ipK1fkbPSY7Q6LgDOXC2';
                $secret_key = '6LfZHy0mAAAAAKj1qQ6_9PUfMgf4N1lUuwh-F0Zn';
                $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
                $recaptcha_data = array(
                    'secret' => $secret_key,
                    'response' => $recaptcha_response
                );

                $options = array(
                    'http' => array(
                        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                        'method' => 'POST',
                        'content' => http_build_query($recaptcha_data)
                    )
                );

                $context = stream_context_create($options);
                $recaptcha_result = file_get_contents($recaptcha_url, false, $context);
                $recaptcha_result_json = json_decode($recaptcha_result, true);
                if ($recaptcha_result_json['success']) {
                    $baza = new Baza();
                    $baza->spojiDB();
                    $baza->registracija($ime, $prezime, $korisnickoIme, $password, $emaill, substr(filter_input(INPUT_POST, 'uvjeti'), 0, 1));
                    $baza->zatvoriDB();
                }
            }
        }
        ?>             
    </section>
</html>