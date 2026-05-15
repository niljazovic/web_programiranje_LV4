<?php
// =============================================
// includes/auth.php - Autentifikacija
// =============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

/**
 * Vraća trenutno prijavljenog korisnika ili null
 */
function trenutniKorisnik(): ?array {
    return $_SESSION['korisnik'] ?? null;
}

/**
 * Je li korisnik prijavljen?
 */
function jePrijavljen(): bool {
    return isset($_SESSION['korisnik']);
}

/**
 * Je li korisnik admin?
 */
function jeAdmin(): bool {
    return ($_SESSION['korisnik']['uloga'] ?? '') === 'admin';
}

/**
 * Preusmjeri na login ako nije prijavljen
 */
function zahtijevajPrijavu(): void {
    if (!jePrijavljen()) {
        header('Location: login.php?poruka=morate_se_prijaviti');
        exit;
    }
}

/**
 * Preusmjeri ako nije admin
 */
function zahtijevajAdmin(): void {
    zahtijevajPrijavu();
    if (!jeAdmin()) {
        header('Location: index.php?poruka=nema_pristupa');
        exit;
    }
}

/**
 * Registracija novog korisnika
 * Vraća ['ok' => true] ili ['greska' => 'poruka']
 */
function registrirajKorisnika(string $ime, string $email, string $lozinka): array {
    // Validacija
    if (strlen($ime) < 3 || strlen($ime) > 50) {
        return ['greska' => 'Korisničko ime mora imati između 3 i 50 znakova.'];
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['greska' => 'Neispravan format email adrese.'];
    }
    if (strlen($lozinka) < 6) {
        return ['greska' => 'Lozinka mora imati najmanje 6 znakova.'];
    }

    $pdo = getDB();

    // Provjeri postoji li već korisnik
    $stmt = $pdo->prepare('SELECT id FROM korisnici WHERE korisnicko_ime = ? OR email = ?');
    $stmt->execute([$ime, $email]);
    if ($stmt->fetch()) {
        return ['greska' => 'Korisničko ime ili email već postoji.'];
    }

    // Spremi s hashiranom lozinkom
    $hash = password_hash($lozinka, PASSWORD_BCRYPT, ['cost' => 12]);
    $stmt = $pdo->prepare('INSERT INTO korisnici (korisnicko_ime, email, lozinka, uloga) VALUES (?, ?, ?, "korisnik")');
    $stmt->execute([$ime, $email, $hash]);

    return ['ok' => true];
}

/**
 * Prijava korisnika
 * Vraća ['ok' => true] ili ['greska' => 'poruka']
 */
function prijaviKorisnika(string $ime, string $lozinka): array {
    if (empty($ime) || empty($lozinka)) {
        return ['greska' => 'Unesite korisničko ime i lozinku.'];
    }

    $pdo = getDB();
    $stmt = $pdo->prepare('SELECT * FROM korisnici WHERE korisnicko_ime = ?');
    $stmt->execute([$ime]);
    $korisnik = $stmt->fetch();

    if (!$korisnik || !password_verify($lozinka, $korisnik['lozinka'])) {
        return ['greska' => 'Neispravno korisničko ime ili lozinka.'];
    }

    // Regeneriraj session ID radi sigurnosti
    session_regenerate_id(true);

    $_SESSION['korisnik'] = [
        'id'              => $korisnik['id'],
        'korisnicko_ime'  => $korisnik['korisnicko_ime'],
        'email'           => $korisnik['email'],
        'uloga'           => $korisnik['uloga'],
    ];

    return ['ok' => true];
}

/**
 * Odjava korisnika
 */
function odjaviKorisnika(): void {
    $_SESSION = [];
    session_destroy();
    header('Location: login.php');
    exit;
}
