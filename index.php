<?php
// =============================================
// index.php - Početna stranica (LV4 PHP)
// Nadovezuje se na postojeći index.html dizajn
// =============================================

require_once 'includes/auth.php';
require_once 'includes/db.php';

$pdo      = getDB();
$korisnik = trenutniKorisnik();

// Statistike za prikaz
$brFilmova   = $pdo->query('SELECT COUNT(*) FROM filmovi')->fetchColumn();
$brKorisnika = $pdo->query('SELECT COUNT(*) FROM korisnici')->fetchColumn();

// Najnovije dodani filmovi (statička tablica ostaje u HTML-u ispod)
// Novih 5 za "nedavno dodano" sekciju
$najnoviji = $pdo->query('SELECT * FROM filmovi ORDER BY datum_dodavanja DESC LIMIT 5')->fetchAll();

$pageTitle    = 'Netflix Filmovi | LV4 PHP/MySQL';
$pageSubtitle = 'Pregled filmova';
require_once 'includes/header.php';
?>

<!-- Sekcija O stranici -->
<section aria-labelledby="o-stranici" class="fade-in">
  <h2 id="o-stranici">O ovoj stranici</h2>
  <p>
    Dobrodošli na Netflix katalog filmova — <strong>LV4 PHP/MySQL verzija</strong>.
    Stranica koristi serversku obradu podataka, MySQL bazu, autentifikaciju korisnika
    i osobnu videoteku za trajno pohranjivanje odabranih filmova.
  </p>
  <div style="display:flex;gap:.75rem;margin-top:1rem;flex-wrap:wrap;">
    <a href="films.php" class="btn-primary" role="button">🎬 Pregledaj filmove</a>
    <?php if (!jePrijavljen()): ?>
      <a href="login.php" class="btn-secondary" role="button">🔐 Prijavi se</a>
    <?php else: ?>
      <a href="videoteka.php" class="btn-secondary" role="button">📚 Moja videoteka</a>
    <?php endif; ?>
    <a href="grafikon.html" class="btn-secondary" role="button">📊 Grafikoni</a>
  </div>
</section>

<!-- Statistike -->
<div class="stat-grid fade-in">
  <div class="stat-box">
    <div class="stat-broj"><?= $brFilmova ?>+</div>
    <div class="stat-label">Filmova u bazi</div>
  </div>
  <div class="stat-box">
    <div class="stat-broj"><?= $brKorisnika ?></div>
    <div class="stat-label">Registriranih korisnika</div>
  </div>
  <div class="stat-box">
    <div class="stat-broj">PHP</div>
    <div class="stat-label">+ MySQL backend</div>
  </div>
</div>

<!-- Layout: tablica + aside -->
<div class="content-grid" style="margin-top:2rem;">

  <section class="table-section" aria-labelledby="popis-filmova">
    <h2 id="popis-filmova">Nedavno dodani filmovi</h2>
    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Naslov</th>
            <th>Žanr</th>
            <th>Godina</th>
            <th>Ocjena</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($najnoviji as $i => $film): ?>
            <tr>
              <td><?= $i + 1 ?></td>
              <td><strong><?= htmlspecialchars($film['naslov']) ?></strong></td>
              <td><span class="genre-badge"><?= htmlspecialchars($film['zanr']) ?></span></td>
              <td><?= $film['godina'] ?></td>
              <td><span class="ocjena-badge">⭐ <?= number_format((float)$film['ocjena'], 1) ?></span></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div style="margin-top:1rem;">
      <a href="films.php" class="btn-secondary">Prikaži sve filmove →</a>
    </div>
  </section>

  <!-- Aside s plakatima -->
  <aside role="complementary" aria-label="Istaknuti filmovi">
    <h3>Istaknuti</h3>
    <div class="aside-images">
      <img class="aside-img img-desktop"
           src="https://cdng.europosters.eu/pod_public/750/262718.jpg"
           alt="Istaknuti film - poster 1" loading="lazy" width="200" height="280">
      <img class="aside-img img-desktop"
           src="https://m.media-amazon.com/images/M/MV5BMjRlNjUwOGYtNGQxZS00ZjhkLTg0NDgtYjcwNzZlNDU2YjBlXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg"
           alt="Istaknuti film - poster 2" loading="lazy" width="200" height="280">
    </div>
  </aside>

</div>

<article aria-labelledby="vijesti">
  <h2 id="vijesti">Zanimljivosti o Netflix katalogu</h2>
  <p>
    Netflix katalog sadrži više od 6.000 filmova snimljenih u rasponu od 1940-ih do danas.
    Najzastupljeniji žanrovi su drame, komedije i akcijski filmovi. SAD je zemlja s najvećim
    brojem filmova u katalogu, a platforma je dostupna u više od 190 zemalja diljem svijeta.
  </p>
</article>

<?php require_once 'includes/footer.php'; ?>
