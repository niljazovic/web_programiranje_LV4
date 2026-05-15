<?php

require_once 'includes/auth.php';
require_once 'includes/db.php';

zahtijevajPrijavu();

$pdo      = getDB();
$korisnik = trenutniKorisnik();
$poruka   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ukloni_film'])) {
    $idFilma = (int)($_POST['id_film'] ?? 0);
    $stmt = $pdo->prepare('DELETE FROM zeljeni_filmovi WHERE id_korisnik = ? AND id_film = ?');
    $stmt->execute([$korisnik['id'], $idFilma]);
    $poruka = 'Film uklonjen iz videoteke.';
}

$stmt = $pdo->prepare('
    SELECT f.*, zf.datum_dodavanja
    FROM zeljeni_filmovi zf
    JOIN filmovi f ON f.id = zf.id_film
    WHERE zf.id_korisnik = ?
    ORDER BY zf.datum_dodavanja DESC
');
$stmt->execute([$korisnik['id']]);
$filmovi = $stmt->fetchAll();

$ukupno    = count($filmovi);
$ukupnoMin = array_sum(array_column($filmovi, 'trajanje_min'));
$prosOcjena = $ukupno > 0 ? array_sum(array_column($filmovi, 'ocjena')) / $ukupno : 0;

$pageTitle    = 'Moja videoteka | Netflix LV4';
$pageSubtitle = 'Moja videoteka';
require_once 'includes/header.php';
?>

<?php if ($poruka): ?>
  <div class="alert alert-success">✅ <?= htmlspecialchars($poruka) ?></div>
<?php endif; ?>

<div class="lv3-section-header">
  <div class="lv4-badge">Osobna videoteka</div>
  <h2>📚 Moja videoteka</h2>
  <p class="lv3-opis">Filmovi koje ste odabrali za gledanje — trajno pohranjeni u bazi.</p>
</div>

<!-- Statistike -->
<div class="stat-grid fade-in">
  <div class="stat-box">
    <div class="stat-broj"><?= $ukupno ?></div>
    <div class="stat-label">Filmova</div>
  </div>
  <div class="stat-box">
    <div class="stat-broj"><?= floor($ukupnoMin / 60) ?>h <?= $ukupnoMin % 60 ?>m</div>
    <div class="stat-label">Ukupno trajanje</div>
  </div>
  <div class="stat-box">
    <div class="stat-broj"><?= number_format($prosOcjena, 1) ?></div>
    <div class="stat-label">Prosj. ocjena</div>
  </div>
</div>

<?php if (empty($filmovi)): ?>
  <div class="prazna-videoteka">
    <p>📭 Vaša videoteka je prazna.</p>
    <a href="films.php" class="btn-primary">Pregledaj filmove</a>
  </div>
<?php else: ?>

<div class="table-wrapper fade-in" style="margin-top:1.5rem;">
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Naslov</th>
        <th>Godina</th>
        <th>Žanr</th>
        <th>Trajanje</th>
        <th>Ocjena</th>
        <th>Dodano</th>
        <th>Akcija</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($filmovi as $i => $film): ?>
        <tr <?= (float)$film['ocjena'] < 5.0 ? 'class="row-niska-ocjena"' : '' ?>>
          <td><?= $i + 1 ?></td>
          <td>
            <strong><?= htmlspecialchars($film['naslov']) ?></strong>
            <?php if ((float)$film['ocjena'] < 5.0): ?>
              <span class="upozorenje-inline">⚠️ niska ocjena</span>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($film['godina']) ?></td>
          <td><span class="genre-badge"><?= htmlspecialchars($film['zanr']) ?></span></td>
          <td><?= htmlspecialchars($film['trajanje_min']) ?> min</td>
          <td>
            <span class="ocjena-badge <?= (float)$film['ocjena'] < 5.0 ? 'ocjena-niska' : '' ?>">
              ⭐ <?= number_format((float)$film['ocjena'], 1) ?>
            </span>
          </td>
          <td style="font-size:.78rem;color:var(--color-text-muted);">
            <?= date('d.m.Y.', strtotime($film['datum_dodavanja'])) ?>
          </td>
          <td>
            <form method="POST" action="videoteka.php"
                  onsubmit="return confirm('Ukloniti film iz videoteke?')">
              <input type="hidden" name="ukloni_film" value="1">
              <input type="hidden" name="id_film" value="<?= $film['id'] ?>">
              <button type="submit" class="btn-ukloni-film">✕ Ukloni</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php
// Upozorenje za filmove s niskom ocjenom u videoteci
$niskaOcjena = array_filter($filmovi, fn($f) => (float)$f['ocjena'] < 5.0);
if (!empty($niskaOcjena)):
?>
<div class="upozorenje-box">
  <h4>⚠️ Upozorenje</h4>
  <p>U Vašoj videoteci se nalazi <?= count($niskaOcjena) ?> film(ova) s prosječnom ocjenom ispod 5.0:</p>
  <ul>
    <?php foreach ($niskaOcjena as $f): ?>
      <li><strong><?= htmlspecialchars($f['naslov']) ?></strong> — ocjena: <?= $f['ocjena'] ?>/10</li>
    <?php endforeach; ?>
  </ul>
  <p>Jeste li sigurni da ih želite zadržati?</p>
</div>
<?php endif; ?>

<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
