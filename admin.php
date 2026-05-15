<?php
// =============================================
// admin.php - Admin sučelje (CRUD filmova)
// =============================================

require_once 'includes/auth.php';
require_once 'includes/db.php';

zahtijevajAdmin();

$pdo    = getDB();
$poruka = '';
$greska = '';
$akcija = $_GET['akcija'] ?? 'popis'; // popis | dodaj | uredi | brisi

// ============================================
// VALIDACIJA FILMA (server-side)
// ============================================
function validirajFilm(array $d): array {
    $greske = [];
    if (strlen(trim($d['naslov'] ?? '')) < 1 || strlen(trim($d['naslov'])) > 200) {
        $greske[] = 'Naslov mora imati 1–200 znakova.';
    }
    if (empty($d['zanr'])) {
        $greske[] = 'Žanr je obavezan.';
    }
    $god = (int)($d['godina'] ?? 0);
    if ($god < 1888 || $god > (int)date('Y') + 2) {
        $greske[] = 'Godina mora biti između 1888 i ' . ((int)date('Y') + 2) . '.';
    }
    $traj = (int)($d['trajanje_min'] ?? 0);
    if ($traj < 1 || $traj > 600) {
        $greske[] = 'Trajanje mora biti između 1 i 600 minuta.';
    }
    $ocj = (float)($d['ocjena'] ?? -1);
    if ($ocj < 0.0 || $ocj > 10.0) {
        $greske[] = 'Ocjena mora biti između 0.0 i 10.0.';
    }
    return $greske;
}

// ---- DODAJ FILM ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dodaj_film'])) {
    $greske = validirajFilm($_POST);
    if ($greske) {
        $greska = implode(' ', $greske);
    } else {
        $stmt = $pdo->prepare('
            INSERT INTO filmovi (naslov, zanr, godina, trajanje_min, ocjena, reziser, zemlja_porijekla)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            trim($_POST['naslov']),
            trim($_POST['zanr']),
            (int)$_POST['godina'],
            (int)$_POST['trajanje_min'],
            (float)$_POST['ocjena'],
            trim($_POST['reziser'] ?? ''),
            trim($_POST['zemlja_porijekla'] ?? ''),
        ]);
        $poruka = 'Film "' . htmlspecialchars(trim($_POST['naslov'])) . '" uspješno dodan!';
        $akcija = 'popis';
    }
}

// ---- UREDI FILM ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['uredi_film'])) {
    $idFilma = (int)($_POST['id_film'] ?? 0);
    $greske  = validirajFilm($_POST);
    if ($greske) {
        $greska = implode(' ', $greske);
        $akcija = 'uredi';
    } else {
        $stmt = $pdo->prepare('
            UPDATE filmovi SET naslov=?, zanr=?, godina=?, trajanje_min=?, ocjena=?, reziser=?, zemlja_porijekla=?
            WHERE id=?
        ');
        $stmt->execute([
            trim($_POST['naslov']),
            trim($_POST['zanr']),
            (int)$_POST['godina'],
            (int)$_POST['trajanje_min'],
            (float)$_POST['ocjena'],
            trim($_POST['reziser'] ?? ''),
            trim($_POST['zemlja_porijekla'] ?? ''),
            $idFilma,
        ]);
        $poruka = 'Film uspješno ažuriran!';
        $akcija = 'popis';
    }
}

// ---- BRIŠI FILM ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['brisi_film'])) {
    $idFilma = (int)($_POST['id_film'] ?? 0);
    $stmt    = $pdo->prepare('DELETE FROM filmovi WHERE id = ?');
    $stmt->execute([$idFilma]);
    $poruka  = 'Film obrisan.';
}

// Dohvati film za uređivanje
$filmZaUredivanje = null;
if ($akcija === 'uredi' && isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM filmovi WHERE id = ?');
    $stmt->execute([(int)$_GET['id']]);
    $filmZaUredivanje = $stmt->fetch();
}

// Dohvati sve filmove
$filmovi = $pdo->query('SELECT * FROM filmovi ORDER BY naslov ASC')->fetchAll();

// Statistike
$brKorisnika = $pdo->query('SELECT COUNT(*) FROM korisnici')->fetchColumn();
$brFilmova   = count($filmovi);
$brVideoteka = $pdo->query('SELECT COUNT(*) FROM zeljeni_filmovi')->fetchColumn();

$pageTitle    = 'Admin | Netflix LV4';
$pageSubtitle = 'Admin panel';
require_once 'includes/header.php';
?>

<div class="lv3-section-header">
  <div class="lv4-badge">ADMIN</div>
  <h2>⚙️ Admin panel</h2>
</div>

<!-- Statistike -->
<div class="stat-grid fade-in">
  <div class="stat-box"><div class="stat-broj"><?= $brFilmova ?></div><div class="stat-label">Filmova</div></div>
  <div class="stat-box"><div class="stat-broj"><?= $brKorisnika ?></div><div class="stat-label">Korisnika</div></div>
  <div class="stat-box"><div class="stat-broj"><?= $brVideoteka ?></div><div class="stat-label">Dodavanja u videoteke</div></div>
</div>

