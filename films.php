<?php
// =============================================
// films.php - Prikaz filmova s filtriranjem
// LV4: Server-side SQL filtriranje + videoteka
// =============================================

require_once 'includes/auth.php';
require_once 'includes/db.php';

$pdo      = getDB();
$korisnik = trenutniKorisnik();
$poruka   = '';
$greska   = '';

// ---- DODAJ U VIDEOTEKU (POST) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dodaj_film'])) {
    zahtijevajPrijavu();
    $idFilma = (int)($_POST['id_film'] ?? 0);

    // Dohvati ocjenu filma
    $stmtFilm = $pdo->prepare('SELECT ocjena, naslov FROM filmovi WHERE id = ?');
    $stmtFilm->execute([$idFilma]);
    $film = $stmtFilm->fetch();

    if ($film) {
        // Provjeri duplikat
        $stmtChk = $pdo->prepare('SELECT id FROM zeljeni_filmovi WHERE id_korisnik = ? AND id_film = ?');
        $stmtChk->execute([$korisnik['id'], $idFilma]);
        if ($stmtChk->fetch()) {
            $greska = '"' . htmlspecialchars($film['naslov']) . '" je već u Vašoj videoteci!';
        } else {
            $stmtIns = $pdo->prepare('INSERT INTO zeljeni_filmovi (id_korisnik, id_film) VALUES (?, ?)');
            $stmtIns->execute([$korisnik['id'], $idFilma]);

            // Upozorenje za nisku ocjenu
            if ((float)$film['ocjena'] < 5.0) {
                $poruka = 'warning:Film "' . htmlspecialchars($film['naslov']) . '" dodan, ali ima nisku prosječnu ocjenu (' . $film['ocjena'] . '/10)!';
            } else {
                $poruka = 'success:"' . htmlspecialchars($film['naslov']) . '" uspješno dodan u Vašu videoteku!';
            }
        }
    }
}

// ---- FILTRIRANJE (SQL server-side) ----
$filterZanr    = trim($_GET['zanr'] ?? '');
$filterNaslov  = trim($_GET['naslov'] ?? '');
$filterGodina  = trim($_GET['godina'] ?? '');
$filterZemlja  = trim($_GET['zemlja'] ?? '');
$sortBy        = $_GET['sort'] ?? 'naslov';
$sortDir       = $_GET['dir'] ?? 'ASC';

// Validacija sorta
$dopusteniSort = ['naslov', 'godina', 'ocjena', 'trajanje_min', 'zanr'];
$dopusteniDir  = ['ASC', 'DESC'];
if (!in_array($sortBy, $dopusteniSort))  $sortBy  = 'naslov';
if (!in_array($sortDir, $dopusteniDir)) $sortDir = 'ASC';

// Izgradnja upita s prepared statements
$uvjeti  = [];
$params  = [];

if ($filterZanr !== '') {
    $uvjeti[] = 'zanr LIKE ?';
    $params[]  = '%' . $filterZanr . '%';
}
if ($filterNaslov !== '') {
    $uvjeti[] = 'naslov LIKE ?';
    $params[]  = '%' . $filterNaslov . '%';
}
if ($filterGodina !== '' && is_numeric($filterGodina)) {
    $uvjeti[] = 'godina = ?';
    $params[]  = (int)$filterGodina;
}
if ($filterZemlja !== '') {
    $uvjeti[] = 'zemlja_porijekla LIKE ?';
    $params[]  = '%' . $filterZemlja . '%';
}

$whereSQL = $uvjeti ? 'WHERE ' . implode(' AND ', $uvjeti) : '';
$sql      = "SELECT * FROM filmovi $whereSQL ORDER BY $sortBy $sortDir";
$stmt     = $pdo->prepare($sql);
$stmt->execute($params);
$filmovi  = $stmt->fetchAll();

// Dohvati sve jedinstvene žanrove za dropdown
$zanrovi = $pdo->query("SELECT DISTINCT zanr FROM filmovi ORDER BY zanr")->fetchAll(PDO::FETCH_COLUMN);

// Dohvati set filmova korisnika (za "Dodano" status)
$videotekaSet = [];
if ($korisnik) {
    $stmtV = $pdo->prepare('SELECT id_film FROM zeljeni_filmovi WHERE id_korisnik = ?');
    $stmtV->execute([$korisnik['id']]);
    $videotekaSet = array_column($stmtV->fetchAll(), 'id_film');
    $videotekaSet = array_flip($videotekaSet); // za O(1) lookup
}

// Helper za sortiranje URL-ova
function sortUrl(string $stupac): string {
    $dir = ($_GET['sort'] ?? '') === $stupac && ($_GET['dir'] ?? 'ASC') === 'ASC' ? 'DESC' : 'ASC';
    $params = array_merge($_GET, ['sort' => $stupac, 'dir' => $dir]);
    return 'films.php?' . http_build_query($params);
}
function sortArrow(string $stupac): string {
    if (($_GET['sort'] ?? '') !== $stupac) return '';
    return ($_GET['dir'] ?? 'ASC') === 'ASC' ? ' ▲' : ' ▼';
}

$pageTitle    = 'Filmovi | Netflix LV4';
$pageSubtitle = 'Filmovi';
require_once 'includes/header.php';
?>

<?php if ($poruka): ?>
  <?php [$tip, $tekst] = explode(':', $poruka, 2); ?>
  <div class="alert alert-<?= $tip ?>"><?= $tip === 'warning' ? '⚠️' : '✅' ?> <?= $tekst ?></div>
