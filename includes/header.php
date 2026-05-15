<?php
// =============================================
// includes/header.php - Zajednički layout
// =============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/auth.php';

$korisnik = trenutniKorisnik();
$aktivnaStr = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="hr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Netflix filmovi - LV4 PHP aplikacija">
  <link rel="stylesheet" href="styles/style.css">
  <link rel="stylesheet" href="styles/lv3.css">
  <link rel="stylesheet" href="styles/lv4.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
  <title><?= htmlspecialchars($pageTitle ?? 'Netflix Filmovi') ?></title>
</head>
<body>

<input type="checkbox" id="menu-toggle" class="menu-toggle" aria-hidden="true">

<header role="banner">
  <h1>
    <span class="accent">NET</span>FLIX
    <span style="color:var(--color-text-muted);font-size:1rem;font-weight:400;margin-left:.5rem;">
      | <?= htmlspecialchars($pageSubtitle ?? 'Filmovi') ?>
    </span>
  </h1>
  <?php if ($korisnik): ?>
    <div class="header-korisnik">
      <span class="header-ime">👤 <?= htmlspecialchars($korisnik['korisnicko_ime']) ?></span>
      <?php if ($korisnik['uloga'] === 'admin'): ?>
        <span class="badge-admin">ADMIN</span>
      <?php endif; ?>
      <a href="odjava.php" class="btn-odjava">Odjava</a>
    </div>
  <?php endif; ?>
  <label for="menu-toggle" class="menu-btn" aria-label="Otvori navigaciju">&#9776; Menu</label>
</header>

<div class="page-wrapper">

  <nav aria-label="Primarna navigacija" role="navigation">
    <ul>
      <li><a href="index.php" <?= $aktivnaStr==='index.php' ? 'class="active" aria-current="page"' : '' ?>>&#127916; Početna</a></li>
      <li><a href="films.php" <?= $aktivnaStr==='films.php' ? 'class="active" aria-current="page"' : '' ?>>🎬 Filmovi</a></li>
      <li><a href="grafikon.html" <?= $aktivnaStr==='grafikon.html' ? 'class="active" aria-current="page"' : '' ?>>&#128200; Grafikoni</a></li>
      <li><a href="galerija.php" <?= $aktivnaStr==='galerija.php' ? 'class="active" aria-current="page"' : '' ?>>&#128444; Galerija</a></li>
      <?php if (jePrijavljen()): ?>
        <li><a href="videoteka.php" <?= $aktivnaStr==='videoteka.php' ? 'class="active" aria-current="page"' : '' ?>>📚 Moja videoteka</a></li>
      <?php endif; ?>
      <?php if (jeAdmin()): ?>
        <li><a href="admin.php" <?= $aktivnaStr==='admin.php' ? 'class="active" aria-current="page"' : '' ?>>⚙️ Admin</a></li>
      <?php endif; ?>
      <?php if (!jePrijavljen()): ?>
        <li><a href="login.php" <?= $aktivnaStr==='login.php' ? 'class="active" aria-current="page"' : '' ?>>🔐 Prijava</a></li>
      <?php endif; ?>
    </ul>
  </nav>

  <main id="main-content">