<?php if ($poruka): ?>
  <div class="alert alert-success">✅ <?= htmlspecialchars($poruka) ?></div>
<?php endif; ?>
<?php if ($greska): ?>
  <div class="alert alert-error">⚠️ <?= htmlspecialchars($greska) ?></div>
<?php endif; ?>

<!-- Gumb za dodavanje -->
<div style="margin:1.5rem 0 1rem;">
  <a href="admin.php?akcija=dodaj" class="btn-primary">+ Dodaj novi film</a>
</div>

<?php if ($akcija === 'dodaj' || $akcija === 'uredi'): ?>
<!-- FORMA ZA DODAVANJE / UREĐIVANJE -->
<div class="admin-forma-box fade-in">
  <h3><?= $akcija === 'uredi' ? '✏️ Uredi film' : '➕ Dodaj novi film' ?></h3>
  <form method="POST" action="admin.php" class="auth-form" style="max-width:600px;" novalidate>
    <?php if ($akcija === 'uredi' && $filmZaUredivanje): ?>
      <input type="hidden" name="uredi_film" value="1">
      <input type="hidden" name="id_film" value="<?= $filmZaUredivanje['id'] ?>">
    <?php else: ?>
      <input type="hidden" name="dodaj_film" value="1">
    <?php endif; ?>

    <?php $f = $filmZaUredivanje ?? $_POST; ?>

    <div class="filteri-grid" style="grid-template-columns:1fr 1fr;">
      <div class="form-group">
        <label>Naslov *</label>
        <input type="text" name="naslov" value="<?= htmlspecialchars($f['naslov'] ?? '') ?>"
               maxlength="200" required placeholder="Naziv filma">
      </div>
      <div class="form-group">
        <label>Žanr *</label>
        <input type="text" name="zanr" value="<?= htmlspecialchars($f['zanr'] ?? '') ?>"
               maxlength="100" required placeholder="npr. Drama, Action">
      </div>
      <div class="form-group">
        <label>Godina * (1888–<?= date('Y') + 2 ?>)</label>
        <input type="number" name="godina" value="<?= htmlspecialchars($f['godina'] ?? date('Y')) ?>"
               min="1888" max="<?= date('Y') + 2 ?>" required>
      </div>
      <div class="form-group">
        <label>Trajanje (min) * (1–600)</label>
        <input type="number" name="trajanje_min" value="<?= htmlspecialchars($f['trajanje_min'] ?? '') ?>"
               min="1" max="600" required>
      </div>
      <div class="form-group">
        <label>Ocjena (0.0–10.0) *</label>
        <input type="number" name="ocjena" value="<?= htmlspecialchars($f['ocjena'] ?? '') ?>"
               min="0" max="10" step="0.1" required>
      </div>
      <div class="form-group">
        <label>Redatelj</label>
        <input type="text" name="reziser" value="<?= htmlspecialchars($f['reziser'] ?? '') ?>"
               maxlength="100" placeholder="Ime Prezime">
      </div>
      <div class="form-group">
        <label>Zemlja porijekla</label>
        <input type="text" name="zemlja_porijekla" value="<?= htmlspecialchars($f['zemlja_porijekla'] ?? '') ?>"
               maxlength="100" placeholder="npr. USA">
      </div>
    </div>

    <div style="display:flex;gap:.75rem;margin-top:1rem;">
      <button type="submit" class="btn-primary"><?= $akcija === 'uredi' ? '💾 Spremi' : '➕ Dodaj' ?></button>
      <a href="admin.php" class="btn-secondary">Odustani</a>
    </div>
  </form>
</div>
<?php endif; ?>

<!-- TABLICA SVIH FILMOVA -->
<div class="table-wrapper fade-in" style="margin-top:1rem;">
  <table>
    <thead>
      <tr>
        <th>ID</th><th>Naslov</th><th>Žanr</th><th>Godina</th>
        <th>Min</th><th>Ocjena</th><th>Zemlja</th><th>Akcije</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($filmovi as $film): ?>
        <tr>
          <td><?= $film['id'] ?></td>
          <td><strong><?= htmlspecialchars($film['naslov']) ?></strong></td>
          <td><span class="genre-badge"><?= htmlspecialchars($film['zanr']) ?></span></td>
          <td><?= $film['godina'] ?></td>
          <td><?= $film['trajanje_min'] ?></td>
          <td>
            <span class="ocjena-badge <?= (float)$film['ocjena'] < 5.0 ? 'ocjena-niska' : '' ?>">
              <?= number_format((float)$film['ocjena'], 1) ?>
            </span>
          </td>
          <td><?= htmlspecialchars($film['zemlja_porijekla'] ?? '—') ?></td>
          <td>
            <a href="admin.php?akcija=uredi&id=<?= $film['id'] ?>" class="btn-edit">✏️</a>
            <form method="POST" action="admin.php" style="display:inline;"
                  onsubmit="return confirm('Brisanjem filma uklonit ćete ga iz svih videoteka. Nastaviti?')">
              <input type="hidden" name="brisi_film" value="1">
              <input type="hidden" name="id_film" value="<?= $film['id'] ?>">
              <button type="submit" class="btn-delete">🗑️</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require_once 'includes/footer.php'; ?>