<?php endif; ?>
<?php if ($greska): ?>
  <div class="alert alert-error">⚠️ <?= $greska ?></div>
<?php endif; ?>

<div class="lv3-section-header" style="margin-bottom:1.5rem;">
  <div class="lv4-badge">LV4 &mdash; PHP/MySQL</div>
  <h2>Popis filmova</h2>
  <p class="lv3-opis">Filtriranje i sortiranje izvodi se <strong>server-side SQL upitima</strong>.</p>
</div>

<!-- FILTERI -->
<form method="GET" action="films.php" class="filteri-wrapper fade-in">
  <div class="filteri-grid">

    <div class="filter-group">
      <label for="f-naslov">🔍 Pretraži naslov</label>
      <input type="text" id="f-naslov" name="naslov"
             value="<?= htmlspecialchars($filterNaslov) ?>"
             placeholder="npr. Godfather...">
    </div>

    <div class="filter-group">
      <label for="f-zanr">🎭 Žanr</label>
      <select id="f-zanr" name="zanr">
        <option value="">— Svi žanrovi —</option>
        <?php foreach ($zanrovi as $z): ?>
          <option value="<?= htmlspecialchars($z) ?>"
                  <?= $filterZanr === $z ? 'selected' : '' ?>>
            <?= htmlspecialchars($z) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="filter-group">
      <label for="f-godina">📅 Godina</label>
      <input type="number" id="f-godina" name="godina"
             value="<?= htmlspecialchars($filterGodina) ?>"
             min="1900" max="2025" placeholder="npr. 1994">
    </div>

    <div class="filter-group">
      <label for="f-zemlja">🌍 Zemlja</label>
      <input type="text" id="f-zemlja" name="zemlja"
             value="<?= htmlspecialchars($filterZemlja) ?>"
             placeholder="npr. USA">
    </div>

  </div>

  <div class="filteri-akcije">
    <button type="submit" class="btn-primary">🔎 Filtriraj</button>
    <a href="films.php" class="btn-secondary">Resetiraj</a>
    <span class="rezultati-info">Pronađeno: <strong><?= count($filmovi) ?></strong> filmova</span>
    <?php if (!jePrijavljen()): ?>
      <span class="rezultati-info" style="color:var(--color-accent);">
        ⚠️ <a href="login.php" style="color:var(--color-accent);">Prijavite se</a> za dodavanje u videoteku
      </span>
    <?php endif; ?>
  </div>
</form>

<!-- TABLICA FILMOVA -->
<div class="table-wrapper fade-in" style="margin-top:1.5rem;">
  <table id="filmovi-tablica">
    <thead>
      <tr>
        <th>#</th>
        <th><a href="<?= sortUrl('naslov') ?>" class="sort-link">Naslov<?= sortArrow('naslov') ?></a></th>
        <th><a href="<?= sortUrl('godina') ?>" class="sort-link">Godina<?= sortArrow('godina') ?></a></th>
        <th>Žanr</th>
        <th><a href="<?= sortUrl('trajanje_min') ?>" class="sort-link">Trajanje<?= sortArrow('trajanje_min') ?></a></th>
        <th><a href="<?= sortUrl('ocjena') ?>" class="sort-link">Ocjena<?= sortArrow('ocjena') ?></a></th>
        <th>Zemlja</th>
        <th>Videoteka</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($filmovi)): ?>
        <tr>
          <td colspan="8" style="text-align:center;padding:2rem;color:var(--color-text-muted);">
            Nema filmova koji odgovaraju filterima.
          </td>
        </tr>
      <?php else: ?>
        <?php foreach ($filmovi as $i => $film): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><strong><?= htmlspecialchars($film['naslov']) ?></strong></td>
            <td><?= htmlspecialchars($film['godina']) ?></td>
            <td><span class="genre-badge"><?= htmlspecialchars($film['zanr']) ?></span></td>
            <td><?= htmlspecialchars($film['trajanje_min']) ?> min</td>
            <td>
              <span class="ocjena-badge <?= (float)$film['ocjena'] < 5.0 ? 'ocjena-niska' : '' ?>">
                ⭐ <?= number_format((float)$film['ocjena'], 1) ?>
              </span>
            </td>
            <td><?= htmlspecialchars($film['zemlja_porijekla'] ?? '—') ?></td>
            <td>
              <?php if (jePrijavljen()): ?>
                <?php if (isset($videotekaSet[$film['id']])): ?>
                  <span class="btn-kosarica btn-dodano">✓ Dodano</span>
                <?php else: ?>
                  <form method="POST" action="films.php?<?= http_build_query($_GET) ?>" style="display:inline;">
                    <input type="hidden" name="dodaj_film" value="1">
                    <input type="hidden" name="id_film" value="<?= $film['id'] ?>">
                    <button type="submit" class="btn-kosarica">
                      <?php if ((float)$film['ocjena'] < 5.0): ?>
                        ⚠️ Dodaj*
                      <?php else: ?>
                        + Dodaj
                      <?php endif; ?>
                    </button>
                  </form>
                <?php endif; ?>
              <?php else: ?>
                <a href="login.php" class="btn-kosarica">🔐 Prijavi se</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php if (jePrijavljen()): ?>
  <p style="font-size:.8rem;color:var(--color-text-muted);margin-top:.5rem;">
    * Filmovi s ocjenom ispod 5.0 su označeni — dodavanje će prikazati upozorenje.
  </p>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
