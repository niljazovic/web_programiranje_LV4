<?php
// =============================================
// galerija.php - Galerija slika (PHP verzija)
// Nadovezuje se na slike.html iz LV1/LV2
// =============================================

require_once 'includes/auth.php';

$pageTitle    = 'Galerija | Netflix LV4';
$pageSubtitle = 'Galerija';
require_once 'includes/header.php';
?>

<link rel="stylesheet" href="styles/style_slike.css">

<!-- Lightbox overlayevi -->
<?php for ($i = 1; $i <= 16; $i++): ?>
<div id="lb<?= $i ?>" class="lightbox-overlay">
  <img src="https://picsum.photos/seed/film<?= $i ?>/900/600" alt="Slika <?= $i ?>" loading="lazy">
  <p class="lightbox-caption">Slika <?= $i ?></p>
  <a href="#" class="lightbox-close">&#x2715; Zatvori</a>
</div>
<?php endfor; ?>

<section class="galerija" role="region" aria-label="Galerija filmskih fotografija">
  <h1>Galerija slika</h1>

  <?php for ($i = 1; $i <= 16; $i++): ?>
  <figure class="galerija_slika">
    <a href="#lb<?= $i ?>" aria-label="Otvori sliku <?= $i ?> u punoj veličini">
      <img src="https://picsum.photos/300/200?random=<?= $i ?>"
           alt="Filmska scena <?= $i ?>" loading="lazy" width="300" height="200">
    </a>
    <figcaption>Slika <?= $i ?></figcaption>
  </figure>
  <?php endfor; ?>

</section>

<?php require_once 'includes/footer.php'; ?>
