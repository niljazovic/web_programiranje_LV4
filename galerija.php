<?php

require_once 'includes/auth.php';
require_once 'includes/db.php';

$pdo      = getDB();
$korisnik = trenutniKorisnik();
$poruka   = '';
$greska   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ocijeni'])) {
    zahtijevajPrijavu();
    $idSlike  = (int)($_POST['id_slika'] ?? 0);
    $ocjena   = (int)($_POST['ocjena'] ?? 0);

    if ($ocjena < 1 || $ocjena > 5) {
        $greska = 'Ocjena mora biti između 1 i 5.';
    } else {
        $stmt = $pdo->prepare('
            INSERT INTO ocjene (id_korisnik, id_slika, ocjena)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE ocjena = VALUES(ocjena), vrijeme_ocjene = CURRENT_TIMESTAMP
        ');
        $stmt->execute([$korisnik['id'], $idSlike, $ocjena]);
        $poruka = 'Ocjena uspješno spremljena!';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_sliku'])) {
    zahtijevajPrijavu();

    if (!isset($_FILES['slika']) || $_FILES['slika']['error'] !== UPLOAD_ERR_OK) {
        $greska = 'Greška pri uploadu slike.';
    } else {
        $file     = $_FILES['slika'];
        $maxSize  = 5 * 1024 * 1024;
        $dozvoljeniTipovi = ['image/jpeg', 'image/png'];
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $dozvoljeniTipovi)) {
            $greska = 'Samo JPEG i PNG formati su dozvoljeni!';
        } elseif ($file['size'] > $maxSize) {
            $greska = 'Slika ne smije biti veća od 5MB!';
        } else {
            $uploadDir = 'uploads/slike/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $ext      = $mimeType === 'image/jpeg' ? 'jpg' : 'png';
            $naziv    = uniqid('slika_') . '.' . $ext;
            $putanja  = $uploadDir . $naziv;

            if (move_uploaded_file($file['tmp_name'], $putanja)) {
                $opis = trim($_POST['opis'] ?? '');
                $stmt = $pdo->prepare('INSERT INTO slike (naziv_datoteke, opis, putanja, izvor) VALUES (?, ?, ?, "lokalno")');
                $stmt->execute([$naziv, $opis, $putanja]);
                $poruka = 'Slika uspješno uploadana!';
            } else {
                $greska = 'Greška pri spremanju slike na server.';
            }
        }
    }
}

$slike = $pdo->query('
    SELECT s.*,
           COALESCE(AVG(o.ocjena), 0) AS prosjecna_ocjena,
           COUNT(o.id) AS broj_ocjena
    FROM slike s
    LEFT JOIN ocjene o ON o.id_slika = s.id
    GROUP BY s.id
    ORDER BY s.datum_dodavanja DESC
')->fetchAll();

$moje_ocjene = [];
if ($korisnik) {
    $stmt = $pdo->prepare('SELECT id_slika, ocjena FROM ocjene WHERE id_korisnik = ?');
    $stmt->execute([$korisnik['id']]);
    foreach ($stmt->fetchAll() as $o) {
        $moje_ocjene[$o['id_slika']] = (int)$o['ocjena'];
    }
}

$pageTitle    = 'Galerija | Netflix LV4';
$pageSubtitle = 'Galerija';
require_once 'includes/header.php';
?>

<link rel="stylesheet" href="styles/style_slike.css">
<link rel="stylesheet" href="styles/galerija_ocjene.css">

<?php if ($poruka): ?>
  <div class="alert alert-success">✅ <?= htmlspecialchars($poruka) ?></div>
<?php endif; ?>
<?php if ($greska): ?>
  <div class="alert alert-error">⚠️ <?= htmlspecialchars($greska) ?></div>
<?php endif; ?>

<div class="lv3-section-header">
  <h2>🖼️ Galerija slika</h2>
  <p class="lv3-opis">
    <?php if (jePrijavljen()): ?>
      Kliknite na zvjezdice ispod slike za ocjenjivanje.
    <?php else: ?>
      <a href="login.php" style="color:var(--color-accent);">Prijavite se</a> za ocjenjivanje slika.
    <?php endif; ?>
  </p>
</div>

<!-- UPLOAD FORMA -->
<?php if (jePrijavljen()): ?>
<div class="upload-box fade-in">
  <h3>📤 Dodaj novu sliku</h3>
  <form method="POST" action="galerija.php" enctype="multipart/form-data" class="upload-forma">
    <input type="hidden" name="upload_sliku" value="1">
    <div class="upload-grid">
      <div class="form-group">
        <label>Odaberi sliku (JPEG/PNG, max 5MB)</label>
        <input type="file" name="slika" accept="image/jpeg,image/png" required>
      </div>
      <div class="form-group">
        <label>Opis slike (opcionalno)</label>
        <input type="text" name="opis" placeholder="npr. Filmska scena..." maxlength="255">
      </div>
    </div>
    <button type="submit" class="btn-primary" style="margin-top:.75rem;">Uploadaj sliku</button>
  </form>
</div>
<?php endif; ?>

<!-- GALERIJA S OCJENAMA -->
<div class="galerija-ocjene fade-in">
  <?php foreach ($slike as $slika): ?>
    <?php $mojaOcjena = $moje_ocjene[$slika['id']] ?? 0; ?>
    <div class="slika-card">

      <div class="slika-wrapper">
        <img src="<?= htmlspecialchars($slika['putanja']) ?>"
             alt="<?= htmlspecialchars($slika['opis'] ?? $slika['naziv_datoteke']) ?>"
             loading="lazy">
      </div>

      <div class="slika-info">
        <p class="slika-opis"><?= htmlspecialchars($slika['opis'] ?? 'Bez opisa') ?></p>

        <div class="prosjecna-ocjena">
          <span class="ocjena-imdb">
            ⭐ <?= $slika['broj_ocjena'] > 0 ? number_format((float)$slika['prosjecna_ocjena'], 1) : '—' ?>
          </span>
          <span class="ocjena-info"><?= $slika['broj_ocjena'] ?> ocjena</span>
        </div>

        <?php if (jePrijavljen()): ?>
        <form method="POST" action="galerija.php" class="forma-ocjena">
          <input type="hidden" name="ocijeni" value="1">
          <input type="hidden" name="id_slika" value="<?= $slika['id'] ?>">
          <div class="zvjezdice">
            <?php for ($z = 5; $z >= 1; $z--): ?>
              <input type="radio" name="ocjena"
                     id="z_<?= $slika['id'] ?>_<?= $z ?>"
                     value="<?= $z ?>"
                     <?= $mojaOcjena === $z ? 'checked' : '' ?>>
              <label for="z_<?= $slika['id'] ?>_<?= $z ?>" title="<?= $z ?> zvjezdica">★</label>
            <?php endfor; ?>
          </div>
          <button type="submit" class="btn-ocijeni">
            <?= $mojaOcjena > 0 ? 'Ažuriraj ocjenu' : 'Ocijeni' ?>
          </button>
        </form>
        <?php else: ?>
          <p class="prijava-hint"><a href="login.php">Prijavi se</a> za ocjenu</p>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
