function containsUppercase(str) {
    return /[A-Z]/.test(str);
}
function containsLowercase(str) {
    return /[a-z]/.test(str);
}
function hasNumber(myString) {
    return /\d/.test(myString);
}
function unosImena() {
    if (document.getElementById('ime').value.length > 0 && document.getElementById('ime').value.charAt(0) === document.getElementById('ime').value.charAt(0).toUpperCase()) {
        document.getElementById('prezime').style.visibility = 'visible';
    } else {
        console.log("Ime ne počinje velikim slovom.");
    }
}
function unosPrezimena() {
    if (document.getElementById('prezime').value.length > 0 && document.getElementById('prezime').value.charAt(0) === document.getElementById('prezime').value.charAt(0).toUpperCase()) {
        document.getElementById('korime').style.visibility = 'visible';
    } else {
        console.log("Prezime ne počinje velikim slovom.");
    }
}
function unosKorIme() {
    if (!containsUppercase(document.getElementById('korime').value) && document.getElementById('korime').value.length > 0) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '../autentifikacija/provjera_korisnickog_imena.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onload = function () {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.postoji) {
                    console.log('Korisničko ime već postoji u bazi podataka.');
                } else {
                    console.log('Korisničko ime je dostupno.');
                    document.getElementById('email').style.visibility = 'visible';
                }
            } else {
                console.log('Došlo je do pogreške prilikom provjere korisničkog imena.');
            }
        };
        xhr.send(JSON.stringify({korisnickoIme: document.getElementById('korime').value}));
    } else {
        console.log("Korisničko ime treba sadržavati samo mala slova!");
    }
}
function unosMaila() {
    const regex = /(?!.*[@.]{2,})(?!.*\.$)(?!.*\.\.)(?!.*[._-]{2,})[a-zA-Z0-9._+-]{1,64}@[a-zA-Z0-9.-]{1,253}\.(com|hr|info)$/;
    if (regex.test(document.getElementById('email').value)) {
        document.getElementById('lozinka').style.visibility = 'visible';
    } else {
        console.log('Mail nije u dobrom formatu');
    }
}
function unosLozinke() {
    const lozinka = document.getElementById('lozinka').value;
    const duljina = lozinka.length;
    var prolaz = 1;
    if (duljina < 10 || duljina > 25) {
        prolaz = 0;
        console.log("Lozinka treba imati između 10 i 25 znakova.");
    }
    if (!/[A-Z]/.test(lozinka)) {
        prolaz = 0;
        console.log("Lozinka treba imati veliko slovo.");
    }
    if (!/[a-z]/.test(lozinka)) {
        prolaz = 0;
        console.log("Lozinka treba imati malo slovo.");
    }
    if (!/[0-9]/.test(lozinka)) {
        prolaz = 0;
        console.log("Lozinka treba imati broj.");
    }
    if (/^[=*\/%]|[\s]|[*\/%=]$/.test(lozinka)) {
        prolaz = 0;
        console.log("Lozinka treba imati specijalni znak.");
    }
    if (prolaz === 1) {
        document.getElementById('ponovljenalozinka').style.visibility = 'visible';
    }
}
function unosPonovljeneLozinke() {
    if (document.getElementById('lozinka').value.length > 0 && document.getElementById('ponovljenalozinka').value === document.getElementById('lozinka').value) {
        document.getElementById('osnovni').style.visibility = 'visible';
        document.getElementById('sve').style.visibility = 'visible';
    } else {
        console.log('Lozinke se ne podudaraju');
    }
}
function unosUvjetaKoristenja() {
    if (document.getElementById('osnovni').checked || document.getElementById('sve').checked) {
        document.getElementById('submit').style.visibility = 'visible';
        document.getElementById('dalje').style.visibility = 'hidden';
    } else {
        console.log('Prihvatite uvjete korištenja');
    }
}
function Dalje() {
    unosImena();
    unosPrezimena();
    unosKorIme();
    unosMaila();
    unosLozinke();
    unosPonovljeneLozinke();
    unosUvjetaKoristenja();
}